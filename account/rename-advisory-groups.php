<?php

require_once('common.inc.php');

use Battis\BootstrapSmarty\NotificationMessage;

define('STEP_INSTRUCTIONS', 1);
define('STEP_RENAME', 2);

$step = (empty($_REQUEST['step']) ? STEP_INSTRUCTIONS : $_REQUEST['step']);

switch ($step) {

    case STEP_RENAME:
        try {
            $updated = 0;
            $unchanged = 0;
            $courses = $toolbox->api_get("accounts/{$_REQUEST['account']}/courses", [
                'enrollment_term_id' => $_REQUEST['term'],
                'with_enrollments' => 'true'
            ]);
            foreach ($courses as $course) {
                $teachers = $toolbox->api_get("/courses/{$course['id']}/enrollments", [
                    'type' => 'TeacherEnrollment'
                ]);
                if ($teacher = $teachers[0]['user']) {
                    $nameParts = explode(',', $teacher['sortable_name']);
                    $courseName = trim($nameParts[0]) . ' Advisory Group';
                    $toolbox->api_put("courses/{$course['id']}", [
                        'course[name]' => $courseName,
                        'course[course_code]' => $courseName
                    ]);
                    $sections = $toolbox->api_get("courses/{$course['id']}/sections");
                    foreach($sections as $section) {
                        if ($section['name'] == $course['name']) {
                            $toolbox->api_put("sections/{$sections[0]['id']}", [
                                'course_section[name]' => $courseName
                            ]);
                        }
                    }
                    $updated++;
                } else {
                    $unchanged++;
                }
            }
        } catch (Exception $e) {
            $toolbox->smarty_addMessage('Error ' . $e->getCode(), $e->getMessage(), NotificationMessage::ERROR);
        }
        $courses = $toolbox->api_get("accounts/{$_REQUEST['account']}/courses", [
            'enrollment_term_id' => $_REQUEST['term'],
            'with_enrollments' => 'true',
            'published' => 'true'
        ]);

        $toolbox->smarty_addMessage(
            'Renamed advisory courses',
            "$updated courses were renamed, and $unchanged were left unchanged.",
            NotificationMessage::GOOD
        );

    case STEP_INSTRUCTIONS:
    default:
        $toolbox->smarty_assign([
            'terms' => $toolbox->getTermList(),
            'formHidden' => [
                'step' => STEP_RENAME,
                'account' => $_SESSION['accountId']
            ]
        ]);
        $toolbox->smarty_display(basename(__FILE__, '.php') . '/instructions.tpl');
}
