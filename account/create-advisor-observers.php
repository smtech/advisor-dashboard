<?php

require_once 'common.inc.php';

use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;
use Battis\BootstrapSmarty\NotificationMessage;

/* some configuration */
// TODO make configurable
$PASSWORD_LENGTH = 10; // be reasonable
$PASSWORD_SECURE = false; // we actually want things people can remember
$PASSWORD_NUMERALS = false; // no need for numbers
$PASSWORD_CAPITALS = false; // let's not have confusing capital letters
$PASSWORD_AMBIGUOUS = false; // since we have no numbers, ambigous characters are fine
$PASSWORD_NO_VOWELS = false; // we'll risk generating dirty words
$PASSWORD_SYMBOLS = false; // no confusing symbols

$STEP_INSTRUCTIONS = 1;
$STEP_GENERATE = 2;

/* create observers table if it doesn't already exist */
// TODO make observer table name configurable
if ($toolbox->mysql_query("SHOW TABLES LIKE 'lti_%'")->num_rows == 0) {
    $toolbox->mysql_query("
        CREATE TABLE `observers` (
          `id` int(11) unsigned NOT NULL,
          `password` varchar(10) NOT NULL DEFAULT '',
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ");
}

$toolbox->cache_pushKey(basename(__FILE__, '.php'));

$pwgen = new PWGen(
    $PASSWORD_LENGTH,
    $PASSWORD_SECURE,
    $PASSWORD_NUMERALS,
    $PASSWORD_CAPITALS,
    $PASSWORD_AMBIGUOUS,
    $PASSWORD_NO_VOWELS,
    $PASSWORD_SYMBOLS
);

$step = (empty($_REQUEST['step']) ? $STEP_INSTRUCTIONS : $_REQUEST['step']);

switch ($step) {
    case $STEP_GENERATE:
        /*
         * TODO test for account and term
         */

        /* walk through all of our advisory courses.. */
        $advisories = $toolbox->api_get("accounts/{$_REQUEST['account']}/courses", [
            'with_enrollments' => 'true',
            'enrollment_term_id' => $_REQUEST['term']
        ]);

        $courses = 0;
        $updated = 0;
        $created = 0;
        $reset = 0;

        foreach ($advisories as $advisory) {
            /* cache the teacher */
            $advisors = $toolbox->api_get("courses/{$advisory['id']}/users", [
                'enrollment_type' => 'teacher'
            ]);
            if ($advisors->count()) {
                $advisor = $advisors[0];
                $courses++;
            } else {
                $toolbox->smarty_addMessage(
                    "{$advisory['name']}",
                    "No teacher was found in <a target=\"_parent\" href=\"" .
                        $_SESSION[CANVAS_INSTANCE_URL] .
                        "/courses/{$advisory['id']}\">this advisory</a> and it was skipped.",
                    NotificationMessage::DANGER
                );
                break;
            }
            $advisorLastName = substr($advisor['sortable_name'], 0, strpos($advisor['sortable_name'], ','));

            /* look at all the student enrollments... */
            $advisees = $toolbox->api_get("courses/{$advisory['id']}/users", [
                'enrollment_type' => 'student'
            ]);

            foreach ($advisees as $advisee) {
                /* generate what the advisor account info should be */
                $observer = [
                    'sis_user_id' => "{$advisee['sis_user_id']}-advisor",
                    'login' => strtolower(
                        'advisor' . substr($advisee['login_id'], 0, strpos($advisee['login_id'], '@'))
                    ),
                    'password' => $pwgen->generate(),
                    'name' => "{$advisee['name']} ($advisorLastName Advisor)",
                    'sortable_name' => "{$advisee['sortable_name']} ($advisorLastName Advisor)",
                    'short_name' => "{$advisee['short_name']} ($advisorLastName Advisor)",

                    /*
                     * this email format works for Google Apps domains --
                     * it's the advisor's email address with a tag that
                     * identifies the email as relating to the advisee
                     */
                    'email' => strtolower(
                        substr($advisor['sis_login_id'], 0, strpos($advisor['sis_login_id'], '@')) .
                        '+' . substr($advisee['sis_login_id'], 0, strpos($advisee['sis_login_id'], '@')) .
                        substr($advisor['sis_login_id'], strpos($advisor['sis_login_id'], '@'))
                    )
                ];

                /* check for an existing advisor account */
                $existing = true;
                try {
                    $existing = $toolbox->api_get("users/sis_user_id:{$observer['sis_user_id']}");
                } catch (Exception $e) {
                    /* if the request generates an error... the observer does not exist */
                    $existing = false;
                }

                /* if there is already an advisor account, update it */
                if ($existing) {
                    /* update name */
                    $toolbox->api_put("users/{$existing['id']}", [
                        'user[name]' => $observer['name'],
                        'user[short_name]' => $observer['short_name'],
                        'user[sortable_name]' => $observer['sortable_name'],
                    ]);

                    /* update email */
                    $communicationChannels = $toolbox->api_get("users/{$existing['id']}/communication_channels");
                    $emailExists = false;
                    $channelsToDelete = [];
                    foreach ($communicationChannels as $communicationChannel) {
                        if ($communicationChannel['address'] != $observer['email']) {
                            $channelsToDelete[] = $communicationChannel['id'];
                        } else {
                            $emailExists = true;
                        }
                    }
                    if (!$emailExists) {
                        $toolbox->api_post("users/{$existing['id']}/communication_channels", [
                            'communication_channel[type]' => 'email',
                            'communication_channel[address]' => $observer['email'],
                            'skip_confirmation' => true,
                            'position' => 1
                        ]);
                    }
                    foreach ($channelsToDelete as $channelToDelete) {
                        $toolbox->api_delete("users/{$existing['id']}/communication_channels/{$channelToDelete}");
                    }

                    /* turn off notifications */
                    $communicationChannels = $toolbox->api_get("users/{$existing['id']}/communication_channels");
                    $notificationPreferences = $toolbox->api_get(
                        "users/{$existing['id']}/communication_channels/" .
                        "{$communicationChannels[0]['id']}/notification_preferences"
                    );
                    $newPrefs = [];
                    foreach ($notificationPreferences['notification_preferences'] as $pref) {
                        if (($pref['frequency'] != 'never') &&
                            ($pref['notification'] != 'confirm_sms_communication_channel')) {
                            $newPrefs["notification_preferences[{$pref['notification']}][frequency]"] = 'never';
                        }
                    }
                    if (count($newPrefs)) {
                        $newPrefs['as_user_id'] = $existing['id'];
                        $toolbox->api_put(
                            "users/self/communication_channels/" .
                            "{$communicationChannels[0]['id']}/notification_preferences",
                            $newPrefs
                        );
                    }

                    /* reset password */
                    if (!empty($_REQUEST['reset_passwords'])) {
                        $logins = $toolbox->api_get("users/{$existing['id']}/logins");

                        // FIXME I'm totally just assuming that a user account has a login (and only one)
                        $toolbox->api_put("accounts/1/logins/{$logins[0]['id']}", [
                            'login[password]' => $observer['password']
                        ]);

                        // FIXME need some error-checking here
                        $toolbox->mysql_query("
                            UPDATE `observers`
                                SET
                                    `password` = '{$observer['password']}'
                                WHERE
                                    `id` = '{$existing['id']}'
                        ");

                        $reset++;
                    }
                    $updated++;

                /* otherwise, create one! */
                } else {
                    $existing = $toolbox->api_post('accounts/1/users', [
                        'user[name]' => $observer['name'],
                        'user[short_name]' => $observer['short_name'],
                        'user[sortable_name]' => $observer['sortable_name'],
                        'pseudonym[unique_id]' => $observer['login'],
                        'psuedonym[password]' => $observer['password'],
                        'pseudonym[sis_user_id]' => $observer['sis_user_id'],
                        'communication_channel[type]' => 'email',
                        'communication_channel[address]' => $observer['email'],
                        'communication_channel[skip_confirmation]' => true
                    ]);

                    // FIXME need a little error-checking here
                    $toolbox->mysql_query("
                        INSERT INTO `observers`
                            (
                                `id`,
                                `password`
                            ) VALUES (
                                '{$existing['id']}',
                                '{$observer['password']}'
                            )
                    ");

                    $created++;
                }

                /* set up observation pairing */
                $toolbox->api_put("users/{$existing['id']}/observees/{$advisee['id']}");
            }
        }

        $toolbox->smarty_addMessage(
            'Advisor-Observers',
            "$created new observers created, $updated observers updated ($reset " .
                "passwords reset) in $courses advisory groups.",
            NotificationMessage::SUCCESS
        );

        /* flows into $STEP_INSTRUCTIONS */

    case $STEP_INSTRUCTIONS:
    default:
        $toolbox->smarty_assign([
            'terms' => $toolbox->getTermList(),
            'formHidden' => [
                'step' => $STEP_GENERATE,
                'account' => $_SESSION[ACCOUNT_ID]
            ]
        ]);
        $toolbox->smarty_display(basename(__FILE__, '.php') . '/instructions.tpl');
}

$toolbox->cache_popKey();
