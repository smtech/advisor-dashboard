{assign var="__DIR__" value=$smarty.current_dir}
{assign var="csv" value=$csv|default:false}
{extends file="subpage.tpl"}

{block name="subcontent"}

	{assign var="csv" value=$csv|default:false}

	<div class="container">
		<div class="readable-width">
			<p>Download a <a target="_parent" href="https://canvas.instructure.com/doc/api/file.sis_csv.html">users.csv</a> file with an additional <code>id</code> column identifying the user's Canvas ID. This CSV file can be used to force a reset of observer passwords if they aren't "taking" via the API.</p>
		</div>
	</div>

    {if $csv}
        <iframe src="../generate-csv.php?data={$csv}&filename={$filename}" style="display: none;"></iframe>
    {/if}

	{include file="$__DIR__/form.tpl"}

{/block}
