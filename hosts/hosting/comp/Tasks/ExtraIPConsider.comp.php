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
$Columns = Array('ID','UserID','OrderID','ContractID','ConsiderDay','SchemeID','(SELECT `IsAutoProlong` FROM `Orders` WHERE `ExtraIPOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`');
$ExtraIPOrders = DB_Select('ExtraIPOrdersOwners',$Columns,Array('Where'=>$Where,'Limit'=>Array('Start'=>0,'Length'=>5)));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return MkTime(4,15,0,Date('n'),Date('j')+1,Date('Y'));
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ExtraIPOrders as $ExtraIPOrder){
      #-------------------------------------------------------------------------
      $ExtraIPOrderID = (integer)$ExtraIPOrder['ID'];
      $OrderID        = (integer)$ExtraIPOrder['OrderID'];
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
          if($ExtraIPOrder['IsAutoProlong']){
            Debug("[comp/Tasks/ExtraIPConsider]: autoprolongation for " . $ExtraIPOrder['OrderID']);
            #---------------------------------------------------------------------
            $ExtraIPScheme = DB_Select('ExtraIPSchemes','MinDaysPay',Array('UNIQ','ID'=>$ExtraIPOrder['SchemeID']));
            #---------------------------------------------------------------------
            switch(ValueOf($ExtraIPScheme)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #-----------------------------------------------------------------
                $ExtraIPOrderPay = Comp_Load('www/API/ExtraIPOrderPay',Array('ExtraIPOrderID'=>$ExtraIPOrderID,'DaysPay'=>$ExtraIPScheme['MinDaysPay'],'IsNoBasket'=>TRUE,'PayMessage'=>'Автоматическое продление заказа, оплата с баланса договора'));
                #-----------------------------------------------------------------
                switch(ValueOf($ExtraIPOrderPay)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    #-------------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $ExtraIPOrder['UserID'],
					'Text'		=> SPrintF('Не удалость автоматически оплатить заказ выделенного IP, причина (%s)',$ExtraIPOrderPay->String)
		    		  );
		    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-------------------------------------------------------------
                    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Deleted','RowsIDs'=>$ExtraIPOrderID,'Comment'=>SPrintF('Срок действия заказа окончен/%s',$ExtraIPOrderPay->String)));
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
            Debug("[comp/Tasks/ExtraIPConsider]: NO autoprolongation for " . $ExtraIPOrder['OrderID']);
            #-------------------------------------------------------------
            $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Deleted','RowsIDs'=>$ExtraIPOrderID,'Comment'=>'Срок действия заказа окончен/Автопродление отключено'));
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
            $Number = Comp_Load('Formats/Order/Number',$ExtraIPOrder['OrderID']);
            if(Is_Error($Number))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $IWorkComplite = Array(
              #-----------------------------------------------------------------
              'ContractID' => $ExtraIPOrder['ContractID'],
              'Month'      => $CurrentMonth,
              'ServiceID'  => 50000,
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
      $ConsiderDay = (integer)$ExtraIPOrder['ConsiderDay'];
      #-------------------------------------------------------------------------
      $ConsiderDay = ($ConsiderDay?$ConsiderDay+1:$CurrentDay);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('ExtraIPOrders',Array('ConsiderDay'=>$ConsiderDay),Array('ID'=>$ExtraIPOrderID));
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
