<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$VPSPoliticID = (integer) @$Args['VPSPoliticID'];
#-------------------------------------------------------------------------------
if($VPSPoliticID){
  #-----------------------------------------------------------------------------
  $VPSPolitic = DB_Select('VPSPolitics','*',Array('UNIQ','ID'=>$VPSPoliticID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($VPSPolitic)){
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
  $VPSPolitic = Array(
     #--------------------------------------------------------------------------
    'GroupID'  => 1,
    'UserID'   => 1,
    'SchemeID' => 0,
    'DaysPay'  => 30,
    'Discont'  => 0.5
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
$Title = ($VPSPoliticID?'Редактирование ценовой политики на VPS':'Добавление ценовой политики на VPS');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец политики',$VPSPolitic['GroupID'],$VPSPolitic['UserID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Columns = Array('ID','Name','(SELECT `Name` FROM `VPSServersGroups` WHERE `VPSServersGroups`.`ID` = `VPSSchemes`.`ServersGroupID`) as `ServersGroupName`');
#-------------------------------------------------------------------------------
$VPSSchemes = DB_Select('VPSSchemes',$Columns,Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACTS_NOT_FOUND','Для назначения политики необходимо добавить хотя бы один тарифный план VPS');
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array('Все тарифы');
    #---------------------------------------------------------------------------
    foreach($VPSSchemes as $VPSScheme)
      $Options[$VPSScheme['ID']] = SPrintF('%s (%s)',$VPSScheme['Name'],$VPSScheme['ServersGroupName']);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$VPSPolitic['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'DaysPay',
    'value' => $VPSPolitic['DaysPay']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дней оплаты',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Discont',
    'value' => $VPSPolitic['Discont']*100
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Размер скидки в %',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/VPSPoliticEdit','VPSPoliticEditForm','%s');",$Title),
    'value'   => ($VPSPoliticID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'VPSPoliticEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($VPSPoliticID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'VPSPoliticID',
      'type'  => 'hidden',
      'value' => $VPSPoliticID
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
