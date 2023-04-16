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
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ExtraIPOrderID){
	#-------------------------------------------------------------------------------
	$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('*'),Array('UNIQ','ID'=>$ExtraIPOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ExtraIPOrder)){
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
	# информация о заказе к которому прицеплен IP, если она есть...
	$DependService = DB_Select(Array('Servers','ServersGroups'),Array('(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServersGroups`.`ServiceID`) AS `Code`', '(SELECT `NameShort` FROM `Services` WHERE `Services`.`ID` = `ServersGroups`.`ServiceID`)'),Array('UNIQ','Where'=>Array('`Servers`.`ServersGroupID` = `ServersGroups`.`ID`',SPrintF('`Servers`.`ID` = %u',$ExtraIPOrder['ServerID']))));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DependService)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		#return new gException('DESTINATION_SERVER_NOT_FOUND','Сервер размещения не найден');
		$ExtraIPOrder['DependOrder'] = "не задано";
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		$ExtraIPDependOrder = DB_Select(SPrintF('%sOrdersOwners',$DependService['Code']),Array('*'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$ExtraIPOrder['DependOrderID'])));
		switch(ValueOf($ExtraIPDependOrder)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			$ExtraIPOrder['DependOrder'] = "не задано";
			break;
		case 'array':
			$ExtraIPOrder['DependOrder'] = (($DependService['Code'] == 'DS')?$ExtraIPDependOrder['IP']:$ExtraIPDependOrder['Login']);
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

}else{
	#-------------------------------------------------------------------------------
	$ExtraIPOrder = Array(
				'UserID'	=> 100,
				'ContractID'	=> 0,
				'Domain'	=> 'domain.com',
				'Login'		=> '0.0.0.0',
				'OrderType'	=> 'VPS',
				'DependOrder'	=> 'v1234',
				'SchemeID'	=> 1,
				'ServerID'	=> 1
				);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Title = ($ExtraIPOrderID?'Редактирование заказа на выделенный IP адрес':'Добавление заказа на выделенный IP адрес');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Contracts/Select','ContractID',$ExtraIPOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор клиента',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UniqID = UniqID('ExtraIPSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','ExtraIPSchemes',$ExtraIPOrder['UserID'],Array('Name'),$UniqID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPSchemes = DB_Select($UniqID,Array('ID','Name','CostMonth','CostInstall'),Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVERS_NOT_FOUND','Тарифы не определены');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($ExtraIPSchemes as $ExtraIPScheme){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$ExtraIPScheme['CostMonth']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Options[$ExtraIPScheme['ID']] = SPrintF('%s, %s',$ExtraIPScheme['Name'],$Comp);
		#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID','style'=>'width: 100%;'),$Options,$ExtraIPOrder['SchemeID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ExtraIPOrderID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'text','size'=>5,'name'=>'DaysReserved','value'=>31));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Дней до окончания',$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsCreate','id'=>'IsCreate','value'=>'yes'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array(new Tag('LABEL',Array('for'=>'IsCreate'),'Добавить IP на сервере'),$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры заказа';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'text','name'=>'Login','value'=>$ExtraIPOrder['Login'],'style'=>'width: 100%;'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('IP адрес',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Address','Params','TemplateID','(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `ServersGroupID`) AS `ServersGroupName`'),Array('Where'=>'`TemplateID` IN ("Hosting","VPS","DS")','SortOn'=>Array('TemplateID','ServersGroupID','Address')));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$Options = $DisabledIDs = Array();
	#-------------------------------------------------------------------------------
	foreach($Servers as $Server){
		#-------------------------------------------------------------------------------
		# имя группы серверов
		if(!IsSet($GroupName) || $GroupName != $Server['ServersGroupName']){
			#-------------------------------------------------------------------------------
			$GroupName = SPrintF('%s / %s',$Server['TemplateID'],$Server['ServersGroupName']);
			#-------------------------------------------------------------------------------
			$DisabledIDs[] = $GroupName;
			#-------------------------------------------------------------------------------
			$Options[$GroupName] = $GroupName;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Options[$Server['ID']] = $Server['Address'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'ServerID','style'=>'width: 100%;'),$Options,$ExtraIPOrder['ServerID'],Array(0));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Сервер размещения',$Comp);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем все услуги юзера
$Comp = Comp_Load('Services/Orders/SelectDependOrder',$ExtraIPOrder['UserID'],$ExtraIPOrder['OrderID'],$ExtraIPOrder['DependOrderID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Заказ к которому прикреплен',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>SPrintF("FormEdit('/Administrator/API/ExtraIPOrderEdit','ExtraIPOrderEditForm','%s');",$Title),'value'=>($ExtraIPOrderID?'Сохранить':'Добавить')));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ExtraIPOrderEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($ExtraIPOrderID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'ExtraIPOrderID','type'=>'hidden','value'=>$ExtraIPOrderID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
