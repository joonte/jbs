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
$DSOrderID	= (integer) @$Args['DSOrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsNoBasket     = (boolean) @$Args['IsNoBasket'];
$IsUseBasket    = (boolean) @$Args['IsUseBasket'];
$PayMessage     =  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ServiceID','IP','ContractID','StatusID','UserID','DaysRemainded','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `DSOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `Balance` FROM `Contracts` WHERE `DSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `DSOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`DSOrdersOwners`.`OrderID`) AS PayedSumm');
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',$Columns,Array('UNIQ','ID'=>$DSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DS_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$DSOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DSOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
        $StatusID = $DSOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended','OnService')))
          return new gException('DS_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
        #-----------------------------------------------------------------------
        $UserID = $DSOrder['UserID'];
        #-----------------------------------------------------------------------
        $DSScheme = DB_Select('DSSchemes',Array('ID','Name','CostDay','CostInstall','IsActive','IsProlong','MinDaysPay','MinDaysProlong','MaxDaysPay'),Array('UNIQ','ID'=>$DSOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($DSScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if($DSOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$DSScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план аренды сервера не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$DSScheme['IsActive'])
                return new gException('SCHEME_NOT_ACTIVE','Тарифный план аренды сервера не активен');
            }
	    #-------------------------------------------------------------------
            # проверяем, это первая оплата или нет? если не первая, то минимальное число дней MinDaysProlong
            Debug(SPrintF('[comp/www/API/DSOrderPay]: ранее оплачено за заказ %s',$DSOrder['PayedSumm']));
            if($DSOrder['PayedSumm'] > 0){
              $MinDaysPay = $DSScheme['MinDaysProlong'];
            }else{
              $MinDaysPay = $DSScheme['MinDaysPay'];
            }
	    #-------------------------------------------------------------------
	    Debug(SPrintF('[comp/www/DSOrderPay]: минимальное число дней %s',$MinDaysPay));
            #-------------------------------------------------------------------
            if($DaysPay < $MinDaysPay || $DaysPay > $DSScheme['MaxDaysPay'])
              return new gException('WRONG_DAYS_PAY','Неверное кол-во дней оплаты');
            #-------------------------TRANSACTION-------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('DSOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Politics',$DSOrder['UserID'],$DSOrder['GroupID'],$DSOrder['ServiceID'],$DSScheme['ID'],$DaysPay,SPrintF('Dedicated/%s',$DSOrder['IP']));
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
	    $DSOrderID = (integer)$DSOrder['ID'];
	    #-------------------------------------------------------------------
            $CostPay = 0.00;
            #-------------------------------------------------------------------
            $DaysRemainded = $DaysPay;
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Bonuses',$DaysRemainded,$DSOrder['ServiceID'],$DSScheme['ID'],$UserID,$CostPay,$DSScheme['CostDay'],$DSOrder['OrderID']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-----------------------------------------------------------------
            $CostPay = $Comp['CostPay'];
            $Bonuses = $Comp['Bonuses'];
            #-------------------------------------------------------------------
            $CostPay = Round($CostPay,2);
	    #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
            if($DSScheme['CostInstall'] > 0){
		# need give installation payment
		if(!$DSOrder['IsPayed']){
			# if it not prolongation
			$CostPay += $DSScheme['CostInstall'];
		}
	    }
	    #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            if($IsUseBasket || (!$IsNoBasket && $CostPay > $DSOrder['ContractBalance'])){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DaysRemainded = $DSOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              $sDate = Comp_Load('Formats/Date/Simple',Time() + $DaysRemainded*86400);
              if(Is_Error($sDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $tDate = Comp_Load('Formats/Date/Simple',Time() + ($DaysRemainded + $DaysPay)*86400);
              if(Is_Error($tDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IBasket = Array('OrderID'=>$DSOrder['OrderID'],'Comment'=>SPrintF('Тариф: %s, с %s по %s',$DSScheme['Name'],$sDate,$tDate),'Amount'=>$DaysPay,'Summ'=>$CostPay);
              #-----------------------------------------------------------------
              $Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$DSOrder['OrderID'])));
              if(Is_Error($Count))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($Count){
                #---------------------------------------------------------------
                $IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$DSOrder['OrderID'])));
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }else{
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('Basket',$IBasket);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Basket/Update',$DSOrder['UserID'],$DSOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              return Array('Status'=>'UseBasket');
            }else{
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Order/Number',$DSOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DSOrder['Number'] = $Comp;
              #-----------------------------------------------------------------
              $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$DSOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>$DSOrder['ServiceID'],'Comment'=>SPrintF('№%s на %s дн.',$Comp,$DaysPay)));
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
                  $IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$DSOrder['OrderID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  switch($StatusID){
                    case 'Waiting':
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'OnCreate','RowsIDs'=>$DSOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен'));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('IsNotNotify'=>TRUE,'ModeID'=>'DSOrders','StatusID'=>'Active','RowsIDs'=>$DSOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен'));
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
                    case 'OnService':
		    	# ничего, делаем Suspended
                    case 'Suspended':
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'Active','RowsIDs'=>$DSOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен и будет активирован'));
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
		  			'UserID'	=> $DSOrder['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Заказ DS (%s) оплачен на период %u дн.',$DSScheme['Name'], $DaysPay)
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
