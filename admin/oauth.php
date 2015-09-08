<?php

require_once('../common.inc.php');

/* save the URL we were given for the OAuth endpoint */
if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
	$metadata['CANVAS_INSTANCE_URL'] = $_REQUEST['url'];
	$metadata['CANVAS_API_URL'] = '@CANVAS_INSTANCE_URL/api/v1';
}

/* are we at the beginning of the process, so we need to give the OAuthNegotiator as much information as possible? */
if (isset($_REQUEST['step'])) {
	
	/* clear any existing session data */
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	$_SESSION = array();
	session_destroy();
	session_start();

	$oauth = new OAuthNegotiator(
		"{$metadata['CANVAS_INSTANCE_URL']}/login/oauth2",
		(string) $secrets->oauth->id,
		(string) $secrets->oauth->key,
		$metadata['APP_URL'] . "/admin/install.php?step={$_REQUEST['step']}",
		(string) $secrets->app->name
	);
}

/* OAuthNegotiator will return here periodically and we will just keep re-instantiating it until it finishes */
$oauth = new OAuthNegotiator();

?>