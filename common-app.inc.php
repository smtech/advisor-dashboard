<?php

/**
 * Get a listing of all accounts organized for presentation in a select picker
 *
 * @return array
 **/
function getAccountList() {
	global $sql; // FIXME grown-ups don't code like this
	global $api; // FIXME grown-ups don't code like this
		
	$cache = new \Battis\HierarchicalSimpleCache($sql, basename(__FILE__, '.php'));
	
	$accounts = $cache->getCache('accounts');
	if ($accounts === false) {
		$accountsResponse = $api->get('accounts/1/sub_accounts', array('recursive' => 'true'));
		$accounts = array();
		foreach ($accountsResponse as $account) {
			$accounts[$account['id']] = $account;
		}
		$cache->setCache('accounts', $accounts, 7 * 24 * 60 * 60);
	}
	return $accounts;
}

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
		$termsResponse = $_terms['enrollment_terms'];
		$terms = array();
		foreach ($termsResponse as $term) {
			$terms[$term['id']] = $term;
		}
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