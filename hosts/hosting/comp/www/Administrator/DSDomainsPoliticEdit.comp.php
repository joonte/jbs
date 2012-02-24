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
$DSDomainsPoliticID = (integer) @$Args['DSDomainsPoliticID'];
#-------------------------------------------------------------------------------
if($DSDomainsPoliticID){
  #-----------------------------------------------------------------------------
  $DSDomainsPolitic = DB_Select('DSDomainsPolitics','*',Array('UNIQ','ID'=>$DSDomainsPoliticID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DSDomainsPolitic)){
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
  $DSDomainsPolitic = Array(
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/DSDomainsPoliticEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($DSDomainsPoliticID?'Редактирование ценовой политики на домены':'Добавление ценовой политики на домены');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец политики',$DSDomainsPolitic['GroupID'],$DSDomainsPolitic['UserID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Columns = Array('ID','Name','(SELECT `Name` FROM `DSServersGroups` WHERE `DSServersGroups`.`ID` = `DSSchemes`.`ServersGroupID`) as `ServersGroupName`');
#-------------------------------------------------------------------------------
$DSSchemes = DB_Select('DSSchemes',$Columns);
#-------------------------------------------------------------------------------
switch(ValueOf($DSSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACTS_NOT_FOUND','Для назначения политики необходимо добавить хотя бы один тарифный план на DS');
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array('Все тарифы');
    #---------------------------------------------------------------------------
    foreach($DSSchemes as $DSScheme)
      $Options[$DSScheme['ID']] = SPrintF('%s (%s)',$DSScheme['Name'],$DSScheme['ServersGroupName']);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$DSDomainsPolitic['SchemeID']);
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
$Comp = Comp_Load('Form/Select',Array('name'=>'DomainsSchemesGroupID'),$Options,$DSDomainsPolitic['DomainsSchemesGroupID']);
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
    'value' => $DSDomainsPolitic['DaysPay']
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
    'value' => $DSDomainsPolitic['Discont']*100
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
    'onclick' => 'DSDomainsPoliticEdit();',
    'value'   => ($DSDomainsPoliticID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'DSDomainsPoliticEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($DSDomainsPoliticID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'DSDomainsPoliticID',
      'type'  => 'hidden',
      'value' => $DSDomainsPoliticID
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
