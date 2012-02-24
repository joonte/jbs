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
$ISPswOrderID = (integer) @$Args['ISPswOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ISPswOrderID){
  #-----------------------------------------------------------------------------
  $ISPswOrder = DB_Select('ISPswOrdersOwners',Array('*'),Array('UNIQ','ID'=>$ISPswOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ISPswOrder)){
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
  $ISPswOrder = Array(
    #---------------------------------------------------------------------------
    'UserID'	=> 100,
    'ContractID'=> 0,
    'IP'        => '0.0.0.0',
    'LicenseID'	=> 0,
    'SchemeID'  => 1
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ISPswOrderEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$ISPswOrderID?'Редактирование заказа на ПО ISPsystem':'Добавление заказа на ПО ISPsystem');
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Contracts/Select','ContractID',$ISPswOrder['ContractID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор клиента',$Comp);
#-------------------------------------------------------------------------------
$UniqID = UniqID('ISPswSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','ISPswSchemes',$ISPswOrder['UserID'],Array('Name'),$UniqID);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ISPswSchemes = DB_Select($UniqID,Array('ID','Name','CostMonth'),Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswSchemes)){
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
foreach($ISPswSchemes as $ISPswScheme){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Currency',$ISPswScheme['CostMonth']);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Options[$ISPswScheme['ID']] = SPrintF('%s, %s',$ISPswScheme['Name'],$Comp);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$ISPswOrder['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
$Servers = DB_Select('ISPswGroups',Array('ID','Address'),Array('SortOn'=>'Address'));
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
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ISPswOrderID){
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
  $Table[] = Array('Создать заказ на сервере',$Comp);
}
#-------------------------------------------------------------------------------
$Table[] = 'Параметры доступа';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'IP',
		'value' => $ISPswOrder['IP'],
		'prompt'=> 'IP адрес на который заказана (будет заказана) лицензия'
		)
	);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('IP лицензии',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'LicenseID',
		'value' => $ISPswOrder['LicenseID'],
		'prompt'=> 'Внутренний идентификатор лицензии. Если лицензии ещё нет, то оставить пустым. (Не elid! Можно посмотреть через редактирование лицензии)'
		)
	);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('ID лицензии',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'ISPswOrderEdit();',
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
$Form = new Tag('FORM',Array('name'=>'ISPswOrderEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($ISPswOrderID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'ISPswOrderID',
      'type'  => 'hidden',
      'value' => $ISPswOrderID
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
