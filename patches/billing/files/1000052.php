<?php
#-------------------------------------------------------------------------------
$Real = SPrintF('%s/hosts/%s/domtemplates/Base.xml',SYSTEM_PATH,HOST_ID);
#-------------------------------------------------------------------------------
if(File_Exists($Real)){
  #-----------------------------------------------------------------------------
  $Source = IO_Read($Real);
  if(Is_Error($Source))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
$Replace = <<<EOD
<TD id="Context">
     <TABLE cellspacing="0" cellpadding="0" width="100%" height="100%">
      <TR>
       <COMP id="MenuLeft" path="Menus/Left" />
       <TD valign="top">
        <DIV class="Title" id="Title" />
        <TABLE width="100%" cellspacing="5" cellpadding="0">
         <TR>
          <TD id="Into" />
         </TR>
        </TABLE>
       </TD>
      </TR>
     </TABLE>
    </TD>
EOD;
  #-----------------------------------------------------------------------------
  $Source = Str_Replace('<TD id="Main" />',$Replace,$Source);
  #-----------------------------------------------------------------------------
  $IsWrite = IO_Write($Real,$Source,TRUE);
  if(Is_Error($IsWrite))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>