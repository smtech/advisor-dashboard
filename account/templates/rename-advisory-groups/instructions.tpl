{assign var="__DIR__" value=$smarty.current_dir}
{extends file="subpage.tpl"}

{block name="subcontent"}

	<div class="container">
		<div class="readable-width">
			<p>Rename all courses in a particular account and term to match the pattern <code>TEACHER_LAST_NAME Advisory Group</code>.</p>
		</div>
	</div>

	{include file="$__DIR__/form.tpl"}

{/block}
