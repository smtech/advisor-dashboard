<?php

require_once('common.inc.php');

$cache->setLifetime(60*60); /* 1 hour */

$cache->pushKey(basename(__FILE__, '.php'));

$observers = $cache->getCache('observers');
if ($observers === false) {
	$observers = array();
	$enrollments = $api->get(
		"courses/{$_SESSION['courseId']}/enrollments",
		array(
			'role[]' => 'ObserverEnrollment' // FIXME this shouldn't requrie the faux-array
		)
	);
	foreach ($enrollments as $enrollment) {
		$observers[] = $api->get("users/{$enrollment['user']['id']}/profile");
	}
	$cache->setCache('observers', $observers);
}

$observees = $cache->getCache('observees');
if ($observees === false) {
	$observees = array();
	foreach ($observers as $observer) {
		$response = $api->get("users/{$observer['id']}/observees");
		$observees[$observer['id']] = $response[0];
	}
	$cache->setCache('observees', $observees);
}

$passwords = array();
foreach ($observers as $observer) {
	$response = $sql->query("
		SELECT * FROM `observers` WHERE `id` = '{$observer['id']}' LIMIT 1
	");
	$password = $response->fetch_assoc();
	$passwords[$observer['id']] = $password['password'];
}

$smarty->assign('observers', $observers);
$smarty->assign('passwords', $passwords);
$smarty->assign('observees', $observees);
$smarty->display('observers.tpl');
	
?>