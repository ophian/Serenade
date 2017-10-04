<?php

if (IN_serendipity !== true) {
    die ("Don't hack!");
}

$data = array();

// do not move to end of switch, since this will change smarty assignment scope
ob_start();
include S9Y_INCLUDE_PATH . 'include/admin/import.inc.php';
$data['importMenu'] = ob_get_contents();
ob_end_clean();

$usedSuffixes = serendipity_db_query("SELECT DISTINCT(thumbnail_name) AS thumbSuffix FROM {$serendipity['dbPrefix']}images", false, 'num');

$data['dbUtf8mb4_ready']     = $serendipity['dbUtf8mb4_ready'];
$data['dbUtf8mb4']           = $serendipity['dbUtf8mb4'];
$data['dbUtf8mb4_converted'] = $serendipity['dbUtf8mb4_converted'];
$data['urltoken']            = serendipity_setFormToken('url');
$data['formtoken']           = serendipity_setFormToken();
$data['thumbsuffix']         = $serendipity['thumbSuffix'];
$data['dbnotmysql']          = ($serendipity['dbType'] == 'mysql' || $serendipity['dbType'] == 'mysqli') ? false : true;
$data['suffixTask']          = (count($usedSuffixes) > 1) ? true : false;

switch($serendipity['GET']['adminAction']) {
    case 'integrity':
        $data['action'] = 'integrity';

        if (!is_readable(S9Y_INCLUDE_PATH . 'checksums.inc.php') || 0 == filesize(S9Y_INCLUDE_PATH . 'checksums.inc.php') ) {
            $data['noChecksum'] = true;
            break;
        }
        $data['badsums'] = serendipity_verifyFTPChecksums();
        break;

    case 'utf8mb4':
        $data['dbUtf8mb4_simulated'] = true;
        if (!serendipity_checkPermission('siteConfiguration') || !serendipity_checkFormToken()) {
            $data['dbUtf8mb4_error'] = PERM_DENIED;
            break;
        }

        if (!function_exists('serendipity_db_migrate_index') || !$serendipity['dbUtf8mb4_ready']) {
            $data['dbUtf8mb4_error'] = 'Not UTF-8mb4 ready.';
        }

        if ($serendipity['dbUtf8mb4']) {

            if (isset($serendipity['POST']['adminOption']['execute'])) {
                $data['dbUtf8mb4_migrate'] = serendipity_db_migrate_index(false, $serendipity['dbPrefix']);
                #echo '<pre>'.print_r($data['dbUtf8mb4_migrate'],1).'</pre>';
                /* TODO: Enable*/
                serendipity_set_config_var('dbUtf8mb4_converted', 'true');
                
                $data['dbUtf8mb4_executed'] = true;
            } else {
                $data['dbUtf8mb4_migrate'] = serendipity_db_migrate_index(true, $serendipity['dbPrefix']);
            }

            if (count($data['dbUtf8mb4_migrate']['errors']) > 0) {
                $data['dbUtf8mb4_simulated'] = false;
            }
        }
        break;

    case 'runcleanup':
        // The smarty method clearCompiledTemplate() clears all compiled smarty template files in templates_c
        // Since there may be other compiled template files in templates_c too, we have to restrict this call() to clear the blogs template only,
        // to not have the following automated recompile, force the servers memory to get exhausted,
        // when using plugins like serendipity_event_gravatar plugin, which can eat up some MB...
        // Restriction to template means: leave the page we are on: ../admin/index.tpl and all others, which are set, included and compiled by runtime. (plugins, etc. this can be quite some..!)
        if (method_exists($serendipity['smarty'], 'clearCompiledTemplate')) {
            $data['cleanup_finish']   = $serendipity['smarty']->clearCompiledTemplate($serendipity['template'], $serendipity['template']);
            $data['cleanup_template'] = $serendipity['template'];
        }
        break;
}

echo serendipity_smarty_showTemplate('admin/maintenance.inc.tpl', $data);

/* vim: set sts=4 ts=4 expandtab : */
