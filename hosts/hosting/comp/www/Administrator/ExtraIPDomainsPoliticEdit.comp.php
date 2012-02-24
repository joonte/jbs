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
$ExtraIPDomainsPoliticID = (integer) @$Args['ExtraIPDomainsPoliticID'];
#-------------------------------------------------------------------------------
if($ExtraIPDomainsPoliticID){
  #-----------------------------------------------------------------------------
  $ExtraIPDomainsPolitic = DB_Select('ExtraIPDomainsPolitics','*',Array('UNIQ','ID'=>$ExtraIPDomainsPoliticID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPDomainsPolitic)){
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
  $ExtraIPDomainsPolitic = Array(
     #--------------------------------------------------------------------------
    'GroupID'               => 1,
    'UserID'                => 1,
    'SchemeID'              => 0,
    'DomainsSchemesGroupID' => 0,
    'DaysPay'               => 30,
    'Discont'               => 0.5
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ExtraIPDomainsPoliticEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($ExtraIPDomainsPoliticID?'Редактирование ценовой политики на домены':'Добавление ценовой политики на домены');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец политики',$ExtraIPDomainsPolitic['GroupID'],$ExtraIPDomainsPolitic['UserID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Columns = Array('ID','Name','(SELECT `Name` FROM `ExtraIPsGroups` WHERE `ExtraIPsGroups`.`ID` = `ExtraIPSchemes`.`sGroupID`) as `sGroupName`');
#-------------------------------------------------------------------------------
$ExtraIPSchemes = DB_Select('ExtraIPSchemes',$Columns);
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACTS_NOT_FOUND','Для назначения политики необходимо добавить хотя бы один тарифный план на ExtraIP');
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array('Все тарифы');
    #---------------------------------------------------------------------------
    foreach($ExtraIPSchemes as $ExtraIPScheme)
      $Options[$ExtraIPScheme['ID']] = SPrintF('%s (%s)',$ExtraIPScheme['Name'],$ExtraIPScheme['sGroupName']);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$ExtraIPDomainsPolitic['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
$DomainsSchemesGroups = DB_Select('DomainsSchemesGroups',Array('ID','Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainsSchemesGroups)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DOMAINS_SCHEMES_GROUPS_NOT_FOUND','Группы тарифов на домены не найдены');
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array();
    #---------------------------------------------------------------------------
    foreach($DomainsSchemesGroups as $DomainsSchemesGroup)
      $Options[$DomainsSchemesGroup['ID']] = $DomainsSchemesGroup['Name'];
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'DomainsSchemesGroupID'),$Options,$ExtraIPDomainsPolitic['DomainsSchemesGroupID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа тарифов на домены',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'DaysPay',
    'value' => $ExtraIPDomainsPolitic['DaysPay']
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
    'value' => $ExtraIPDomainsPolitic['Discont']*100
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
    'onclick' => 'ExtraIPDomainsPoliticEdit();',
    'value'   => ($ExtraIPDomainsPoliticID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'ExtraIPDomainsPoliticEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($ExtraIPDomainsPoliticID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'ExtraIPDomainsPoliticID',
      'type'  => 'hidden',
      'value' => $ExtraIPDomainsPoliticID
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
