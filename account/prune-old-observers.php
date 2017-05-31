<?php

require 'common.inc.php';

use Battis\BootstrapSmarty\NotificationMessage;

$STEP_INSTRUCTIONS = 1;
$STEP_PRUNE = 2;

$toolbox->cache_pushKey(basename(__FILE__, '.php'));

$step = (empty($_REQUEST['step']) ? $STEP_INSTRUCTIONS : $_REQUEST['step']);

switch ($step) {
    case $STEP_PRUNE:
        /* generate CSV for download */
        try {
            $toolbox->cache_pushKey($_REQUEST['account']);
            $expired = [];
            foreach ($toolbox->getTermList() as $term) {
                if (strtotime($term['end_at']) < time()) {
                    $expired[$term['id']] = true;
                }
            }
            $prune = $toolbox->cache_get('prune');
            if ($prune === false) {
                $prune[] = [
                    'user_id',
                    'login_id',
                    'sortable_name',
                    'email',
                    'status'
                ];
                $advisors = $toolbox->api_get("accounts/{$_REQUEST['account']}/users", [
                    'search_term' => '-advisor'
                ]);

                foreach ($advisors as $advisor) {
                    $observees = $toolbox->api_get("users/{$advisor['id']}/observees");
                    if ($observees->count() > 0) {
                        $inactive = true;
                        $courses = $toolbox->api_get("users/{$advisor['id']}/courses");
                        foreach ($courses as $course) {
                            if (!isset($course['access_restricted_by_date']) &&
                                (
                                    isset($course['enrollment_term_id']) &&
                                    !array_key_exists($course['enrollment_term_id'], $expired)
                                )
                            ) {
                                $inactive = false;
                                break;
                            }
                        }
                        if ($inactive) {
                            $prune[] = [
                                $toolbox->blank($advisor, 'sis_user_id'),
                                $toolbox->blank($advisor, 'login_id'),
                                $toolbox->blank($advisor, 'sortable_name'),
                                $toolbox->blank($advisor, 'email'),
                                'deleted'
                            ];
                        }
                    }
                }
                $toolbox->getCache()->setLifetime(60);
                $toolbox->cache_set('prune', $prune);
            }

            $csv = urlencode($toolbox->getCache()->getHierarchicalKey('prune'));
            $filename = urlencode(date('Y-m-d_H-i-s') . "_prune-account-{$_REQUEST['account']}_observers");
            $toolbox->smarty_assign([
                'csv' => $csv,
                'filename' => $filename
            ]);
            $toolbox->smarty_addMessage(
                'Ready for Download',
                "<a href=\"../generate-csv.php?data=$csv&" .
                "filename=$filename\">$filename</a> is ready and download " .
                'should start automatically in a few seconds. Click the link ' .
                'if the download does not start automatically.',
                NotificationMessage::SUCCESS
            );
            $toolbox->cache_popKey();
        } catch (Exception $e) {
            $toolbox->smarty_addMessage('Error ' . $e->getCode(), $e->getMessage(), NotificationMessage::DANGER);
        }

        /* flows into $STEP_INSTRUCTIONS */
    case $STEP_INSTRUCTIONS:
        $toolbox->smarty_assign('formHidden', [
            'step' => $STEP_PRUNE,
            'account' => $_SESSION[ACCOUNT_ID]
        ]);
        $toolbox->smarty_display(basename(__FILE__, '.php') . '/instructions.tpl');
}

$toolbox->cache_popKey();
