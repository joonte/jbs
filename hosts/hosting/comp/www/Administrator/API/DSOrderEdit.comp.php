<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
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
$DSOrderID 	= (integer) @$Args['DSOrderID'];
$ContractID     = (integer) @$Args['ContractID'];
$SchemeID       = (integer) @$Args['SchemeID'];
$DaysReserved   = (integer) @$Args['DaysReserved'];
$IsCreate       = (boolean) @$Args['IsCreate'];
$IP		=  (string) @$Args['IP'];
$ExtraIP	=  (string) @$Args['ExtraIP'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DSScheme = DB_Select('DSSchemes',Array('ID','ServersGroupID','CostDay'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Тарифный план не найден');
  break;
  case 'array':
    #---------------------------------------------------------------------------
#    if($DSScheme['ServersGroupID'] != $Server['ServersGroupID'])
#      return new gException('SERVERS_GROUP_NOT_EQUAL','Группа серверов сервера размещения и тарифного плана не совпадают');
    #---------------------------------------------------------------------------
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!$ContractID)
  return new gException('CONTRACT_NOT_FILLED','Договор клиента не указан');
#-------------------------------------------------------------------------------
$IDSOrder = Array(
	#-----------------------------------------------------------------------------
	'IP'		=> $IP,
	'ExtraIP'	=> $ExtraIP,
	'SchemeID'	=> $DSScheme['ID']
);
#-------------------------------------------------------------------------------
if($DSOrderID){
  #-----------------------------------------------------------------------------
  $DSOrder = DB_Select('DSOrders','OrderID',Array('UNIQ','ID'=>$DSOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DSOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('DS_ORDER_NOT_FOUND','Заказ на DS не найден');
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID),Array('ID'=>$DSOrder['OrderID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('DSOrders',$IDSOrder,Array('ID'=>$DSOrderID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServiceID'=>40000,'IsPayed'=>TRUE));
  if(Is_Error($OrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IDSOrder['OrderID'] = $OrderID;
  #-----------------------------------------------------------------------------
  $DSOrderID = DB_Insert('DSOrders',$IDSOrder);
  if(Is_Error($DSOrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$DSScheme['CostDay']);
  #-----------------------------------------------------------------------------
  $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
  if(Is_Error($OrdersConsiderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$DSOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ на DS успешно добавлен'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Comp)){
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
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
