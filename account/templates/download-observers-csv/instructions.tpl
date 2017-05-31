{assign var="__DIR__" value=$smarty.current_dir}
{extends file="subpage.tpl"}

{block name="subcontent"}

	{assign var="csv" value=$csv|default:false}

	<div class="container">
		<div class="readable-width">
			<p>Download a <a target="_parent" href="https://canvas.instructure.com/doc/api/file.sis_csv.html">users.csv</a> file with an additional <code>id</code> column identifying the user's Canvas ID. </p>

			{if $csv}<p>If the download does not begin automatically, <a href="../generate-csv.php?data={$csv}&filename={$filename}">click here.</a><iframe src="../generate-csv.php?data={$csv}&filename={$filename}" style="display: none;"></iframe></p>{/if}
		</div>
	</div>

		{include file="$__DIR__/form.tpl"}

{/block}
