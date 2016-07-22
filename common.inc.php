<?php

require_once __DIR__ . '/vendor/autoload.php';

use smtech\AdvisorDashboard\Toolbox;
use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;
use smtech\ReflexiveCanvasLTI\Exception\ConfigurationException;
use Battis\DataUtilities;

define('CONFIG_FILE', __DIR__ . '/config.xml');
define('CANVAS_INSTANCE_URL', 'canvas_instance_url');
define('ACCOUNT_ID', 'account_id');
define('COURSE_ID', 'course_id');
define('OAUTH_STATE', 'oauth_state');

session_start();

/* prepare the toolbox */
if (empty($_SESSION[Toolbox::class])) {
	$_SESSION[Toolbox::class] = Toolbox::fromConfiguration(CONFIG_FILE);
}
$toolbox =& $_SESSION[Toolbox::class];

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
		if (empty($_SESSION[OAUTH_STATE]) || (is_integer($_SESSION[OAUTH_STATE]) && $_SESSION[OAUTH_STATE]+5 < time())) {
			$_SESSION[OAUTH_STATE] = time();
			header('Location: ' . $toolbox->config('APP_URL') . '/admin/oauth.php?return=' . $toolbox->config('APP_URL') . '/admin');
			exit;
		}
	} else {
		throw $e;
	}
}

if (empty($_SESSION[CANVAS_INSTANCE_URL]) &&
	!empty($_SESSION[ToolProvider::class]['canvas']['api_domain'])
) {
	$_SESSION[CANVAS_INSTANCE_URL] = 'https://' . $_SESSION[ToolProvider::class]['canvas']['api_domain'];
}

$toolbox->smarty_assign('category', DataUtilities::titleCase(preg_replace('/[\-_]+/', ' ', basename(__DIR__))));
