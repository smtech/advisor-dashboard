<?php

require_once __DIR__ . '/../common.inc.php';

use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;

$toolbox->getSmarty()->prependTemplateDir(__DIR__ . '/templates', basename(__DIR__));

$toolbox->cache_pushKey($_SESSION[ToolProvider::class]['canvas']['course_id']);

/* get and cache ID of first student in the advisory group */
$firstStudent = $toolbox->cache_get('first-student');
if (empty($firstStudent)) {
    $enrollments = $toolbox->api_get(
        'courses/' . $_SESSION[ToolProvider::class]['canvas']['course_id'] . '/enrollments',
        [
            'role[]' => 'StudentEnrollment'
        ]
    );
    if ($enrollments->count() > 0) {
        $firstStudent = $enrollments[0]['user']['id'];
    } else {
        $firstStudent = false;
    }
    $toolbox->cache_set('first-student', $firstStudent);
}

/* generate faculty journal URL for use by `smtech/canvashack-plugin-faculty-journal` */
if ($firstStudent !== false) {
    $toolbox->smarty_assign([
        'facultyJournal' => $_SESSION[CANVAS_INSTANCE_URL] . "/users/$firstStudent/user_notes?course_id=" .
            $_SESSION[ToolProvider::class]['canvas']['course_id'] . '&course_name=Advisory%20Group'
    ]);
} else {
    $toolbox->smarty_assign([
        'facultyJournal' => '#'
    ]);
}

$toolbox->cache_popKey();
