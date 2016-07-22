{extends file="form.tpl"}

{block name="form-content"}

    <div class="form-group">
        <label class="control-label col-sm-{$formLabelWidth}" for="url">Canvas URL</label>
        <input id="url" name="url" type="text" class="form-control" placeholder="https://canvas.instructure.com" />
    </div>

    {assign var="formButton" value="Log In"}

{/block}
