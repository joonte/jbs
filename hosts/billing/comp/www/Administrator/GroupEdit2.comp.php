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
$GroupID = (integer) @$Args['GroupID'];
#-------------------------------------------------------------------------------
/*if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);*/

$smarty=$GLOBALS['smarty'];

if($GroupID){
  $Group = DB_Select('Groups','*',Array('UNIQ','ID'=>$GroupID));

  switch(ValueOf($Group)){
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
  $Group = Array(
    #---------------------------------------------------------------------------
    'ParentID'     => 1,
    'Name'         => 'Новая группа',
    'IsDefault'    => FALSE,
    'IsDepartment' => FALSE,
    'Comment'      => 'Очень нужная группа'
  );
}
#-------------------------------------------------------------------------------
$smarty->assign('title', ($GroupID?'Редактирование группы':'Добавление новой группы'));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Groups = DB_Select('Groups','*');
#-------------------------------------------------------------------------------
switch(ValueOf($Groups)){
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
$Options = Array();
#-------------------------------------------------------------------------------
foreach($Groups as $Element)
  $Options[$Element['ID']] = $Element['Name'];
#-------------------------------------------------------------------------------
$smarty->assign('groups', $Options);
$smarty->assign('selectedGroup', $Group['ParentID']);
$smarty->assign('groupName', $Group['Name']);
#-------------------------------------------------------------------------------
if($Group['IsDefault'])
    $smarty->assign('isDefault', 'yes');
#-------------------------------------------------------------------------------
if($Group['IsDepartment'])
    $smarty->assign('isDepartment', 'yes');
#-------------------------------------------------------------------------------
$smarty->assign('comment', $Group['Comment']);

#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'GroupEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($GroupID){
  $smarty->assign('groupId', $GroupID);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'HTML','DOM'=>$smarty->fetch('groupEdit.tpl'));
#-------------------------------------------------------------------------------

?>
