{block name="navigation-menu"}
<ul class="nav navbar-nav">
	<li><a href="relative-grades.php">Relative Grades</a></li>
	<li><a href="observers.php">Observer Logins</a></li>
	<li>
		{if $facultyJournal == '#'}
			<a href="faculty-journal.php">Faculty Journal</a>
		{else}
			<a target="_parent" href="{$facultyJournal}">Faculty Journal</a>
		{/if}
	</li>
</ul>
{/block}
