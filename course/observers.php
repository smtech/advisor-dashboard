<?php

require_once 'common.inc.php';

if ($firstStudent === false) {
    $toolbox->smarty_display('no-advisees.tpl');
    exit;
}

$toolbox->cache_pushKey(basename(__FILE__, '.php'));
$toolbox->cache_pushKey($_SESSION[COURSE_ID]);

$observers = $toolbox->cache_get('observers');
if ($observers === false) {
    $observers = [];
    $enrollments = $toolbox->api_get('courses/' . $_SESSION[COURSE_ID] . '/enrollments', [
        'role[]' => 'ObserverEnrollment' // FIXME this shouldn't require the faux-array
    ]);
    foreach ($enrollments as $enrollment) {
        $observers[] = $toolbox->api_get("users/{$enrollment['user']['id']}/profile");
    }
    $toolbox->cache_set('observers', $observers);
}

$observees = $toolbox->cache_get('observees');
if ($observees === false) {
    $observees = [];
    foreach ($observers as $observer) {
        $response = $toolbox->api_get("users/{$observer['id']}/observees");
        $observees[$observer['id']] = $response[0];
    }
    $toolbox->cache_set('observees', $observees);
}

$passwords = [];
foreach ($observers as $observer) {
    $password = false;
    if ($response = $toolbox->mysql_query("
        SELECT * FROM `observers` WHERE `id` = '{$observer['id']}' LIMIT 1
    ")) {
        $password = $response->fetch_assoc()['password'];
    }
    $passwords[$observer['id']] = $password;
}

$toolbox->cache_popKey();

$toolbox->smarty_assign([
    'observers' => $observers,
    'passwords' => $passwords,
    'observees' => $observees
]);
$toolbox->smarty_display('observers.tpl');
