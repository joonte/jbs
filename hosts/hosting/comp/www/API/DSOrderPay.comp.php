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
$PayMessage     =  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ContractID','StatusID','UserID','DaysRemainded','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `DSOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `Balance` FROM `Contracts` WHERE `DSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `DSOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`DSOrdersOwners`.`OrderID`) AS PayedSumm');
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
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
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
            if($DaysPay < $MinDaysPay || $DaysPay > $DSScheme['MaxDaysPay'])
              return new gException('WRONG_DAYS_PAY','Неверное кол-во дней оплаты');
            #-------------------------TRANSACTION-------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('DSOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DSOrderID = (integer)$DSOrder['ID'];
            #-------------------------------------------------------------------
            $Entrance = Tree_Path('Groups',(integer)$DSOrder['GroupID']);
            #-------------------------------------------------------------------
            switch(ValueOf($Entrance)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #---------------------------------------------------------------
                $Where = SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysPay` <= %u',Implode(',',$Entrance),$DSOrder['UserID'],$DSScheme['ID'],$DaysPay);
                #---------------------------------------------------------------
                $DSPolitic = DB_Select('DSPolitics','*',Array('UNIQ','Where'=>$Where,'SortOn'=>'Discont','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
                #---------------------------------------------------------------
                switch(ValueOf($DSPolitic)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    # No more...
                  break 2;
                  case 'array':
                    #-----------------------------------------------------------
                    $IsInsert = DB_Insert('DSBonuses',Array('UserID'=>$UserID,'SchemeID'=>$DSScheme['ID'],'DaysReserved'=>$DaysPay,'Discont'=>$DSPolitic['Discont'],'Comment'=>'Ценовая политика'));
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
              $IOrdersConsider = Array('OrderID'=>$DSOrder['OrderID'],'Cost'=>$DSScheme['CostDay']);
              #-----------------------------------------------------------------
              $Where = SPrintF('`UserID` = %u AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysRemainded` > 0',$UserID,$DSScheme['ID']);
              #-----------------------------------------------------------------
              $DSBonus = DB_Select('DSBonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
              #-----------------------------------------------------------------
              switch(ValueOf($DSBonus)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  #-------------------------------------------------------------
                  $CostPay += $DSScheme['CostDay']*$DaysRemainded;
                  #-------------------------------------------------------------
                  $IOrdersConsider['DaysReserved'] = $DaysRemainded;
                  #-------------------------------------------------------------
                  $DaysRemainded = 0;
                break;
                case 'array':
                  #-------------------------------------------------------------
                  $DSBonus = Current($DSBonus);
                  #-------------------------------------------------------------
                  $Discont = (1 - $DSBonus['Discont']);
                  #-------------------------------------------------------------
                  $IOrdersConsider['Discont'] = $DSBonus['Discont'];
                  #-------------------------------------------------------------
                  if($DSBonus['DaysRemainded'] - $DaysRemainded < 0){
                    #-----------------------------------------------------------
                    $CostPay += $DSScheme['CostDay']*$DSBonus['DaysRemainded']*$Discont;
                    #-----------------------------------------------------------
                    $IOrdersConsider['DaysReserved'] = $DSBonus['DaysRemainded'];
                    #-----------------------------------------------------------
                    $UDSBonus = Array('DaysRemainded'=>0);
                    #-----------------------------------------------------------
                    $DaysRemainded -= $DSBonus['DaysRemainded'];
                  }else{
                    #-----------------------------------------------------------
                    $CostPay += $DSScheme['CostDay']*$DaysRemainded*$Discont;
                    #-----------------------------------------------------------
                    $IOrdersConsider['DaysReserved'] = $DaysRemainded;
                    #-----------------------------------------------------------
                    $UDSBonus = Array('DaysRemainded'=>$DSBonus['DaysRemainded'] - $DaysRemainded);
                    #-----------------------------------------------------------
                    $DaysRemainded = 0;
                  }
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('DSBonuses',$UDSBonus,Array('ID'=>$DSBonus['ID']));
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
            if($DSScheme['CostInstall'] > 0){
		# need give installation payment
		if(!$DSOrder['IsPayed']){
			# if it not prolongation
			$CostPay += $DSScheme['CostInstall'];
		}
	    }
	    #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            if(!$IsNoBasket && $CostPay > $DSOrder['ContractBalance']){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DaysRemainded = $DSOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              $sDate = Comp_Load('/Formats/Date/Simple',Time() + $DaysRemainded*86400);
              if(Is_Error($sDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $tDate = Comp_Load('/Formats/Date/Simple',Time() + ($DaysRemainded + $DaysPay)*86400);
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
              $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$DSOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>40000,'Comment'=>SPrintF('№%s на %s дн.',$Comp,$DaysPay)));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'OnCreate','RowsIDs'=>$DSOrderID,'Comment'=>'Заказ успешно оплачен'));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('IsNotNotify'=>TRUE,'ModeID'=>'DSOrders','StatusID'=>'Active','RowsIDs'=>$DSOrderID,'Comment'=>$PayMessage));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'Active','RowsIDs'=>$DSOrderID,'Comment'=>'Заказ успешно оплачен и будет активирован'));
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
                  $DSDomainPolitic = DB_Select('DSDomainsPolitics','*',Array('IsDesc'=>TRUE,'SortOn'=>'DaysPay','Where'=>SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR `SchemeID` IS NULL) AND `DaysPay` <= %u',Implode(',',$Entrance),$DSOrder['UserID'],$DSOrder['SchemeID'],$DaysPay)));
                  #-------------------------------------------------------------
                  switch(ValueOf($DSDomainPolitic)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      # No more...
                    break;
                    case 'array':
                      #---------------------------------------------------------
                      $DSDomainPolitic = Current($DSDomainPolitic);
                      #---------------------------------------------------------
                      $IDomainBonus = Array(
                        #-------------------------------------------------------
                        'UserID'                => $DSOrder['UserID'],
                        'SchemeID'              => NULL,
                        'DomainsSchemesGroupID' => $DSDomainPolitic['DomainsSchemesGroupID'],
                        'YearsReserved'         => 1,
                        'OperationID'           => 'Order',
                        'Discont'               => $DSDomainPolitic['Discont'],
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
		  			'UserID'	=> $DSOrder['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Заказ DS (%s) успешно оплачен на период %u дн.',$DSScheme['Name'], $DaysPay)
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
