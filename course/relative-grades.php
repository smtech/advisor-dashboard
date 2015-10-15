<?php

require_once('common.inc.php');

function isAcademic($account, $accounts = false) {
	if (!$accounts) {
		$accounts = getAccountList(); // FIXME this is not terribly efficient!
	}
	if ($account == 132) { // FIXME really, hard-coded values? Really?
		return true;
	} elseif ($account == 1) {
		return false;
	} else {
		return isAcademic($accounts[$account]['parent_account_id'], $accounts);
	}
}

$cache->pushKey(basename(__FILE__, '.php')); {
	$terms = getTermList();
	
	$advisees = $cache->getCache('advisees');
	if ($advisees === false) {
		$advisees = $api->get(
			"courses/{$_SESSION['courseId']}/enrollments",
			array(
				'role[]' => 'StudentEnrollment' // FIXME this shouldn't require the faux-array
			)
		);
		$cache->setCache('advisees', $advisees);
	}
	
	$advisee = (isset($_REQUEST['advisee']) ? $_REQUEST['advisee'] : $advisees[0]['user']['id']);
	$cache->pushKey($advisee); {
		$courses = $cache->getCache('courses');
		if ($courses === false) {
			$allCourses = $api->get(
				"courses",
				array(
					'as_user_id' => $advisee
				)
			);
		
			$courses = array();
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
			$cache->setCache('courses', $courses);
		}
		
		$analytics = $cache->getCache('analytics');
		if ($analytics === false) {
			$analytics = array();
			foreach ($courses as $course) {
				$analytics[$course['id']] = $api->get("courses/{$course['id']}/analytics/users/$advisee/assignments");
			}
			$cache->setCache('analytics', $analytics);
		}
	} $cache->popKey();
} $cache->popKey();

$smarty->assign('advisee', $advisee);
$smarty->assign('advisees', $advisees);
$smarty->assign('terms', $terms);
$smarty->assign('courses', $courses);
$smarty->assign('analytics', $analytics);

$smarty->display('relative-grades.tpl');

?>