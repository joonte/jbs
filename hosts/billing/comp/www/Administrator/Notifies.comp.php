<?
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
$Method = (string) @$Args['Method'];
#-------------------------------------------------------------------------------
if(!$Method){
    $Method = "Email";
}
$smarty  = JSmarty::get();

$Where = SPrintF("`Partition` LIKE '/Notifies/%s/%%'", $Method);

$clauses = DB_Select('Clauses', '*', Array('Where'=>$Where));
switch(ValueOf($clauses)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    //return ERROR | @Trigger_Error(400);
    break;
  case 'array':
    $smarty->assign('clauses', $clauses);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}

$Config = Config();

$Notifies = $Config['Notifies'];

$methods = Array();
foreach (Array_Keys($Notifies['Methods']) as $methodID) {
  $methods[] = $methodID;
}

$smarty->assign('methods', $methods);

return $smarty->display('notifies.tpl');
#-------------------------------------------------------------------------------
?>