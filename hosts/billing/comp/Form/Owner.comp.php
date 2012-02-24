<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Title','GroupID','UserID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5),new Tag('CAPTION',$Title));
#-------------------------------------------------------------------------------
$Groups = DB_Select('Groups',Array('ID','Name'));
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
foreach($Groups as $Group)
  $Options[$Group['ID']] = $Group['Name'];
#-------------------------------------------------------------------------------
$IsChecked = ($GroupID != 4000000);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'GroupID'),$Options,$GroupID);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$IsChecked)
  $Comp->AddAttribs(Array('disabled'=>TRUE));
#-------------------------------------------------------------------------------
$Checkbox = Comp_Load('Form/Input',Array('type'=>'checkbox','onclick'=>'form.GroupID.disabled = !checked;if(!checked) { form.GroupID.value = 4000000; }'));
if(Is_Error($Checkbox))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($IsChecked)
  $Checkbox->AddAttribs(Array('checked'=>TRUE));
#-------------------------------------------------------------------------------
$Table->AddChild(new Tag('TR',new Tag('TD',$Checkbox),new Tag('TD','Группа'),new Tag('TD',$Comp)));
#-------------------------------------------------------------------------------
$UniqID = UniqID('ID');
#-------------------------------------------------------------------------------
$IsChecked = ($UserID != 1);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Users/Select','UserID',$UserID,$UniqID,!$IsChecked);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Checkbox = Comp_Load('Form/Input',Array('type'=>'checkbox','onclick'=>SPrintF("form['%s'].disabled = !checked;if(!checked) { form.UserID.value = 1; }",$UniqID)));
if(Is_Error($Checkbox))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($IsChecked)
  $Checkbox->AddAttribs(Array('checked'=>TRUE));
#-------------------------------------------------------------------------------
$Table->AddChild(new Tag('TR',new Tag('TD',$Checkbox),new Tag('TD','Пользователь'),new Tag('TD',$Comp)));
#-------------------------------------------------------------------------------
return $Table;
#-------------------------------------------------------------------------------

?>
