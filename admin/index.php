<?php

require_once('common.inc.php');

use Battis\BootstrapSmarty\NotificationMessage;

/* initialize consumer fields */
$name = '';
$key = hash('md4', time());
$secret = hash('md5', time()); /* no particular reason for these algorithms -- just chose two different, short-ish hashes */
$enabled = true;

/* validate new consumer information and save it */
if (isset($_REQUEST['name']) && isset($_REQUEST['key']) && isset($_REQUEST['secret'])) {
	$valid = true;
	$message = 'Invalid consumer information. ';
	if (empty($_name = trim($_REQUEST['name']))) {
		$valid = false;
		$message .= 'Consumer name must not be empty. ';
	}
	if (empty($_key = trim($_REQUEST['key']))) {
		$valid = false;
		$message .= 'Consumer key must not be empty. ';
	}
	if (empty(trim($_REQUEST['secret']))) { // secret may contain intentional whitespace -- leave untrimmed
		$valid = false;
		$message .= 'Shared secret must not be empty. ';
	}

	if ($valid) {
		$consumer = $toolbox->lti_createConsumer($_name, $_key, $secret, $enabled);
		if (!$consumer->save()) {
			$valid = false;
			$message = "<strong>Consumer could not be saved.</strong> {$toolbox->getMySQL()->error}";
		}
	}

	if (!$valid) {
		$toolbox->smarty_addMessage(
			'Required information missing',
			$message,
			NotificationMessage::ERROR
		);
	}

/* look up consumer to edit, if requested */
} elseif (isset($_REQUEST['consumer_key'])) {
	$consumer = new LTI_Tool_Consumer($_REQUEST['consumer_key'], LTI_Data_Connector::getDataConnector($toolbox->getMySQL()));
	if (isset($_REQUEST['action']))
		switch ($_REQUEST['action']) {
			case 'delete': {
				$consumer->delete();
				break;
			}
			case 'select': {
				$name = $consumer->name;
				$key = $consumer->getKey();
				$secret = $consumer->secret;
				$enabled = $consumer->enabled;
				break;
			}
			case 'update':
			case 'insert':
			default: {
				// leave default form values set
			}
		}
}

/* display a list of consumers */
$consumers = $toolbox->lti_getConsumers();
$toolbox->smarty_assign([
	'consumers' => $consumers,
	'name' => $name,
	'key' => $key,
	'secret' => $secret,
	'enabled' => $enabled,
	'formAction' => $_SERVER['PHP_SELF'],
	'requestKey' => (isset($_REQUEST['consumer_key']) ? $_REQUEST['consumer_key'] : null)
]);
$toolbox->smarty_display('consumers.tpl');
