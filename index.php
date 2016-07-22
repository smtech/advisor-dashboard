<?php

require_once 'common.inc.php';

use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;

/* did we receive an LTI launch request from a Tool Consumer? */
if ($toolbox->lti_isLaunching()) {
	$_SESSION = [];
	$toolbox->lti_authenticate();
	exit;
}

/* did we receive a request for the LTI configuration XML? */
if (!empty($_REQUEST['config'])) {
	header('Content-type: application/xml');
	echo $toolbox->saveConfigurationXML();
	exit;
}

/* otherwise, route into account or course views */
if (!empty($_SESSION[ToolProvider::class]['canvas']['account_id'])) {
	$_SESSION[ACCOUNT_ID] = $_SESSION[ToolProvider::class]['canvas']['account_id'];
	header("Location: account/");
	exit;
} elseif (!empty($_SESSION[ToolProvider::class]['canvas']['course_id'])) {
	$_SESSION[COURSE_ID] = $_SESSION[ToolProvider::class]['canvas']['course_id'];
	header('Location: course/');
	exit;
} else {
	$toolbox->smarty_display('unauthorized.tpl');
}
