<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ISPswGroup');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# TODO пофиксить запрос после утсаканивания набора столбцов и таблиц
$Count = DB_Count('ISPswOrders',Array('Where'=>SPrintF('`aaaa` = %u',$ISPswGroup['ID'])));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
  return new gException('DELETE_DENIED',SPrintF('Удаление группы (%s) не возможно, %u заказ(ов) связаны с этой группой',$ISPswGroup['GroupName'],$Count));
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
