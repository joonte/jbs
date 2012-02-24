<?php
#-------------------------------------------------------------------------------
$Folder = $_SERVER['DOCUMENT_ROOT'];
#-------------------------------------------------------------------------------
$HtAccess = IO_Read(SPrintF('%s/.htaccess',$Folder));
if(Is_Error($HtAccess))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<IfModule mod_headers.c>
 <FilesMatch "\.(css|js|png)$">
Header set Cache-Control "max-age=600, proxy-revalidate"
 </FilesMatch>
</IfModule>
EOD;
#-------------------------------------------------------------------------------
$HtAccess = SPrintF("%s\n\n%s",$HtAccess,$Parse);
#-------------------------------------------------------------------------------
$IsWrite = IO_Write(SPrintF('%s/.htaccess',$Folder),$HtAccess,TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>