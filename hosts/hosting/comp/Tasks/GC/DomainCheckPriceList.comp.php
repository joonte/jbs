<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Theme = "Проверка стоимости доменных имён";
$Message = "";
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/DomainServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Params'),Array('Where'=>Array('`IsActive` = "yes"','(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 20000')));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	# No more...
	Debug("[comp/Tasks/GC/DomainCheckPriceList]: Регистраторы не найдены");
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($Servers as $Registrator){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/GC/DomainCheckPriceList]: Проверка цен на домены для %s (ID %d, тип %s)',$Registrator['Params']['Name'],$Registrator['ID'],$Registrator['Params']['SystemID']));
	#-------------------------------------------------------------------------------
	$Server = new DomainServer();
	#-------------------------------------------------------------------------------
	$IsSelected = $Server->Select((integer)$Registrator['ID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
	case 'true':
		break;
	default:
		return new gException('WRONG_STATUS','Регистратор не определён');
	}
	#-------------------------------------------------------------------------------
	$Prices = $Server->DomainPriceList();
	#-------------------------------------------------------------------------------
	switch(ValueOf($Prices)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		switch($Prices->CodeID){
		case 'REGISTRATOR_ERROR':
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/GC/DomainCheckPriceList]: %s: %s',$Registrator['Params']['Name'],$Prices->String));
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/GC/DomainCheckPriceList]: Для регистратора %s (ID %d, тип %s) проверка стоимости доменов не реализована.',$Registrator['Params']['Name'],$Registrator['ID'],$Registrator['Params']['SystemID']));
			#-------------------------------------------------------------------------------
			$Message .= SPrintF("Для регистратора %s (ID %d, тип %s) проверка стоимости доменов не реализована. \n",$Registrator['Params']['Name'],$Registrator['ID'],$Registrator['Params']['SystemID']);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		continue 2;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		break;
	default:
		return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Prices) as $Key){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/GC/DomainCheckPriceList]: регистратор = %s; зона = %s; период = %s-%s; валюта = %s; цена регистрации = %s; цена продления = %s',$Registrator['Params']['Name'],$Key,$Prices[$Key]['min.period'],$Prices[$Key]['max.period'],$Prices[$Key]['curr'],$Prices[$Key]['new'],$Prices[$Key]['renew']));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

return TRUE;


$Message .= SPrintF("Остаток на счете регистратора %s ниже допустимого минимума - %01.2f\n",$Registrator['Params']['Name'],$Prices['Prepay']);

#-------------------------------------------------------------------------------


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# если нет сообщения, то нефига и отсылать пустое
if(StrLen($Message) < 10)
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ищщем сторудников бухгалтерии
$Entrance = Tree_Entrance('Groups',3200000);
#-------------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#---------------------------------------------------------------
	$String = Implode(',',$Entrance);
	#---------------------------------------------------------------
	$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
	#---------------------------------------------------------------
	switch(ValueOf($Employers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# найти всех сотрудников, раз нет сотрудников в бухгалтерии
		$Entrance = Tree_Entrance('Groups',3000000);
		#-------------------------------------------------------------------
		switch(ValueOf($Entrance)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#---------------------------------------------------------------
			$String = Implode(',',$Entrance);
			#---------------------------------------------------------------
			$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
			#---------------------------------------------------------------
			switch(ValueOf($Employers)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				Debug(SPrintF("[comp/Tasks/GC/DomainCheckPriceList]: найдено %s сотрудников любых отделов",SizeOf($Employers)));
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		break;
	case 'array':
		Debug(SPrintF("[comp/Tasks/GC/DomainCheckPriceList]: найдено %s сотрудников отдела бухгалтерии",SizeOf($Employers)));
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#---------------------------------------------------------
#---------------------------------------------------------
foreach($Employers as $Employer){
	#---------------------------------------------------------
	$msg = new DispatchMsg(Array('Theme'=>$Theme,'Message'=>$Message), (integer)$Employer['ID'], 100 /*$FromID*/);
    	$IsSend = NotificationManager::sendMsg($msg);
	#---------------------------------------------------------
	switch(ValueOf($IsSend)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
	case 'true':
		# No more...
		Debug(SPrintF("[comp/Tasks/GC/DomainCheckPriceList]: Сообщение для сотрудника #%s отослано",$Employer['ID']));
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#---------------------------------------------------------
#---------------------------------------------------------
return TRUE;


?>
