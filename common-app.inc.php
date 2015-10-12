<?php
	
if (isset($_SESSION['toolProvider']->user)) {
	$_SESSION['canvasInstanceUrl'] = 'https://' . $_SESSION['toolProvider']->user->getResourceLink()->settings['custom_canvas_api_domain'];
}

if (isset($_SESSION['apiUrl']) && isset($_SESSION['apiToken'])) {
	$api = new CanvasPest($_SESSION['apiUrl'], $_SESSION['apiToken']);
}

?>