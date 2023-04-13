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
$ExtraIPSchemeID= (integer) @$Args['ExtraIPSchemeID'];
$StepID		= (integer) @$Args['StepID'];
$DependOrderID	= (integer) @$Args['DependOrderID'];	# номер заказа к которому цепляем IP
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/WhoIs.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$UniqID = UniqID('ExtraIPSchemes');
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
$DOM->AddText('Title','Заказ выделенного IP адреса');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Order.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ExtraIPOrderForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# составляем список серверов на которых можно добавлять IP адреса
$ExtraIPSchemes = DB_Select('ExtraIPSchemes',Array('ID','Params'),Array('Where'=>"`IsActive` = 'yes'"));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('NO_IP_SCHEMES','Нет ни одного тарифа на выделенные IP адреса');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$ServerIDs = Array();
#-------------------------------------------------------------------------------
foreach($ExtraIPSchemes as $ExtraIPScheme)
	foreach($ExtraIPScheme['Params']['Servers'] as $iServerID)
		if(!In_Array($iServerID,$ServerIDs))
			$ServerIDs[] = $iServerID;
#-------------------------------------------------------------------------------
if(!SizeOf($ServerIDs))
	return new gException('NO_SERVERS_FOR_IP_SCHEMES','У существующих тарифных планов не отмечено ни одного сервера на котором можно было бы добавлять IP адреса');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug("[comp/www/ExtraIPOrder]: StepID = $StepID");
