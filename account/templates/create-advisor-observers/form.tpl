{extends file="form.tpl"}

{block name="form-content"}

	<div class="form-group">
		<label for="term" class="control-label col-sm-{$formLabelWidth}">Term</label>
		<div class="col-sm-3">
			<select id="term" name="term" class="form-control selectpicker">
				<option value="" disabled="disabled" selected="selected">Select a term</option>
				<option disabled="disabled"></option>
				{foreach $terms as $term}
					<option value="{$term['id']}">{$term['name']}</option>
				{/foreach}
			</select>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-{$formLabelWidth}">
			<div class="checkbox">
				<label for="reset_passwords" class="control-label">
				<input type="checkbox" name="reset_passwords" id="reset_passwords" value="true" />
				Reset existing observer passwords
				</label>
			</div>
		</div>
	</div>

	{assign var="formButton" value="Generate"}

{/block}