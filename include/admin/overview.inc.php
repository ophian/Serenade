<?php

if (IN_serendipity !== true) {
    die ("Don't hack!");
}

$data   = array(); // init Smarty assignment data array
$output = array(); // init backend_frontpage_display hook array
$output['probe'] = '';

// Alert non accessible SQLite database on login
if (isset($serendipity['POST']['admin']['user']) && stristr($serendipity['dbType'], 'sqlite') && (defined('S9Y_DB_INCLUDED') && S9Y_DB_INCLUDED === true)) {
    $errs  = array();
    $probe = array('dbName' => $serendipity['dbName']);
    serendipity_db_probe($probe, $errs);
    $errs = (count($errs) > 0) ? $errs : null;
    if (is_array($errs)) {
        $output['probe'] = '<span class="msg_error"><span class="icon-info-circled"></span> The SQLite Database is not accessible. Please check availability or missing write permissions!</span>'."\n";
    }
    unset($probe);
}

if (isset($serendipity['POST']['adminAction'])) {
    switch($serendipity['POST']['adminAction']) {
        case 'publish':
            if (!serendipity_checkFormToken()) {
                break;
            }
            $success = serendipity_updertEntry(array(
                'id' => serendipity_specialchars($serendipity['POST']['id']),
                'timestamp' => time(),
                'isdraft' => 0
            ));
            if (is_numeric($success)) {
                $data['published'] = $success;
            } else {
                $data['error_publish'] = $success;
            }
            break;

        case 'updateCheckDisable':
            if (!serendipity_checkFormToken() || !serendipity_checkPermission('blogConfiguration')) {
                break;
            }
            serendipity_set_config_var('updateCheck', false);
            break;
    }
}

$user = serendipity_fetchAuthor($serendipity['authorid']);
// chrome-compatible, from Oliver Gassner, adapted from TextPattern. Hi guys, keep it up. :-)
$bookmarklet = "javascript:var%20d=document,w=window,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),f='" . $serendipity['baseURL'] . "',l=d.location,e=encodeURIComponent,p='serendipity_admin.php?serendipity[adminModule]=entries&serendipity[adminAction]=new&serendipity[title]='+e(d.title)+'&serendipity[body]='+e(s)+'&serendipity[url]='+location.href,u=f+p;a=function(){%20%20if(!w.open(u,'t','toolbar=0,resizable=1,scrollbars=1,status=1,width=800,height=800'))%20%20%20%20l.href=u;};if(/Firefox/.test(navigator.userAgent))%20%20setTimeout(a,0);else%20%20a();void(0)";

$data['bookmarklet'] = $bookmarklet;
$data['username'] = $user[0]['realname'];
$data['js_failure_file'] = serendipity_getTemplateFile('admin/serendipity_editor.js');

serendipity_plugin_api::hook_event('backend_frontpage_display', $output);
$data['backend_frontpage_display'] = isset($output['more']) ? $output['probe'] . $output['more'] : '';
$output = array(); // re-new array for the autoupdate empty check below

if (serendipity_checkPermission('adminUsers')) {
    $data['usedVersion']  = $serendipity['version'];
    $data['updateCheck']  = $serendipity['updateCheck'];
    $data['curVersion']   = serendipity_getCurrentVersion();
    $data['releaseFUrl']  = serendipity_get_config_var('updateReleaseFileUrl', 'https://raw.githubusercontent.com/ophian/styx/master/docs/RELEASE'); // https://raw.github.com/s9y/Serendipity/master/docs/RELEASE
    $data['curVersName']  = $serendipity['updateVersionName'];
    $data['update']       = version_compare($data['usedVersion'], $data['curVersion'], '<');
    serendipity_plugin_api::hook_event('plugin_dashboard_updater', $output, $data['curVersion']);
    $output = !empty($output) ? $output : '<span class="msg_error"><span class="icon-info-circled"></span> To get a button, check if the "Serendipity Autoupdate" event plugin is installed!</span>';
    $data['updateButton'] = $output;
}

$data['urltoken'] = serendipity_setFormToken('url');
$data['token'] = serendipity_setFormToken();

