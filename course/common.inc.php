<?php

require_once __DIR__ . '/../common.inc.php';

use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;

$toolbox->smarty_addTemplateDir(__DIR__ . '/templates', basename(__DIR__));

$toolbox->cache_pushKey($_SESSION[ToolProvider::class]['canvas']['course_id']);

$firstStudent = $toolbox->cache_get('first-student');
if ($firstStudent === false) {
	$enrollments = $toolbox->api_get(
		"courses/{$_SESSION['courseId']}/enrollments", [
			'role[]' => 'StudentEnrollment'
		]
	);
	$firstStudent = $enrollments[0]['user']['id'];
	$toolbox->cache_set('first-student', $firstStudent);
}

$toolbox->smarty_assign('facultyJournal', $_SESSION[CANVAS_INSTANCE_URL] . "/users/$firstStudent/user_notes?course_id=" . $_SESSION[ToolProvider::class]['canvas']['course_id'] . '&course_name=Advisory%20Group');

$toolbox->cache_popKey();
