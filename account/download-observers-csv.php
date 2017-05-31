<?php

require_once 'common.inc.php';

use Battis\BootstrapSmarty\NotificationMessage;

$STEP_INSTRUCTIONS = 1;
$STEP_CSV = 2;

$toolbox->cache_pushKey(basename(__FILE__, '.php'));

$step = (empty($_REQUEST['step']) ? $STEP_INSTRUCTIONS : $_REQUEST['step']);

switch ($step) {
    case $STEP_CSV:
        try {
            $account = (empty($_REQUEST['account']) ? 1 : $_REQUEST['account']);
            $toolbox->cache_pushKey($account);
            if (empty($_REQUEST['account'])) {
                $toolbox->smarty_addMessage(
                    'No Account',
                    'No account specified, all users included in CSV file.',
                    NotificationMessage::WARNING
                );
            }

            $data = $toolbox->cache_get('observers');
            if ($data === false) {
                $users = $toolbox->api_get("accounts/$account/users", [
                    'search_term' => '-advisor'
                ]);
                $data[] = [
                    'id',
                    'user_id',
                    'login_id',
                    'password',
                    'full_name',
                    'sortable_name',
                    'short_name',
                    'email',
                    'status'
                ];
                foreach ($users as $user) {
                    $response = $toolbox->mysql_query("
                        SELECT *
                            FROM `observers`
                            WHERE
                                `id` = '{$user['id']}'
                            LIMIT 1
                    ");
                    $row = $response->fetch_assoc();
                    if ($row) {
                        $data[] = [
                            $toolbox->blank($user, 'id'),
                            $toolbox->blank($user, 'sis_user_id'),
                            $toolbox->blank($user, 'login_id'),
                            $toolbox->blank($row, 'password'),
                            $toolbox->blank($user, 'name'),
                            $toolbox->blank($user, 'sortable_name'),
                            $toolbox->blank($user, 'short_name'),
                            $toolbox->blank($user, 'email'),
                            'active'
                        ];
                    }
                }
                $toolbox->getCache()->setLifetime(60);
                $toolbox->cache_set('observers', $data);
            }

            $csv = urlencode($toolbox->getCache()->getHierarchicalKey('observers', $data));
            $filename = urlencode(date('Y-m-d_H-i-s') . "_account-{$_REQUEST['account']}_observers");
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
    default:
        $toolbox->smarty_assign('formHidden', [
            'step' => $STEP_CSV,
            'account' => $_SESSION[ACCOUNT_ID]
        ]);
        $toolbox->smarty_display(basename(__FILE__, '.php') . '/instructions.tpl');
}

$toolbox->cache_popKey();
