{foreach $comments AS $comment}
    <article id="c{$comment.id|default:0}" class="comment commentlevel_{if $comment.depth > 8}9{else}{$comment.depth}{/if}">
        <h4>{if $comment.url}<a href="{$comment.url}">{/if}{$comment.author|default:$CONST.ANONYMOUS}{if $comment.url}</a>{/if} {$CONST.ON} <time datetime="{$comment.timestamp|serendipity_html5time}">{$comment.timestamp|formatTime:($template_option.date_format|default:$CONST.DATE_FORMAT_ENTRY)}</time>:</h4>

        <div class="comment_content u-cf">
            {$comment.avatar|default:''}
        {if $comment.body == 'COMMENT_DELETED'}
            <p class="serendipity_msg_important">{$CONST.COMMENT_IS_DELETED}</p>
        {else}
            {if isset($comment.type) AND $comment.type == 'TRACKBACK'}{$comment.body|strip_tags:false} [&hellip;]{else}{$comment.body}{/if}
        {/if}
        </div>
    {if $entry.allow_comments AND $comment.body != 'COMMENT_DELETED'}
        <a id="serendipity_reply_{$comment.id}" class="comment_reply" href="#serendipity_CommentForm" onclick="document.getElementById('serendipity_replyTo').value='{$comment.id}'; {$comment_onchange}">{$CONST.REPLY}</a>
        <div id="serendipity_replyform_{$comment.id}"></div>
    {/if}
    </article>
{foreachelse}
    <p class="serendipity_msg_notice">{$CONST.NO_COMMENTS}</p>
{/foreach}
