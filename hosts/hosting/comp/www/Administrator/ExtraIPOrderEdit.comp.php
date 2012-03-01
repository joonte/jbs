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
  #-----------------------------------------------------------------------------
  $ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('*'),Array('UNIQ','ID'=>$ExtraIPOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      # Select DependOrder
      $ExtraIPDependOrder = DB_Select($ExtraIPOrder['OrderType'] . 'OrdersOwners',Array('*'),Array('UNIQ','ID'=>$ExtraIPOrder['DependOrderID']));
      switch(ValueOf($ExtraIPOrder)){
      case 'error':
      	return ERROR | @Trigger_Error(500);
      case 'exception':
        $ExtraIPOrder['DependOrder'] = "не задано";
	break;
      case 'array':
        $ExtraIPOrder['DependOrder'] = $ExtraIPDependOrder['Login'];
	break;
      default:
        return ERROR | @Trigger_Error(101);
      }
      # No more...
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $ExtraIPOrder = Array(
    #---------------------------------------------------------------------------
    'UserID'	 => 100,
    'ContractID' => 0,
    'Domain'     => 'domain.com',
    'Login'      => '0.0.0.0',
    'OrderType'  => 'VPS',
    'DependOrder'=> 'v1234',
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ExtraIPOrderEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$ExtraIPOrderID?'Редактирование заказа на виртуальный сервер':'Добавление заказа на виртуальный сервер');
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
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Currency',$ExtraIPScheme['CostMonth']);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Options[$ExtraIPScheme['ID']] = SPrintF('%s, %s',$ExtraIPScheme['Name'],$Comp);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$ExtraIPOrder['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ExtraIPOrderID){
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
  $Table[] = Array('Добавить IP на сервере',$Comp);
}
#-------------------------------------------------------------------------------
$Table[] = 'Параметры заказа';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Login',
    'value' => $ExtraIPOrder['Login']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
$Table[] = Array('IP адрес',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Options = Array(
		'Hosting'	=> 'Хостинг',
		'VPS'		=> 'VPS',
		'DS'		=> 'Выделенный сервер',
		'Manual'	=> 'Без заказа'
		);
$Comp = Comp_Load('Form/Select',Array('name'=>'OrderType'),$Options,$ExtraIPOrder['OrderType']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Тип заказа',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'DependOrder',
    'value' => $ExtraIPOrder['DependOrder']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
$Table[] = Array('Заказ к которому прикреплен',$Comp);



#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'ExtraIPOrderEdit();',
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
$Form = new Tag('FORM',Array('name'=>'ExtraIPOrderEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($ExtraIPOrderID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'ExtraIPOrderID',
      'type'  => 'hidden',
      'value' => $ExtraIPOrderID
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
