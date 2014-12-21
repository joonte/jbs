<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Servers = DB_Select('Servers',Array('ID','TemplateID','ServersGroupID','IsActive','Address','IsOK','AdminNotice','(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) as `ServersGroupName`','(SELECT `Comment` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) as `ServersGroupComment`','(SELECT (SUM(`UpTime`*`Count`)/SUM(`Count`)) as `UpTime` FROM `ServersUpTime` WHERE `ServersUpTime`.`ServerID` = `Servers`.`ID`) as `UpTime`'),Array('SortOn'=>'ServersGroupID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return FALSE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Rows = Array();
#-------------------------------------------------------------------------------
$ServersGroupName = UniqID();
#-------------------------------------------------------------------------------
foreach($Servers as $Server){
	#-------------------------------------------------------------------------------
	$Row = Array();
	#-------------------------------------------------------------------------------
	if($Server['ServersGroupName'] != $ServersGroupName){
		#-------------------------------------------------------------------------------
		$ServersGroupName = !Is_Null($Server['ServersGroupName'])?$Server['ServersGroupName']:'-';
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/String',(!Is_Null($Server['ServersGroupComment'])?$Server['ServersGroupComment']:'-'),40);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>5,'class'=>'Separator'),new Tag('SPAN',Array('style'=>'font-size:16px;'),SPrintF('%s |',$ServersGroupName)),new Tag('SPAN',$Comp)));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Notice','Servers',$Server['ID'],$Server['AdminNotice']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Row[] = new Tag('TD',$Comp);
	$Row[] = new Tag('TD',Array('class'=>'Standard','onclick'=>SPrintF("ServerNoticeEdit(%u,'%s');",$Server['ID'],$Server['AdminNotice'])),$Server['Address']);
	$Row[] = new Tag('TD',Array('class'=>'Standard'),new Tag('SPAN',$Server['Address']),new Tag('IMG',Array('alt'=>'+','class'=>'Button','onclick'=>SPrintF("window.open('http://www.reputationauthority.org/lookup.php?ip=%s&Submit.x=0&Submit.y=0&Submit=Search');",$Server['Address']),'src'=>'SRC:{Images/Icons/Flag16.gif}')));
	$Row[] = new Tag('TD',Array('class'=>'Standard'),$Server['TemplateID']);
	$Row[] = new Tag('TD',Array('class'=>'Standard','align'=>'center'),SPrintF('%01.2f%%',$Server['UpTime']));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Servers/IsOK',$Server['IsOK'],$Server['ID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Row[] = new Tag('TD',Array('align'=>'center'),$Comp);
	#-------------------------------------------------------------------------------
	$Rows[] = $Row;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Extended',$Rows);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Title'=>'Мониторинг серверов хостинга','DOM'=>$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


?>
