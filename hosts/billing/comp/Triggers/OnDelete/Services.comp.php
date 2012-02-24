<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Service');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if($Service['IsProtected'])
  return new gException('SERVICE_IS_PROTECTED',SPrintF('Услуга (%s) защищена и не может быть удалена',$Service['Name']));
#-------------------------------------------------------------------------------
$Count = DB_Count('Orders',Array('Where'=>SPrintF('`ServiceID` = %u',$Service['ID'])));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
  return new gException('SERVICE_ORDERS_EXISTS',SPrintF('Услуга (%s) не может быть удалена, т.к. на нее существуют заказы',$Service['Name']));
#-------------------------------------------------------------------------------
$Count = DB_Count('InvoicesItems',Array('Where'=>SPrintF('`ServiceID` = %u',$Service['ID'])));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
  return new gException('SERVICE_INVOICES_EXISTS',SPrintF('Услуга (%s) не может быть удалена, т.к. на были выписаны счета',$Service['Name']));
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
