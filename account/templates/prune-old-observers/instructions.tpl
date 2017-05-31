{assign var="__DIR__" value=$smarty.current_dir}
{assign var="csv" value=$csv|default:false}
{extends file="subpage.tpl"}

{block name="subcontent"}

    <div class="container">
        <div class="readable-width">
            <p>This script generates a users.csv file that deletes all old advisor accounts. An advisor account is any account whose SIS ID is of the form <code>*-advisor</code> and which has at least one observee. Old, for our purposes, means that all of the courses in which that advisor is enrolled have now ended.</p>
        </div>
    </div>

    {if $csv}
        <iframe src="../generate-csv.php?data={$csv}&filename={$filename}" style="display: none;"></iframe>
    {/if}

    {include file="$__DIR__/form.tpl"}

    <div class="container">
        <div class="readable-width">
            <p>Once you have downloaded the CSV file, you can upload it to the SIS CSV import tool to delete the "pruned" observers.</p>
        </div>
    </div>

{/block}
