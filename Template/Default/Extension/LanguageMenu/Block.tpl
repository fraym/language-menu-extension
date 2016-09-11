<div class="dropdown">
    <button class="btn btn-default dropdown-toggle" type="button" id="languageMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        {_('Language')}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="languageMenu">
        {foreach $languageMenu as $language}
            <li{if $language.active} class="active"{/if}><a href="{$language.url}">{$language.name}</a></li>
        {/foreach}
    </ul>
</div>