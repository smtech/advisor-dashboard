<?php

require_once('common.inc.php');

use Battis\BootstrapSmarty\NotificationMessage;

/* clean request values */
$name = (empty($_POST['name']) ? null : trim($_POST['name']));
$key = (empty($_POST['key']) ? null : trim($_POST['key']));
$secret = (empty($_POST['secret']) ? null : trim($_POST['secret']));
$enabled = (empty($_POST['enabled']) ? false : (boolean) $_POST['enabled']);
$action = (empty($_POST['action']) ? false : strtolower(trim($_POST['action'])));

function createConsumer($toolbox, $key = null) {
	/*
	 * load an existing consumer (if we have a consumer_key) or create a blank
	 * that we will fill in
	 */
	// TODO is there a way to do this without exposing the guts of LTI_Tool_Provider's data abstraction?
	$consumer = new LTI_Tool_Consumer(
		(empty($key) ? LTI_Data_Connector::getRandomString(32) : $key),
		LTI_Data_Connector::getDataConnector($toolbox->getMySQL()),
		true // wicked confusing _not_ to autoenable
	);

	/* pre-fill secret if not editing an existing consumer */
	if (empty($key)) {
		$consumer->secret = LTI_Data_Connector::getRandomString(32);
	}

	return $consumer;
}

/* load requested consumer (or create new if none requested) */
$consumer = createConsumer($toolbox, $key);

/* what are we asked to do with this consumer? */
switch ($action) {
	case 'update':
	case 'insert': {
		$consumer->name = $name;
		$consumer->secret = $secret;
		$consumer->enabled = $enabled;
		if (!$consumer->save()) {
			$toolbox->smarty_addMessage(
				'Error saving consumer',
				'There was an error attempting to save your new or updated consumer information to the database.',
				NotificationMessage::ERROR
			);
		}
		break;
	}
	case 'delete': {
		$consumer->delete();
		break;
	}
	case 'select': {
		$toolbox->smarty_assign('key', $key);
		break;
	}
}

/*
 * if action was anything other than 'select', create a new empty consumer to
 * fill the form with
 */
if ($action && $action !== 'select') {
	$consumer = createConsumer($toolbox);
}

/* display a list of consumers */
$consumers = $toolbox->lti_getConsumers();
$toolbox->smarty_assign([
	'consumers' => $consumers,
	'consumer' => $consumer,
	'formAction' => $_SERVER['PHP_SELF'],
	'appUrl' => $toolbox->config('APP_URL')
]);
$toolbox->smarty_display(basename(__FILE__, '.php') . '.tpl');
