<?php

require_once 'common.inc.php';

$accounts = $toolbox->getAccountList();
function isAcademic($account) {
	global $accounts;
	if ($account == 132) { // FIXME really, hard-coded values? Really?
		return true;
	} elseif ($account == 1 || !is_integer($account)) {
		return false;
	} else {
		return isAcademic($accounts[$account]['parent_account_id']);
	}
}

$toolbox->cache_pushKey(basename(__FILE__, '.php')); {
	$terms = $toolbox->getTermList();

	$advisees = $toolbox->cache_get('advisees');
	if ($advisees === false) {
		$advisees = $toolbox->api_get(
			'courses/' . $_SESSION[COURSE_ID] . '/enrollments', [
				'role[]' => 'StudentEnrollment' // FIXME this shouldn't require the faux-array
			]
		);
		$toolbox->cache_set('advisees', $advisees);
	}

	$advisee = (isset($_REQUEST['advisee']) ? $_REQUEST['advisee'] : $advisees[0]['user']['id']);
	$toolbox->cache_pushKey($advisee); {
		$courses = $toolbox->cache_get('courses');
		if ($courses === false) {
			$allCourses = $toolbox->api_get(
				"courses", [
					'as_user_id' => $advisee
				]
			);

			$courses = [];
			$today = time();
			foreach ($allCourses as $course) {
				if (
					isAcademic($course['account_id']) &&
					strtotime($terms[$course['enrollment_term_id']]['start_at']) < $today &&
					strtotime($terms[$course['enrollment_term_id']]['end_at']) > $today
				) {

					$courses[$course['id']] = $course;
				}
			}
			$toolbox->cache_set('courses', $courses);
		}

		$analytics = $toolbox->cache_get('analytics');
		if ($analytics === false) {
			$analytics = [];
			foreach ($courses as $course) {
				$analytics[$course['id']] = $toolbox->api_get("courses/{$course['id']}/analytics/users/$advisee/assignments");
			}
			$toolbox->cache_set('analytics', $analytics);
		}
	} $toolbox->cache_popKey();
} $toolbox->cache_popKey();

$toolbox->smarty_assign([
	'advisee' => $advisee,
	'advisees' => $advisees,
	'terms' => $terms,
	'courses' => $courses,
	'analytics' => $analytics
]);
$toolbox->smarty_display('relative-grades.tpl');
