<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$lang}"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js lt-ie9 lt-ie8" lang="{$lang}"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js lt-ie9" lang="{$lang}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{$lang}"> <!--<![endif]-->
<head>
    <meta charset="{$head_charset}">
    <title>{$CONST.SERENDIPITY_ADMIN_SUITE}</title>
    <meta name="generator" content="Serendipity v.{$serendipityVersion}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
{if $template_option.webfonts == 'droid'}
    <link  rel="stylesheet" href="//fonts.googleapis.com/css?family=Droid+Sans:400,700">
{elseif $template_option.webfonts == 'ptsans'}
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic">
{elseif $template_option.webfonts == 'osans'}
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic">
{elseif $template_option.webfonts == 'cabin'}
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Cabin:400,400italic,700,700italic">
{elseif $template_option.webfonts == 'ubuntu'}
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Ubuntu:400,400italic,700,700italic">
{elseif $template_option.webfonts == 'dserif'}
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Droid+Serif:400,400italic,700,700italic">
{/if}
{if $head_link_stylesheet_frontend}
    <link rel="stylesheet" href="{$head_link_stylesheet_frontend}">
{else}
    <link rel="stylesheet" href="{$serendipityHTTPPath}{$serendipityRewritePrefix}serendipity.css">
{/if}
    <link rel="stylesheet" href="{serendipity_getFile file='admin/preview_iconizr.css'}">
{if $mode == 'save'}{* we need this for modernizr.indexDB cleaning up autosave entry modifications *}

    <script src="{serendipity_getFile file="admin/js/modernizr.min.js"}"></script>
{/if}

    <script type="text/javascript">
        window.onload = function() {ldelim}
            parent.document.getElementById('serendipity_iframe').style.height = document.getElementById('content').offsetHeight
                                                                              + parseInt(document.getElementById('content').style.marginTop)
                                                                              + parseInt(document.getElementById('content').style.marginBottom)
                                                                              + 'px';
            parent.document.getElementById('serendipity_iframe').scrolling    = 'no';
            parent.document.getElementById('serendipity_iframe').style.border = 0;
        {rdelim}
    </script>
</head>

<body{if $template_option.webfonts != 'none'} class="{$template_option.webfonts}"{/if}>
    <div id="page" class="clearfix container">
        <div class="clearfix{if $leftSidebarElements > 0 && $rightSidebarElements > 0} col3{elseif $leftSidebarElements > 0 && $rightSidebarElements == 0} col2l{else} col2r{/if}">
            <main id="content" style="padding: 1em 0; margin: 0;">
            {if $mode == 'preview'}
                <div class="clearfix">
            {elseif $mode == 'save'}
                <div class="clearfix">
                    <div style="float: left; height: 75px"></div>
                    {$updertHooks}
                {if $res}
                    <div class="serendipity_msg_important">{$CONST.ERROR}: <b>{$res}</b></div>
                {else}
                    {if isset($lastSavedEntry) && (int)$lastSavedEntry}

                    <script type="text/javascript">
                        window.onload = function() {ldelim}
                            parent.document.forms['serendipityEntry']['serendipity[id]'].value = "{$lastSavedEntry}";
                        {rdelim};
                    </script>
                    {/if}

                    <span class="msg_success"><span class="icon-ok-circled"></span> {$CONST.ENTRY_SAVED}</span>
                    <a href="{$entrylink}" target="_blank">{$CONST.VIEW}</a>
                {/if}
            {/if}
                {$preview}
                </div>
            </main>
        </div>
    </div>
<!-- filed by standard theme 2k11 -->
</body>
</html>
