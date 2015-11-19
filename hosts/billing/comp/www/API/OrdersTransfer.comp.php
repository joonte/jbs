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
$ServiceID 		= (integer) @$Args['ServiceID'];
$ServiceOrderID		= (integer) @$Args['ServiceOrderID'];
$Email			=  (string) @$Args['Email'];
$OrdersTransferID	= (integer) @$Args['OrdersTransferID'];
$ContractID		= (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ствим как выполненные все старые задачи
$IsUpdate = DB_Update('OrdersTransfer',Array('IsExecuted'=>TRUE),Array('Where'=>SPrintF('`CreateDate` < %u',(Time() - 24*3600))));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# варианты, если задано OrdersTransferID - это подтверждение приёма, иначе - передача
Debug(SPrintF('[comp/www/API/OrdersTransfer]: OrdersTransferID = %u',$OrdersTransferID));
if(!$OrdersTransferID){
	#-------------------------------------------------------------------------------
	# достаём сервис
	$Service = DB_Select('ServicesOwners',Array('ID','Code','NameShort'),Array('UNIQ','ID'=>$ServiceID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Service)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SERVICE_NOT_FOUND','Указанный сервис не найден');
	case 'array':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Order = DB_Select(SPrintF('%sOrdersOwners',($Service['Code'] == 'Default')?'':$Service['Code']),Array('*','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Email`'),Array('UNIQ','ID'=>$ServiceOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Order)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#---------------------------------------------------------------------------
		$IsPermission = Permission_Check('OrdersTransfer',(integer)$__USER['ID'],(integer)$Order['UserID']);
		#---------------------------------------------------------------------------
		switch(ValueOf($IsPermission)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'false':
			return ERROR | @Trigger_Error(700);
		case 'true':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# смотрим, есть ли такой юзер
	$Regulars = Regulars();
	#-------------------------------------------------------------------------------
	$Email = StrToLower($Email);
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['Email'],$Email))
		return new gException('WRONG_EMAIL','Неверно указан электронный адрес');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$User = DB_Select('Users',Array('ID','Email'),Array('UNIQ','Where'=>SPrintF("`Email` = '%s'",$Email)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($User)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('NEW_USER_NOT_FOUND','Указанный пользователь не найден, проверьте правильность ввода почтового адреса.');
	case 'array':
		#---------------------------------------------------------------------------
		if($User['ID'] == $Order['UserID'])
			return new gException('SOME_OWNER','Нельзя передать заказ самому себе');
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# проверяем, нет ли уже такой заявки на перенос
	$Where = Array(
			SPrintF('`UserID` = %u',$Order['UserID']),
			SPrintF('`ToUserID` = %u',$User['ID']),
			SPrintF('`ServiceOrderID` = %u',$ServiceOrderID),
			SPrintF('`ServiceID` = "%s"',$ServiceID),
			'`IsExecuted` = "no"'
			);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('OrdersTransfer',Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	if($Count)
		return new gException('TRANSFER_ALREDY_EXISTS','Задача на передачу заказа уже существует');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IOrdersTransfer = Array(
				#-------------------------------------------------------------------------------
				'CreateDate'	=> Time(),
				'UserID'	=> $Order['UserID'],
				'ServiceID'	=> $ServiceID,
				'ServiceOrderID'=> $ServiceOrderID,
				'ToUserID'	=> $User['ID'],
				'IsExecuted'	=> FALSE
				#-------------------------------------------------------------------------------
				);
	#-------------------------------------------------------------------------------
	$OrdersTransfer = DB_Insert('OrdersTransfer',$IOrdersTransfer);
	if(Is_Error($OrdersTransfer))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# событие, о постановке задачи на перенос
	$Event = Array(
			'UserID'	=> Array($Order['UserID'],$User['ID']),
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Подана заявка на перенос заказа #%u (%s) с аккаунта (%s) на аккаунт (%s)',IsSet($Order['OrderID'])?$Order['OrderID']:$ServiceOrderID,$Service['NameShort'],$Order['Email'],$User['Email'])
	);
	#-------------------------------------------------------------------------------
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Columns = Array(
			'*',
			'(SELECT `NameShort` FROM `Services` WHERE `Services`.`ID` = `OrdersTransfer`.`ServiceID`) AS `Name`',
			'(SELECT `Email` FROM `Users` WHERE `OrdersTransfer`.`UserID` = `Users`.`ID`) AS `EmailFrom`',
			'(SELECT `Email` FROM `Users` WHERE `OrdersTransfer`.`ToUserID` = `Users`.`ID`) AS `EmailTo`'
			);
	$OrdersTransfer = DB_Select('OrdersTransfer',$Columns,Array('UNIQ','ID'=>$OrdersTransferID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($OrdersTransfer)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('TRANSFER_ORDER_NOT_FOUND','Задача на перенос не найдена');
	case 'array':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# проверяем что это его запись
	if($__USER['ID'] != $OrdersTransfer['ToUserID'])
		if(!$__USER['IsAdmin'])
			return ERROR | @Trigger_Error(700);
	#-------------------------------------------------------------------------------
	# проверяем  что она активна
	if($OrdersTransfer['IsExecuted'])
		return new gException('ALREDY_EXECUTED','Задача переноса уже выполнена');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$ContractID){
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$DOM = new DOM();
		#-------------------------------------------------------------------------------
		$Links = &Links();
		# Коллекция ссылок
		$Links['DOM'] = &$DOM;
		#-------------------------------------------------------------------------------
		if(Is_Error($DOM->Load('Base')))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$DOM->AddAttribs('MenuLeft',Array('args'=>'User/Services'));
		#-------------------------------------------------------------------------------
		$DOM->AddText('Title','Перенос услуги');
		#-------------------------------------------------------------------------------
		$Form = new Tag('FORM',Array('name'=>'OrdersTransferForm','onsubmit'=>'return false;'));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Contracts = DB_Select('Contracts',Array('ID','Customer'),Array('Where'=>SPrintF("`UserID` = %u AND `TypeID` != 'NaturalPartner' AND (`StatusID` = 'Complite' OR `StatusID` = 'Public')",$OrdersTransfer['ToUserID'])));
		#-----------------------------------------------------------------------------
		switch(ValueOf($Contracts)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return new gException('CONTRACTS_NOT_FOUND','Система не обнаружила у Вас ни одного договора. Пожалуйста, перейдите в раздел [Мой офис - Договора] и сформируйте хотя бы 1 договор.');
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$Options = Array();
		#-------------------------------------------------------------------------
		foreach($Contracts as $Contract){
			#-----------------------------------------------------------------------
			$Customer = $Contract['Customer'];
			#-----------------------------------------------------------------------
			if(Mb_StrLen($Customer) > 20)
				$Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
			#-----------------------------------------------------------------------
			$Options[$Contract['ID']] = $Customer;
		}
		#-------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Select',Array('name'=>'ContractID'),$Options);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------
		$NoBody = new Tag('NOBODY',$Comp);
		#-------------------------------------------------------------------------
		$Window = JSON_Encode(Array('Url'=>'/API/OrdersTransfer','Args'=>Array()));
		#-------------------------------------------------------------------------
		$A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
		#-------------------------------------------------------------------------
		$NoBody->AddChild($A);
		#-------------------------------------------------------------------------
		$Table = Array(Array('Базовый договор',$NoBody));
		#-------------------------------------------------------------------------
		$Comp = Comp_Load(
				'Form/Input',
				Array(  'type'    => 'button',
					'name'    => 'Submit',
					'onclick' => "FormEdit('/API/OrdersTransfer','OrdersTransferForm','Перенос услуги на выбранный договор');",
					'value'   => 'Перенести'
					)
				);
		#---------------------------------------------------------------------
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#---------------------------------------------------------------------
		$Table[] = $Comp;
		#---------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Standard',$Table);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#---------------------------------------------------------------------
		$Form->AddChild($Comp);
		#---------------------------------------------------------------------
		$Comp = Comp_Load(
				'Form/Input',
				Array(
					'name'  => 'OrdersTransferID',
					'value' => $OrdersTransferID,
					'type'  => 'hidden',
					)
				);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#---------------------------------------------------------------------
		$Form->AddChild($Comp);
		#---------------------------------------------------------------------
		$DOM->AddChild('Into',$Form);
		#---------------------------------------------------------------------
		$Out = $DOM->Build(FALSE);
		#-------------------------------------------------------------------------------
		if(Is_Error($Out))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return Array('Status'=>'Ok','DOM'=>$DOM->Object);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		# последний шаг, собственно перенос
		#-------------------------------------------------------------------------------
		# проверяем договор
		$Contracts = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
		#-----------------------------------------------------------------------------
		switch(ValueOf($Contracts)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return new gException('CONTRACT_NOT_FOUND','Выбранный договор не найден.');
		case 'array':
			#-------------------------------------------------------------------------------
			if($Contracts['UserID'] != $__USER['ID'])
				return ERROR | @Trigger_Error(700);
			#-------------------------------------------------------------------------------
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# достаём сервис
		$Service = DB_Select('ServicesOwners',Array('ID','Code','NameShort'),Array('UNIQ','ID'=>$OrdersTransfer['ServiceID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Service)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return new gException('SERVICE_NOT_FOUND','Указанный сервис не найден');
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# достаём OrderID
		$Order = DB_Select(SPrintF('%sOrdersOwners',($Service['Code'] == 'Default')?'':$Service['Code']),Array('*'),Array('UNIQ','ID'=>$OrdersTransfer['ServiceOrderID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Order)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#---------------------------------------------------------------------------
			$IsPermission = Permission_Check('OrdersTransfer',(integer)$OrdersTransfer['UserID'],(integer)$Order['UserID']);
			#---------------------------------------------------------------------------
			switch(ValueOf($IsPermission)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'false':
				return ERROR | @Trigger_Error(700);
			case 'true':
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# поехали!
		#-----------------------------TRANSACTION-----------------------------
		if(Is_Error(DB_Transaction($TransactionID = UniqID('OrdersTransfer'))))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID),Array('ID'=>IsSet($Order['OrderID'])?$Order['OrderID']:$Order['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		# помечаем как выполненную
		$IsUpdate = DB_Update('OrdersTransfer',Array('IsExecuted'=>TRUE),Array('ID'=>$OrdersTransfer['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# событие, о выполнении переноса
		$Event = Array(
				'UserID'	=> Array($OrdersTransfer['ToUserID'],$OrdersTransfer['UserID']),
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Выполнен перенос заказа #%u (%s) с аккаунта (%s) на аккаунт (%s)',IsSet($Order['OrderID'])?$Order['OrderID']:$Order['ID'],$Service['NameShort'],$OrdersTransfer['EmailFrom'],$OrdersTransfer['EmailTo'])
		);
		#-------------------------------------------------------------------------------
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Commit($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# чистим кэш
		$CacheFlush = Comp_Load('www/CacheFlush');
		if(Is_Error($CacheFlush))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
