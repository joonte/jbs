<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('EdeskID','CreateDate','UserID','Theme');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$User = DB_Select('Users',Array('ID','GroupID','Name'),Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    # No more...
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Edesk/Number',$EdeskID);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$CreateDate = Comp_Load('Formats/Date/Extended',$CreateDate);
if(Is_Error($CreateDate))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Group = DB_Select('Groups','Name',Array('UNIQ','ID'=>$User['GroupID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Group)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    # No more...
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Theme = Comp_Load('Edesks/Text',Array('String'=>$Theme));
if(Is_Error($Theme))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = new Tag('TABLE',Array('class'=>'Edesk','cellspacing'=>5,'height'=>'100%','width'=>'100%'));
#-------------------------------------------------------------------------------
$String = <<<EOD
<NOBODY>
 <TR>
  <TD rowspan="2" valign="top" width="90">
   <IMG class="UserFoto" alt="Персональная фотография" height="110" width="90" src="/UserFoto?UserID=%u" />
  </TD>
  <TD height="25" class="EdeskInfo">%s | %s [%s]</TD>
 </TR>
 <TR>
  <TD height="75" class="EdeskTheme">%s</TD>
 </TR>
</NOBODY>
EOD;
#-------------------------------------------------------------------------------
$Table->AddHTML(SPrintF($String,$User['ID'],$CreateDate,$User['Name'],$Group['Name'],$Theme));
#-------------------------------------------------------------------------------
$Count = DB_Count('EdesksMessages',Array('Where'=>SPrintF('`EdeskID` = %u',(integer)$EdeskID)));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$String = <<<EOD
<NOBODY>
 <TR>
  <TD align="right" colspan="2">
   <SPAN>Всего сообщений: %u</SPAN>
   <SPAN> | </SPAN>
   <A href="/EdeskMessages?EdeskID=%u">читать сообщения</A>
   <IMG width="12" height="10" src="SRC:{Images/ArrowRight.gif}" />
  </TD>
 </TR>
</NOBODY>
EOD;
#-------------------------------------------------------------------------------
$Table->AddHTML(SPrintF($String,$Count,$EdeskID));
#-------------------------------------------------------------------------------
return $Table;
#-------------------------------------------------------------------------------

?>
