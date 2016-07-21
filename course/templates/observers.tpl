{extends file="subpage.tpl"}

{block name="subcontent"}

	<div class="container">
		<div class="readable-width">
			<p>At any point during the year, you may log in to observe your advisees in Canvas -- you will see what they see, without any danger that you will accidentally make changes. To observe an advisee, use the respective login below to access Canvas.</p>
			<p>You may find it useful to open a "private" window in your browser (<a href="https://support.google.com/chrome/answer/95464?hl=en">Chrome</a>, <a href="https://support.mozilla.org/en-US/kb/private-browsing-use-firefox-without-history">Firefox</a>, <a href="https://support.apple.com/kb/PH19216?locale=en_US">Safari</a>) to log in as an observer -- this will allow you to be logged in to Canvas simultaneously as yourself in your regular browser and observing your advisee in the private window.
			<p>You may configure notifications for each advisee as you wish. Emailed notifications for each advisee will be sent to you at each respective address (hint: <a href="https://support.google.com/mail/answer/6579?hl=en">Gmail filters</a> rock!).</p>
		</div>
	</div> 

	<div class="container">
		<table class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th>Advisee</th>
					<th>Observer Login</th>
					<th>Password</th>
					<th>Notification Email</th>
				</tr>
			</thead>
			<tbody>
				{foreach $observers as $observer}
					<tr>
						<td>{$observees[$observer['id']]['name']}</td>
						<td>{$observer['login_id']}</td>
						<td><code>{$passwords[$observer['id']]}</code></td>
						<td><email>{$observer['primary_email']}</email></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

{/block}