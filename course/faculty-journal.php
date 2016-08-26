<?php

require_once 'common.inc.php';

if ($firstStudent === false) {
    $toolbox->smarty_display('no-advisees.tpl');
    exit;
}
