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
$DomainsSchemesGroupID     = (integer) @$Args['DomainsSchemesGroupID'];
$DomainsSchemesGroupItemID = (integer) @$Args['DomainsSchemesGroupItemID'];
#-------------------------------------------------------------------------------
if($DomainsSchemesGroupItemID){
  #-----------------------------------------------------------------------------
  $DomainsSchemesGroupItem = DB_Select('DomainsSchemesGroupsItems','*',Array('UNIQ','ID'=>$DomainsSchemesGroupItemID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DomainsSchemesGroupItem)){
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
  $DomainsSchemesGroupItem = Array(
    #---------------------------------------------------------------------------
    'SchemeID' => 1
  );
}
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Standard')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/DomainsSchemesGroupItemEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($DomainsSchemesGroupItemID?'Редактирование тарифа группы тарифов на домены':'Добавление нового тарифа в группу тарифов на домены');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$DOM->Delete('Title');
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$DomainsSchemes = DB_Select('DomainsSchemes',Array('ID','Name','(SELECT `Name` FROM `Registrators` WHERE `DomainsSchemes`.`RegistratorID` = `Registrators`.`ID`) as `RegistratorName`','CostOrder'),Array('SortOn'=>'Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainsSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Information','Тарифы на домены не найдены.','Notice');
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Comp);
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array();
    #---------------------------------------------------------------------------
    foreach($DomainsSchemes as $DomainScheme){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Currency',$DomainScheme['CostOrder']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Options[$DomainScheme['ID']] = SPrintF('%s, %s, %s',$DomainScheme['Name'],$DomainScheme['RegistratorName'],$Comp);
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$DomainsSchemesGroupItem['SchemeID']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Тарифный план',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'type'    => 'button',
        'onclick' => 'DomainsSchemesGroupItemEdit();',
        'value'   => ($DomainsSchemesGroupItemID?'Сохранить':'Добавить')
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = $Comp;
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form = new Tag('FORM',Array('name'=>'DomainsSchemesGroupItemEditForm','onsubmit'=>'return false;'),$Comp);
    #---------------------------------------------------------------------------
    if($DomainsSchemesGroupID){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'name'  => 'DomainsSchemesGroupID',
          'type'  => 'hidden',
          'value' => $DomainsSchemesGroupID
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
    }
    #---------------------------------------------------------------------------
    if($DomainsSchemesGroupItemID){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'name'  => 'DomainsSchemesGroupItemID',
          'type'  => 'hidden',
          'value' => $DomainsSchemesGroupItemID
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
    }
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Form);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
