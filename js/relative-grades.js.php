<?php

define('IGNORE_LTI', true);
require_once(__DIR__ . '/../course/common.inc.php');

// http://paletton.com/#uid=33s0u0koA++cP+Mj9+Yus+WMOZA
define('TRANSPARENT', 'rgba(0, 0, 0, 0)');
define('HIGH_STROKE', '#6ed0ff'); // less light blue
define('HIGH_FILL', TRANSPARENT);
define('THIRD_QUARTILE_STROKE', TRANSPARENT);
define('THIRD_QUARTILE_FILL', '#9ddffe'); // light blue
define('MEDIAN_STROKE', '#fff'); // white
define('MEDIAN_FILL', THIRD_QUARTILE_FILL);
define('FIRST_QUARTILE_STROKE', TRANSPARENT);
define('FIRST_QUARTILE_FILL', '#ffe399');  // light yellow
define('LOW_STROKE', '#ff3f0c'); // medium red
define('LOW_FILL', '#fff'); // white
define('SCORE_STROKE', '#000'); // black
define('SCORE_FILL', TRANSPARENT);


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
			if ($data['points_possible'] > 0 && $data['max_score'] > 0) {
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
				fillColor: "<?= HIGH_FILL ?>",
				strokeColor: "<?= HIGH_STROKE ?>",
				data: [<?= implode(', ', $max_scores) ?>]
			},
			{
				label: "Third Quartile",
				fillColor: "<?= THIRD_QUARTILE_FILL ?>",
				strokeColor: "<?= THIRD_QUARTILE_STROKE ?>",
				data: [<?= implode(', ', $third_quartiles) ?>]
			},
			{
				label: "Median",
				fillColor: "<?= MEDIAN_FILL ?>",
				strokeColor: "<?= MEDIAN_STROKE ?>",
				data: [<?= implode(', ', $medians) ?>]
			},
			{
				label: "First Quartile",
				fillColor: "<?= FIRST_QUARTILE_FILL ?>",
				strokeColor: "<?= FIRST_QUARTILE_STROKE ?>",
				data: [<?= implode(', ', $first_quartiles) ?>]
			},
			{
				label: "Low Score",
				fillColor: "<?= LOW_FILL ?>",
				strokeColor: "<?= LOW_STROKE ?>",
				data: [<?= implode(', ', $min_scores) ?>]
			},
			{
				label: "Score",
				fillColor: "<?= SCORE_FILL ?>",
				strokeColor: "<?= SCORE_STROKE ?>",
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