{extends file="subpage.tpl"}

{block name="subcontent"}

	{include file="select-advisee.tpl"}
	
	{foreach $courses as $course}
		<div class="container">
			<h3>{$course['name']} <small>{$terms[$course['enrollment_term_id']]['name']}</small></h3>
			<canvas id="course_{$course['id']}" width="600" height="200"></canvas>
		</div>
	{/foreach}
{/block}

{block name="scripts"}
	<script src="../js/Chart.min.js"></script>
	<script src="../js/relative-grades.js.php?advisee={$advisee}"></script>
{/block}