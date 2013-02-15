<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$TicketID 		= (integer) @$Args['TicketID'];
$Email			=  (string) @$Args['Email'];
$Flags			=  (string) @$Args['Flags'];
$FromID			= (integer) @$Args['FromID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# проверка наличия выбранного тикета
$Edesks = DB_Select('Edesks',Array('*'),Array('UNIQ','ID'=>$TicketID));
#-------------------------------------------------------------------------------
switch(ValueOf($Edesks)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('EDESK_NOT_FOUND','Указанный тикет не найден');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
# проверка наличия юзера
$User = DB_Select('Users',Array('ID','Email'),Array('UNIQ','Where'=>SPrintF("`Email` = '%s'",$Email)));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('USER_NOT_FOUND','Указанный пользователь не найден, проверьте правильность ввода почтового адреса.');
case 'array':
	#---------------------------------------------------------------------------
	if($User['ID'] == $Edesks['UserID'])
		return new gException('SOME_OWNER','Бессмысленно копировать тикет одному и тому же пользователю');
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
# проверяем наличие юзера от которого будет тикет
$UserFrom = DB_Select('Users',Array('ID','Email'),Array('UNIQ','ID'=>$FromID));
#-------------------------------------------------------------------------------
switch(ValueOf($UserFrom)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('FROM_USER_NOT_FOUND','Пользователь от которого будет тикет, не найден');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
# выбираем текст первого сообщения
# select ID,UserID,EdeskID from EdesksMessages where EdeskID = 22953 ORDER BY ID ASC LIMIT 1;
$Message = DB_Select('EdesksMessages',Array('*'),Array('UNIQ','Where'=>SPrintF('`EdeskID` = %u',$TicketID),'SortOn'=>'ID','Limits'=>Array(0,1)));
#-------------------------------------------------------------------------------
switch(ValueOf($Message)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('MESSAGE_NOT_FOUND','Первое сообщение тикета не найдено');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-----------------------------TRANSACTION-----------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('TicketCopy'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IEdesk = Array(
		'UserID'	=> $User['ID'],
		'TargetGroupID'	=> $Edesks['TargetGroupID'],
		'TargetUserID'	=> $Edesks['TargetUserID'],
		'Theme'		=> $Edesks['Theme'],
		'UpdateDate'	=> Time(),
		'StatusID'	=> $Edesks['StatusID'],
		'Flags'		=> $Flags
		);
#-------------------------------------------------------------------------------
$EdeskID = DB_Insert('Edesks',$IEdesk);
if(Is_Error($EdeskID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IMessage = Array(
		'UserID'	=> $FromID,
		'EdeskID'	=> $EdeskID,
		'Content'	=> $Message['Content']
		);
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('EdesksMessages',$IMessage);
if(Is_Error($IsInsert))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','IsNotNotify'=>TRUE,'IsNoTrigger'=>TRUE,'StatusID'=>'Opened','Comment'=>SPrintF('Скопирован из #%s',$TicketID),'RowsIDs'=>$EdeskID));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
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
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
