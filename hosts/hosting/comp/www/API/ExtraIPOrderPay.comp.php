<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsNoBasket     = (boolean) @$Args['IsNoBasket'];
$IsUseBasket    = (boolean) @$Args['IsUseBasket'];
$PayMessage     =  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ServiceID','ContractID','Login','StatusID','UserID','DaysRemainded','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `ExtraIPOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `Balance` FROM `Contracts` WHERE `ExtraIPOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `ExtraIPOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`ExtraIPOrdersOwners`.`OrderID`) AS PayedSumm');
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',$Columns,Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('ExtraIP_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$ExtraIPOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ExtraIPOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $StatusID = $ExtraIPOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
          return new gException('ExtraIP_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
        #-----------------------------------------------------------------------
        $UserID = $ExtraIPOrder['UserID'];
        #-----------------------------------------------------------------------
        $ExtraIPScheme = DB_Select('ExtraIPSchemes',Array('ID','Name','CostDay','CostInstall','IsActive','IsProlong','MinDaysPay','MinDaysProlong','MaxDaysPay'),Array('UNIQ','ID'=>$ExtraIPOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($ExtraIPScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
	    #-------------------------------------------------------------------
            # проверяем, это первая оплата или нет? если не первая, то минимальное число дней MinDaysProlong
            Debug(SPrintF('[comp/www/API/ExtraIPOrderPay]: ранее оплачено за заказ %s',$ExtraIPOrder['PayedSumm']));
            if($ExtraIPOrder['PayedSumm'] > 0){
              $MinDaysPay = $ExtraIPScheme['MinDaysProlong'];
            }else{
              $MinDaysPay = $ExtraIPScheme['MinDaysPay'];
            }
            #-------------------------------------------------------------------
            Debug(SPrintF('[comp/www/API/ExtraIPOrderPay]: минимальное число дней %s',$MinDaysPay));
            #-------------------------------------------------------------------
            if($ExtraIPOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$ExtraIPScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план аренды сервера не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$ExtraIPScheme['IsActive'])
                return new gException('SCHEME_NOT_ACTIVE','Тарифный план аренды сервера не активен');
            }
            #-------------------------------------------------------------------
            if($DaysPay < $MinDaysPay || $DaysPay > $ExtraIPScheme['MaxDaysPay'])
              return new gException('WRONG_DAYS_PAY','Неверное кол-во дней оплаты');
            #-------------------------TRANSACTION-------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('ExtraIPOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Politics',$ExtraIPOrder['UserID'],$ExtraIPOrder['GroupID'],$ExtraIPOrder['ServiceID'],$ExtraIPScheme['ID'],$DaysPay,SPrintF('IP/%s',$ExtraIPOrder['Login']));
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
            $ExtraIPOrderID = (integer)$ExtraIPOrder['ID'];
	    #-------------------------------------------------------------------
            $CostPay = 0.00;
            #-------------------------------------------------------------------
            $DaysRemainded = $DaysPay;
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Bonuses',$DaysRemainded,$ExtraIPOrder['ServiceID'],$ExtraIPScheme['ID'],$UserID,$CostPay,$ExtraIPScheme['CostDay'],$ExtraIPOrder['OrderID']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-----------------------------------------------------------------
            $CostPay = $Comp['CostPay'];
            $Bonuses = $Comp['Bonuses'];
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            if($ExtraIPScheme['CostInstall'] > 0){
              # need give installation payment
              if(!$ExtraIPOrder['IsPayed']){
                # if it not prolongation
                $Comp = Comp_Load('Formats/Currency',$ExtraIPScheme['CostInstall']);
                if(Is_Error($Comp))
                   return ERROR | @Trigger_Error(500);
                $CostPay += $ExtraIPScheme['CostInstall'];
              }
            }
            #-------------------------------------------------------------------
            if($IsUseBasket || (!$IsNoBasket && $CostPay > $ExtraIPOrder['ContractBalance'])){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DaysRemainded = $ExtraIPOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              $sDate = Comp_Load('Formats/Date/Simple',Time() + $DaysRemainded*86400);
              if(Is_Error($sDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $tDate = Comp_Load('Formats/Date/Simple',Time() + ($DaysRemainded + $DaysPay)*86400);
              if(Is_Error($tDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IBasket = Array('OrderID'=>$ExtraIPOrder['OrderID'],'Comment'=>SPrintF('Тариф: %s, с %s по %s',$ExtraIPScheme['Name'],$sDate,$tDate),'Amount'=>$DaysPay,'Summ'=>$CostPay);
              #-----------------------------------------------------------------
              $Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$ExtraIPOrder['OrderID'])));
              if(Is_Error($Count))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($Count){
                #---------------------------------------------------------------
                $IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$ExtraIPOrder['OrderID'])));
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }else{
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('Basket',$IBasket);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Basket/Update',$ExtraIPOrder['UserID'],$ExtraIPOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              return Array('Status'=>'UseBasket');
            }else{
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Order/Number',$ExtraIPOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $ExtraIPOrder['Number'] = $Comp;
              #-----------------------------------------------------------------
              $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$ExtraIPOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>$ExtraIPOrder['ServiceID'],'Comment'=>SPrintF('№%s на %s дн.',$Comp,$DaysPay)));
              #-----------------------------------------------------------------
              switch(ValueOf($IsUpdate)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  #-------------------------------------------------------------
                  if(Is_Error(DB_Roll($TransactionID)))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  return $IsUpdate;
                case 'array':
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$ExtraIPOrder['OrderID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  switch($StatusID){
                    case 'Waiting':
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'OnCreate','RowsIDs'=>$ExtraIPOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен'));
                      #---------------------------------------------------------
                      switch(ValueOf($Comp)){
                        case 'error':
                          return ERROR | @Trigger_Error(500);
                        case 'exception':
                          return ERROR | @Trigger_Error(400);
                        case 'array':
                          # No more...
                        break 2;
                        default:
                          return ERROR | @Trigger_Error(101);
                      }
                    case 'Active':
                      #---------------------------------------------------------
		      # вариант автопродления может быть только когда заказ ещё активен
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('IsNotNotify'=>TRUE,'ModeID'=>'ExtraIPOrders','StatusID'=>'Active','RowsIDs'=>$ExtraIPOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен'));
                      #---------------------------------------------------------
                      switch(ValueOf($Comp)){
                        case 'error':
                          return ERROR | @Trigger_Error(500);
                        case 'exception':
                          return ERROR | @Trigger_Error(400);
                        case 'array':
                          # No more...
                        break 2;
                        default:
                          return ERROR | @Trigger_Error(101);
                      }
                    case 'Suspended':
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Active','RowsIDs'=>$ExtraIPOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен и будет активирован'));
                      #---------------------------------------------------------
                      switch(ValueOf($Comp)){
                        case 'error':
                          return ERROR | @Trigger_Error(500);
                        case 'exception':
                          return ERROR | @Trigger_Error(400);
                        case 'array':
                          # No more...
                        break 2;
                        default:
                          return ERROR | @Trigger_Error(101);
                      }
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                  #-------------------------------------------------------------
                  #-------------------------------------------------------------
		  $Event = Array(
		  			'UserID'	=> $ExtraIPOrder['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Заказ ExtraIP (%s) оплачен на период %u дн.',$ExtraIPScheme['Name'], $DaysPay)
		                );
                  $Event = Comp_Load('Events/EventInsert',$Event);
                  if(!$Event)
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  if(Is_Error(DB_Commit($TransactionID)))
                    return ERROR | @Trigger_Error(500);
                  #-------------------END TRANSACTION---------------------------
                  return Array('Status'=>'Ok');
                default:
                   return ERROR | @Trigger_Error(101);
              }
            }
          default:
             return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
