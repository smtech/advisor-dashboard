{extends file="subpage.tpl"}

{block name="subcontent"}

	{include file="select-advisee.tpl"}
	
	<div class="container">
		<p>Below are visualizations of your advisee's relative performance in their classes, as compared to the other students' performance on each assignment. The blue <span style="color: #9ddffe;">&#9608;</span> "river" represents the middle ground: the white line is the median score, and everyone "in the river" is within a standard deviation of the median. The yellow <span style="color: #ffe399;">&#9608;</span> bank of the river represents scores more than a standard deviation below the median, and the red <span style="color: #ff3f0c;">&#9608;</span> line represents the lowest score on each assignment. The blue <span style="color: #6ed0ff;">&#9608;</span> line is the highest score on each assignment.</p>
		
		<p><em>Nota bene: zero-point assignments and ungraded assignments (assignments where the maximum grade was zero) have been filtered out of this view. If your advisee has "bottomed out" at zero on an assignment, it may mean that their submission is not yet graded, while other students' submissions have been graded.</em></p>
	
	{foreach $courses as $course}
		<div class="container">
			<h3><a target="_parent" href="{$metadata['CANVAS_INSTANCE_URL']}/courses/{$course['id']}">{$course['name']} <small>{$terms[$course['enrollment_term_id']]['name']}</small></h3>
			<canvas id="course_{$course['id']}" width="600" height="200"></canvas>
		</div>
	{/foreach}
{/block}

{block name="scripts"}
	<script src="../js/Chart.min.js"></script>
	<script src="../js/relative-grades.js.php?advisee={$advisee}"></script>
{/block}