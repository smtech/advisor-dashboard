<?php

require_once 'common.inc.php';

use smtech\AdvisorDashboard\Toolbox;
use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;
use smtech\ReflexiveCanvasLTI\Exception\ConfigurationException;

define('ACTION_CONFIG', 'config');
define('ACTION_INSTALL', 'install');
define('ACTION_CONSUMERS', 'consumers');
define('ACTION_UNSPECIFIED', false);

/* store any requested actions for future handling */
$action = (empty($_REQUEST['action']) ?
    ACTION_UNSPECIFIED :
    strtolower($_REQUEST['action'])
);

/* action requests only come from outside the LTI! */
if ($action) {
    unset($_SESSION[ToolProvider::class]);
}

/* authenticate LTI launch request, if present */
if ($toolbox->lti_isLaunching()) {
    $toolbox->resetSession();
    $toolbox->lti_authenticate();
    exit;
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
    case ACTION_INSTALL:
        $_SESSION['toolbox'] = Toolbox::fromConfiguration(CONFIG_FILE, true);
        $toolbox =& $_SESSION['toolbox'];

        /* test to see if we can connect to the API */
        try {
            $toolbox->getAPI();
        } catch (ConfigurationException $e) {
            /* if there isn't an API token in config.xml, are there OAuth credentials? */
            if ($e->getCode() === ConfigurationException::CANVAS_API_INCORRECT) {
                $toolbox->interactiveGetAccessToken(
                    'This tool requires access to the Canvas APIs by an administrative user. ' .
                    'This API access is used to query student analytics data that is presented on ' .
                    'the Advisor Dashboard. Please enter the URL of your Canvas instance below ' .
                    '(e.g. <code>https://canvas.instructure.com</code> -- the URL that you would ' .
                    'enter to log in to Canvas). If you are not already logged in, you will be asked ' .
                    'to log in. After logging in, you will be asked to authorize this tool.</p>' .
                    '<p>If you are already logged, but <em>not</em> logged in as an administrative user, ' .
                    'please log out now, so that you may log in as administrative user to authorize this tool.'
                );
                exit;
            } else { /* no (understandable) API credentials available -- doh! */
                throw $e;
            }
        }

        /* finish by opening consumers control panel */
        header('Location: consumers.php');
        exit;

    /* show LTI configuration XML file */
    case ACTION_CONFIG:
        header('Content-type: application/xml');
        echo $toolbox->saveConfigurationXML();
        exit;
}
