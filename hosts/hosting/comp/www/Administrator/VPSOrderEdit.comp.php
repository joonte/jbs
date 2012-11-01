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
$VPSOrderID = (integer) @$Args['VPSOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSOrderID){
  #-----------------------------------------------------------------------------
  $VPSOrder = DB_Select('VPSOrdersOwners',Array('UserID','ContractID','ServerID','Domain','Login','Password','SchemeID'),Array('UNIQ','ID'=>$VPSOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($VPSOrder)){
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
}else{
  #-----------------------------------------------------------------------------
  $VPSOrder = Array(
    #---------------------------------------------------------------------------
    'UserID' => 100,
    'ContractID' => 0,
    'ServerID'   => 1,
    'Domain'     => 'domain.com',
    'Login'      => 'login',
    'Password'   => UniqID(),
    'SchemeID'   => 1
  );
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
$Title = ($VPSOrderID?'Редактирование заказа на виртуальный сервер':'Добавление заказа на виртуальный сервер');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Contracts/Select','ContractID',$VPSOrder['ContractID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор клиента',$Comp);
#-------------------------------------------------------------------------------
$UniqID = UniqID('VPSSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','VPSSchemes',$VPSOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$VPSSchemes = DB_Select($UniqID,Array('ID','Name','CostMonth',SPrintF('(SELECT `Name` FROM `VPSServersGroups` WHERE `%s`.`ServersGroupID` = `VPSServersGroups`.`ID`) as `ServersGroupName`',$UniqID)),Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSSchemes)){
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
foreach($VPSSchemes as $VPSScheme){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Currency',$VPSScheme['CostMonth']);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Options[$VPSScheme['ID']] = SPrintF('%s, %s, %s',$VPSScheme['Name'],$VPSScheme['ServersGroupName'],$Comp);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$VPSOrder['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
$Servers = DB_Select('VPSServers',Array('ID','Address'),Array('SortOn'=>'Address'));
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
$Comp = Comp_Load('Form/Select',Array('name'=>'ServerID'),$Options,$VPSOrder['ServerID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервер размещения',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Domain',
    'value' => $VPSOrder['Domain']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменное имя',$Comp);
#-------------------------------------------------------------------------------
if(!$VPSOrderID){
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
  $Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsCreate','value'=>'yes'));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsCreate\'); return false;'),'Создать заказ на сервере'),$Comp);
}
#-------------------------------------------------------------------------------
$Table[] = 'Параметры доступа';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Login',
    'value' => $VPSOrder['Login']
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
    'value' => $VPSOrder['Password']
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
    'onclick' => SPrintF("FormEdit('/Administrator/API/VPSOrderEdit','VPSOrderEditForm','%s');",$Title),
    'value'   => 'Сохранить'
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
$Form = new Tag('FORM',Array('name'=>'VPSOrderEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($VPSOrderID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'VPSOrderID',
      'type'  => 'hidden',
      'value' => $VPSOrderID
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
