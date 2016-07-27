<?php

require_once 'common.inc.php';

use smtech\AdvisorDashboard\Toolbox;
use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;

define('ACTION_CONFIG', 'config');
define('ACTION_INSTALL', 'install');
define('ACTION_UNSPECIFIED', false);

/* store any requested actions for future handling */
$action = (empty($_REQUEST['action']) ?
	ACTION_UNSPECIFIED :
	strtolower($_REQUEST['action'])
);

/* authenticate LTI launch request, if present */
if ($toolbox->lti_isLaunching()) {
	$_SESSION = []; /* clear all session data */
	$toolbox->lti_authenticate();
	exit;
}

/* check if referrer matches LTI launch request referrer */
if (!empty($_SESSION[ToolProvider::class]) && (
	empty($_SERVER['HTTP_REFERRER']) ||
	$_SERVER['HTTP_REFERRER'] != $_SESSION[ToolProvider::class]['httpReferrer'])
) {
	/* ...and clear authentication data if referrers don't match */
	unset($_SESSION[ToolProvider::class]);
}

/* if authenticated LTI launch, redirect to appropriate placement view */
if (!empty($_SESSION[ToolProvider::class]['canvas']['account_id'])) {
	$_SESSION[ACCOUNT_ID] = $_SESSION[ToolProvider::class]['canvas']['account_id'];
	header("Location: account/");
	exit;
} elseif (!empty($_SESSION[ToolProvider::class]['canvas']['course_id'])) {
	$_SESSION[COURSE_ID] = $_SESSION[ToolProvider::class]['canvas']['course_id'];
	header('Location: course/');
	exit;

/* if not authenticated, default to showing credentials */
} else {
	$action = (empty($action) ?
		ACTION_CONFIG :
		$action
	);
}

/* process any actions */
switch ($action) {

	/* reset cached install data from config file */
	case ACTION_INSTALL: {
		$_SESSION['toolbox'] = Toolbox::fromConfiguration(CONFIG_FILE, true);
		$toolbox =& $_SESSION['toolbox'];

		/* test to see if we can connect to the API */
		try {
			$toolbox->getAPI();
		} catch (ConfigurationException $e) {

			/* if there isn't an API token in config.xml, are there OAuth credentials? */
			$canvas = $toolbox->config('TOOL_CANVAS_API');
			if ($e->getCode() === ConfigurationException::CANVAS_API_INCORRECT &&
				!empty($canvas['key']) &&
				!empty($canvas['secret'])
			) {
				/* if so, request an API access token interactively */
				header('Location: ' . $toolbox->config('APP_URL') . "/admin/oauth.php?return={$_SERVER['REQUEST_URI']}");
				exit;
			} else { /* no (understandable) API credentials available -- doh! */
				throw $e;
			}
		}

		/* finish by opening consumers control panel */
		header("Location: admin/");
		exit;
	}

	/* show LTI configuration XML file */
	case ACTION_CONFIG: {
		header('Content-type: application/xml');
		echo $toolbox->saveConfigurationXML();
		exit;
	}
}
