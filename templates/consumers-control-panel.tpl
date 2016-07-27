{assign var="formLabelWidth" value=$formLabelWidth|Default: 4}
{extends file="subpage.tpl"}

{block name="subcontent"}

	<div class="container">
		<table id="lti-consumers" class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Consumer Key</th>
					<th>Shared Secret</th>
					<th>Enabled</th>
				</tr>
			</thead>
			<tbody>
				{if count($consumers) > 0}
					{foreach $consumers as $_consumer}
						{if empty($key) || (!empty($key) && $key != $_consumer->getKey())}
							{$selected = false}
						{else}
							{$selected = true}
						{/if}
						<tr {if $selected}class="open-record"{/if}>
							<td>{$_consumer->name}</td>
							<td>{$_consumer->getKey()}</td>
							<td>{$_consumer->secret}</td>
							<td>
								<div class="checkbox">
									<input type="checkbox" {if $_consumer->enabled}checked="checked"{/if} readonly />
								</div>
							</td>
							{if !$selected}
								<td>
									<form action="{$formAction}" method="post" class="form-horizontal">
										<div class="form-group">
											<input type="hidden" name="key" value="{$_consumer->getKey()}" />
											<button type="submit" name="action" value="select" class="btn btn-default">Edit</button>
											<button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
										</div>
									</form>
								</td>
							{/if}
						</tr>
					{/foreach}
				{else}
					<tr>
						<td colspan="3">No consumers registered yet.</td>
					</td>
				{/if}
			</tbody>
		</table>
	</div>
	<div class="container">
		<form action="{$formAction}" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="name" class="control-label col-sm-{$formLabelWidth}">Name</label>
				<div class="col-sm-{12 - $formLabelWidth * 2}">
					<input type="text" name="name" id="name" value="{$consumer->name}" class="form-control" autofocus="autofocus" placeholder="Human-readable consumer name" />
				</div>
			</div>

			<div class="form-group">
				<label for="key" class="control-label col-sm-{$formLabelWidth}">Consumer Key</label>
				<div class="col-sm-{12 - $formLabelWidth * 2}">
					<input type="text" name="key" id="key" value="{$consumer->getKey()}" class="form-control" {if !empty($key)}readonly{/if} placeholder="Unique key for this consumer (cannot be changed)" />
				</div>
			</div>

			<div class="form-group">
				<label for="secret" class="control-label col-sm-{$formLabelWidth}">Shared Secret</label>
				<div class="col-sm-{12 - $formLabelWidth * 2}">
					<input type="text" name="secret" id="secret" value="{$consumer->secret}" class="form-control" placeholder="Secret used to authenticate consumer requests" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-{$formLabelWidth} col-sm-{12 - $formLabelWidth * 2}">
					<div class="checkbox">
						<label for="enabled" class="control-label">
							<input type="checkbox" name="enabled" id="enabled" value="1" {if $consumer->enabled} checked{/if} />
							Enabled
						</label>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-{$formLabelWidth} col-sm-{12 - $formLabelWidth * 2}">
					<button type="submit" name="action" value="{if empty($key)}insert{else}update{/if}" class="btn btn-primary">{if !empty($key)}Update{else}Add{/if} Consumer</button>
					{if !empty($key)}
						<button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
					{/if}
					<button type="button" onclick="window.location.href='{$formAction}'" class="btn btn-default">Cancel</button>
				</div>
			</div>
		</form>
	</div>

	<div class="container">
		<p>To install this LTI, users should choose configuration type <em>By URL</em> and provide their consumer key and secret above. They should point their installer at:</p>
		<p><code><a href="{$appUrl}?action=config">{$appUrl}?action=config</a></code></p>
	</div>

{/block}
