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
$ExtraIPPoliticID = (integer) @$Args['ExtraIPPoliticID'];
#-------------------------------------------------------------------------------
if($ExtraIPPoliticID){
  #-----------------------------------------------------------------------------
  $ExtraIPPolitic = DB_Select('ExtraIPPolitics','*',Array('UNIQ','ID'=>$ExtraIPPoliticID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPPolitic)){
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
  $ExtraIPPolitic = Array(
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ExtraIPPoliticEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($ExtraIPPoliticID?'Редактирование ценовой политики на IP адреса':'Добавление ценовой политики на IP адреса');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец политики',$ExtraIPPolitic['GroupID'],$ExtraIPPolitic['UserID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Columns = Array('ID','Name');
#-------------------------------------------------------------------------------
$ExtraIPSchemes = DB_Select('ExtraIPSchemes',$Columns,Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACTS_NOT_FOUND','Для назначения политики необходимо добавить хотя бы один тарифный план на IP адреса');
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array('Все тарифы');
    #---------------------------------------------------------------------------
    foreach($ExtraIPSchemes as $ExtraIPScheme)
      $Options[$ExtraIPScheme['ID']] = $ExtraIPScheme['Name'];
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$ExtraIPPolitic['SchemeID']);
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
    'value' => $ExtraIPPolitic['DaysPay']
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
    'value' => $ExtraIPPolitic['Discont']*100
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
    'onclick' => 'ExtraIPPoliticEdit();',
    'value'   => ($ExtraIPPoliticID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'ExtraIPPoliticEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($ExtraIPPoliticID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'ExtraIPPoliticID',
      'type'  => 'hidden',
      'value' => $ExtraIPPoliticID
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
