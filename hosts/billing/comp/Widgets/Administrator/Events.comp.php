<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Events = DB_Select('Events',Array('ID','CreateDate','UserID','IsReaded','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `Events`.`UserID`) as `Email`','Text','PriorityID'),Array('Where'=>"`CreateDate` >= BEGIN_DAY() AND `PriorityID` IN ('Billing','Warning','Error')",'SortOn'=>'CreateDate','IsDesc'=>TRUE,'Limits'=>Array(0,100)));
#-------------------------------------------------------------------------------
switch(ValueOf($Events)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return FALSE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = new Tag('TABLE',Array('cellspacing'=>0,'width'=>'100%'));
#-------------------------------------------------------------------------------
foreach($Events as $Event){
	#-------------------------------------------------------------------------------
	$CreateDate = Comp_Load('Formats/Date/Extended',$Event['CreateDate']);
	if(Is_Error($CreateDate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Colors/Events',$Event['PriorityID'],$Event['IsReaded']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#$Style = Array('class'=>'Standard','style'=>$Comp);
	$Style = Array('class'=>'Standard','style'=>SPrintF('background-color:%s;',$Comp['bgcolor']));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Td = new Tag('TD',$Style);
	#-------------------------------------------------------------------------------
	$Td->AddChild(new Tag('B',$CreateDate));
	$Td->AddChild(new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/UserInfo',{UserID:%u});",$Event['UserID'])),SPrintF('[%s]',$Event['Email'])));
	$Td->AddChild(new Tag('BR'));
	$Td->AddChild(new Tag('SPAN',$Event['Text']));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table->AddChild(new Tag('TR',$Td));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('style'=>'border:1px solid #FFFFFF;width:400px;height:200px;overflow:scroll;overflow-x:auto;overflow-y:auto;'),$Table);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Title'=>'Последние 100 событий, за сегодня','DOM'=>new Tag('NOBODY',$Div,new Tag('A',Array('href'=>'/Administrator/Events'),'[все события]')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
