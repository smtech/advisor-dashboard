{extends file="page.tpl"}

{block name="content"}

    <div class="container page-header">
        <h1>Advisor Dashboard <small>Control Panel</small></h1>
    </div>

    <div class="container">
        <div class="readable-width">
            <h3>Options</h3>
            <table class="table">
                <tbody>
                    <tr>
                        <td><a class="btn btn-primary" href="create-advisor-observers.php">Create Advisor Observers</a></td>
                        <td>Generate a matching observer user for every student in every course in a given account and term (presumably the current term and the Advisory Groups account, but hey&hellip; go wild!). Passwords can optionally be reset (and will be cached in MySQL for future recovery and presentation as part of the Advisor Dashboard).</td>
                    </tr>
                    <tr>
                        <td><a class="btn btn-primary" href="rename-advisory-groups.php">Rename Advisory Groups</a></td>
                        <td>Rename all courses in a particular account and term to match the pattern <code>TEACHER_LAST_NAME Advisory Group</code>. It seems that the enrollment syncing script sometimes does silly things with advisory group names and they need to be cleaned up.</td>
                    </tr>
                    <tr>
                        <td><a class="btn btn-primary" href="download-observers-csv.php">Download Observers CSV</a></td>
                        <td>Download a <code>users.csv</code> file with an additional <code>id</code> column identifying the user's Canvas ID. This CSV file can be used to force a reset of observer passwords if they aren't "taking" via the API.</td>
                    </tr>
                    <tr>
                        <td><a class="btn btn-primary" href="prune-old-observers.php">Prune Old Observers</a></td>
                        <td>This script generates a <code>users.csv</code> file that deletes all old advisor accounts. An advisor account is any account whose SIS ID is of the form <code>*-advisor</code> and which has at least one observee. Old, for our purposes, means that all of the courses in which that advisor is enrolled have now ended.</td>
                    </tr>
                </tbody>
            </tabl>
        </div>
    </div>

{/block}
