<?xml version="1.0" encoding="utf-8" ?>

<opml version="<?= $GLOBALS['tpl']['metadata']['version'] ?>" <?= $GLOBALS['tpl']['namespace_display_dat'] ?>>
<head>
    <title><?= $GLOBALS['tpl']['metadata']['title'] ?></title>
    <dateModified><?= $GLOBALS['tpl']['last_modified'] ?></dateModified>
    <ownerName>Serendipity <?= $GLOBALS['tpl']['serendipityVersion'] ?> - http://www.s9y.org/</ownerName>
</head>
<body>

<?php foreach ($GLOBALS['tpl']['entries'] AS $entry): ?>
    <outline text="<?= $entry['feed_title'] ?>" type="url" htmlUrl="<?= $entry['feed_entryLink'] ?>" urlHTTP="<?= $entry['feed_entryLink'] ?>" />
<?php foreachend; ?>

</body>
</opml>