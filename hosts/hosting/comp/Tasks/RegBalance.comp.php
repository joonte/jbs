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
if(Is_Error(System_Load('classes/Registrator.class.php','libs/IspSoft.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['RegBalance'];
#-------------------------------------------------------------------------------
$Registrators = DB_Select('Registrators',Array('ID','Name','TypeID','BalanceLowLimit'),Array('Where'=>'`BalanceLowLimit` > 0'));
#-------------------------------------------------------------------------------
switch(ValueOf($Registrators)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		Debug("[comp/Tasks/RegBalance]: Регистраторы не найдены");
		return MkTime(7,20,0,Date('n'),Date('j')+1,Date('Y'));
	case 'array':
		#-----------------------------------------------------------------------
		foreach($Registrators as $NowReg){
			#-------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/RegBalance]: Проверка баланса для %s (ID %d, тип %s)',$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']));
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
						Debug(SPrintF('[comp/Tasks/RegBalance]: %s: %s',$NowReg['Name'],$Balance->String));
						break;
					default:
						Debug(SPrintF('[comp/Tasks/RegBalance]: Для регистратора %s (ID %d, тип %s) проверка баланса счета не реализована.',$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']));
						$Message .= SPrintF("Для регистратора %s (ID %d, тип %s) проверка баланса счета не реализована. \n",$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']);
				}
				#---------------------------------------------------------------
				break;
				case 'array':
					Debug("[comp/Tasks/RegBalance]: Баланс: " . $Balance['Prepay']);
					#-----------------------------------------------------------
					if ((float)$Balance['Prepay'] < $NowReg['BalanceLowLimit']){
						Debug("[comp/Tasks/RegBalance]: Баланс ниже порога уведомления!");
						$Message .= SPrintF("Остаток на счете регистратора %s ниже допустимого минимума - %01.2f\n", $NowReg['Name'],$Balance['Prepay']);
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
#-----------------------------------------------------------------------
#-----------------------------------------------------------------------
# баланс ISPsystem
$ISPSettings = $Config['IspSoft']['Settings'];
# проверяем - настроено ли соединение с испсисем
if($ISPSettings['Password'] && $ISPSettings['BalanceLowLimit'] > 0){
	# получаем баланс
	$Balances = IspSoft_Get_Balance($ISPSettings);
	#Debug("[comp/Tasks/RegBalance]: " . print_r($Balances, true) );
	foreach($Balances as $Balance){
		Debug("[comp/Tasks/RegBalance]: " . $Balance['name'] . " / " . $Balance['balance']);
		if($Balance['name'] == 'ISPsystem'){
			if($Balance['balance'] < $ISPSettings['BalanceLowLimit']){
				Debug("[comp/Tasks/RegBalance]: add to message: " . $Balance['name'] . " / " . $Balance['balance']);
				$Message .= SPrintF("Остаток на счете ISPsystem ниже допустимого минимума - %01.2f евро. \n",$Balance['balance']);
			}
		}
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# если нет сообщения, то нефига и отсылать пустое
if(StrLen($Message) < 10)
	return MkTime(7,20,0,Date('n'),Date('j')+1,Date('Y'));
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
				Debug(SPrintF("[comp/Tasks/RegBalance]: найдено %s сотрудников любых отделов",SizeOf($Employers)));
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
		Debug(SPrintF("[comp/Tasks/RegBalance]: найдено %s сотрудников отдела бухгалтерии",SizeOf($Employers)));
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
    $msg = new DispatchMsg(Array('Theme'=>$Theme,'Message'=>$Message), (integer)$Employer['ID'], $FromID);
	$IsSend = NotificationManager::sendMsg($msg);
	#---------------------------------------------------------
	switch(ValueOf($IsSend)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
	case 'true':
		# No more...
		Debug(SPrintF("[comp/Tasks/RegBalance]: Сообщение для сотрудника #%s отослано",$Employer['ID']));
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#---------------------------------------------------------
#---------------------------------------------------------
return MkTime(7,20,0,Date('n'),Date('j')+1,Date('Y'));


?>