#-------------------------------------------------------------------------------
# intermediate step
if(!$StepID){
	#-------------------------------------------------------------------------------
	$Table[] = new Tag('TD',Array('colspan'=>2,'width'=>300,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Необходимо выбрать заказ хостинга, VPS или выделенного сервера, к которому будет прикреплен заказ выделенного IP адреса');
	#-------------------------------------------------------------------------------
	$OrderCount = 0;
	#-------------------------------------------------------------------------------
	// список заказов из которых выбирается
	$Options = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# Where общее для Hosting/VPS/DS
	$Where = Array(
			SPrintF('`UserID` = %u',$__USER['ID']),
			SPrintF('`ServerID` IN (%s)',Implode(',',$ServerIDs)),
			"`StatusID` = 'Active' OR `StatusID` = 'Waiting' OR `StatusID` = 'Suspended'"
			);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# create select, using UserID for HostingOrders
	$Columns = Array('ID','Login','OrderID','(SELECT `Address` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`)) AS `Address`');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$HostingOrders = DB_Select('HostingOrdersOwners',$Columns,Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingOrders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($HostingOrders as $HostingOrder){
			#-------------------------------------------------------------------------------
			$DependOrderID = $HostingOrder['OrderID'];
			#-------------------------------------------------------------------------------
			$Options[$DependOrderID] = SPrintF('Хостинг: %s [%s]',$HostingOrder['Login'],$HostingOrder['Address']);
			#-------------------------------------------------------------------------------
			$OrderCount++;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	//Debug(SPrintF('[comp/www/ExtraIPOrder]: Options = %s',print_r($Options,true)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# create select, using UserID for VPSOrders
	$Columns = Array('ID','Login','OrderID','(SELECT `Address` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`)) AS `Address`');
	#-------------------------------------------------------------------------------
	$VPSOrders = DB_Select('VPSOrdersOwners',$Columns,Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($VPSOrders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($VPSOrders as $VPSOrder){
			#-------------------------------------------------------------------------------
			$DependOrderID = $VPSOrder['OrderID'];
			#-------------------------------------------------------------------------------
			$Options[$DependOrderID] = SPrintF('VPS: %s [%s]',$VPSOrder['Login'],$VPSOrder['Address']);
			#-------------------------------------------------------------------------------
			$OrderCount++;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	//Debug(SPrintF('[comp/www/ExtraIPOrder]: Options = %s',print_r($Options,true)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# create select, using UserID for DSOrders
	$Columns = Array('ID','IP','OrderID','(SELECT `Name` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `SchemeID`) as `Name`');
	#-------------------------------------------------------------------------------
	$DSOrders = DB_Select('DSOrdersOwners',$Columns,Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DSOrders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($DSOrders as $DSOrder){
			#-------------------------------------------------------------------------------
			$DependOrderID = $DSOrder['OrderID'];
			#-------------------------------------------------------------------------------
			$Options[$DependOrderID] = SPrintF('Сервер: %s [%s]',$DSOrder['IP'],$DSOrder['Name']);
			#-------------------------------------------------------------------------------
			$OrderCount++;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	//Debug(SPrintF('[comp/www/ExtraIPOrder]: Options = %s',print_r($Options,true)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($OrderCount < 1)
		return new gException('ExtraIP_OWNER_NOT_HAVE_ORDERS','У вас нет заказанных услуг, или, для этих услуг невозможно заказать IP адреса. Закажите какую-либо услугу: хостинг, VPS, выделенный сервер. После этого, вы сможете заказать для неё дополнительный IP адрес.');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'DependOrderID','style'=>'width: 240px;'),$Options,$DependOrderID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Услуга',$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
				'Form/Input',
				Array(
					'type'		=> 'button',
					'name'		=> 'Submit',
					'onclick'	=> "ShowWindow('/ExtraIPOrder',FormGet(form));",
					'value'		=> 'Продолжить'
				)
			);
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Standard',$Table);
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'StepID','value'=>2,'type'=>'hidden'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Form);
	#-------------------------------------------------------------------------------
}else{ # $StepID 1 -> another
	#-------------------------------------------------------------------------------
	# выбран ли заказ
	if(!$DependOrderID)
		return new gException('ExtraIP_ORDER_NOT_SELECTED','Необходимо выбрать заказ к которому прикрепляется IP адрес');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// достаём код услуги которая была выбрана
	$DependOrder = DB_Select('OrdersOwners',Array('ID','ServerID','ContractID','(SELECT `Code` FROM `Services` WHERE `ID` = `OrdersOwners`.`ServiceID`) AS `Code`'),Array('UNIQ','ID'=>$DependOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DependOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('ORDER_NOT_FOUND',SPrintF('Заказ (#%u) не найден. Обратитесь в службу поддержки пользователей.',$DependOrderID));
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$ServerID = $DependOrder['ServerID'];
	#-------------------------------------------------------------------------------
	$ContractID = $DependOrder['ContractID'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# номер заказа к которому надо прицепить IP адрес
	$Comp = Comp_Load('Form/Input',Array('name'=>'DependOrderID','type'=>'hidden','value'=>$DependOrderID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'ContractID','type'=>'hidden','value'=>$ContractID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Schemes','ExtraIPSchemes',$__USER['ID'],Array('Name'),$UniqID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Columns = Array('ID','Name','Comment','CostMonth','CostInstall','Params');
	#-------------------------------------------------------------------------------
	$ExtraIPSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('SortID'),'Where'=>"`IsActive` = 'yes'"));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ExtraIPSchemes)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('ExtraIP_SCHEMES_NOT_FOUND','Активные тарифы на выделенные IP адреса не найдены. Обратитесь в службу поддержки пользователей.');
	case 'array':
		#-------------------------------------------------------------------------------
		$NoBody = new Tag('NOBODY');
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		#-------------------------------------------------------------------------------
		$Tr->AddChild(new Tag('TD',Array('class'=>'Head','colspan'=>2),'Тариф'));
		#-------------------------------------------------------------------------------
		$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center','style'=>'white-space: nowrap;'),'Цена в месяц'));
		#-------------------------------------------------------------------------------
		$Td = new Tag('TD',Array('class'=>'Head','align'=>'center','style'=>'white-space: nowrap;'),new Tag('SPAN','Цена подключения'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
		#-------------------------------------------------------------------------------
		$LinkID = UniqID('Prompt');
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = &$Td;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Prompt',$LinkID,'Стоимость подключения услуги. Взимается единоразово, при подключении.');
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Tr->AddChild($Td);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		UnSet($Links[$LinkID]);
		#-------------------------------------------------------------------------------
		$Rows = Array($Tr);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		foreach($ExtraIPSchemes as $ExtraIPScheme){
			#-------------------------------------------------------------------------------
			# если сервер заказа не содержится в тарифе на выделенный IP - пропускаем тариф
			if(!In_Array($ServerID,$ExtraIPScheme['Params']['Servers']))
				continue;
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Input',Array('name'=>'ExtraIPSchemeID','type'=>'radio','value'=>$ExtraIPScheme['ID']));
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if($ExtraIPScheme['ID'] == $ExtraIPSchemeID || (!$ExtraIPSchemeID && !IsSet($IsChecked))){
				#-------------------------------------------------------------------------------
				$Comp->AddAttribs(Array('checked'=>'true'));
				#-------------------------------------------------------------------------------
				$IsChecked = TRUE;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Comment = $ExtraIPScheme['Comment'];
			#-------------------------------------------------------------------------------
			if($Comment)
				$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>2)),new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),$Comment));
			#-------------------------------------------------------------------------------
			$CostMonth = Comp_Load('Formats/Currency',$ExtraIPScheme['CostMonth']);
			if(Is_Error($CostMonth))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$CostInstall = Comp_Load('Formats/Currency',$ExtraIPScheme['CostInstall']);
			if(Is_Error($CostInstall))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Rows[] = new Tag(
						'TR',
						Array('OnClick'=>SPrintF('document.forms[\'ExtraIPOrderForm\'].ExtraIPSchemeID.value=%s',$ExtraIPScheme['ID'])),
						new Tag('TD',Array('width'=>20),$Comp),
						new Tag('TD',Array('class'=>'Comment','align'=>'right','style'=>'white-space: nowrap;'),$ExtraIPScheme['Name']),
						new Tag('TD',Array('class'=>'Standard','align'=>'right'),$CostMonth),
						new Tag('TD',Array('class'=>'Standard','align'=>'right'),$CostInstall)
					);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Rows,Array('align'=>'center'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = $Comp;
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'),'');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'Order("ExtraIP");','value'=>'Продолжить'));
	if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Standard',$Table,Array('width'=>400));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Form);
	#-------------------------------------------------------------------------------
}	# end of $StepID is set, and $StepID != 1 or 2
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
