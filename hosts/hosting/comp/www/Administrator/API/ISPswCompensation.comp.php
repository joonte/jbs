<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ISPswOrderID	= (integer) @$Args['ISPswOrderID'];
$ISPswSchemeID	= (integer) @$Args['ISPswSchemeID'];
$DaysReserved	= (integer) @$Args['DaysReserved'];
#-------------------------------------------------------------------------------
if(!$DaysReserved)
  return new gException('DAYS_NOT_DEFINED','Кол-во дней компенсации не указано');
#-------------------------------------------------------------------------------
if($ISPswOrderID){
  #-----------------------------------------------------------------------------
  $ISPswOrder = DB_Select('ISPswOrdersOwners','StatusID',Array('UNIQ','ID'=>$ISPswOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ISPswOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('ISPsw_ORDER_NOT_FOUND','Заказ на ПО не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if($ISPswOrder['StatusID'] != 'Active')
        return new gException('ISPsw_ORDER_NOT_ACTIVE','Заказ ПО не активен');
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $Count = DB_Count('ISPswSchemes',Array('ID'=>$ISPswSchemeID));
  if(Is_Error($Count))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Count)
    return new gException('SCHEME_NOT_FOUND','Таифный план не найден');
}
#-------------------------------------------------------------------------------
$ISPswOrders = DB_Select(
		'ISPswOrders',
		Array(
			'ID',
			'OrderID',
			'(SELECT `CostDay` FROM `ISPswSchemes` WHERE `ISPswSchemes`.`ID` = `ISPswOrders`.`SchemeID`) as `CostDay`'
			),
		$ISPswOrderID?Array('ID'=>$ISPswOrderID):Array('Where'=>SPrintF("`SchemeID` = %u AND `StatusID` = 'Active'",$ISPswSchemeID))
		);
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('ISPsw_ORDERS_NOT_FOUND','Нет активных заказов ПО в данной группе');
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ISPswOrders as $ISPswOrder){
      #-------------------------------------------------------------------------
      $IOrdersConsider = Array(
        #-----------------------------------------------------------------------
        'OrderID'        => $ISPswOrder['OrderID'],
        'DaysReserved'   => $DaysReserved,
        'Cost'           => $ISPswOrder['CostDay'],
        'Discont'        => 1
      );
      #-------------------------------------------------------------------------
      $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
      if(Is_Error($OrdersConsiderID))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','Orders'=>Count($ISPswOrders));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
