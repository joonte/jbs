<?php
#-------------------------------------------------------------------------------
/** @author Vitaly Velikodny */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array();

$currentVersion = "##VERSION##";

$versionInfoJson = @file_get_contents("http://joonte.com/public/version");
Debug(SprintF($versionInfoJson));

$versionInfo = @json_decode($versionInfoJson, true);

if($versionInfoJson && $versionInfo) {
    if (!isset($versionInfo['version'])) {
        return $Result;
    }

$Parse = <<<EOD
<NOBODY>
 <SPAN>Внимание, доступна новая версия биллинговой системы </SPAN>
 <SPAN style="font-weight:bold;">%s</SPAN>
 <A href="/Administrator/Update">Перейти к обновлению »</A>
</NOBODY>
EOD;
    $LastVersion = $versionInfo['version'];
	#-------------------------------------------------------------------------------
    if ($LastVersion != $currentVersion) {
        $NoBody = new Tag('NOBODY');
        $NoBody->AddHTML(SPrintF($Parse, $LastVersion));
        $Result[] = $NoBody;
    }
}
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------
?>
