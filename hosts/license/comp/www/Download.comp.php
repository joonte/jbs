<?
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$smarty=$GLOBALS['smarty'];

$versionsJson = @file_get_contents("http://jira.joonte.com/rest/api/2.0.alpha1/project/JBS/versions");
$versions = @json_decode($versionsJson);

$smarty->assign('latestVersion', VERSION);

if ($versionsJson && $versions) {
    $versions = array_reverse($versions);

    $smarty->assign('versions', $versions);

    $versionInfoJson = @file_get_contents("http://joonte.com/public/version");
    $versionInfo = @json_decode($versionInfoJson, true);
    if ($versionInfoJson && $versionInfo) {
        if($versionInfoJson && $versionInfo) {
            if (isset($versionInfo['version'])) {
                $smarty->assign('latestVersion', $versionInfo['version']);
            }
        }
    }
}

return $smarty->display('download.tpl');
#-------------------------------------------------------------------------------
?>