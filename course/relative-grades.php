<?php

require_once 'common.inc.php';

use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;
use Battis\DataUtilities;
use Battis\Educoder\Pest_NotFound;

if ($firstStudent === false) {
    $toolbox->smarty_display('no-advisees.tpl');
    exit;
}

$toolbox->getCache()->purgeExpired();
$toolbox->cache_pushKey(basename(__FILE__, '.php'));
$toolbox->cache_pushKey($_SESSION[COURSE_ID]);

$terms = $toolbox->getTermList();

$advisees = $toolbox->cache_get('advisees');
if ($advisees === false) {
    $advisees = $toolbox->api_get('courses/' . $_SESSION[COURSE_ID] . '/users', [
        'enrollment_type' => 'student'
    ]);
    $toolbox->cache_set('advisees', $advisees);
}

$advisee = (isset($_REQUEST['advisee']) ? $_REQUEST['advisee'] : $firstStudent);

$toolbox->cache_popKey();
$toolbox->cache_pushKey($advisee);

$courses = $toolbox->cache_get('courses');
if ($courses === false) {
    $allCourses = $toolbox->api_get("users/$advisee/courses");

    $courses = [];
    foreach ($allCourses as $course) {
        if (!empty($course['account_id']) &&
            $toolbox->isAcademic($course['account_id'])) {
            $courses[$course['id']] = $course;
        }
    }
    $toolbox->cache_set('courses', $courses);
}

$analytics = $toolbox->cache_get('analytics');
if ($analytics === false) {
    $analytics = [];
    foreach ($courses as $course) {
        try {
            $analytics[$course['id']] = $toolbox->api_get(
                "courses/{$course['id']}/analytics/users/$advisee/assignments"
            );
        } catch (Pest_NotFound $e) {
            $toolbox->smarty_addMessage(
                "Analytics unavailable for {$course[name]}",
                "Canvas is unable to provide analytics data for assignments and grades in " .
                "<a href=\"" . $_SESSION[CANVAS_INSTANCE_URL] . "/courses/{$course['id']}\">{$course['name']}.</a> " .
                "This is is likely because either no assignments have been " .
                "posted or the course itself has not yet been published."
            );
        }
    }
    $toolbox->cache_set('analytics', $analytics);
}

$toolbox->cache_popKey();
$toolbox->cache_popKey();

$toolbox->smarty_assign([
    'advisee' => $advisee,
    'advisees' => $advisees,
    'terms' => $terms,
    'courses' => $courses,
    'analytics' => $analytics,
    'canvasInstanceUrl' => $_SESSION[CANVAS_INSTANCE_URL]
]);

/*
 * FIXME unclear why the post-bootstrap-scripts block isn't working in the
 *     relative-grades.tpl file
 */
$toolbox->getSmarty()->addScript(
    DataUtilities::URLfromPath(__DIR__ . '/../vendor/npm-asset/chart.js/dist/Chart.min.js')
);
$toolbox->getSmarty()->addScript(
    DataUtilities::URLfromPath(__DIR__ . '/../js/relative-grades.js.php') . "?advisee={$advisee}"
);

$toolbox->smarty_display('relative-grades.tpl');
