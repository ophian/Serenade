{if NOT empty($staticpage_jsStr)}
    <div class="staticpage_sbJsList">
    {$staticpage_jsStr}
    </div>
{/if}
{if NOT $staticpage_jsStr OR empty($staticpage_jsStr)}
    <ul class="plainList">
        {if NOT empty($frontpage_path)}
        <li><a href="{$frontpage_path}">{$CONST.PLUGIN_STATICPAGELIST_FRONTPAGE_LINKNAME}</a></li>
        {/if}
    {if is_array($staticpage_listContent) AND NOT empty($staticpage_listContent)}
    {foreach $staticpage_listContent AS $pageList}
        {if NOT empty($pageList.permalink)}
        <li class="depth_{$pageList.depth}"><a href="{$pageList.permalink}" title="{$pageList.pagetitle}">{$pageList.headline|truncate:20:"..."}</a></li>
        {else}
        <li class="depth_{$pageList.depth}">{$pageList.headline|truncate:20:"..."}</li>
        {/if}
    {/foreach}
    {/if}
  </ul>
{/if}
