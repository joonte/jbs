<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$VPSBonusID = (integer) @$Args['VPSBonusID'];
#-------------------------------------------------------------------------------
if($VPSBonusID){
  #-----------------------------------------------------------------------------
  $VPSBonus = DB_Select('VPSBonuses','*',Array('UNIQ','ID'=>$VPSBonusID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($VPSBonus)){
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
  $VPSBonus = Array(
     #--------------------------------------------------------------------------
    'UserID'        => 1,
    'SchemeID'      => 0,
    'DaysReserved'  => 30,
    'DaysRemainded' => 30,
    'Discont'       => 0.5,
    'Comment'       => 'Как партнеру'
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/VPSBonusEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($VPSBonusID?'Редактирование бонуса на VPS':'Добавление нового бонуса на VPS');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Users/Select','UserID',$VPSBonus['UserID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Пользователь',$Comp);
#-------------------------------------------------------------------------------
$Columns = Array('ID','Name','(SELECT `Name` FROM `VPSServersGroups` WHERE `VPSServersGroups`.`ID` = `VPSSchemes`.`ServersGroupID`) as `ServersGroupName`');
#-------------------------------------------------------------------------------
$VPSSchemes = DB_Select('VPSSchemes',$Columns,Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('VPS_SCHEMES_NOT_FOUND','Для назначения бонуса необходимо добавить хотя бы один тарифный план на VPS');
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
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$VPSBonus['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'DaysReserved',
    'value' => $VPSBonus['DaysReserved']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Действителен дней',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'DaysRemainded',
    'value' => $VPSBonus['DaysRemainded']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Действителен осталось',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Discont',
    'value' => $VPSBonus['Discont']*100
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Размер скидки в %',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name'  => 'Comment',
    'style' => 'width:100%;',
    'rows'  => 5
  ),
  $VPSBonus['Comment']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = 'Комментарий';
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'VPSBonusEdit();',
    'value'   => ($VPSBonusID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'VPSBonusEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($VPSBonusID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'VPSBonusID',
      'type'  => 'hidden',
      'value' => $VPSBonusID
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
