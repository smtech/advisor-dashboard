<?php
	
require_once(__DIR__ . '/../common.inc.php');

$smarty->addTemplateDir(__DIR__ . '/templates', basename(__DIR__));

$cache = new \Battis\HierarchicalSimpleCache($sql, basename(__DIR__));
$cache->pushKey($_SESSION['courseId']);
$cache->pushKey(basename(__FILE__, '.php'));

$firstStudent = $cache->getCache('first-student');
if ($firstStudent === false) {
	$enrollments = $api->get(
		"courses/{$_SESSION['courseId']}/enrollments",
		array(
			'role[]' => 'StudentEnrollment'
		)
	);
	$firstStudent = $enrollments[0]['user']['id'];
	$cache->setCache('first-student', $firstStudent, 7*24*60*60);
}

$smarty->assign('facultyJournal', "{$_SESSION['canvasInstanceUrl']}/users/$firstStudent/user_notes?course_id={$_SESSION['courseId']}&course_name=Advisory%20Group");

$cache->popKey();

?>