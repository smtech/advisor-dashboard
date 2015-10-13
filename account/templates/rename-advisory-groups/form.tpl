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
	
	{assign var="formButton" value="Rename"}

{/block}