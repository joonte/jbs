<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$MessageID = (integer) @$Args['MessageID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$MessageID)
  return new gException('MESSAGE_ID_IS_EMPTY','Выберите сообщение');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-----------------------------------------------------------------------------
if(!$__USER['IsAdmin'])
	return ERROR | @Trigger_Error(700);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ПРоверяем - есть ли такое сообщение?
$Count = DB_Count('EdesksMessages',Array('ID'=>$MessageID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('MESSAGE_NOT_FOUND','Сообщение не найдено');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выбираем текст тикета, и его тему - для записи события
$EdeskMessage = DB_Select('EdesksOwners',Array('ID','Theme','Content'),Array('UNIQ','Where'=>SPrintF('`MessageID` = %u',$MessageID)));
#-------------------------------------------------------------------------------
switch(ValueOf($EdeskMessage)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	# All OK
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем - не единственное ли это сообщение треда?
$Count = DB_Count('EdesksMessages',Array('Where'=>SPrintF("`EdeskID` = (SELECT `EdeskID` FROM `EdesksMessages` WHERE `ID` = %u)",$MessageID)));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count == 1){
	#-------------------------------------------------------------------------------
	#return new gException('LAST_MESSAGE_IN_TRED','Это - последнее сообщение тикета. Необходимо удалять тикет.');
	$Comp = Comp_Load('www/API/Delete',Array('TableID'=>'Edesks','RowsIDs'=>$EdeskMessage['ID']));
	if(Is_Error($Comp))
		return new gException('CANNOT_DELETE_TABLE_ROW','Не удалось удалить тикет');
	#-------------------------------------------------------------------------------
	$Message = SPrintF('Удалён тикет #%u (%s)',$EdeskMessage['ID'],$EdeskMessage['Theme']);
	#-------------------------------------------------------------------------------
	$Out['Location'] = '/Administrator/Tickets';
}else{
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/Delete',Array('TableID'=>'EdesksMessages','RowsIDs'=>$MessageID));
	if(Is_Error($Comp))
		return new gException('CANNOT_DELETE_TABLE_ROW','Не удалось удалить выбранное сообщение');
	#-------------------------------------------------------------------------------
	$Message = SPrintF('Удалено сообщение #%u, тикет #%u (%s), текст сообщения (%s...)',$MessageID,$EdeskMessage['ID'],$EdeskMessage['Theme'],SubStr($EdeskMessage['Content'],0,100));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Array(
		'UserID'	=> $__USER['ID'],
		'PriorityID'	=> 'Warning',
		'Text'		=> $Message
		);
$Event = Comp_Load('Events/EventInsert',$Event);
#-------------------------------------------------------------------------------
if(!$Event)
	return ERROR | @Trigger_Error(500);
#--------------------------------------------------------------------------------
#--------------------------------------------------------------------------------
# удалять могут тока админы. значит и локейшен туда же
return $Out;
#-------------------------------------------------------------------------------

?>
