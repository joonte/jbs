<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Name','Info');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(!Comp_IsLoaded('Upload')){
  #-----------------------------------------------------------------------------
  $Links = &Links();
  #-----------------------------------------------------------------------------
  $DOM = &$Links['DOM'];
  #-----------------------------------------------------------------------------
  $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Upload.js}'));
  #-----------------------------------------------------------------------------
  $DOM->AddChild('Head',$Script);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Css',Array('Upload'));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  foreach($Comp as $Css)
    $DOM->AddChild('Head',$Css);
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<DIV id="Upload" style="display:none;position:absolute;top:-1000;left:-1000;">
 <IFRAME name="UploadIframe" id="UploadIframe" onload="if(typeof UploadIframeOnLoad != 'undefined') UploadIframeOnLoad();" src="about:blank" style="display:none;">-</IFRAME>
 <FORM id="UploadForm" name="UploadForm" action="/API/Upload" target="UploadIframe" method="POST" enctype="multipart/form-data">
  <TABLE class="Upload" width="5" cellspacing="5">
   <TR>
    <TD>
     <INPUT type="file" name="Upload" size="15" onchange="UploadProgress();" />
    </TD>
    <TD>
     <INPUT type="button" onclick="UploadHide();" value="Закрыть" />
    </TD>
   </TR>
  </TABLE>
 </FORM>
</DIV>
EOD;
#-------------------------------------------------------------------------------
  $DOM->AddHTML('Floating',$Parse);
}
#-------------------------------------------------------------------------------
$Table = new Tag('TABLE',Array('width'=>'200','cellspacing'=>0));
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<TR>
 <TD class="Standard" id="Upload%sInfo">%s</TD>
 <TD width="30">
  <INPUT style="font-weight:bold;" type="button" value=" + " onclick="UploadShow(event,form.name,'%s');" />
 </TD>
</TR>
EOD;
#-------------------------------------------------------------------------------
$Table->AddHTML(SPrintF($Parse,$Name,($Info?$Info:'-'),$Name));
#-------------------------------------------------------------------------------
return $Table;
#-------------------------------------------------------------------------------

?>
