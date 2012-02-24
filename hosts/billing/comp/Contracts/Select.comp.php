<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Name','ContractID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$UniqID = UniqID('ID');
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'    => $UniqID,
    'onfocus' => "value='';",
    'onclick' => SPrintF("AutoComplite(this,GetPosition(this),'/Administrator/AutoComplite/ContractID',function(Text,Value){form.%s.value = Text;form.%s.value = Value;});",$UniqID,$Name),
    'type'    => 'text'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ContractID){
  #-----------------------------------------------------------------------------
  $Contract = DB_Select('Contracts',Array('ID','Customer'),Array('UNIQ','ID'=>$ContractID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Contract)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      $Comp->AddAttribs(Array('value'=>$Contract['Customer']));
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$NoBody->AddChild($Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => $Name,
    'type'  => 'hidden',
    'value' => $ContractID
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody->AddChild($Comp);
#-------------------------------------------------------------------------------
return $NoBody;
#-------------------------------------------------------------------------------

?>
