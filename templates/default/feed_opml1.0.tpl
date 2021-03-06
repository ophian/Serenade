<?xml version="1.0" encoding="utf-8" ?>

<opml version="{$metadata.version}" {$namespace_display_dat}>
<head>
    <title>{$metadata.title}</title>
    <dateModified>{$last_modified}</dateModified>
    <ownerName>Serendipity Styx - https://ophian.github.io/</ownerName>
</head>
<body>

{foreach $entries AS $entry}
    <outline text="{$entry.feed_title}" type="url" htmlUrl="{$entry.feed_entryLink}{if $is_comments}#c{$entry.commentid}{/if}" urlHTTP="{$entry.feed_entryLink}{if $is_comments}#c{$entry.commentid}{/if}" />
{/foreach}

</body>
</opml>