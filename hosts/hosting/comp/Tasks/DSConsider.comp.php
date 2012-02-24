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
$Columns = Array('ID','UserID','OrderID','ContractID','ConsiderDay','SchemeID','(SELECT `IsAutoProlong` FROM `Orders` WHERE `DSOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`');
$DSOrders = DB_Select('DSOrdersOwners',$Columns,Array('Where'=>$Where,'Limit'=>Array('Start'=>0,'Length'=>5)));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return MkTime(4,15,0,Date('n'),Date('j')+1,Date('Y'));
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DSOrders as $DSOrder){
      #-------------------------------------------------------------------------
      $DSOrderID = (integer)$DSOrder['ID'];
      $OrderID   = (integer)$DSOrder['OrderID'];
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
          if($DSOrder['IsAutoProlong']){
            Debug("[comp/Tasks/DSConsider]: autoprolongation for " . $DSOrder['OrderID']);
            #---------------------------------------------------------------------
            $DSScheme = DB_Select('DSSchemes','MinDaysPay',Array('UNIQ','ID'=>$DSOrder['SchemeID']));
            #---------------------------------------------------------------------
            switch(ValueOf($DSScheme)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #-----------------------------------------------------------------
                $DSOrderPay = Comp_Load('www/API/DSOrderPay',Array('DSOrderID'=>$DSOrderID,'DaysPay'=>$DSScheme['MinDaysPay'],'IsNoBasket'=>TRUE,'PayMessage'=>'Автоматическое продление заказа, оплата с баланса договора'));
                #-----------------------------------------------------------------
                switch(ValueOf($DSOrderPay)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    #-------------------------------------------------------------
		    $Event = Array(
					'UserID'	=> $DSOrder['UserID'],
					'Text'		=> SPrintF('Не удалость автоматически оплатить заказ выделенного сервера, причина (%s)',$DSOrderPay->String),
					'PriorityID'	=> 'Hosting'
		    		  );
		    $Event = Comp_Load('Events/EventInsert',$Event);
		    if(!$Event)
			return ERROR | @Trigger_Error(500);
                    #-------------------------------------------------------------
                    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'Suspended','RowsIDs'=>$DSOrderID,'Comment'=>SPrintF('Срок действия заказа окончен/%s',$DSOrderPay->String)));
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
            Debug("[comp/Tasks/DSConsider]: NO autoprolongation for " . $DSOrder['OrderID']);
            #-------------------------------------------------------------
            $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'Suspended','RowsIDs'=>$DSOrderID,'Comment'=>'Срок действия заказа окончен/Автопродление отключено'));
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
            $Number = Comp_Load('Formats/Order/Number',$DSOrder['OrderID']);
            if(Is_Error($Number))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $IWorkComplite = Array(
              #-----------------------------------------------------------------
              'ContractID' => $DSOrder['ContractID'],
              'Month'      => $CurrentMonth,
              'ServiceID'  => 40000,
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
      $ConsiderDay = (integer)$DSOrder['ConsiderDay'];
      #-------------------------------------------------------------------------
      $ConsiderDay = ($ConsiderDay?$ConsiderDay+1:$CurrentDay);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('DSOrders',Array('ConsiderDay'=>$ConsiderDay),Array('ID'=>$DSOrderID));
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
