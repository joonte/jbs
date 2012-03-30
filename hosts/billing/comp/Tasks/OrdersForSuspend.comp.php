<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Columns = Array(
		'ID',
		'IsAutoProlong',
		'UserID',
		'(SELECT `IsProlong` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `IsProlong`',
		'(SELECT `Item` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Item`',
		);
$Where = "(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) = 'Default' AND (SELECT `ConsiderTypeID` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) != 'Upon' AND `StatusID` = 'Active' AND `ExpirationDate` - UNIX_TIMESTAMP() <= 0";
#-------------------------------------------------------------------------------
$Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = SPrintF('Handled %n orders',SizeOf($Orders));
    #---------------------------------------------------------------------------
    foreach($Orders as $Order){
      if($Order['IsAutoProlong'] && $Order['IsProlong']){
        # включено автопродление, и, для этой услуги разрешено продление
	$ServiceOrderPay = Comp_Load('www/API/ServiceOrderPay',Array('ServiceOrderID'=>$Order['ID'],'AmountPay'=>1,'IsNoBasket'=>TRUE,'PayMessage'=>'Автоматическое продление заказа, оплата с баланса договора'));
	#-----------------------------------------------------------------
	switch(ValueOf($ServiceOrderPay)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------
		$Event = Array(
				'UserID'        => $Order['UserID'],
				'Text'          => SPrintF('Не удалость автоматически оплатить заказ (%s), причина (%s)',$Order['Item'],$ServiceOrderPay->String)
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------
		$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Orders','StatusID'=>'Suspended','RowsIDs'=>$Order['ID'],'Comment'=>SPrintF('Срок действия заказа окончен/%s',$ServiceOrderPay->String)));
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
		#-------------------------------------------------------------
		break;
	case 'array':
		# TODO может надо в этом месте ставить статус "Active" для услуги?
		#-------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $Order['UserID'],
				'Text'		=> SPrintF('Была автоматически продлена услуга (%s), заказ #%u',$Order['Item'],$Order['ID']),
				'IsReaded'	=> FALSE,
				'PriorityID'	=> 'Billing'
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
      }else{
        #-------------------------------------------------------------------------
        $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Orders','StatusID'=>'Suspended','RowsIDs'=>$Order['ID'],'Comment'=>'Заказ не был оплачен'));
        #-------------------------------------------------------------------------
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
        } # end set staus
      }
      #-------------------------------------------------------------------------
    } # end foreach
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return MkTime(5,20,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
