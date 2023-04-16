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
$DNSmanagerOrderID = (integer) @$Args['DNSmanagerOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($DNSmanagerOrderID){
	#-------------------------------------------------------------------------------
	$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',Array('UserID','ContractID','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `ServerID`','Login','Password','SchemeID','DependOrderID','OrderID'),Array('UNIQ','ID'=>$DNSmanagerOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DNSmanagerOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Password = Comp_Load('Passwords/Generator');
	if(Is_Error($Password))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$DNSmanagerOrder = Array(
				'UserID'	=> 100,
				'ContractID'	=> 0,
				'ServerID'	=> 1,
				'Login'		=> 'login',
				'Password'	=> $Password,
				'SchemeID'	=> 1,
				'DependOrderID'	=> $DependOrderID,
			);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
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
$Title = ($DNSmanagerOrderID?'Редактирование заказа на DNSmanager':'Добавление заказа на DNSmanager');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Contracts/Select','ContractID',$DNSmanagerOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор клиента',$Comp);
#-------------------------------------------------------------------------------
$UniqID = UniqID('DNSmanagerSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','DNSmanagerSchemes',$DNSmanagerOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DNSmanagerSchemes = DB_Select($UniqID,Array('ID','Name','CostMonth',SPrintF('(SELECT `Name` FROM `ServersGroups` WHERE `%s`.`ServersGroupID` = `ServersGroups`.`ID`) as `ServersGroupName`',$UniqID)),Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerSchemes)){
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
foreach($DNSmanagerSchemes as $DNSmanagerScheme){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$DNSmanagerScheme['CostMonth']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Options[$DNSmanagerScheme['ID']] = SPrintF('%s, %s, %s',$DNSmanagerScheme['Name'],$DNSmanagerScheme['ServersGroupName'],$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID','style'=>'width: 100%;'),$Options,$DNSmanagerOrder['SchemeID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Address'),Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) = 52000','SortOn'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVERS_NOT_FOUND','Сервера не найдены');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
foreach($Servers as $Server)
	$Options[$Server['ID']] = $Server['Address'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'ServerID','style'=>'width: 100%;'),$Options,$DNSmanagerOrder['ServerID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервер размещения',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем все услуги юзера
$Comp = Comp_Load('Services/Orders/SelectDependOrder',$DNSmanagerOrder['UserID'],$DNSmanagerOrder['OrderID'],$DNSmanagerOrder['DependOrderID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Заказ',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DNSmanagerOrderID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'text','size'=>5,'name'=>'DaysReserved','value'=>31));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Дней до окончания',$Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsCreate','value'=>'yes'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsCreate\'); return false;'),'Создать заказ на сервере'),$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры доступа';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'text','name'=>'Login','value'=>$DNSmanagerOrder['Login']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Логин на сервере',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'text','name'=>'Password','value'=>$DNSmanagerOrder['Password']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Пароль от аккаунта',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick' => SPrintF("FormEdit('/Administrator/API/DNSmanagerOrderEdit','DNSmanagerOrderEditForm','%s');",$Title),'value'=>($DNSmanagerOrderID?'Сохранить':'Добавить')));
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
$Form = new Tag('FORM',Array('name'=>'DNSmanagerOrderEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DNSmanagerOrderID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'DNSmanagerOrderID','type'=>'hidden','value'=>$DNSmanagerOrderID));
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
