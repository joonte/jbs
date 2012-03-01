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
$DomainBonusID = (integer) @$Args['DomainBonusID'];
#-------------------------------------------------------------------------------
if($DomainBonusID){
  #-----------------------------------------------------------------------------
  $DomainBonus = DB_Select('DomainsBonuses','*',Array('UNIQ','ID'=>$DomainBonusID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DomainBonus)){
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
  $DomainBonus = Array(
    #---------------------------------------------------------------------------
    'UserID'                => 1,
    'SchemeID'              => 0,
    'DomainsSchemesGroupID' => 0,
    'YearsReserved'         => 5,
    'YearsRemainded'        => 5,
    'Discont'               => 0.5,
    'Comment'               => 'Как партнеру'
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/DomainBonusEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($DomainBonusID?'Редактирование бонуса на домен':'Добавление нового бонуса на домен');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Users/Select','UserID',$DomainBonus['UserID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Пользователь',$Comp);
#-------------------------------------------------------------------------------
$Options = Array('Не указан');
#-------------------------------------------------------------------------------
$DomainsSchemes = DB_Select('DomainsSchemes',Array('ID','Name','(SELECT `Name` FROM `Registrators` WHERE `DomainsSchemes`.`RegistratorID` = `Registrators`.`ID`) as `RegistratorName`','CostOrder'),Array('SortOn'=>'Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainsSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
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
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$DomainBonus['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
$Options = Array('Не указана');
#-------------------------------------------------------------------------------
$DomainsSchemesGroups = DB_Select('DomainsSchemesGroups',Array('ID','Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainsSchemesGroups)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DomainsSchemesGroups as $DomainsSchemesGroup)
      $Options[$DomainsSchemesGroup['ID']] = $DomainsSchemesGroup['Name'];
    #---------------------------------------------------------------------------
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'DomainsSchemesGroupID'),$Options,$DomainBonus['DomainsSchemesGroupID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа тарифов на домены',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'YearsReserved',
    'value' => $DomainBonus['YearsReserved']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Действителен лет',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'YearsRemainded',
    'value' => $DomainBonus['YearsRemainded']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Лет осталось',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Discont',
    'value' => $DomainBonus['Discont']*100
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
  $DomainBonus['Comment']
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
    'onclick' => 'DomainBonusEdit();',
    'value'   => ($DomainBonusID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'DomainBonusEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($DomainBonusID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'DomainBonusID',
      'type'  => 'hidden',
      'value' => $DomainBonusID
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
