{extends file="form.tpl"}

{block name="form-content"}

	<div class="form-group">
		<div class="col-sm-4">
			<div class="input-group">
				<select name="advisee" class="form-control" onchange="$('#select-advisee-button').click();">
					{foreach $advisees as $a}
						<option value="{$a['id']}" {if $a['id'] == $advisee}selected="selected"{/if}>{$a['name']}</option>
					{/foreach}
				</select>
				<span class="input-group-btn">
					<button id="select-advisee-button" type="submit" class="btn btn-primary has-spinner">Go <span class="spinner"><i class="fa fa-refresh fa-spin"></i></span></button>
				</span>
			</div>
		</div>
	</div>

{/block}

{block name="form-buttons"}{/block}
