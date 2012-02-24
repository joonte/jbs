<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$OrderID	= (integer) @$Args['OrderID'];
$OrdersConsider	=   (array) @$Args['OrdersConsider'];
#-------------------------------------------------------------------------------
#Debug("[comp/www/Administrator/API/OrderConsider]: " . print_r($OrdersConsider,true));
#--------------------------------TRANSACTION------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('OrderConsider'))))
  return ERROR | @Trigger_Error(500);
#-----------------------------------------------------------------------------
$IsDelete = DB_Delete('OrdersConsider',Array('Where'=>SPrintF('`OrderID` = %u',$OrderID)));
if(Is_Error($IsDelete))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
foreach($OrdersConsider as $ConsiderItem){
  #-----------------------------------------------------------------------------
  if(Count($ConsiderItem) < 5)
    return ERROR | @Trigger_Error(201);
  #-----------------------------------------------------------------------------
  if(Implode(':',$ConsiderItem) != '-:-:-:-:-'){
    #---------------------------------------------------------------------------
    $IOrdersConsider = Array_Combine(Array('DaysReserved','DaysRemainded','DaysConsidered','Cost','Discont'),$ConsiderItem);
    #---------------------------------------------------------------------------
    $IOrdersConsider['OrderID'] = $OrderID;
    #---------------------------------------------------------------------------
    $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
    if(Is_Error($OrdersConsiderID))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('OrdersConsider',Array('DaysRemainded'=>$IOrdersConsider['DaysRemainded'],'DaysConsidered'=>$IOrdersConsider['DaysConsidered']),Array('ID'=>$OrdersConsiderID));
    if(Is_Error($IsUpdate))
      return ERROR | @Trigger_Error(500);
  }
}
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
