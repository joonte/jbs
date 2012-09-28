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
$VPSOrderID	= (integer) @$Args['VPSOrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsNoBasket     = (boolean) @$Args['IsNoBasket'];
$PayMessage     =  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ContractID','StatusID','UserID','Login','Domain','DaysRemainded','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `VPSOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `Balance` FROM `Contracts` WHERE `VPSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `VPSOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`VPSOrdersOwners`.`OrderID`) AS PayedSumm');
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('VPS_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$VPSOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('VPSOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
        $StatusID = $VPSOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
          return new gException('VPS_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
        #-----------------------------------------------------------------------
        $UserID = $VPSOrder['UserID'];
        #-----------------------------------------------------------------------
        $VPSScheme = DB_Select('VPSSchemes',Array('ID','Name','CostDay','CostInstall','IsActive','IsProlong','MinDaysPay','MinDaysProlong','MaxDaysPay'),Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($VPSScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if($VPSOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$VPSScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа виртуального сервера не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$VPSScheme['IsActive'])
                return new gException('SCHEME_NOT_ACTIVE','Тарифный план заказа виртуального сервера не активен');
            }
	    #-------------------------------------------------------------------
            # проверяем, это первая оплата или нет? если не первая, то минимальное число дней MinDaysProlong
            Debug(SPrintF('[comp/www/API/VPSOrderPay]: ранее оплачено за заказ %s',$VPSOrder['PayedSumm']));
            if($VPSOrder['PayedSumm'] > 0){
              $MinDaysPay = $VPSScheme['MinDaysProlong'];
            }else{
              $MinDaysPay = $VPSScheme['MinDaysPay'];
            }
	    #-------------------------------------------------------------------
	    Debug(SPrintF('[comp/www/VPSOrderPay]: минимальное число дней %s',$MinDaysPay));
            #-------------------------------------------------------------------
            if($DaysPay < $MinDaysPay || $DaysPay > $VPSScheme['MaxDaysPay'])
              return new gException('WRONG_DAYS_PAY','Неверное кол-во дней оплаты');
            #-------------------------TRANSACTION-------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('VPSOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $VPSOrderID = (integer)$VPSOrder['ID'];
            #-------------------------------------------------------------------
            $Entrance = Tree_Path('Groups',(integer)$VPSOrder['GroupID']);
            #-------------------------------------------------------------------
            switch(ValueOf($Entrance)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #---------------------------------------------------------------
                $Where = SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysPay` <= %u',Implode(',',$Entrance),$VPSOrder['UserID'],$VPSScheme['ID'],$DaysPay);
                #---------------------------------------------------------------
                $VPSPolitic = DB_Select('VPSPolitics','*',Array('UNIQ','Where'=>$Where,'SortOn'=>'Discont','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
                #---------------------------------------------------------------
                switch(ValueOf($VPSPolitic)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    # No more...
                  break 2;
                  case 'array':
                    #-----------------------------------------------------------
                    $IsInsert = DB_Insert('VPSBonuses',Array('UserID'=>$UserID,'SchemeID'=>$VPSScheme['ID'],'DaysReserved'=>$DaysPay,'Discont'=>$VPSPolitic['Discont'],'Comment'=>'Ценовая политика'));
                    if(Is_Error($IsInsert))
                      return ERROR | @Trigger_Error(500);
                  break 2;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $CostPay = 0.00;
            #-------------------------------------------------------------------
            $DaysRemainded = $DaysPay;
            #-------------------------------------------------------------------
            while($DaysRemainded > 0){
              #-----------------------------------------------------------------
              $IOrdersConsider = Array('OrderID'=>$VPSOrder['OrderID'],'Cost'=>$VPSScheme['CostDay']);
              #-----------------------------------------------------------------
              $Where = SPrintF('`UserID` = %u AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysRemainded` > 0',$UserID,$VPSScheme['ID']);
              #-----------------------------------------------------------------
              $VPSBonus = DB_Select('VPSBonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
              #-----------------------------------------------------------------
              switch(ValueOf($VPSBonus)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  #-------------------------------------------------------------
                  $CostPay += $VPSScheme['CostDay']*$DaysRemainded;
                  #-------------------------------------------------------------
                  $IOrdersConsider['DaysReserved'] = $DaysRemainded;
                  #-------------------------------------------------------------
                  $DaysRemainded = 0;
                break;
                case 'array':
                  #-------------------------------------------------------------
                  $VPSBonus = Current($VPSBonus);
                  #-------------------------------------------------------------
                  $Discont = (1 - $VPSBonus['Discont']);
                  #-------------------------------------------------------------
                  $IOrdersConsider['Discont'] = $VPSBonus['Discont'];
                  #-------------------------------------------------------------
                  if($VPSBonus['DaysRemainded'] - $DaysRemainded < 0){
                    #-----------------------------------------------------------
                    $CostPay += $VPSScheme['CostDay']*$VPSBonus['DaysRemainded']*$Discont;
                    #-----------------------------------------------------------
                    $IOrdersConsider['DaysReserved'] = $VPSBonus['DaysRemainded'];
                    #-----------------------------------------------------------
                    $UVPSBonus = Array('DaysRemainded'=>0);
                    #-----------------------------------------------------------
                    $DaysRemainded -= $VPSBonus['DaysRemainded'];
                  }else{
                    #-----------------------------------------------------------
                    $CostPay += $VPSScheme['CostDay']*$DaysRemainded*$Discont;
                    #-----------------------------------------------------------
                    $IOrdersConsider['DaysReserved'] = $DaysRemainded;
                    #-----------------------------------------------------------
                    $UVPSBonus = Array('DaysRemainded'=>$VPSBonus['DaysRemainded'] - $DaysRemainded);
                    #-----------------------------------------------------------
                    $DaysRemainded = 0;
                  }
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('VPSBonuses',$UVPSBonus,Array('ID'=>$VPSBonus['ID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              $IsInsert = DB_Insert('OrdersConsider',$IOrdersConsider);
              if(Is_Error($IsInsert))
                return ERROR | @Trigger_Error(500);
            }
            #-------------------------------------------------------------------
            $CostPay = Round($CostPay,2);
	    #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
	    if($VPSScheme['CostInstall'] > 0){
	      # need give installation payment
	      if(!$VPSOrder['IsPayed']){
	        # if it not prolongation
		$CostPay += $VPSScheme['CostInstall'];
	      }
	    }
	    #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            if(!$IsNoBasket && $CostPay > $VPSOrder['ContractBalance']){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DaysRemainded = $VPSOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              $sDate = Comp_Load('/Formats/Date/Simple',Time() + $DaysRemainded*86400);
              if(Is_Error($sDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $tDate = Comp_Load('/Formats/Date/Simple',Time() + ($DaysRemainded + $DaysPay)*86400);
              if(Is_Error($tDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IBasket = Array('OrderID'=>$VPSOrder['OrderID'],'Comment'=>SPrintF('Тариф: %s, с %s по %s',$VPSScheme['Name'],$sDate,$tDate),'Amount'=>$DaysPay,'Summ'=>$CostPay);
              #-----------------------------------------------------------------
              $Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$VPSOrder['OrderID'])));
              if(Is_Error($Count))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($Count){
                #---------------------------------------------------------------
                $IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$VPSOrder['OrderID'])));
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }else{
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('Basket',$IBasket);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Basket/Update',$VPSOrder['UserID'],$VPSOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              return Array('Status'=>'UseBasket');
            }else{
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Order/Number',$VPSOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $VPSOrder['Number'] = $Comp;
              #-----------------------------------------------------------------
              $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$VPSOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>30000,'Comment'=>SPrintF('№%s на %s дн.',$Comp,$DaysPay)));
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
                  $IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$VPSOrder['OrderID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  switch($StatusID){
                    case 'Waiting':
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'OnCreate','RowsIDs'=>$VPSOrderID,'Comment'=>'Заказ успешно оплачен'));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('IsNotNotify'=>TRUE,'ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>$PayMessage));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>'Заказ успешно оплачен и будет активирован'));
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
                  $VPSDomainPolitic = DB_Select('VPSDomainsPolitics','*',Array('IsDesc'=>TRUE,'SortOn'=>'DaysPay','Where'=>SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR `SchemeID` IS NULL) AND `DaysPay` <= %u',Implode(',',$Entrance),$VPSOrder['UserID'],$VPSOrder['SchemeID'],$DaysPay)));
                  #-------------------------------------------------------------
                  switch(ValueOf($VPSDomainPolitic)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      # No more...
                    break;
                    case 'array':
                      #---------------------------------------------------------
                      $VPSDomainPolitic = Current($VPSDomainPolitic);
                      #---------------------------------------------------------
                      $IDomainBonus = Array(
                        #-------------------------------------------------------
                        'UserID'                => $VPSOrder['UserID'],
                        'SchemeID'              => NULL,
                        'DomainsSchemesGroupID' => $VPSDomainPolitic['DomainsSchemesGroupID'],
                        'YearsReserved'         => 1,
                        'OperationID'           => 'Order',
                        'Discont'               => $VPSDomainPolitic['Discont'],
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
                  #-------------------------------------------------------------
		  $Event = Array(
		  		'UserID'	=> $VPSOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Заказ VPS логин (%s), тариф (%s) успешно оплачен на период %u дн.',$VPSOrder['Login'],$VPSScheme['Name'],$DaysPay)
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
