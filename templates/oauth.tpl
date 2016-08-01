{extends file="page.tpl"}

{block name="content"}

    <div class="container page-header">
        <h1>Configure Tool Provider <small>Canvas API</small></h1>
    </div>

    <div class="container">
        <p>This tool requires access to the Canvas APIs. You can provide this access either by editing your <code>config.xml</code> file to include the URL of your Canvas API and a valid API access token, or by interactively authenticating to Canvas to issue an API token directly right now.</p>
    </div>

    {include file="oauth-form.tpl"}

{/block}
