<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Comp = Comp_Load('Tables/Widget','Invoices[User]');
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Comp->Attribs['count'])
  return FALSE;
#-------------------------------------------------------------------------------
return Array('Title'=>'Последние счета на оплату','DOM'=>$Comp);
#-------------------------------------------------------------------------------

?>
