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
$HostingOrderID = (integer) @$Args['HostingOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingOrderID){
	#-------------------------------------------------------------------------------
	$HostingOrder = DB_Select('HostingOrdersOwners',Array('UserID','ContractID','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`) AS `ServerID`','Domain','Login','Password','SchemeID','DependOrderID','OrderID'),Array('UNIQ','ID'=>$HostingOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingOrder)){
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
	$HostingOrder = Array(
				'UserID'	=> 100,
				'ContractID'	=> 0,
				'ServerID'	=> 1,
				'Domain'	=> 'example.su',
				'Login'		=> 'login',
				'Password'	=> $Password,
				'SchemeID'	=> 1,
				'DependOrderID'	=> 0,
				'OrderID'	=> 0
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
$Title = ($HostingOrderID?'Редактирование заказа на хостинг':'Добавление заказа на хостинг');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Contracts/Select','ContractID',$HostingOrder['ContractID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор клиента',$Comp);
#-------------------------------------------------------------------------------
$UniqID = UniqID('HostingSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','HostingSchemes',$HostingOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingSchemes = DB_Select($UniqID,Array('ID','Name','CostMonth',SPrintF('(SELECT `Name` FROM `ServersGroups` WHERE `%s`.`ServersGroupID` = `ServersGroups`.`ID`) as `ServersGroupName`',$UniqID)),Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingSchemes)){
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
foreach($HostingSchemes as $HostingScheme){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$HostingScheme['CostMonth']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Options[$HostingScheme['ID']] = SPrintF('%s, %s, %s',$HostingScheme['Name'],$HostingScheme['ServersGroupName'],$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID','style'=>'width: 100%;'),$Options,$HostingOrder['SchemeID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Address'),Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) = 10000','SortOn'=>'Address'));
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
$Comp = Comp_Load('Form/Select',Array('name'=>'ServerID','style'=>'width: 100%;'),$Options,$HostingOrder['ServerID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервер размещения',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Domain',
    'value' => $HostingOrder['Domain'],
    'style'=>'width: 100%;'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменное имя',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем все услуги юзера
$Comp = Comp_Load('Services/Orders/SelectDependOrder',$HostingOrder['UserID'],$HostingOrder['OrderID'],$HostingOrder['DependOrderID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Заказ',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$HostingOrderID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'type'  => 'text',
      'size'  => 5,
      'name'  => 'DaysReserved',
      'value' => 31
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = Array('Дней до окончания',$Comp);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsCreate','id'=>'IsCreate','value'=>'yes'));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = Array(new Tag('LABEL',Array('for'=>'IsCreate'),'Создать заказ на сервере'),$Comp);
}
#-------------------------------------------------------------------------------
$Table[] = 'Параметры доступа';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Login',
    'value' => $HostingOrder['Login']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Логин на сервере',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Password',
    'value' => $HostingOrder['Password']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Пароль от аккаунта',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/HostingOrderEdit','HostingOrderEditForm','%s');",$Title),
    'value'   => ($HostingOrderID?'Сохранить':'Добавить')
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'HostingOrderEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($HostingOrderID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'HostingOrderID',
      'type'  => 'hidden',
      'value' => $HostingOrderID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
