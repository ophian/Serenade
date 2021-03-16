<?php
// Theme options
@define('B46_INSTR', '<b>NOTE:</b> "b46" is an upgrade Engine:Template of the "bootstrap4" theme.<p>For custom theme styling of entries freetags, enable the "Extended Smarty" option in the event freetag plugin.</p><p>If you leave the "#" for the URL of a navigation-link, a popover submenu can be created manually in the index.tpl file. Example inside.</p>');
@define('B46_USE_CORENAV', 'Use global navigation?');
@define('B46_USE_SEARCH', 'Use quicksearch in the navigation head bar?');
@define('B46_JUMPSCROLL', 'Use scroll button jumper?');
@define('B46_HUGO', 'Article [text] as toggle summary teaser with length');
@define('B46_HUGO_TTT', 'When over, click mouse or toggle by keyboards space-bar when set active');
@define('B46_HUGO_TITLE_ELSE', 'Open entry');
@define('B46_TEASE', '0 means: Not set!');
@define('B46_TEASE_COND', ' - Integers only & choose type either/or!'); // start with space!
@define('B46_CARD', 'Article [text] as a Grid-Card teaser with length');
@define('B46_CARD_META', ' The card subheader user & date 1-line meta data may get too long and then hiddenly truncated. Please check up your personal length need by using short usernames and/or by this configuration upper dateformat option.'); // start with space!
@define('B46_CARD_TITLE_ELSE', 'No entry summary text available');
@define('B46_LEAD', 'By teaser type, add a top featured article');
@define('B46_LEAD_DESC', ' Or use this "URI" alike plain text string "array" representation, to fill in the text (with unchanged keys) - "title" and "text" keys are mandatory: '); // start with space!
@define('B46_NAV_ONELINE', 'Navigation as independent line?');

// If used within template files, add previously parent theme defines here, since the lang CONSTANT engine:fallback will work only for the config.inc file up from Styx 3.3.1!
@define('BS_REPLYORIGIN', 'Origin');

// Lang constants non-translate
@define('B46_PLACE_SEARCH', 'Search term(s)');
@define('B46_SEND_MAIL', 'Send email');

