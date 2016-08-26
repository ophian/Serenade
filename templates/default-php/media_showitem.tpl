<?php if ($GLOBALS['tpl']['is_embedded'] != true): ?>
<!doctype html>
<html lang="<?= $GLOBALS['tpl']['lang'] ?>">
<head>
    <meta charset="<?= $GLOBALS['tpl']['head_charset'] ?>">
    <title><?= (!empty($GLOBALS['tpl']['media']['file']['props']['base_property']['TITLE']) ? $GLOBALS['tpl']['media']['file']['props']['base_property']['TITLE'] : $GLOBALS['tpl']['media']['file']['realname']) ?></title>
    <meta name="generator" content="Serendipity v.<?= $GLOBALS['tpl']['serendipityVersion'] ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= $GLOBALS['tpl']['head_link_stylesheet'] ?>">
<?php serendipity_plugin_api::hook_event('frontend_header', $GLOBALS['template']) ?>
    <script src="<?= $GLOBALS['tpl']['head_link_script'] ?>"></script>
</head>
<body>
<?php else: ?>
<?php serendipity_plugin_api::hook_event('frontend_header', $GLOBALS['template']) ?>
<?php endif; ?>

<?php if ($GLOBALS['tpl']['is_raw_mode'] != true): ?>
<div id="serendipity_banner">
    <h1><a class="homelink1" href="<?= $GLOBALS['tpl']['serendipityBaseURL'] ?>"><?= $GLOBALS['template']->getdefault('head_title', 'blogTitle'); ?></a></h1>
    <h2><a class="homelink2" href="<?= $GLOBALS['tpl']['media']['from'] ?>"><?= $GLOBALS['template']->getdefault('head_subtitle', 'blogDescription'); ?></a></h2>
</div>

<table id="mainpane">
    <tr>
        <td id="content" valign="top">
            <main id="content" <?php if ($GLOBALS['tpl']['template_option']['imgstyle'] != 'none'): ?> class="<?= $GLOBALS['tpl']['template_option']['imgstyle'] ?>"<?php endif; ?>>
                <article class="clearfix serendipity_entry">
                    <h2><?= (!empty($GLOBALS['tpl']['media']['file']['props']['base_property']['TITLE']) ? $GLOBALS['tpl']['media']['file']['props']['base_property']['TITLE'] : $GLOBALS['tpl']['media']['file']['realname']) ?></h2>
                <?php if ($GLOBALS['tpl']['perm_denied']): ?>
                    <p class="msg_important"><?= PERM_DENIED ?></p>
                <?php else: ?>
                    <div class="media_show">
                    <?php if ($GLOBALS['tpl']['media']['file']['is_image']): ?>
                        <img src="<?= $GLOBALS['tpl']['media']['file']['full_file'] ?>" alt="<?= $GLOBALS['tpl']['media']['file']['realname'] ?>">
                    <?php else: ?>
                        <a href="<?= $GLOBALS['tpl']['media']['file']['full_file'] ?>"><?= $GLOBALS['tpl']['media']['file']['realname'] ?> (<?= $GLOBALS['tpl']['media']['file']['displaymime'] ?>)</a>
                    <?php endif; ?>
                        <p><a href="<?= $GLOBALS['tpl']['media']['from'] ?>"><?= BACK_TO_BLOG ?></a></p>
                    </div>
                <?php endif; ?>
                </article>
            </main>
        </td>

        <td id="serendipityRightSideBar" valign="top">
            <aside>
            <?php if ($GLOBALS['tpl']['media']['file']['base_property']): ?>
                <section class="media_props_base sidebar_plugin clearfix">
                    <h3><?= MEDIA_PROP ?></h3>

                    <dl>
                    <?php foreach ($GLOBALS['tpl']['media']['file']['base_property'] AS $prop_fieldname => $prop_content): ?>
                        <?php if ($prop_content['val']): ?>
                        <dt><?= $prop_content['label'] ?></dt>
                        <dd><?= $prop_content['val'] ?></dd>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </dl>
                </section>
            <?php endif; ?>
            <?php if ($GLOBALS['tpl']['media']['file']['props']['base_keyword']): ?>
                <section class="media_props_keywords sidebar_plugin clearfix">
                    <h3><?= MEDIA_KEYWORDS ?></h3>

                    <div class="media_keywords">
                    <?php foreach ($GLOBALS['tpl']['media']['file']['props']['base_keyword'] AS $prop_fieldname => $prop_content): ?>
                        <span><?= $prop_fieldname ?></span>
                    <?php endforeach; ?>
                    <div>
                </section>
            <?php endif; ?>
            <?php if ($GLOBALS['tpl']['media']['file']['props']['base_metadata']): ?>
                <section class="media_props_metadata sidebar_plugin clearfix">
                    <h3>EXIF/IPTC/XMP</h3>

                    <dl>
                    <?php foreach ($GLOBALS['tpl']['media']['file']['props']['base_metadata'] AS $meta_type => $meta_data): ?>
                        <dt><?= $meta_type ?></dt>
                        <?php if (is_array($meta_data)): ?>
                            <?php foreach ($meta_data AS $meta_name => $meta_value): ?>
                            <dd class="meta_name"><?= $meta_name ?></dd>
                            <dd class="meta_value">
                            <?php if (is_array($meta_value)): ?>
                                <pre><?= print_r($meta_value) ?></pre>
                            <?php else: ?>
                                <span><?= serendipity_formatTime(DATE_FORMAT_SHORT, $meta_name); ?></span>
                            <?php endif; ?></dd>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <dd><?= serendipity_formatTime(DATE_FORMAT_SHORT, $meta_type); ?></dd>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </dl>
                </section>
            <?php endif; ?>
            <?php if ($GLOBALS['tpl']['media']['file']['references']): ?>
                <section class="media_props_filerefs sidebar_plugin clearfix">
                    <h3><?= REFERER ?></h3>

                    <ul class="plainList">
                    <?php foreach ($GLOBALS['tpl']['media']['file']['references'] AS $ref): ?>
                        <li><a rel="nofollow" href="<?= $ref['link'] ?>"><?= (empty($ref) ? NONE : $ref['link']) ?></a> (<?= $ref['name'] ?>)</li>
                    <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
            </aside>
        </td>
    </tr>
</table>

<?php endif; ?>
<?= $GLOBALS['tpl']['raw_data'] ?>
<?php serendipity_plugin_api::hook_event('frontend_footer', $GLOBALS['template']) ?>
<?php if ($GLOBALS['tpl']['is_embedded'] != true): ?>
</body>
</html>
<?php endif; ?>
