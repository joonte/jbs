<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$CurrentDay = (integer)(Time()/86400);
#-------------------------------------------------------------------------------
$Where = SPrintF("`StatusID` = 'Active' AND `ConsiderDay` < %u",$CurrentDay);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','OrderID','ContractID','ConsiderDay','SchemeID','(SELECT `IsAutoProlong` FROM `Orders` WHERE `VPSOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`');
$VPSOrders = DB_Select('VPSOrdersOwners',$Columns,Array('Where'=>$Where,'Limit'=>Array('Start'=>0,'Length'=>5)));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return MkTime(4,15,0,Date('n'),Date('j')+1,Date('Y'));
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = SPrintF('considered %u accounts',SizeOf($VPSOrders));
    #---------------------------------------------------------------------------
    foreach($VPSOrders as $VPSOrder){
      #-------------------------------------------------------------------------
      $VPSOrderID = (integer)$VPSOrder['ID'];
      $OrderID    = (integer)$VPSOrder['OrderID'];
      #------------------------------TRANSACTION--------------------------------
      if(Is_Error(DB_Transaction($TransactionID = UniqID('OrdersConsider'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Where = SPrintF('`OrderID` = %u AND `DaysRemainded` > 0 AND `ID` = (SELECT MIN(`ID`) FROM `OrdersConsider` WHERE `OrderID` = %u AND `DaysRemainded` > 0)',$OrderID,$OrderID);
      #-------------------------------------------------------------------------
      $OrdersConsider = DB_Select('OrdersConsider','*',Array('UNIQ','Where'=>$Where));
      #-------------------------------------------------------------------------
      switch(ValueOf($OrdersConsider)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # check autoprolongation
          if($VPSOrder['IsAutoProlong']){
            Debug("[comp/Tasks/VPSConsider]: autoprolongation for " . $VPSOrder['OrderID']);
            #---------------------------------------------------------------------
            $VPSScheme = DB_Select('VPSSchemes','MinDaysPay',Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
            #---------------------------------------------------------------------
            switch(ValueOf($VPSScheme)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #-----------------------------------------------------------------
                $VPSOrderPay = Comp_Load('www/API/VPSOrderPay',Array('VPSOrderID'=>$VPSOrderID,'DaysPay'=>$VPSScheme['MinDaysPay'],'IsNoBasket'=>TRUE,'PayMessage'=>'Автоматическое продление заказа, оплата с баланса договора'));
                #-----------------------------------------------------------------
                switch(ValueOf($VPSOrderPay)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    #-------------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $VPSOrder['UserID'],
					'Text'		=> SPrintF('Не удалость автоматически оплатить заказ хостинга, причина (%s)',$VPSOrderPay->String)
		    		  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-------------------------------------------------------------
                    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Suspended','RowsIDs'=>$VPSOrderID,'Comment'=>SPrintF('Срок действия заказа окончен/%s',$VPSOrderPay->String)));
                    #-------------------------------------------------------------
                    switch(ValueOf($Comp)){
                      case 'error':
                        return ERROR | @Trigger_Error(500);
                      case 'exception':
                        return ERROR | @Trigger_Error(400);
                      case 'array':
                        # No more...
                      break 4;
                      default:
                        return ERROR | @Trigger_Error(101);
                    }
                  case 'array':
                    # No more...
                  break 3;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
          }else{	# autoprolongation -> no autoprolongation
            Debug("[comp/Tasks/VPSConsider]: NO autoprolongation for " . $VPSOrder['OrderID']);
            #-------------------------------------------------------------
            $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Suspended','RowsIDs'=>$VPSOrderID,'Comment'=>'Срок действия заказа окончен/Aвтопродление отключено'));
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
          }
        case 'array':
          #---------------------------------------------------------------------
          $IsUpdate = DB_Update('OrdersConsider',Array('DaysRemainded'=>$OrdersConsider['DaysRemainded']-1),Array('ID'=>$OrdersConsider['ID']));
          if(Is_Error($IsUpdate))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $DaysConsidered = (integer)$OrdersConsider['DaysConsidered'];
          #---------------------------------------------------------------------
          if($DaysConsidered){
            #-------------------------------------------------------------------
            $CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
            #-------------------------------------------------------------------
            $Number = Comp_Load('Formats/Order/Number',$VPSOrder['OrderID']);
            if(Is_Error($Number))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $IWorkComplite = Array(
              #-----------------------------------------------------------------
              'ContractID' => $VPSOrder['ContractID'],
              'Month'      => $CurrentMonth,
              'ServiceID'  => 30000,
              'Comment'    => SPrintF('№%s',$Number),
              'Amount'     => 1,
              'Cost'       => $OrdersConsider['Cost'],
              'Discont'    => $OrdersConsider['Discont']
            );
            #-------------------------------------------------------------------
            $IsInsert = DB_Insert('WorksComplite',$IWorkComplite);
            if(Is_Error($IsInsert))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>$DaysConsidered-1),Array('ID'=>$OrdersConsider['ID']));
            if(Is_Error($IsUpdate))
              return ERROR | @Trigger_Error(500);
          }
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $ConsiderDay = (integer)$VPSOrder['ConsiderDay'];
      #-------------------------------------------------------------------------
      $ConsiderDay = ($ConsiderDay?$ConsiderDay+1:$CurrentDay);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('VPSOrders',Array('ConsiderDay'=>$ConsiderDay),Array('ID'=>$VPSOrderID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if(Is_Error(DB_Commit($TransactionID)))
        return ERROR | @Trigger_Error(500);
    }
    #---------------------------------------------------------------------------
    return 60;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
