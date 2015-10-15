<?php

define('IGNORE_LTI', true);
require_once(__DIR__ . '/../course/common.inc.php');

$points = 0;
function normalize($numerator, $denominator = false) {
	global $points;
	$denominator = ($denominator !== false ? $denominator : $points);
	$points = $denominator;
	return min(100, $numerator / $denominator * 100);
}
	
header('Content-Type: application/javascript');

$cache->pushKey(basename(__FILE__, '.js.php'));
$cache->pushKey($_REQUEST['advisee']);

$analytics = $cache->getCache('analytics');
if ($analytics === false) {
	exit;
}

?>

Chart.defaults.global.showTooltips = false;
Chart.defaults.global.scaleShowLabels = false;
Chart.defaults.global.scaleBeginAtZero = true;

<?php foreach ($analytics as $course => $analytic): ?>

	<?php
		
		$labels = array();
		$max_scores = array();
		$min_scores = array();
		$medians = array();
		$first_quartiles = array();
		$third_quartiles = array();
		$scores = array();
		foreach($analytic as $data) {
			if ($data['points_possible'] > 0) {
				$labels[] = ''; // htmlentities($data['title']);
				$max_scores[] = normalize($data['max_score'], $data['points_possible']);
				$min_scores[] = normalize($data['min_score']);
				$medians[] = normalize($data['median']);
				$first_quartiles[] = normalize($data['first_quartile']);
				$third_quartiles[] = normalize($data['third_quartile']);
				$scores[] = normalize($data['submission']['score']);
			}
		}
	?>

	var data = {
		labels: [<?= '"' . implode('", "', $labels) . '"' ?>],
		datasets: [
			{
				label: "High Score",
				fillColor: "rgba(0, 0, 0, 0)",
				strokeColor: "rgba(0, 150, 225, 0.1)",
				data: [<?= implode(', ', $max_scores) ?>]
			},
			{
				label: "Third Quartile",
				fillColor: "rgba(0, 150, 225, 0.2)",
				strokeColor: "rgba(0, 0, 0, 0)",
				data: [<?= implode(', ', $third_quartiles) ?>]
			},
			{
				label: "Median",
				fillColor: "rgba(0, 0, 0, 0)",
				strokeColor: "rgba(0, 150, 225, 0.5)",
				data: [<?= implode(', ', $medians) ?>]
			},
			{
				label: "First Quartile",
				fillColor: "rgba(255, 255, 255, 1)",
				strokeColor: "rgba(0, 0, 0, 0)",
				data: [<?= implode(', ', $first_quartiles) ?>]
			},
			{
				label: "Low Score",
				fillColor: "rgba(0, 0, 0, 0)",
				strokeColor: "rgba(0, 150, 225, 0.1)",
				data: [<?= implode(', ', $min_scores) ?>]
			},
			{
				label: "Score",
				fillColor: "rgba(0, 0, 0, 0)",
				strokeColor: "rgba(0, 0, 0, 1)",
				data: [<?= implode(', ', $scores) ?>]
			}
		]
	};
	
	var options = {
		pointDot: false,
		scaleShowGridLines: false
	};

	// Get context with jQuery - using jQuery's .get() method.
	var ctx = $("#course_<?= $course ?>").get(0).getContext("2d");

	// This will get the first returned node in the jQuery collection.
	var chart = new Chart(ctx).Line(data, options);

<?php endforeach; ?>