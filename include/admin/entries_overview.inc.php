<?php

if (IN_serendipity !== true) {
    die ("Don't hack!");
}

if (!is_object($serendipity['smarty'])) {
    serendipity_smarty_init();
}

echo serendipity_smarty_showTemplate('admin/entries_overview.inc.tpl');


/* vim: set sts=4 ts=4 expandtab : */
