<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$IsExternal = !IsSet($Args);
#-------------------------------------------------------------------------------
if($IsExternal){
	#-------------------------------------------------------------------------------
	if(Is_Error(System_Load('modules/Authorisation.mod')))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Args = Args();
	#-------------------------------------------------------------------------------
	# added by lissyara 2012-10-03 in 19:34 MSK
	if(!$GLOBALS['__USER']['IsAdmin'])
		return new gException('EXTERNAL_STATUS_SET_ONLY_FOR_ADMINS','Установка статусов доступна только персоналу');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ModeID      =   (string) @$Args['ModeID'];
$StatusID    =   (string) @$Args['StatusID'];
$RowsIDs     =    (array) @$Args['RowsIDs'];
$Comment     =   (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
$IsNoTrigger	= IsSet($Args['IsNoTrigger'])?((boolean)$Args['IsNoTrigger']):FALSE;
$IsNotNotify	= IsSet($Args['IsNotNotify'])?((boolean)$Args['IsNotNotify']):FALSE;
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/API/StatusSet]: ModeID = %s; StatusID = %s; RowsIDs = %s; Comment = %s; IsNoTrigger = %s; IsNotNotify = %s',$ModeID,$StatusID,Is_Array($RowsIDs)?'Array':$RowsIDs,$Comment,($IsNoTrigger)?'TRUE':'FALSE',($IsNotNotify)?'TRUE':'FALSE'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Count($RowsIDs) < 1)
	return new gException('ROWS_NOT_SELECTED','Записи для установки статуса не указаны');
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach($RowsIDs as $RowID)
	$Array[] = (integer)$RowID;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['ID'],$ModeID))
	return ERROR | @Trigger_Error(201);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Rows = DB_Select(SPrintF('%sOwners',$ModeID),'*',Array('Where'=>SPrintF('`ID` IN (%s)',Implode(',',$Array)),'GroupBy'=>'ID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Rows)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('ROW_NOT_FOUND','Записи для установки статуса не найдены');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#if($GLOBALS['__USER']['ID'] == 2248){
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$NeedConfirmed = $Config['Interface']['User']['InvoiceMake']['NeedConfirmed'];
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/API/StatusSet]: $NeedConfirmed = %s; SizeOf[ConfirmedWas] = %s; ConfirmedWas = %s',$NeedConfirmed,SizeOf($GLOBALS['__USER']['ConfirmedWas']),print_r($GLOBALS['__USER']['ConfirmedWas'],true)));
// требуется подтверждённый адрес, и юзер не подтверждён
if($NeedConfirmed != "NONE"){
	#-------------------------------------------------------------------------------
	// достаём даныне юзера к которму относится объект которому проставляется статус
	// строк может быть более одной, но к разным юзерам они будут относится только если это админ делает - статус ставит
	// оставляем этот вариант на его совести и используем только первую строку
	$User = DB_Select('Users',Array('ID','ConfirmedWas'),Array('ID'=>$Rows[0]['UserID'],'UNIQ'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($User)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('OBJECT_OWNER_NOT_FOUND','Записи для установки статуса не найдены');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!SizeOf($User['ConfirmedWas'])){
		#-------------------------------------------------------------------------------
		// меняем статус счёта
		if($ModeID == 'Invoices' && $StatusID == 'Payed'){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/StatusSet]: юзер не подтверждён, меняем статус счёта на "Не подтверждён"'));
			#-------------------------------------------------------------------------------
			$StatusID = 'NotConfirmed';
			#-------------------------------------------------------------------------------
			// шлём сразу сообщение юзеру, результат не интересен
			$IsSend = NotificationManager::sendMsg(new Message('NotConfirmedInvoice',(integer)$User['ID'],Array('Theme'=>SPrintF('Необходимо подтвердить аккаунт'),'InvoiceID'=>$Rows[0]['ID'])));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// если это услуга и идёт её создание...
		// домены пропускаем, бесплатно они не регистрируются, значит на уровне счетов срубится...
		if(In_Array($ModeID,Array('HostingOrders','VPSOrders','DSOrders','DNSmanagerOrders')) && In_Array($StatusID,Array('OnCreate'))){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/StatusSet]: юзер не подтверждён, активация невозможна"'));
			#-------------------------------------------------------------------------------
			return new gException('USER_NOT_CONFIRMED','Ваша учётная запись не подтверждена, необходимо добавить и подтвердить мобильный телефон');
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/API/StatusSet]: не требуется подтверждение, $NeedConfirmed = %s; SizeOf[ConfirmedWas] = %s',$NeedConfirmed,SizeOf($GLOBALS['__USER']['ConfirmedWas'])));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Statuses = $Config['Statuses'][$ModeID];
#-------------------------------------------------------------------------------
if(!IsSet($Statuses[$StatusID]))
	return new gException('STATUS_NOT_FOUND','Выбранный статус не найден');
#-------------------------------------------------------------------------------
$Status = $Statuses[$StatusID];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------








if(Is_Error(DB_Transaction($TransactionID = UniqID('StatusSet'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Exceptions = Array();
#-------------------------------------------------------------------------------
foreach($Rows as $Row){
	#-------------------------------------------------------------------------------
	if($IsExternal && IsSet($GLOBALS['__USER'])){
		#-------------------------------------------------------------------------------
		$IsPermission = Permission_Check(SPrintF('%sStatusSet',$ModeID),(integer)$GLOBALS['__USER']['ID'],(integer)$Row['UserID']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsPermission)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'false':
			return ERROR | @Trigger_Error(700);
		case 'true':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$IsNoTrigger){
		#-------------------------------------------------------------------------------
		# реализация JBS-903
		#$Path = SPrintF('Triggers/%s',$StatusID);
		$Path = 'Triggers/GLOBAL';
		#-------------------------------------------------------------------------------
		if(!Is_Error(System_Element(SPrintF('comp/%s.comp.php',$Path)))){
			#-------------------------------------------------------------------------------
			$Results = Comp_Load($Path,Array('Row'=>$Row,'ModeID'=>$ModeID,'StatusID'=>$StatusID),COMP_ALL_HOSTS);
			#-------------------------------------------------------------------------------
			switch(ValueOf($Results)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'array':
				#-------------------------------------------------------------------------------
				foreach($Results as $Result){
					#-------------------------------------------------------------------------------
					switch(ValueOf($Result)){
					case 'exception':
						#-------------------------------------------------------------------------------
						if(Is_Error(DB_Roll($TransactionID)))
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
						return new gException('STATUS_SET_ERROR','Не удалось установить статус объекту',$Result);
						#-------------------------------------------------------------------------------
					case 'true':
						# No more...
						break;
					default:
						return ERROR | @Trigger_Error(101);
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
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Path = SPrintF('Triggers/Statuses/%s/%s',$ModeID,$StatusID);
		#-------------------------------------------------------------------------------
		if(!Is_Error(System_Element(SPrintF('comp/%s.comp.php',$Path)))){
			#-------------------------------------------------------------------------------
			$Results = Comp_Load($Path,$Row,COMP_ALL_HOSTS);
			#-------------------------------------------------------------------------------
			switch(ValueOf($Results)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'array':
				#-------------------------------------------------------------------------------
				foreach($Results as $Result){
					#-------------------------------------------------------------------------------
					switch(ValueOf($Result)){
					case 'exception':
						#-------------------------------------------------------------------------------
						if(Is_Error(DB_Roll($TransactionID)))
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
						return new gException('STATUS_SET_ERROR','Не удалось установить статус объекту',$Result);
						#-------------------------------------------------------------------------------
					case 'true':
						# No more...
						break;
					default:
						return ERROR | @Trigger_Error(101);
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
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$StatusDate = Time();
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update($ModeID,Array('StatusID'=>$StatusID,'StatusDate'=>$StatusDate),Array('ID'=>$Row['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IStatusHistory = Array('StatusDate'=>$StatusDate,'ModeID'=>$ModeID,'RowID'=>$Row['ID'],'StatusID'=>$StatusID,'Comment'=>$Comment);
	#-------------------------------------------------------------------------------
	if(IsSet($GLOBALS['__USER'])){
		#-------------------------------------------------------------------------------
		$__USER = $GLOBALS['__USER'];
		#-------------------------------------------------------------------------------
		$IStatusHistory['Initiator'] = SPrintF('%s (%s)',$__USER['Name'],$__USER['Email']);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('StatusesHistory',$IStatusHistory);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Row = DB_Select(SPrintF('%sOwners',$ModeID),'*',Array('GroupBy'=>'ID','UNIQ','ID'=>$Row['ID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Row)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		if(!$IsNoTrigger && !$IsNotNotify){
			#-------------------------------------------------------------------------------
			try{
				#-------------------------------------------------------------------------------
				Debug(SPrintF("[comp/www/API/StatusSet]: try send message"));
				#-------------------------------------------------------------------------------
				$msgClass = SPrintF('%s%sMsg',$ModeID,$StatusID);
				#-------------------------------------------------------------------------------
				if(class_exists($msgClass)){
					#-------------------------------------------------------------------------------
					Debug(SPrintF("[comp/www/API/StatusSet]: send message %s%sMsg",$ModeID,$StatusID));
					#-------------------------------------------------------------------------------
					$msg = new $msgClass($Row, $Row['UserID']);
					#-------------------------------------------------------------------------------
					$IsSend = NotificationManager::sendMsg($msg);
					#-------------------------------------------------------------------------------
					switch(ValueOf($IsSend)){
					#-------------------------------------------------------------------------------
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						# No more...
						break;
					case 'true':
						# No more...
						break;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			catch(Exception $e){
				#-------------------------------------------------------------------------------
				Debug(SPrintF("Couldn't load dispatcher class: %s Message: %s",$msgClass,$e->getTraceAsString()));
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
