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
$ContractID	=  (string) @$Args['ContractID'];
$VPSSchemeID	= (integer) @$Args['VPSSchemeID'];
$StepID		= (integer) @$Args['StepID'];
$DiskTemplate	=  (string) @$Args['DiskTemplate'];
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
$DOM->AddText('Title','Заказ виртуального сервера');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Order.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'VPSOrderForm','onsubmit'=>'return false;'));
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
	$Regulars = Regulars();
	#-------------------------------------------------------------------------------
	if(!$VPSSchemeID)
		return new gException('VPS_SCHEME_NOT_DEFINED','Тарифный план не выбран');
	#-------------------------------------------------------------------------------
	$VPSScheme = DB_Select('VPSSchemes',Array('ID','Name','IsActive'),Array('UNIQ','ID'=>$VPSSchemeID));
	#-----------------------------------------------------------------------------
	switch(ValueOf($VPSScheme)){
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
	if(!$VPSScheme['IsActive'])
		return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа VPS не активен');
	#-------------------------------------------------------------------------------
	$Table = Array(Array('Тарифный план',$VPSScheme['Name']));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'VPSSchemeID','type'=>'hidden','value'=>$VPSScheme['ID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$VPSScheme = DB_Select('VPSSchemes',Array('ID','Name','ServersGroupID','IsActive'),Array('UNIQ','ID'=>$VPSSchemeID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($VPSScheme)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа виртуального сервера не найден');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Server = DB_Select('Servers',Array('ID','Params'),Array('UNIQ','Where'=>SPrintF("`ServersGroupID` = %u AND `IsDefault` = 'yes'",$VPSScheme['ServersGroupID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Server)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SERVER_NOT_DEFINED','Сервер размещения не определён');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'ServerID','type'=>'hidden','value'=>$Server['ID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/www/VPSOrder]: DiskTemplate = %s',print_r(Explode("\n",$Server['Params']['DiskTemplate']),true)));
	$Array = Array();
	#-------------------------------------------------------------------------------
	foreach(Explode("\n",$Server['Params']['DiskTemplate']) as $Line){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/VPSOrder]: Line = %s',$Line));
		$Template = Explode('=',$Line);
		#-------------------------------------------------------------------------------
		$Array[$Template[0]] = IsSet($Template[1])?$Template[1]:$Template[0];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	//Debug(SPrintF('[comp/www/VPSOrder]: Array = %s',print_r($Array,true)));
	//Debug(SPrintF('[comp/www/VPSOrder]: ASort Array = %s',print_r(ASort($Array),true)));
	ASort($Array);
	$Comp = Comp_Load('Form/Select',Array('name'=>'DiskTemplate','style'=>'width: 100%;'),$Array,$DiskTemplate);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Шаблон диска',$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Rows = Array();
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>SPrintF("ShowWindow('/VPSOrder',{VPSSchemeID:%u,DiskTemplate:document.forms.VPSOrderForm.DiskTemplate.value,ServerID:%u});",$VPSScheme['ID'],$Server['ID']),'value'=>'Изменить тариф'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'Order("VPS");','value'=>'Продолжить'));
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
}else{
	#-------------------------------------------------------------------------------
	$__USER = $GLOBALS['__USER'];
	#-------------------------------------------------------------------------------
	$Contracts = DB_Select('Contracts',Array('ID','Customer'),Array('Where'=>SPrintF("`UserID` = %u AND `TypeID` != 'NaturalPartner' AND `IsHidden` = 'no'",$__USER['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Contracts)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('CONTRACTS_NOT_FOUND','Система не обнаружила у Вас ни одного активного договора. Пожалуйста, перейдите в раздел [Мой офис - Договоры] и сформируйте/активируйте хотя бы один договор.');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
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
	$Window = JSON_Encode(Array('Url'=>'/VPSOrder','Args'=>Array()));
	#-------------------------------------------------------------------------------
	$A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
	#-------------------------------------------------------------------------------
	$NoBody->AddChild($A);
	#-------------------------------------------------------------------------------
	$Table = Array(Array('Базовый договор',$NoBody));
	#-------------------------------------------------------------------------------
	$UniqID = UniqID('VPSSchemes');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Schemes','VPSSchemes',$__USER['ID'],Array('Name','ServersGroupID'),$UniqID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Columns = Array('ID','Name','ServersGroupID','Comment','CostMonth','CostInstall','SchemeParams','(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupName`','(SELECT `Comment` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupComment`','(SELECT `SortID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) as `ServersGroupSortID`');
	#-------------------------------------------------------------------------------
	$VPSSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('ServersGroupSortID','SortID'),'Where'=>"`IsActive` = 'yes'"));
	#-------------------------------------------------------------------------------
	switch(ValueOf($VPSSchemes)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('VPS_SCHEMES_NOT_FOUND','Тарифные планы на виртуальные сервера не определены');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$NoBody = new Tag('NOBODY');
	#-------------------------------------------------------------------------------
	$Tr = new Tag('TR');
	#-------------------------------------------------------------------------------
	$Tr->AddChild(new Tag('TD',Array('class'=>'Head','colspan'=>2),'Тариф'));
	$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),'Цена в мес.'));
	$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),'Цена установки'));
	#-------------------------------------------------------------------------------
	$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','Место'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
	#-------------------------------------------------------------------------------
	$LinkID = UniqID('Prompt');
	#-------------------------------------------------------------------------------
	$Links[$LinkID] = &$Td;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Prompt',$LinkID,'Размер диска');
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Tr->AddChild($Td);
	#-------------------------------------------------------------------------------
	$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','Проц.'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
	#-------------------------------------------------------------------------------
	$Links[$LinkID] = &$Td;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Prompt',$LinkID,'Число процессоров');
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Tr->AddChild($Td);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Td = new Tag('TD',Array('class'=>'Head','align'=>'center'),new Tag('SPAN','RAM'),new Tag('SPAN',Array('style'=>'font-weight:bold;font-size:14px;'),'?'));
	#-------------------------------------------------------------------------------
	$Links[$LinkID] = &$Td;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Prompt',$LinkID,'Количество оперативной памяти');
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
	foreach($VPSSchemes as $VPSScheme){
		#-------------------------------------------------------------------------------
		if($ServersGroupName != $VPSScheme['ServersGroupName']){
			#-------------------------------------------------------------------------------
			$ServersGroupName = $VPSScheme['ServersGroupName'];
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Formats/String',$VPSScheme['ServersGroupComment'],75);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>8,'class'=>'Separator'),new Tag('SPAN',Array('style'=>'font-size:16px;'),SPrintF('%s |',$ServersGroupName)),new Tag('SPAN',Array('style'=>'font-size:11px;'),$Comp)));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'VPSSchemeID','type'=>'radio','value'=>$VPSScheme['ID']));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if($VPSScheme['ID'] == $VPSSchemeID || (!$VPSSchemeID && !IsSet($IsChecked))){
			#-------------------------------------------------------------------------------
			$Comp->AddAttribs(Array('checked'=>'true'));
			#-------------------------------------------------------------------------------
			$IsChecked = TRUE;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comment = $VPSScheme['Comment'];
		#-------------------------------------------------------------------------------
		if($Comment)
			$Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>2)),new Tag('TD',Array('colspan'=>6,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),$Comment));
		#-------------------------------------------------------------------------------
		$CostMonth = Comp_Load('Formats/Currency',$VPSScheme['CostMonth']);
		if(Is_Error($CostMonth))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$CostInstall = Comp_Load('Formats/Currency',$VPSScheme['CostInstall']);
		if(Is_Error($CostMonth))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Rows[] = new Tag('TR',
					Array('OnClick'=>SPrintF('document.forms[\'VPSOrderForm\'].VPSSchemeID.value=%s',$VPSScheme['ID'])),
					new Tag('TD',Array('width'=>20),$Comp),
					new Tag('TD',Array('class'=>'Comment'),$VPSScheme['Name']),
					new Tag('TD',Array('class'=>'Standard','align'=>'right'),$CostMonth),
					new Tag('TD',Array('class'=>'Standard','align'=>'right'),$CostInstall),
					new Tag('TD',Array('class'=>'Standard','align'=>'right'),SPrintF('%u Мб.',$VPSScheme['SchemeParams']['InternalName']['HDD'])),
					new Tag('TD',Array('class'=>'Standard','align'=>'right'),$VPSScheme['SchemeParams']['InternalName']['CPU']),
					new Tag('TD',Array('class'=>'Standard','align'=>'right'),$VPSScheme['SchemeParams']['InternalName']['RAM'])
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
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','name'=>'Submit','onclick'=>"ShowWindow('/VPSOrder',FormGet(form));",'value'=>'Продолжить'));
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
	$Comp = Comp_Load('Form/Input',Array('name'=>'DiskTemplate','value'=>$DiskTemplate,'type'=>'hidden'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Form);
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
