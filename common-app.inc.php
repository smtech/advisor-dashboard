<?php

/**
 * Get a listing of all terms organized for presentation in a select picker
 *
 * @return array
 **/
function getTermList() {
	global $sql; // FIXME grown-ups don't code like this
	global $api; // FIXME grown-ups don't code like this
		
	$cache = new \Battis\HierarchicalSimpleCache($sql, basename(__FILE__, '.php'));
	
	$terms = $cache->getCache('terms');
	if ($terms === false) {
		$_terms = $api->get(
			'accounts/1/terms',
			array(
				'workflow_state' => 'active'
			)
		);
		$terms = $_terms['enrollment_terms'];
		$cache->setCache('terms', $terms, 7 * 24 * 60 * 60);
	}
	return $terms;
}

if (isset($_SESSION['toolProvider']->user)) {
	$_SESSION['canvasInstanceUrl'] = 'https://' . $_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_api_domain'];
}

if (isset($_SESSION['apiUrl']) && isset($_SESSION['apiToken'])) {
	$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);
}

$smarty->assign('category', \Battis\DataUtilities::titleCase(preg_replace('/[\-_]+/', ' ', basename(__DIR__))));

?>