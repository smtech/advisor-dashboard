<?php

require_once 'common.inc.php';

use smtech\OAuth2\Client\Provider\CanvasLMS;
use Battis\DataUtilities;

define('OAUTH_STATE', 'oauth_state');

/* have we been asked to return to a particular URL? */
if (!empty($_REQUEST['oauth-return'])) {
	$_SESSION['oauth-return'] = $_REQUEST['oauth-return'];
}

/* do we have a Canvas instance URL yet? */
if (empty($_SESSION[CANVAS_INSTANCE_URL]) && empty ($_REQUEST['url'])) {
	$toolbox->smarty_assign('formAction', $_SERVER['PHP_SELF']);
	$toolbox->smarty_display('oauth.tpl');
	exit;
} elseif (empty($_SESSION[CANVAS_INSTANCE_URL]) && !empty($_REQUEST['url'])) {
	$_SESSION[CANVAS_INSTANCE_URL] = $_REQUEST['url'];
}

$canvas = $toolbox->config('TOOL_CANVAS_API');
$provider = new CanvasLMS([
    'clientId' => $canvas['key'],
    'clientSecret' => $canvas['secret'],
    'purpose' => $toolbox->config('TOOL_NAME'),
    'redirectUri' => DataUtilities::URLfromPath(__FILE__),
    'canvasInstanceUrl' => $_SESSION[CANVAS_INSTANCE_URL]
]);

/* if we don't already have an authorization code, let's get one! */
if (!isset($_GET['code'])) {
    $authorizationUrl = $provider->getAuthorizationUrl();
    $_SESSION[OAUTH_STATE] = $provider->getState();
    header("Location: $authorizationUrl");
    exit;

/* check that the passed state matches the stored state to mitigate cross-site request forgery attacks */
} elseif (empty($_GET['state']) || $_GET['state'] !== $_SESSION[OAUTH_STATE]) {
    unset($_SESSION[OAUTH_STATE]);
    exit('Invalid state');

} else {
	/* acquire and save our token (using our existing code) */
	$canvas = $toolbox->config('TOOL_CANVAS_API');
	$canvas['url'] = $_SESSION[CANVAS_INSTANCE_URL];
	$canvas['token'] = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']])->getToken();

	/* pass back the newly-acquired token in session data */
	$_SESSION['TOOL_CANVAS_API'] = $canvas;

	/* return to what we were doing before we had to authenticate */
	header("Location: {$_SESSION['oauth-return']}");
	unset($_SESSION[OAUTH_STATE]);
	unset($_SESSION['oauth-return']);
    exit;
}
