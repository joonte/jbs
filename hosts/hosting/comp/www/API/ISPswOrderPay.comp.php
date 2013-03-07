<?php

#-------------------------------------------------------------------------------
/** @author  Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ISPswOrderID	= (integer) @$Args['ISPswOrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsNoBasket     = (boolean) @$Args['IsNoBasket'];
$PayMessage     =  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ContractID','StatusID','UserID','DaysRemainded','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `ISPswOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `Balance` FROM `Contracts` WHERE `ISPswOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `ISPswOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`ISPswOrdersOwners`.`OrderID`) AS PayedSumm');
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('HOSTING_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$ISPswOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ISPswOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
        $StatusID = $ISPswOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
          return new gException('HOSTING_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
        #-----------------------------------------------------------------------
        $UserID = $ISPswOrder['UserID'];
        #-----------------------------------------------------------------------
        $ISPswScheme = DB_Select('ISPswSchemes',Array('ID','Name','CostDay','CostMonth','IsActive','IsProlong','MinDaysPay','MinDaysProlong','MaxDaysPay','ConsiderTypeID'),Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($ISPswScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if($ISPswOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$ISPswScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа ПО ISPsystem не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$ISPswScheme['IsActive'])
                return new gException('SCHEME_NOT_ACTIVE','Тарифный план заказа ПО ISPsystem не активен');
            }
            #-------------------------------------------------------------------
            # проверяем, это первая оплата или нет? если не первая, то минимальное число дней MinDaysProlong
            Debug(SPrintF('[comp/www/API/ISPswOrderPay]: ранее оплачено за заказ %s',$ISPswOrder['PayedSumm']));
            if($ISPswOrder['PayedSumm'] > 0){
              $MinDaysPay = $ISPswScheme['MinDaysProlong'];
            }else{
              $MinDaysPay = $ISPswScheme['MinDaysPay'];
            }
            #-------------------------------------------------------------------
            Debug(SPrintF('[comp/www/API/ISPswOrderPay]: минимальное число дней %s',$MinDaysPay));
            #-------------------------------------------------------------------
            if($DaysPay < $MinDaysPay || $DaysPay > $ISPswScheme['MaxDaysPay']){
	      if($ISPswScheme['ConsiderTypeID'] == 'Daily'){
                return new gException('WRONG_DAYS_PAY','Неверное кол-во дней оплаты');
	      }
	    }
            #-------------------------TRANSACTION-------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('ISPswOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Politics',$ISPswOrder['UserID'],$ISPswOrder['GroupID'],51000,$ISPswScheme['ID'],$DaysPay,SPrintF('ISPsystem/%s',$ISPswOrder['ID']));
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
	    $ISPswOrderID = (integer)$ISPswOrder['ID'];
	    #-------------------------------------------------------------------
            $CostPay = 0.00;
            #-------------------------------------------------------------------
            $DaysRemainded = $DaysPay;
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Bonuses',$DaysRemainded,51000,$ISPswScheme['ID'],$UserID,$CostPay,$ISPswScheme['CostDay'],$ISPswOrder['OrderID'],$ISPswScheme['ConsiderTypeID']);
            if(Is_Error($Comp))
               return ERROR | @Trigger_Error(500);
            #-----------------------------------------------------------------
            $CostPay = $Comp['CostPay'];
            $Bonuses = $Comp['Bonuses'];
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $CostPay = Round($CostPay,2);
	    if($ISPswScheme['ConsiderTypeID'] == 'Upon')
	      $CostPay = $ISPswScheme['CostMonth'];
            #-------------------------------------------------------------------
            if(!$IsNoBasket && $CostPay > $ISPswOrder['ContractBalance']){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DaysRemainded = $ISPswOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              $sDate = Comp_Load('/Formats/Date/Simple',Time() + $DaysRemainded*86400);
              if(Is_Error($sDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $tDate = Comp_Load('/Formats/Date/Simple',Time() + ($DaysRemainded + $DaysPay)*86400);
              if(Is_Error($tDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IBasket = Array('OrderID'=>$ISPswOrder['OrderID'],'Comment'=>SPrintF('Тариф: %s, с %s по %s',$ISPswScheme['Name'],$sDate,$tDate),'Amount'=>$DaysPay,'Summ'=>$CostPay);
              #-----------------------------------------------------------------
              $Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$ISPswOrder['OrderID'])));
              if(Is_Error($Count))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($Count){
                #---------------------------------------------------------------
                $IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$ISPswOrder['OrderID'])));
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }else{
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('Basket',$IBasket);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Basket/Update',$ISPswOrder['UserID'],$ISPswOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              return Array('Status'=>'UseBasket');
            }else{
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Order/Number',$ISPswOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $ISPswOrder['Number'] = $Comp;
              #-----------------------------------------------------------------
              $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$ISPswOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>51000,'Comment'=>SPrintF('№%s на %s дн.',$Comp,$DaysPay)));
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
                  $IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$ISPswOrder['OrderID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  switch($StatusID){
                    case 'Waiting':
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'OnCreate','RowsIDs'=>$ISPswOrderID,'Comment'=>'Заказ успешно оплачен'));
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
		      if(!$PayMessage)
                        $PayMessage = "Заказ успешно оплачен";
		      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('IsNotNotify'=>TRUE,'ModeID'=>'ISPswOrders','StatusID'=>'Active','RowsIDs'=>$ISPswOrderID,'Comment'=>$PayMessage));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Active','RowsIDs'=>$ISPswOrderID,'Comment'=>'Заказ успешно оплачен и будет активирован'));
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
/*
                  $ISPswDomainPolitic = DB_Select('ISPswDomainsPolitics','*',Array('IsDesc'=>TRUE,'SortOn'=>'DaysPay','Where'=>SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR `SchemeID` IS NULL) AND `DaysPay` <= %u',Implode(',',$Entrance),$ISPswOrder['UserID'],$ISPswOrder['SchemeID'],$DaysPay)));
                  #-------------------------------------------------------------
                  switch(ValueOf($ISPswDomainPolitic)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      # No more...
                    break;
                    case 'array':
                      #---------------------------------------------------------
                      $ISPswDomainPolitic = Current($ISPswDomainPolitic);
                      #---------------------------------------------------------
                      $IDomainBonus = Array(
                        #-------------------------------------------------------
                        'UserID'                => $ISPswOrder['UserID'],
                        'SchemeID'              => NULL,
                        'DomainsSchemesGroupID' => $ISPswDomainPolitic['DomainsSchemesGroupID'],
                        'YearsReserved'         => 1,
                        'OperationID'           => 'Order',
                        'Discont'               => $ISPswDomainPolitic['Discont'],
                        'Comment'               => 'Назначен доменной политикой'
                      );
                      #---------------------------------------------------------
                      $IsInsert = DB_Insert('DomainsBonuses',$IDomainBonus);
                      if(Is_Error($IsInsert))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
*/
                  #-------------------------------------------------------------
		  $Event = Array(
		  			'UserID'	=> $ISPswOrder['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Заказ программного обеспечения ISPsystem, успешно оплачен на период %u дн.',$DaysPay)
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
