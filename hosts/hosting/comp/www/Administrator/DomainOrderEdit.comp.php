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
$DomainOrderID = (integer) @$Args['DomainOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners',Array('UserID','ContractID','DomainName','SchemeID','ExpirationDate','PersonID','Ns1Name','Ns1IP','Ns2Name','Ns2IP','Ns3Name','Ns3IP','Name'),Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
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
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/FormEdit.js}')));
#-------------------------------------------------------------------------------
$Title = SPrintF('Редактирование заказа на домен %s.%s',$DomainOrder['DomainName'],$DomainOrder['Name']);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общие параметры');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Contracts/Select','ContractID',$DomainOrder['ContractID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор клиента',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'DomainName',
    'type'  => 'text',
    'value' => $DomainOrder['DomainName']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменное имя',$Comp);
#-------------------------------------------------------------------------------
$UniqID = UniqID('DomainsSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','DomainsSchemes',$DomainOrder['UserID'],Array('Name','RegistratorID'),$UniqID);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','Name',SPrintF('(SELECT `Name` FROM `Registrators` WHERE `Registrators`.`ID` = `%s`.`RegistratorID`) as `RegistatorName`',$UniqID),'CostOrder');
#-------------------------------------------------------------------------------
$DomainsSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>'Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainsSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  break;
  case 'array':
    # No more...
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
foreach($DomainsSchemes as $DomainScheme){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Currency',$DomainScheme['CostOrder']);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Options[$DomainScheme['ID']] = SPrintF('%s, %s, %s',$DomainScheme['Name'],$DomainScheme['RegistatorName'],$Comp);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID'),$Options,$DomainOrder['SchemeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('jQuery/DatePicker','ExpirationDate',$DomainOrder['ExpirationDate']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата окончания',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'PersonID',
    'type'  => 'text',
    'value' => $DomainOrder['PersonID']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор регистратора',$Comp);
#-------------------------------------------------------------------------------
$Messages = Messages();
#-------------------------------------------------------------------------------
$Table[] = 'Первичный сервер имен';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'    => 'Ns1Name',
    'size'    => 15,
    'type'    => 'text',
    'prompt'  => $Messages['Prompts']['Domain']['NsName'],
    'onkeyup' => 'IsNewNs();',
    'value'   => $DomainOrder['Ns1Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменный адрес',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'    => 'Ns1IP',
    'size'    => 15,
    'prompt'  => $Messages['Prompts']['IP'],
    'type'    => 'text',
    'value'   => $DomainOrder['Ns1IP']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('IP адрес',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Вторичный сервер имен';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'    => 'Ns2Name',
    'size'    => 15,
    'type'    => 'text',
    'prompt'  => $Messages['Prompts']['Domain']['NsName'],
    'onkeyup' => 'IsNewNs();',
    'value'   => $DomainOrder['Ns2Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменный адрес',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'    => 'Ns2IP',
    'size'    => 15,
    'prompt'  => $Messages['Prompts']['IP'],
    'type'    => 'text',
    'value'   => $DomainOrder['Ns2IP']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('IP адрес',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Дополнительный сервер имен';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'    => 'Ns3Name',
    'size'    => 15,
    'type'    => 'text',
    'prompt'  => $Messages['Prompts']['Domain']['NsName'],
    'onkeyup' => 'IsNewNs();',
    'value'   => $DomainOrder['Ns3Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменный адрес',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'    => 'Ns3IP',
    'size'    => 15,
    'prompt'  => $Messages['Prompts']['IP'],
    'type'    => 'text',
    'value'   => $DomainOrder['Ns3IP']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('IP адрес',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/DomainOrderEdit','DomainOrderEditForm','%s');",$Title),
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
$Form = new Tag('FORM',Array('name'=>'DomainOrderEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'DomainOrderID',
    'type'  => 'hidden',
    'value' => $DomainOrderID
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
