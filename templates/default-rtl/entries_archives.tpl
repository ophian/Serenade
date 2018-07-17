{serendipity_hookPlugin hook="entries_header"}
<div class="serendipity_Entry_Date">
    <h3 class="serendipity_date">{$CONST.ARCHIVES}{if NOT empty($category_info.categoryid)} :: {$category_info.category_name}{/if}</h3>
{if isset($archives) AND is_array($archives)}
{foreach $archives AS $archive}
<table cellspacing="4" cellpadding="4" border="0">
    <tr>
        <td colspan="4"><h2>{$archive.year}</h2></td>
    </tr>
    {foreach $archive.months AS $month}
    <tr>
        <td width="100"><img src="{serendipity_getFile file="img/graph_bar_horisontal.png"}" height="10" width="{math width=100 equation="count * width / max" count=$month.entry_count max=$max_entries format="%d"}" style="border: 1px solid #000000"></td>
        <td>{$month.entry_count} {$CONST.ENTRIES} {$CONST.ON}</td>
        <td>{$month.date|formatTime:"%B"}</td>
        <td>({if $month.entry_count}<a href="{$month.link}">{/if}{$CONST.VIEW_FULL}{if $month.entry_count}</a>{/if})</td>
        <td>({if $month.entry_count}<a href="{$month.link_summary}">{/if}{$CONST.VIEW_TOPICS}{if $month.entry_count}</a>{/if})</td>
    </tr>
    {/foreach}
</table>
{/foreach}
{/if}
</div>
<div class="serendipity_entryFooter" style="text-align: center">
{serendipity_hookPlugin hook="entries_footer"}
</div>
