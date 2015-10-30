<?php

require_once('common.inc.php');

if (!empty($_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_account_id'])) {
	$_SESSION['accountId'] = $_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_account_id'];
	header("Location: account/");
	exit;
} else {
	$_SESSION['courseId'] = $_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_course_id'];
	header('Location: course/');
}


?>