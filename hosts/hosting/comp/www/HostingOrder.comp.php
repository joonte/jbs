<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContractID	=  (string) @$Args['ContractID'];
$HostingSchemeID= (integer) @$Args['HostingSchemeID'];
$Domain		=  (string) @$Args['Domain'];
$IsNoDomain	= (boolean) @$Args['IsNoDomain'];
$StepID		= (integer) @$Args['StepID'];
$DomainTypeID	=  (string) @$Args['DomainTypeID'];
$DomainName	=  (string) @$Args['DomainName'];
$DomainSchemeID	= (integer) @$Args['DomainSchemeID'];
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
$DOM->AddText('Title','Заказ хостинга');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Order.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'HostingOrderForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if($StepID){
	#-------------------------------------------------------------------------------
	if(!$HostingSchemeID)
		return new gException('HOSTING_SCHEME_NOT_DEFINED','Тарифный план не выбран');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'ContractID','type'=>'hidden','value'=>$ContractID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$HostingSchemeID)
		return new gException('HOSTING_SCHEME_NOT_DEFINED','Тарифный план не выбран');
	#-------------------------------------------------------------------------------
	$HostingScheme = DB_Select('HostingSchemes',Array('ID','Name','IsActive','HardServerID'),Array('UNIQ','ID'=>$HostingSchemeID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingScheme)){
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
	if(!$HostingScheme['IsActive'])
		return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа хостинга не активен');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($StepID == 2 || !$Domain){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'HostingSchemeID','type'=>'hidden','value'=>$HostingSchemeID));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'Domain','type'=>'hidden','value'=>$Domain));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'IsNoDomain','type'=>'hidden','value'=>$IsNoDomain));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'hidden','value'=>$DomainTypeID));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'DomainName','type'=>'hidden','value'=>$DomainName));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'DomainSchemeID','type'=>'hidden','value'=>$DomainSchemeID));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Table = Array('Дополнительные параметры заказа');
		$Table[] = Array('Тарифный план',$HostingScheme['Name']);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Доменное имя',($Domain)?$Domain:'без домена');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Servers = DB_Select('Servers',Array('ID','Params'),Array('Where'=>SPrintF('`ServersGroupID` = (SELECT `ServersGroupID` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = %u) AND `IsActive` = "yes"',$HostingSchemeID),'SortOn'=>'Address'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Servers)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return new gException('SERVERS_NOT_FOUND','Серверы на хостинг не настроены');
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$Array = Array();
		#-------------------------------------------------------------------------------
		foreach($Servers as $Server)
			if($Server['Params']['ServerAttrib'])
				if(!IsSet($Array[$Server['Params']['ServerAttrib']]))
					$Array[$Server['Params']['ServerAttrib']] = $Server['Params']['ServerAttrib'];
		#-------------------------------------------------------------------------------
		if(SizeOf($Array) < 2 || IntVal($HostingScheme['HardServerID']) > 0){
			#-------------------------------------------------------------------------------
			$DOM->AddAttribs('Body',Array('onload'=>'Order("Hosting");'));
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			ASort($Array);
			#-------------------------------------------------------------------------------
			$Options = Array('0'=>'Всё равно') + $Array;
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Select',Array('name'=>'ServerAttrib','style'=>'width: 100%;'),$Options);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Table[] = Array('Дополнительный параметр',$Comp);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load(
				'Form/Input',
				Array(
					'type'		=> 'button',
					'onclick'	=> SPrintF("ShowWindow('/HostingOrder',{HostingSchemeID:%u,Domain:'%s'});",$HostingScheme['ID'],$Domain),
					'value'		=> 'Изменить домен/тариф'
					)
				);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>"Order('Hosting');",'value'=>'Продолжить'));
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


	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Regulars = Regulars();
	#-------------------------------------------------------------------------------
	$Domain = Mb_StrToLower($Domain,'UTF-8');
	#-------------------------------------------------------------------------------
	if(Preg_Match('/^www\.(.+)$/',$Domain,$Mathces))
		$Domain = Next($Mathces);
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['Domain'],$Domain))
		return new gException('WRONG_DOMAIN','Неверный домен');

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table = Array(Array('Тарифный план',$HostingScheme['Name']));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',	Array('name'=>'HostingSchemeID','type'=>'hidden','value'=>$HostingScheme['ID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',	Array('name'=>'Domain','type'=>'hidden','value'=>$Domain));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Rows = Array();
	#-------------------------------------------------------------------------------
	$Parse = WhoIs_Parse($Domain);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Parse)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'false':
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'hidden','value'=>'Nothing'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Table[] = new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'border:1px solid #F07D00;'),SPrintF('Доменная зона вашего имени [%s] не поддерживается нашей организацией, домен просто будет связан с заказом хостинга',$Domain));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		$IsCheck = WhoIs_Check($DomainName = $Parse['DomainName'],$DomainZone = $Parse['DomainZone']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsCheck)){
		case 'error':
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'hidden','value'=>'Nothing'));
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Form->AddChild($Comp);
			#-------------------------------------------------------------------------------
			$Table[] = new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'border:1px solid #F07D00;'),SPrintF('Ошибка определения доступности домена [%s], домен просто будет связан с заказом хостинга',$Domain));
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'exception':
			return $IsCheck;
		case 'false':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			$Rows[] = Array(new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'border:1px solid #F07D00;'),SPrintF('Выбранное Вами доменное [%s] имя занято:',$Domain)));
			#-------------------------------------------------------------------------------
			$Radio1 = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'radio','value'=>'Transfer'));
			if(Is_Error($Radio1))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Radio2 = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'radio','value'=>'Nothing'));
			if(Is_Error($Radio2))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$UniqID = UniqID('DomainSchemes');
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Services/Schemes','DomainSchemes',$GLOBALS['__USER']['ID'],Array('Name','ServerID'),$UniqID);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$DomainScheme = DB_Select($UniqID,'ID',Array('Where'=>SPrintF("`Name` = '%s' AND `IsTransfer` = 'yes'",$DomainZone)));
			#-------------------------------------------------------------------------------
			switch(ValueOf($DomainScheme)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$Radio1->AddAttribs(Array('disabled'=>'true'));
				#-------------------------------------------------------------------------------
				$Radio2->AddAttribs(Array('checked'=>'true'));
				#-------------------------------------------------------------------------------
				$Rows[] = Array(new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'border:1px solid #F07D00;'),'К сожалению наша организация не осуществляет поддержку доменов в указанной Вами зоне'));
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'array':
				#-------------------------------------------------------------------------------
				$Radio1->AddAttribs(Array('checked'=>'true'));
				#-------------------------------------------------------------------------------
				$DomainScheme = Current($DomainScheme);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Form/Input',	Array('name'=>'DomainName','type'=>'hidden','value'=>$DomainName));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Form->AddChild($Comp);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Form/Input',Array('name'=>'DomainSchemeID','type'=>'hidden','value'=>$DomainScheme['ID']));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Form->AddChild($Comp);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			$Rows[] = Array(new Tag('TD',$Radio1),new Tag('TD',Array('class'=>'Standard'),'Я являюсь владельцем данного доменного имени, я хочу связать домен с заказом хостинга и перенести домен на поддержку в Вашу организацию'));
			#-------------------------------------------------------------------------------
			$Rows[] = Array(new Tag('TD',$Radio2),new Tag('TD',Array('class'=>'Standard'),'Я являюсь владельцем данного доменного имени и хочу связать домен с заказом хостинга, поддержку домена осуществляет другая организация'));
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Tables/Extended',$Rows);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Table[] = $Comp;
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'true':
			#-------------------------------------------------------------------------------
			$Rows[] = Array(new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'border:1px solid #B9F00A;'),SPrintF('Выбранное Вами доменное [%s] имя свободно:',$Domain)));
			#-------------------------------------------------------------------------------
			$Radio1 = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'radio','value'=>'Order'));
			if(Is_Error($Radio1))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Radio2 = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'radio','value'=>'Nothing'));
			if(Is_Error($Radio2))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$UniqID = UniqID('DomainSchemes');
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Services/Schemes','DomainSchemes',$GLOBALS['__USER']['ID'],Array('Name','ServerID'),$UniqID);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$DomainScheme = DB_Select($UniqID,'ID',Array('Where'=>SPrintF("`Name` = '%s' AND `IsActive` = 'yes'",$DomainZone)));
			#-------------------------------------------------------------------------------
			switch(ValueOf($DomainScheme)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$Radio1->AddAttribs(Array('disabled'=>'true'));
				#-------------------------------------------------------------------------------
				$Radio2->AddAttribs(Array('checked'=>'true'));
				#-------------------------------------------------------------------------------
				$Rows[] = Array(new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'border:1px solid #F07D00;'),'К сожалению наша организация не осуществляет регистрацию доменов в указанной Вами зоне'));
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'array':
				#-------------------------------------------------------------------------------
				$Radio1->AddAttribs(Array('checked'=>'true'));
				#-------------------------------------------------------------------------------
				$DomainScheme = Current($DomainScheme);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Form/Input',Array('name'=>'DomainName','type'=>'hidden','value'=>$DomainName));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Form->AddChild($Comp);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Form/Input',Array('name'=>'DomainSchemeID','type'=>'hidden','value'=>$DomainScheme['ID']));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Form->AddChild($Comp);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			$Rows[] = Array(new Tag('TD',$Radio1),new Tag('TD',Array('class'=>'Standard'),'Я хочу зарегистрировать выбранное доменное имя в вашей организации и связать его с данным заказом хостинга'));
			#-------------------------------------------------------------------------------
			$Rows[] = Array(new Tag('TD',$Radio2),new Tag('TD',Array('class'=>'Standard'),'Я хочу связать выбранное доменное имя с заказом хостинга, само имя я зарегистрирую в другой организации'));
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Tables/Extended',$Rows);
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
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'StepID','value'=>2,'type'=>'hidden'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'		=> 'button',
				'onclick'	=> SPrintF("ShowWindow('/HostingOrder',{HostingSchemeID:%u,Domain:'%s'});",$HostingScheme['ID'],$Domain),
				'value'		=> 'Изменить домен'
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>"ShowWindow('/HostingOrder',FormGet(form));",'value'=>'Продолжить'));
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
	$Window = JSON_Encode(Array('Url'=>'/HostingOrder','Args'=>Array()));
	#-------------------------------------------------------------------------------
	$A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
	#-------------------------------------------------------------------------------
	$NoBody->AddChild($A);
	#-------------------------------------------------------------------------------
	$Table = Array(Array('Базовый договор',$NoBody));
	#-------------------------------------------------------------------------------
	$UniqID = UniqID('HostingSchemes');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Schemes','HostingSchemes',$__USER['ID'],Array('Name','ServersGroupID'),$UniqID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Columns = Array(
			'ID','Name','ServersGroupID','Comment','CostMonth','QuotaDisk','QuotaEmail','QuotaDomains','QuotaDBs',
			'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupName`',
			'(SELECT `Comment` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupComment`',
			'(SELECT `SortID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupSortID`'
			);
	#-------------------------------------------------------------------------------
	$HostingSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('ServersGroupSortID','SortID'),'Where'=>"`IsActive` = 'yes'"));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingSchemes)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_SCHEMES_NOT_FOUND','Тарифные планы на хостинг не определены');
	case 'array':
		#-------------------------------------------------------------------------------
		$NoBody = new Tag('NOBODY');
		#-------------------------------------------------------------------------------
		if($Config['Hosting']['IsNoDomain']){
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Input',Array('name'=>'IsNoDomain','type'=>'checkbox','onclick'=>"form.Domain.disabled = checked;form.StepID.value = document.getElementsByName('IsNoDomain')[0].checked?2:1;"));
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$OnClick = "ChangeCheckBox('IsNoDomain'); document.getElementsByName('Domain')[0].disabled = document.getElementsByName('IsNoDomain')[0].checked?true:false; document.getElementsByName('StepID')[0].value = document.getElementsByName('IsNoDomain')[0].checked?2:1;";
			#-------------------------------------------------------------------------------
			$NoBody->AddChild(new Tag('DIV',Array('style'=>'margin-bottom:5px;'),$Comp,new Tag('SPAN',Array('style'=>'font-size:10px; cursor:pointer;','onclick'=>$OnClick),'Заказать хостинг без домена')));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'Domain','size'=>25,'type'=>'text','value'=>$Domain));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$NoBody->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',	Array('type'=>'button','name'=>'Submit1','onclick'=>"ShowWindow('/HostingOrder',FormGet(form));",'value'=>'Продолжить'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$NoBody->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Доменное имя',$NoBody);
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
		$Comp = Comp_Load('Form/Prompt',$LinkID,'Кол-во места на аккаунте');
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Tr->AddChild($Td);
		#-------------------------------------------------------------------------------
		$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','Почты'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = &$Td;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Prompt',$LinkID,'Кол-во почтовых ящиков');
		#-------------------------------------------------------------------------------
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Tr->AddChild($Td);
		#-------------------------------------------------------------------------------
		$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','Доменов'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = &$Td;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Prompt',$LinkID,'Кол-во дополнительных доменов');
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Tr->AddChild($Td);
		#-------------------------------------------------------------------------------
		$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','Баз'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = &$Td;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Prompt',$LinkID,'Кол-во баз данных');
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Tr->AddChild($Td);
		#-------------------------------------------------------------------------------
		UnSet($Links[$LinkID]);
		#-------------------------------------------------------------------------------
		$Rows = Array($Tr);
		#-------------------------------------------------------------------------------
		$ServersGroupName = UniqID();
		#-------------------------------------------------------------------------------
		foreach($HostingSchemes as $HostingScheme){
			#-------------------------------------------------------------------------------
			if($ServersGroupName != $HostingScheme['ServersGroupName']){
				#-------------------------------------------------------------------------------
				$ServersGroupName = $HostingScheme['ServersGroupName'];
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Formats/String',$HostingScheme['ServersGroupComment'],75);
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>7,'class'=>'Separator'),new Tag('SPAN',Array('style'=>'font-size:16px;'),SPrintF('%s |',$ServersGroupName)),new Tag('SPAN',Array('style'=>'font-size:11px;'),$Comp)));
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Input',Array('name'=>'HostingSchemeID','type'=>'radio','value'=>$HostingScheme['ID']));
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if($HostingScheme['ID'] == $HostingSchemeID)
				$Comp->AddAttribs(Array('checked'=>'true'));
			#-------------------------------------------------------------------------------
			$Comment = $HostingScheme['Comment'];
			#-------------------------------------------------------------------------------
			if($Comment)
				$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>2)),new Tag('TD',Array('colspan'=>5,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),$Comment));
			#-------------------------------------------------------------------------------
			$CostMonth = Comp_Load('Formats/Currency',$HostingScheme['CostMonth']);
			if(Is_Error($CostMonth))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Rows[] = new Tag('TR',new Tag('TD',Array('width'=>20),$Comp),new Tag('TD',Array('class'=>'Comment'),$HostingScheme['Name']),new Tag('TD',Array('class'=>'Standard','align'=>'right'),$CostMonth),new Tag('TD',Array('class'=>'Standard','align'=>'right'),SPrintF('%u Мб.',$HostingScheme['QuotaDisk'])),new Tag('TD',Array('class'=>'Standard','align'=>'right'),$HostingScheme['QuotaEmail']),new Tag('TD',Array('class'=>'Standard','align'=>'right'),$HostingScheme['QuotaDomains']),new Tag('TD',Array('class'=>'Standard','align'=>'right'),$HostingScheme['QuotaDBs']));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Rows,Array('align'=>'center'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = $Comp;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('type'=>'button','name'=>'Submit','onclick'=>"ShowWindow('/HostingOrder',FormGet(form));",'value'=>'Продолжить'));
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
		$Comp = Comp_Load('Form/Input',Array('name'=>'DomainTypeID','type'=>'hidden','value'=>'None'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
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
