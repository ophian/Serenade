<?php

if (IN_serendipity !== true) {
    die ("Don't hack!");
}

if (!serendipity_checkPermission('adminEntries')) {
    return;
}

$per_page   = array('12', '16', '50', '100');
$sort_order = array('timestamp'     => DATE,
                    'isdraft'       => PUBLISH . '/' . DRAFT,
                    'a.realname'    => AUTHOR,
                    'category_name' => CATEGORY,
                    'last_modified' => LAST_UPDATED,
                    'title'         => TITLE,
                    'id'            => 'ID');

$data = array();
// "global" Smarty index defines
$data['switched_output'] = false;
$data['iframe'] = false;
$data['drawList'] = false;
$data['single_error'] = false;
$data['dateval'] = false;
$data['is_draft'] = false;
$data['is_iframe'] = false;
$data['is_doMultiDelete'] = false;
$data['is_doDelete'] = false;
$data['is_delete'] = false;
$data['is_multidelete'] = false;


if (!empty($serendipity['GET']['editSubmit'])) {
    $serendipity['GET']['adminAction'] = 'edit'; // does this change smarty.get vars?
}

$preview_only = false;
$entryForm = '';

switch($serendipity['GET']['adminAction']) {

    case 'preview':
        $entry = serendipity_fetchEntry('id', $serendipity['GET']['id'], 1, 1);
        $serendipity['POST']['preview'] = true;
        $preview_only = true;
        // no break [PSR-2] - extends save

    case 'save':
        if (empty($serendipity['POST']['title']) && empty($serendipity['POST']['body']) && empty($serendipity['POST']['extended'])) {
            $data['is_empty'] = sprintf(EMPTY_SETTING, TITLE.', '.ENTRY_BODY.', '.EXTENDED_BODY);// .' This submit was changed back to a preview request.';
            $data['single_error'] = true;
            // reset/fallback to preview view, since we don't want any storage
            $serendipity['POST']['preview'] = 'true';
        }

        if (!$preview_only) {
            $entry = array(
                       'id'                 => isset($serendipity['POST']['id']) ? $serendipity['POST']['id'] : null,
                       'title'              => isset($serendipity['POST']['title']) ? $serendipity['POST']['title'] : '',
                       'timestamp'          => isset($serendipity['POST']['timestamp']) ? $serendipity['POST']['timestamp'] : '',
                       'body'               => isset($serendipity['POST']['body']) ? $serendipity['POST']['body'] : '',
                       'extended'           => isset($serendipity['POST']['extended']) ? $serendipity['POST']['extended'] : '',
                       'categories'         => isset($serendipity['POST']['categories']) ? $serendipity['POST']['categories'] : '',
                       'isdraft'            => isset($serendipity['POST']['isdraft']) ? $serendipity['POST']['isdraft'] : false,
                       'allow_comments'     => isset($serendipity['POST']['allow_comments']) ? $serendipity['POST']['allow_comments'] : 'false',
                       'moderate_comments'  => isset($serendipity['POST']['moderate_comments']) ? $serendipity['POST']['moderate_comments'] : '',
                       'exflag'             => !empty($serendipity['POST']['extended']) ? true : false,
                       'had_categories'     => isset($serendipity['POST']['had_categories']) ? $serendipity['POST']['had_categories'] : false
                       // Messing with other attributes causes problems when entry is saved
                       // Attributes need to explicitly matched/addressed in serendipity_updertEntry!

            );
        }

        if ($entry['allow_comments'] != 'true' && $entry['allow_comments'] !== true) {
            $entry['allow_comments'] = 'false';
        }

        if ($entry['moderate_comments'] != 'true' && $entry['moderate_comments'] !== true) {
            $entry['moderate_comments'] = 'false';
        }

        // Check if the user changed the timestamp.
        if (isset($serendipity['allowDateManipulation']) && $serendipity['allowDateManipulation'] && isset($serendipity['POST']['new_timestamp']) && $serendipity['POST']['new_timestamp'] != date(DATE_FORMAT_2, $serendipity['POST']['chk_timestamp'])) {
            // The user changed the timestamp, now set the DB-timestamp to the user's date
            $entry['timestamp'] = strtotime($serendipity['POST']['new_timestamp']);

            if ($entry['timestamp'] == -1) {
                $data['switched_output'] = true;
                $data['dateval'] = true; // date invalid message
                // The date given by the user is not convertible. Reset the timestamp.
                $entry['timestamp'] = $serendipity['POST']['timestamp'];
            }
        }

        // Save server timezone in database always, so subtract the offset we added for display; otherwise it would be added time and again
        if (!empty($entry['timestamp'])) {
            $entry['timestamp'] = serendipity_serverOffsetHour($entry['timestamp'], true);
        }

        // Save the entry, or just display a preview
        $data['use_legacy'] = $use_legacy = true;
        serendipity_plugin_api::hook_event('backend_entry_iframe', $use_legacy);

        if ($use_legacy) {
            $data['switched_output'] = true;
            if ($serendipity['POST']['preview'] != 'true') {
                /* We don't need an iframe to save a draft */
                if ($serendipity['POST']['isdraft'] == 'true') {
                    $data['is_draft'] = true;
                    $errors = serendipity_updertEntry($entry);
                    if (is_numeric($errors)) {
                        $errors = '';
                    }
                } else {
                    if ($serendipity['use_iframe']) {
                        $data['is_iframe'] = true;
                        $data['iframe'] = serendipity_iframe_create('save', $entry);
                    } else {
                        $data['iframe'] = serendipity_iframe($entry, 'save');
                    }
                }
            } else {
                // Only display the preview
                $serendipity['hidefooter'] = true;
                // Advanced templates use this to show update status and elapsed time
                if (!isset($entry['last_modified']) || !is_numeric($entry['last_modified'])) {
                    $entry['last_modified'] = time();
                }

                if (!is_numeric($entry['timestamp'])) {
                    $entry['timestamp'] = time();
                }

                if (!isset($entry['trackbacks']) || !$entry['trackbacks']) {
                    $entry['trackbacks'] = 0;
                }

                if (!isset($entry['comments']) || !$entry['comments']) {
                    $entry['comments'] = 0;
                }

                if (!isset($entry['realname']) || !$entry['realname']) {
                    if (is_numeric($entry['id'])) {
                        $_entry = serendipity_fetchEntry('id', $entry['id'], 1, 1);
                        $entry['realname'] = $_entry['author'];
                    } elseif (!empty($serendipity['realname'])) {
                        $entry['realname'] = $serendipity['realname'];
                    } else {
                        $entry['realname'] = $serendipity['serendipityUser'];
                    }
                }

                $categories = (array)$entry['categories'];
                $entry['categories'] = array();
                foreach($categories AS $catid) {
                    if ($catid == 0) {
                        continue;
                    }
                    $entry['categories'][] = serendipity_fetchCategoryInfo($catid);
                }

                if (count($entry['categories']) < 1) {
                    unset($entry['categories']);
                }

                if (isset($entry['id'])) {
                    $serendipity['GET']['id'] = $entry['id'];
                } else {
                    $serendipity['GET']['id'] = 1;
                }

                if ($serendipity['use_iframe']) {
                    $data['is_iframepreview'] = true;
                    $data['iframe'] = serendipity_iframe_create('preview', $entry);
                } else {
                    $data['iframe'] = serendipity_iframe($entry, 'preview');
                }
            }
        }

        // serendipity_updertEntry sets this global variable to store the entry id. Couldn't pass this
        // by reference or as return value because it affects too many places inside our API and dependent
        // function calls.
        if (!empty($serendipity['lastSavedEntry'])) {
            $entry['id'] = $serendipity['lastSavedEntry'];
        }

        if (!$preview_only) {
            include_once S9Y_INCLUDE_PATH . 'include/functions_entries_admin.inc.php';
            $errors = isset($errors) ? $errors : null; // set null to check again at end of file
            $entryForm = serendipity_printEntryForm(
                '?',
                array(
                  'serendipity[action]'      => 'admin',
                  'serendipity[adminModule]' => 'entries',
                  'serendipity[adminAction]' => 'save',
                  'serendipity[timestamp]'   => serendipity_specialchars($entry['timestamp'])
                ),
                $entry,
                $errors
            );
        }
        break;

    case 'doDelete':
        if (!serendipity_checkFormToken()) {
            break;
        }

        $entry = serendipity_fetchEntry('id', $serendipity['GET']['id'], 1, 1);
        serendipity_deleteEntry((int)$serendipity['GET']['id']);
        $data['switched_output'] = true;
        $data['is_doDelete']     = true;
        $data['del_entry']       = sprintf(RIP_ENTRY, $entry['id'] . ' - ' . serendipity_specialchars($entry['title']));
        // no break [PSR-2] - extends editSelect

    case 'doMultiDelete':
        if ($serendipity['GET']['adminAction'] != 'doDelete') {
            if (!serendipity_checkFormToken() || !isset($serendipity['GET']['id'])) {
                break;
            }

            $parts = explode(',', $serendipity['GET']['id']);
            $data['switched_output'] = true;
            $data['del_entry']       = array();
            foreach($parts AS $id) {
                $id = (int)$id;
                if ($id > 0) {
                    $entry = serendipity_fetchEntry('id', $id, 1, 1);
                    serendipity_deleteEntry((int)$id);
                    $data['is_doMultiDelete'] = true;
                    $data['del_entry'][]      = sprintf(RIP_ENTRY, $entry['id'] . ' - ' . serendipity_specialchars($entry['title']));
                }
            }
        }
        // no break [PSR-2] - extends editSelect

    case 'editSelect':
        $data['switched_output'] = false;

        $filter_import = array('author', 'category', 'isdraft');
        $sort_import   = array('perPage', 'ordermode', 'order');

        foreach($filter_import AS $f_import) {
            if (isset($serendipity['GET']['filter'])) {
                @serendipity_restoreVar($serendipity['COOKIE']['entrylist_filter_' . $f_import], serendipity_specialchars($serendipity['GET']['filter'][$f_import]));
                $data["get_filter_$f_import"] = @serendipity_specialchars($serendipity['GET']['filter'][$f_import]);
            }
        }

        foreach($sort_import AS $s_import) {
            if (isset($serendipity['GET']['sort'])) {
                @serendipity_restoreVar($serendipity['COOKIE']['entrylist_sort_' . $s_import], serendipity_specialchars($serendipity['GET']['sort'][$s_import]));
                $data["get_sort_$s_import"] = @serendipity_specialchars($serendipity['GET']['sort'][$s_import]);
            }
        }

        $perPage = !empty($serendipity['GET']['sort']['perPage']) ? $serendipity['GET']['sort']['perPage'] : $per_page[0];
        $page    = isset($serendipity['GET']['page']) ? (int)$serendipity['GET']['page'] : null;
        $offSet  = $perPage*$page;

        if (empty($serendipity['GET']['sort']['ordermode']) || $serendipity['GET']['sort']['ordermode'] != 'ASC') {
            $serendipity['GET']['sort']['ordermode'] = 'DESC';
        }

        if (!empty($serendipity['GET']['sort']['order']) && !empty($sort_order[$serendipity['GET']['sort']['order']])) {
            $orderby = serendipity_db_escape_string($serendipity['GET']['sort']['order'] . ' ' . $serendipity['GET']['sort']['ordermode']);
        } else {
            $orderby = 'timestamp ' . serendipity_db_escape_string($serendipity['GET']['sort']['ordermode']);
        }

        $filter = array();

        if (!empty($serendipity['GET']['filter']['author'])) {
            $filter[] = "e.authorid = '" . serendipity_db_escape_string($serendipity['GET']['filter']['author']) . "'";
        }

        if (!empty($serendipity['GET']['filter']['category'])) {
            $filter[] = "ec.categoryid = '" . serendipity_db_escape_string($serendipity['GET']['filter']['category']) . "'";
        }

        if (!empty($serendipity['GET']['filter']['isdraft'])) {
            if ($serendipity['GET']['filter']['isdraft'] == 'draft') {
                $filter[] = "e.isdraft = 'true'";
            } elseif ($serendipity['GET']['filter']['isdraft'] == 'publish') {
                $filter[] = "e.isdraft = 'false'";
            }
        }

        if (!empty($serendipity['GET']['filter']['body'])) {
            $term = serendipity_db_escape_string($serendipity['GET']['filter']['body']);
            if ($serendipity['dbType'] == 'postgres' || $serendipity['dbType'] == 'pdo-postgres') {
                $r = serendipity_db_query("SELECT count(routine_name) AS counter
                                             FROM information_schema.routines
                                            WHERE routine_name LIKE 'to_tsvector'
                                              AND specific_catalog = '" . $serendipity['dbName'] . "'");
                if (is_array($r) && $r[0]['counter'] > 0) {
                    $term = str_replace('&amp;', '&', $term);
                    $filter[] = "(
                    to_tsvector('english', title)    @@to_tsquery('$term') OR
                    to_tsvector('english', body)     @@to_tsquery('$term') OR
                    to_tsvector('english', extended) @@to_tsquery('$term')
                    )";
                } else {
                    $filter[] = "(title ILIKE '%$term%' OR body ILIKE '%$term%' OR extended ILIKE '%$term%')";
                }
            } elseif ($serendipity['dbType'] == 'sqlite' || $serendipity['dbType'] == 'sqlite3' || $serendipity['dbType'] == 'pdo-sqlite' || $serendipity['dbType'] == 'sqlite3oo') {
                $term = str_replace('*', '%', $term);
                $term = serendipity_mb('strtolower', $term);
                $filter[] = "(lower(title) LIKE '%$term%' OR lower(body) LIKE '%$term%' OR lower(extended) LIKE '%$term%')";
            } else {
                if (@mb_detect_encoding($term, 'UTF-8', true) && @mb_strlen($term, 'utf-8') < strlen($term)) {
                    $_term = str_replace('*', '', $term);
                    $filter['find_part'] = "(title LIKE '%$_term%' OR body LIKE '%$_term%' OR extended LIKE '%$_term%')";
                } else {
                    if (preg_match('@["\+\-\*~<>\(\)]+@', $term)) {
                        $filter['find_part'] = "MATCH(title,body,extended) AGAINST('$term' IN BOOLEAN MODE)";
                    } else {
                        $filter['find_part'] = "MATCH(title,body,extended) AGAINST('$term')";
                    }
                }
            }
        }

        $filter_sql = implode(' AND ', $filter);

        // Fetch the entries
        $entries = serendipity_fetchEntries(
                     false,
                     false,
                     serendipity_db_limit(
                       $offSet,
                       $perPage + 1
                     ),
                     true,
                     false,
                     $orderby,
                     $filter_sql
                   );

        $users      = serendipity_fetchUsers('', 'hidden', true);
        $categories = serendipity_fetchCategories();
        $categories = serendipity_walkRecursive($categories, 'categoryid', 'parentid', VIEWMODE_THREADED);

        $data['drawList']   = true;
        $data['sort_order'] = $sort_order;
        $data['perPage']    = $perPage;
        $data['per_page']   = $per_page;
        $data['urltoken']   = serendipity_setFormToken('url');
        $data['formtoken']  = serendipity_setFormToken();
        $data['users']      = $users;
        $data['categories'] = $categories;
        $data['offSet']     = $offSet;
        $data['use_iframe'] = $serendipity['use_iframe'];
        $data['page']       = $page;

        $data['totalEntries']  = serendipity_getTotalEntries();
        $data['simpleFilters'] = isset($serendipity['simpleFilters']) ? $serendipity['simpleFilters'] : true;

        if (is_array($entries)) {
            $data['is_entries'] = true;
            $data['count'] = count($entries);

            $qString = '?serendipity[adminModule]=entries&amp;serendipity[adminAction]=editSelect';
            foreach((array)$serendipity['GET']['sort'] AS $k => $v) {
                $qString .= '&amp;serendipity[sort]['. $k .']='. $v;
            }
            if (isset($serendipity['GET']['filter'])) {
                foreach((array)$serendipity['GET']['filter'] AS $k => $v) {
                    $qString .= '&amp;serendipity[filter]['. $k .']='. $v;
                }
            }
            $data['linkFirst']    = $qString . '&amp;serendipity[page]=' . 0;
            $data['linkPrevious'] = $qString . '&amp;serendipity[page]=' . ($page-1);
            $data['linkNext']     = $qString . '&amp;serendipity[page]=' . ($page+1);
            $data['linkLast']     = $qString . '&amp;serendipity[page]='; // is done in tpl per $totalPages

            $smartentries = array();
            foreach($entries AS $ey) {
                $entry_cats = array();
                if (count($ey['categories'])) {
                    foreach($ey['categories'] AS $cat) {
                        $cat['link'] = serendipity_categoryURL($cat);
                        $entry_cats[] = $cat;
                    }
                }

                $smartentry = array(
                    'id'            => $ey['id'],
                    'title'         => serendipity_specialchars($ey['title']),
                    'timestamp'     => (int)$ey['timestamp'],
                    'last_modified' => (int)$ey['last_modified'],
                    'isdraft'       => serendipity_db_bool($ey['isdraft']),
                    'ep_is_sticky'  => (isset($ey['properties']['ep_is_sticky']) && serendipity_db_bool($ey['properties']['ep_is_sticky']) ? true : false),
                    'pubdate'       => date('c', (int)$ey['timestamp']),
                    'author'        => serendipity_specialchars($ey['author']),
                    'cats'          => $entry_cats,
                    'preview'       => ((serendipity_db_bool($ey['isdraft']) || (!$serendipity['showFutureEntries'] && $ey['timestamp'] >= serendipity_serverOffsetHour())) ? true : false),
                    'archive_link'  => serendipity_archiveURL($ey['id'], $ey['title'], 'serendipityHTTPPath', true, array('timestamp' => $ey['timestamp'])),
                    'preview_link'  => '?serendipity[action]=admin&amp;serendipity[adminModule]=entries&amp;serendipity[adminAction]=preview&amp;' . serendipity_setFormToken('url') . '&amp;serendipity[id]=' . $ey['id'],
                    'lang'          => isset($ey['multilingual_lang']) ? $ey['multilingual_lang'] : 'all'
                );
                serendipity_plugin_api::hook_event('backend_view_entry', $smartentry);
                $smartentries[] = $smartentry;
            }

            $data['entries']           = $smartentries;
            $data['urltoken']          = serendipity_setFormToken('url');
            $data['formtoken']         = serendipity_setFormToken();
            $data['serverOffsetHour']  = serendipity_serverOffsetHour();
            $data['showFutureEntries'] = $serendipity['showFutureEntries'];
            $data['filter_import']     = $filter_import;
            $data['sort_import']       = $sort_import;
        } else {
            $data['no_entries'] = true;
        }
        // if entries end
        break;

    case 'delete':
        if (!serendipity_checkFormToken()) {
            break;
        }
        $newLoc = '?' . serendipity_setFormToken('url') . '&amp;serendipity[action]=admin&amp;serendipity[adminModule]=entries&amp;serendipity[adminAction]=doDelete&amp;serendipity[id]=' . (int)$serendipity['GET']['id'];

        $entry = serendipity_fetchEntry('id', $serendipity['GET']['id'], 1, 1);
        $data['switched_output'] = true;
        $data['is_delete']       = true;
        $data['newLoc']          = $newLoc;
        // for Smarty, printf had to turn into sprintf!!
        $data['rip_entry']       = sprintf(DELETE_SURE, $entry['id'] . ' - ' . serendipity_specialchars($entry['title']));
        break;

    case 'multidelete':
        if (!serendipity_checkFormToken()) {
            return; // blank content page, but default token check parameter is presenting a XSRF message when false
        }
        if (!is_array($serendipity['POST']['multiDelete'])) {
            echo '<div class="msg_notice"><span class="icon-attention-circled" aria-hidden="true"></span> ' . sprintf(MULTICHECK_NO_ITEM, serendipity_specialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES | ENT_HTML401)) . '</div>'."\n";
            break;
        }

        $ids = '';
        $data['rip_entry'] = array();
        foreach($serendipity['POST']['multiDelete'] AS $idx => $id) {
            $ids .= (int)$id . ',';
            $entry = serendipity_fetchEntry('id', $id, 1, 1);
            $data['is_multidelete'] = true;
            $data['rip_entry'][]    = sprintf(DELETE_SURE, $entry['id'] . ' - ' . serendipity_specialchars($entry['title']));
        }
        $newLoc = '?' . serendipity_setFormToken('url') . '&amp;serendipity[action]=admin&amp;serendipity[adminModule]=entries&amp;serendipity[adminAction]=doMultiDelete&amp;serendipity[id]=' . $ids;
        $data['switched_output'] = true;
        $data['newLoc']          = $newLoc;
        break;

    case 'edit':
        $entry = serendipity_fetchEntry('id', $serendipity['GET']['id'], 1, 1);
        // no break [PSR-2] - extends default

    default:
        include_once S9Y_INCLUDE_PATH . 'include/functions_entries_admin.inc.php';
        // edit entry mode
        $entryForm = serendipity_printEntryForm(
            '?',
            array(
            'serendipity[action]'      => 'admin',
            'serendipity[adminModule]' => 'entries',
            'serendipity[adminAction]' => 'save'
            ),
            (isset($entry) ? $entry : array())
        );
        break;
}

$data['entryForm'] = $entryForm;
$data['errors'] = isset($errors) ? $errors : false;
$data['get'] = $serendipity['GET']; // don't trust {$smarty.get.vars} if not proofed, as we often change GET vars via serendipity['GET'] by runtime
// make sure we've got these
if (!isset($data['urltoken']))  $data['urltoken']  = serendipity_setFormToken('url');
if (!isset($data['formtoken'])) $data['formtoken'] = serendipity_setFormToken();

echo serendipity_smarty_showTemplate('admin/entries.inc.tpl', $data);

/* vim: set sts=4 ts=4 expandtab : */