if ($serendipity['default_widgets']) {
    // Can be set through serendipity_config_local.inc.php
    if (!isset($serendipity['dashboardCommentsLimit'])) {
        $serendipity['dashboardCommentsLimit'] = 5;
    }
    if (!isset($serendipity['dashboardLimit'])) {
        $serendipity['dashboardLimit'] = 5;
    }
    if (!isset($serendipity['dashboardDraftLimit'])) {
        $serendipity['dashboardDraftLimit'] = 5;
    }

    $cjoin  = ($serendipity['serendipityUserlevel'] == USERLEVEL_EDITOR) ? "
            LEFT JOIN {$serendipity['dbPrefix']}authors a ON (e.authorid = a.authorid)
                WHERE e.authorid = " . (int)$serendipity['authorid']
            : '';
    $cquery = "SELECT c.*, e.title, e.authorid
                 FROM {$serendipity['dbPrefix']}comments c
            LEFT JOIN {$serendipity['dbPrefix']}entries e ON (e.id = c.entry_id)
            " . $cjoin ."
             ORDER BY c.id DESC LIMIT " . (int)$serendipity['dashboardCommentsLimit'];
    $comments = serendipity_db_query($cquery);

    if (is_array($comments) && count($comments) > 0) {
        foreach($comments AS &$comment) {
            $comment['entrylink'] = serendipity_archiveURL($comment['entry_id'], 'comments', 'serendipityHTTPPath', true) . '#c' . $comment['id'];
            $comment['fullBody']  = $comment['body'];
            $comment['summary']   = serendipity_mb('substr', $comment['body'], 0, 100);

            if (strlen($comment['fullBody']) > strlen($comment['summary']) ) {
                $comment['excerpt'] = true;

                // When summary is not the full body, strip HTML tags from summary, as it might break and leave unclosed HTML.
                $comment['fullBody'] = nl2br(serendipity_specialchars($comment['fullBody']));
                $comment['summary']  = nl2br(strip_tags($comment['summary']));
            }
        }
    }

    $data['comments'] = $comments;

    $efilter = ($serendipity['serendipityUserlevel'] == USERLEVEL_EDITOR) ? ' AND e.authorid = ' . (int)$serendipity['authorid'] : '';
    $entries = serendipity_fetchEntries(
                        false,
                        false,
                        (int)$serendipity['dashboardLimit'],
                        true,
                        false,
                        'timestamp DESC',
                        'e.timestamp >= ' . serendipity_serverOffsetHour() . $efilter
                    );

    $entriesAmount = is_array($entries) ? count($entries) : 0; // since in case of an empty future $entries fetch, it returns the $result_type = $type_map[$result_type]; which is 1 in this case
    if ($entriesAmount < (int)$serendipity['dashboardDraftLimit']) {
        // there is still space for drafts
        $drafts = serendipity_fetchEntries(
                        false,
                        false,
                        (int)$serendipity['dashboardDraftLimit'] - $entriesAmount,
                        true,
                        false,
                        'timestamp DESC',
                        "isdraft = 'true' AND e.timestamp <= " . serendipity_serverOffsetHour() . $efilter
                    );

        if (is_array($entries) && is_array($drafts)) {
            $entries = array_merge($entries, $drafts);
        } else {
            if (is_array($drafts)) {
                // $entries is not an array, thus empty
                $entries = $drafts;
            }
        }
    }

    $data['entries'] = $entries;
} // end default_widgets
else {
    // Init
    $data['shortcuts'] = $data['comments']['pending'] = $data['entries']['futures'] =  $data['entries']['drafts'] = null;

    // SQL
    $cjoin  = ($serendipity['serendipityUserlevel'] == USERLEVEL_EDITOR) ? "
            LEFT JOIN {$serendipity['dbPrefix']}authors a ON (e.authorid = a.authorid)
                WHERE e.authorid = " . (int)$serendipity['authorid']
            : '';
    $cquery = "SELECT COUNT(c.id) AS newcom
                 FROM {$serendipity['dbPrefix']}comments c
            LEFT JOIN {$serendipity['dbPrefix']}entries e ON (e.id = c.entry_id)
            " . $cjoin ."
             WHERE status = 'pending'";
    $efilter  = ($serendipity['serendipityUserlevel'] == USERLEVEL_EDITOR) ? ' AND e.authorid = ' . (int)$serendipity['authorid'] : '';
    $comments = serendipity_db_query($cquery);
    $futures  = serendipity_db_query("SELECT COUNT(e.id) AS count FROM {$serendipity['dbPrefix']}entries AS e $cjoin WHERE e.timestamp >= " . serendipity_serverOffsetHour() . $efilter, true);
    $drafts   = serendipity_db_query("SELECT COUNT(e.id) AS count FROM {$serendipity['dbPrefix']}entries AS e $cjoin WHERE e.isdraft = 'true'" . $efilter, true);

    $permByAuthor = (!serendipity_checkPermission('adminUsers') && (int)$serendipity['authorid'] > 1) ? '&serendipity[filter][author]=' .(int)$serendipity['authorid'] : '';

    // Assign
    if (is_array($comments)) {
        $data['comments']['pending']['count'] = $comments[0]['newcom'];
        $data['comments']['pending']['link'] = 'serendipity_admin.php?'.$data['urltoken'].'&serendipity[adminModule]=comments'.$permByAuthor.'&serendipity[filter][show]=pending&submit=1';
        if ($comments[0]['newcom'] > 0) $data['shortcuts'] = true;
    }
    if (is_array($futures)) {
        $data['entries']['futures']['count'] = $futures['count'];
        $data['entries']['futures']['link']  = 'serendipity_admin.php?serendipity[adminModule]=entries&serendipity[adminAction]=editSelect&'.$data['urltoken'];
        if ($futures['count'] > 0) $data['shortcuts'] = true;
    }
    if (is_array($drafts)) {
        $data['entries']['drafts']['count'] = $drafts['count'];
        $data['entries']['drafts']['link'] = 'serendipity_admin.php?serendipity[action]=admin&serendipity[adminModule]=entries&serendipity[adminAction]=editSelect'.$permByAuthor.'&serendipity[filter][isdraft]=draft&dashboard[filter][noset]=1&go=1&serendipity[sort][perPage]=12&'.$data['urltoken'].'';
        if ($drafts['count'] > 0) $data['shortcuts'] = true;
    }
}

$data['no_create'] = $serendipity['no_create'];
$data['default_widgets'] = $serendipity['default_widgets'];

echo serendipity_smarty_showTemplate('admin/overview.inc.tpl', $data);

/* vim: set sts=4 ts=4 expandtab : */
