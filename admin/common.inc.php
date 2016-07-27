<?php

require_once __DIR__ . '/../common.inc.php';

use Battis\DataUtilities;

$toolbox->smarty_assign('category', DataUtilities::titleCase(basename(__DIR__)));
