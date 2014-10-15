<?php

#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Theme = "Проверка баланса счета регистратора";
$Message = "";
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Registrator.class.php','libs/IspSoft.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Registrators = DB_Select('Registrators',Array('ID','Name','TypeID','BalanceLowLimit'),Array('Where'=>'`BalanceLowLimit` > 0'));
#-------------------------------------------------------------------------------
switch(ValueOf($Registrators)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		Debug("[comp/Tasks/GC/CheckBalance]: Регистраторы не найдены");
		return TRUE;
	case 'array':
		#-----------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'] = Array();
		#-----------------------------------------------------------------------
		foreach($Registrators as $NowReg){
			#-----------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'][] = $NowReg['Name'];
			#-------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: Проверка баланса для %s (ID %d, тип %s)',$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']));
			#-------------------------------------------------------------------
			$Registrator = new Registrator();
			#-------------------------------------------------------------------
			$IsSelected = $Registrator->Select((integer)$NowReg['ID']);
			#-------------------------------------------------------------------
			switch(ValueOf($IsSelected)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
				case 'true':
					#-----------------------------------------------------------
					$Balance = $Registrator->GetBalance();
					#-----------------------------------------------------------
					break;
				default:
					return new gException('WRONG_STATUS','Регистратор не определён');
			}
			switch(ValueOf($Balance)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
				#---------------------------------------------------------------
				switch($Balance->CodeID){
					case 'REGISTRATOR_ERROR':
						Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: %s: %s',$NowReg['Name'],$Balance->String));
						break;
					default:
						Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: Для регистратора %s (ID %d, тип %s) проверка баланса счета не реализована.',$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']));
						$Message .= SPrintF("Для регистратора %s (ID %d, тип %s) проверка баланса счета не реализована. \n",$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']);
				}
				#---------------------------------------------------------------
				break;
				case 'array':
					Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: Регистратор (%s), баланс: %s',$NowReg['Name'],$Balance['Prepay']));
					#-----------------------------------------------------------
					if((float)$Balance['Prepay'] < $NowReg['BalanceLowLimit']){
						Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: Баланс (%s) ниже порога уведомления',$NowReg['Name']));
						$Message .= SPrintF("Остаток на счете регистратора %s ниже допустимого минимума - %01.2f\n",$NowReg['Name'],$Balance['Prepay']);
					}
					#-----------------------------------------------------------
					break;
				default:
					return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
			}
			#-------------------------------------------------------------------
		}
		#-----------------------------------------------------------------------
		break;
	default:
        return ERROR | @Trigger_Error(101);
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# баланс ISPsystem
$Settings = SelectServerSettingsByService(51000);
#Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: Settings = %s',print_r($Settings,true)));
switch(ValueOf($Settings)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: Исключение при поиске сервера, Settings = %s',print_r($Settings,true)));
	break;
	case 'array':
		#-------------------------------------------------------------------------------
		if($Settings['Params']['BalanceLowLimit'] > 0){
			#-------------------------------------------------------------------------------
			# получаем баланс
			$Balances = IspSoft_Get_Balance($Settings);
			Debug("[comp/Tasks/GC/CheckBalance]: " . print_r($Balances, true) );
			#-------------------------------------------------------------------------------
			foreach($Balances as $Balance){
				#-------------------------------------------------------------------------------
				if(IsSet($Balance['project']) && $Balance['project'] == 'ISPsystem'){
					#-------------------------------------------------------------------------------
					Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: %s / %s',$Balance['project'],$Balance['balance']));
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					if((double)$Balance['balance'] < $Settings['Params']['BalanceLowLimit']){
						#-------------------------------------------------------------------------------
						Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: add to message: %s / %s',$Balance['name'],$Balance['balance']));
						#-------------------------------------------------------------------------------
						$Message .= SPrintF("Остаток на счете ISPsystem ниже допустимого минимума - %s \n",$Balance['balance']);
						#-------------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# баланс SMS машинки
$ServerSettings = SelectServerSettingsByTemplate('SMS');
#-------------------------------------------------------------------------------
switch(ValueOf($ServerSettings)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	if($ServerSettings['Params']['BalanceLowLimit'] > 0){
		#-------------------------------------------------------------------------------
		if(Is_Error(System_Load(SPrintF('classes/%s.class.php', $ServerSettings['Params']['Provider']))))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$SMS = new $ServerSettings['Params']['Provider']($ServerSettings['Login'],$ServerSettings['Password'],$ServerSettings['Params']['ApiKey'],$ServerSettings['Params']['Sender']);
		if (Is_Error($SMS))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsAuth = $SMS->balance('rur');
		#-------------------------------------------------------------------------------
	        switch (ValueOf($IsAuth)){
		case 'true':
			#-------------------------------------------------------------------------------
			$Balance = (double)$SMS->balance;
			Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: баланс SMS шлюза "%s" равен: %s',$ServerSettings['Params']['Provider'],$Balance));
			#-------------------------------------------------------------------------------
			if($Balance < $ServerSettings['Params']['BalanceLowLimit']){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/GC/CheckBalance]: SMS provider low balance = %s',$Balance));
				$Message .= SPrintF("Остаток на счете SMS шлюза \"%s\" ниже допустимого минимума: %01.2f руб.\n",$ServerSettings['Params']['Provider'],$Balance);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			break;
		}
		#-------------------------------------------------------------------------------
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
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
				Debug(SPrintF("[comp/Tasks/GC/CheckBalance]: найдено %s сотрудников любых отделов",SizeOf($Employers)));
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
		Debug(SPrintF("[comp/Tasks/GC/CheckBalance]: найдено %s сотрудников отдела бухгалтерии",SizeOf($Employers)));
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
		Debug(SPrintF("[comp/Tasks/GC/CheckBalance]: Сообщение для сотрудника #%s отослано",$Employer['ID']));
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#---------------------------------------------------------
#---------------------------------------------------------
return TRUE;


?>
