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
$ContractID		=  (string) @$Args['ContractID'];
$DNSmanagerSchemeID	= (integer) @$Args['DNSmanagerSchemeID'];
$StepID			= (integer) @$Args['StepID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/WhoIs.php')))
	return ERROR | @Trigger_Error(500);
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
$DOM->AddText('Title','Заказ вторичного DNS');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Order.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'DNSmanagerOrderForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if($StepID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'ContractID','type'=>'hidden','value'=>$ContractID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$DNSmanagerSchemeID)
		return new gException('DNSMANAGER_SCHEME_NOT_DEFINED','Тарифный план не выбран');
	#-------------------------------------------------------------------------------
	$DNSmanagerScheme = DB_Select('DNSmanagerSchemes',Array('*'),Array('UNIQ','ID'=>$DNSmanagerSchemeID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DNSmanagerScheme)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$DNSmanagerScheme['IsActive'])
		return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план вторичного DNS не активен');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$DNSmanagerSchemeID)
		return new gException('HOSTING_SCHEME_NOT_DEFINED','Тарифный план не выбран');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'DNSmanagerSchemeID','type'=>'hidden','value'=>$DNSmanagerSchemeID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table = Array('Дополнительные параметры заказа');
	#-------------------------------------------------------------------------------
	$Table[] = Array('Тарифный план',$DNSmanagerScheme['Name']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Server = DB_Select('Servers',Array('ID','Params','IsActive'),Array('UNIQ','ID'=>$DNSmanagerScheme['HardServerID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Server)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SERVERS_NOT_FOUND','Серверы для вторичного DNS не настроены');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($DNSmanagerScheme['Reseller'] || $Server['Params']['DefaultView'] == $DNSmanagerScheme['ViewArea']){
		#-------------------------------------------------------------------------------
		$DOM->AddAttribs('Body',Array('onload'=>"Order('DNSmanager');"));
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('type'=>'text','style'=>'width: 100%;','name'=>'ViewArea','value'=>'','prompt'=>'Введите область, в которой будут размещаться ваши домены. Обычно, она именуется по имени DNS сервера. Например: dns0.example.ru'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Область (view)',$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'		=> 'button',
				'onclick'	=> SPrintF("ShowWindow('/DNSmanagerOrder',{DNSmanagerSchemeID:%u});",$DNSmanagerScheme['ID']),
				'value'		=> 'Изменить тариф'
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>"Order('DNSmanager');",'value'=>'Продолжить'));
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
		$Out = $DOM->Build(FALSE);
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok','DOM'=>$DOM->Object);














	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table = Array(Array('Тарифный план',$DNSmanagerScheme['Name']));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',	Array('name'=>'DNSmanagerSchemeID','type'=>'hidden','value'=>$DNSmanagerScheme['ID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'StepID','value'=>2,'type'=>'hidden'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	/*
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'		=> 'button',
				'onclick'	=> SPrintF("ShowWindow('/DNSmanagerOrder',{DNSmanagerSchemeID:%u});",$DNSmanagerScheme['ID']),
				'value'		=> 'Изменить домен'
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
	*/
	$Div = new Tag('DIV',Array('align'=>'right'));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>"ShowWindow('/DNSmanagerOrder',FormGet(form));",'value'=>'Продолжить'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Standard',$Table,Array('width'=>400));
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Form);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$__USER = $GLOBALS['__USER'];
	#-------------------------------------------------------------------------------
	$Contracts = DB_Select('Contracts',Array('ID','Customer'),Array('Where'=>SPrintF("`UserID` = %u AND `TypeID` != 'NaturalPartner'",$__USER['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Contracts)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('CONTRACTS_NOT_FOUND','Система не обнаружила у Вас ни одного договора. Пожалуйста, перейдите в раздел [Мой офис - Договоры] и сформируйте хотя бы 1 договор.');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Options = Array();
	#-------------------------------------------------------------------------------
	foreach($Contracts as $Contract){
		#-------------------------------------------------------------------------------
		$Customer = $Contract['Customer'];
		#-------------------------------------------------------------------------------
		$Number = Comp_Load('Formats/Contract/Number',$Contract['ID']);
		if(Is_Error($Number))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(Mb_StrLen($Customer) > 20)
			$Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
		#-------------------------------------------------------------------------------
		$Options[$Contract['ID']] = SPrintF('#%s / %s',$Number,$Customer);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'ContractID'),$Options,$ContractID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$NoBody = new Tag('NOBODY',$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Window = JSON_Encode(Array('Url'=>'/DNSmanagerOrder','Args'=>Array()));
	#-------------------------------------------------------------------------------
	$A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
	#-------------------------------------------------------------------------------
	$NoBody->AddChild($A);
	#-------------------------------------------------------------------------------
	$Table = Array(Array('Базовый договор',$NoBody));
	#-------------------------------------------------------------------------------
	$UniqID = UniqID('DNSmanagerSchemes');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Schemes','DNSmanagerSchemes',$__USER['ID'],Array('Name','ServersGroupID'),$UniqID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Columns = Array(
			'ID','Name','ServersGroupID','Comment','CostMonth','DomainLimit',
			'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupName`',
			'(SELECT `Comment` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupComment`',
			'(SELECT `SortID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupSortID`'
			);
	#-------------------------------------------------------------------------------
	$DNSmanagerSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('ServersGroupSortID','SortID'),'Where'=>"`IsActive` = 'yes'"));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DNSmanagerSchemes)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_SCHEMES_NOT_FOUND','Тарифные планы на вторичный DNS не определены');
	case 'array':
		#-------------------------------------------------------------------------------
		$NoBody = new Tag('NOBODY');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		#-------------------------------------------------------------------------------
		$Tr->AddChild(new Tag('TD',Array('class'=>'Head','colspan'=>2),'Тариф'));
		$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),'Цена в мес.'));
		#-------------------------------------------------------------------------------
		$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','Место'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
		#-------------------------------------------------------------------------------
		$LinkID = UniqID('Prompt');
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = &$Td;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','Доменов'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = &$Td;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Prompt',$LinkID,'Кол-во доменов которые можно разместить на вторичном DNS сервере');
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
		$ServersGroupName = UniqID();
		#-------------------------------------------------------------------------------
		foreach($DNSmanagerSchemes as $DNSmanagerScheme){
			#-------------------------------------------------------------------------------
			if($ServersGroupName != $DNSmanagerScheme['ServersGroupName']){
				#-------------------------------------------------------------------------------
				$ServersGroupName = $DNSmanagerScheme['ServersGroupName'];
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Formats/String',$DNSmanagerScheme['ServersGroupComment'],75);
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>7,'class'=>'Separator'),new Tag('SPAN',Array('style'=>'font-size:16px;'),SPrintF('%s |',$ServersGroupName)),new Tag('SPAN',Array('style'=>'font-size:11px;'),$Comp)));
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Input',Array('name'=>'DNSmanagerSchemeID','type'=>'radio','value'=>$DNSmanagerScheme['ID']));
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if($DNSmanagerScheme['ID'] == $DNSmanagerSchemeID)
				$Comp->AddAttribs(Array('checked'=>'true'));
			#-------------------------------------------------------------------------------
			$Comment = $DNSmanagerScheme['Comment'];
			#-------------------------------------------------------------------------------
			if($Comment)
				$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>2)),new Tag('TD',Array('colspan'=>5,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),$Comment));
			#-------------------------------------------------------------------------------
			$CostMonth = Comp_Load('Formats/Currency',$DNSmanagerScheme['CostMonth']);
			if(Is_Error($CostMonth))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Rows[] = new Tag('TR',Array('OnClick'=>SPrintF('document.forms[\'DNSmanagerOrderForm\'].DNSmanagerSchemeID.value=%s',$DNSmanagerScheme['ID'])),new Tag('TD',Array('width'=>20),$Comp),new Tag('TD',Array('class'=>'Comment',),$DNSmanagerScheme['Name']),new Tag('TD',Array('class'=>'Standard','align'=>'right'),$CostMonth),new Tag('TD',Array('class'=>'Standard','align'=>'right'),$DNSmanagerScheme['DomainLimit']));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Rows,Array('align'=>'center'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = $Comp;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('type'=>'button','name'=>'Submit','onclick'=>"ShowWindow('/DNSmanagerOrder',FormGet(form));",'value'=>'Продолжить'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = $Comp;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Standard',$Table);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'StepID','value'=>1,'type'=>'hidden'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$DOM->AddChild('Into',$Form);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
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
