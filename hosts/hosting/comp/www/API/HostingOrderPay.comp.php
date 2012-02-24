<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$HostingOrderID = (integer) @$Args['HostingOrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsNoBasket     = (boolean) @$Args['IsNoBasket'];
$PayMessage	=  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ContractID','StatusID','UserID','Login','Domain','DaysRemainded','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `HostingOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `Balance` FROM `Contracts` WHERE `HostingOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `HostingOrdersOwners`.`OrderID`) as `IsPayed`', '(SELECT `Name` FROM `HostingSchemes` WHERE `HostingOrdersOwners`.`SchemeID` = `HostingSchemes`.`ID`) as `SchemeName`');
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('HOSTING_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$HostingOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('HostingOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
        $StatusID = $HostingOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
          return new gException('HOSTING_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
        #-----------------------------------------------------------------------
        $UserID = $HostingOrder['UserID'];
        #-----------------------------------------------------------------------
        $HostingScheme = DB_Select('HostingSchemes',Array('ID','Name','CostDay','IsActive','IsProlong','MinDaysPay','MaxDaysPay'),Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($HostingScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if($HostingOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$HostingScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа хостинга не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$HostingScheme['IsActive'])
                return new gException('SCHEME_NOT_ACTIVE','Тарифный план заказа хостинга не активен');
            }
            #-------------------------------------------------------------------
            if($DaysPay < $HostingScheme['MinDaysPay'] || $DaysPay > $HostingScheme['MaxDaysPay'])
              return new gException('WRONG_DAYS_PAY','Неверное кол-во дней оплаты');
            #-------------------------TRANSACTION-------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('HostingOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $HostingOrderID = (integer)$HostingOrder['ID'];
            #-------------------------------------------------------------------
            $Entrance = Tree_Path('Groups',(integer)$HostingOrder['GroupID']);
            #-------------------------------------------------------------------
            switch(ValueOf($Entrance)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #---------------------------------------------------------------
                $Where = SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysPay` <= %u',Implode(',',$Entrance),$HostingOrder['UserID'],$HostingScheme['ID'],$DaysPay);
                #---------------------------------------------------------------
                $HostingPolitic = DB_Select('HostingPolitics','*',Array('UNIQ','Where'=>$Where,'SortOn'=>'Discont','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
                #---------------------------------------------------------------
                switch(ValueOf($HostingPolitic)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    # No more...
                  break 2;
                  case 'array':
                    #-----------------------------------------------------------
                    $IsInsert = DB_Insert('HostingBonuses',Array('UserID'=>$UserID,'SchemeID'=>$HostingScheme['ID'],'DaysReserved'=>$DaysPay,'Discont'=>$HostingPolitic['Discont'],'Comment'=>'Ценовая политика'));
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
              $IOrdersConsider = Array('OrderID'=>$HostingOrder['OrderID'],'Cost'=>$HostingScheme['CostDay']);
              #-----------------------------------------------------------------
              $Where = SPrintF('`UserID` = %u AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysRemainded` > 0',$UserID,$HostingScheme['ID']);
              #-----------------------------------------------------------------
              $HostingBonus = DB_Select('HostingBonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
              #-----------------------------------------------------------------
              switch(ValueOf($HostingBonus)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  #-------------------------------------------------------------
                  $CostPay += $HostingScheme['CostDay']*$DaysRemainded;
                  #-------------------------------------------------------------
                  $IOrdersConsider['DaysReserved'] = $DaysRemainded;
                  #-------------------------------------------------------------
                  $DaysRemainded = 0;
                break;
                case 'array':
                  #-------------------------------------------------------------
                  $HostingBonus = Current($HostingBonus);
                  #-------------------------------------------------------------
                  $Discont = (1 - $HostingBonus['Discont']);
                  #-------------------------------------------------------------
                  $IOrdersConsider['Discont'] = $HostingBonus['Discont'];
                  #-------------------------------------------------------------
                  if($HostingBonus['DaysRemainded'] - $DaysRemainded < 0){
                    #-----------------------------------------------------------
                    $CostPay += $HostingScheme['CostDay']*$HostingBonus['DaysRemainded']*$Discont;
                    #-----------------------------------------------------------
                    $IOrdersConsider['DaysReserved'] = $HostingBonus['DaysRemainded'];
                    #-----------------------------------------------------------
                    $UHostingBonus = Array('DaysRemainded'=>0);
                    #-----------------------------------------------------------
                    $DaysRemainded -= $HostingBonus['DaysRemainded'];
                  }else{
                    #-----------------------------------------------------------
                    $CostPay += $HostingScheme['CostDay']*$DaysRemainded*$Discont;
                    #-----------------------------------------------------------
                    $IOrdersConsider['DaysReserved'] = $DaysRemainded;
                    #-----------------------------------------------------------
                    $UHostingBonus = Array('DaysRemainded'=>$HostingBonus['DaysRemainded'] - $DaysRemainded);
                    #-----------------------------------------------------------
                    $DaysRemainded = 0;
                  }
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('HostingBonuses',$UHostingBonus,Array('ID'=>$HostingBonus['ID']));
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
            if(!$IsNoBasket && $CostPay > $HostingOrder['ContractBalance']){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DaysRemainded = $HostingOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              $sDate = Comp_Load('/Formats/Date/Simple',Time() + $DaysRemainded*86400);
              if(Is_Error($sDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $tDate = Comp_Load('/Formats/Date/Simple',Time() + ($DaysRemainded + $DaysPay)*86400);
              if(Is_Error($tDate))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IBasket = Array('OrderID'=>$HostingOrder['OrderID'],'Comment'=>SPrintF('Тариф: %s, с %s по %s',$HostingScheme['Name'],$sDate,$tDate),'Amount'=>$DaysPay,'Summ'=>$CostPay);
              #-----------------------------------------------------------------
              $Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$HostingOrder['OrderID'])));
              if(Is_Error($Count))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($Count){
                #---------------------------------------------------------------
                $IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$HostingOrder['OrderID'])));
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }else{
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('Basket',$IBasket);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Basket/Update',$HostingOrder['UserID'],$HostingOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              return Array('Status'=>'UseBasket');
            }else{
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Order/Number',$HostingOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $HostingOrder['Number'] = $Comp;
              #-----------------------------------------------------------------
              $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$HostingOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>10000,'Comment'=>SPrintF('№%s на %s дн.',$Comp,$DaysPay)));
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
                  $IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$HostingOrder['OrderID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  switch($StatusID){
                    case 'Waiting':
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'OnCreate','RowsIDs'=>$HostingOrderID,'Comment'=>'Заказ успешно оплачен'));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('IsNotNotify'=>TRUE,'ModeID'=>'HostingOrders','StatusID'=>'Active','RowsIDs'=>$HostingOrderID,'Comment'=>$PayMessage));
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
                      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Active','RowsIDs'=>$HostingOrderID,'Comment'=>'Заказ успешно оплачен и будет активирован'));
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
                  $HostingDomainPolitic = DB_Select('HostingDomainsPolitics','*',Array('IsDesc'=>TRUE,'SortOn'=>'DaysPay','Where'=>SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR `SchemeID` IS NULL) AND `DaysPay` <= %u',Implode(',',$Entrance),$HostingOrder['UserID'],$HostingOrder['SchemeID'],$DaysPay)));
                  #-------------------------------------------------------------
                  switch(ValueOf($HostingDomainPolitic)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      # No more...
                    break;
                    case 'array':
                      #---------------------------------------------------------
                      $HostingDomainPolitic = Current($HostingDomainPolitic);
                      #---------------------------------------------------------
                      $IDomainBonus = Array(
                        #-------------------------------------------------------
                        'UserID'                => $HostingOrder['UserID'],
                        'SchemeID'              => NULL,
                        'DomainsSchemesGroupID' => $HostingDomainPolitic['DomainsSchemesGroupID'],
                        'YearsReserved'         => 1,
                        'OperationID'           => 'Order',
                        'Discont'               => $HostingDomainPolitic['Discont'],
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
		                  'UserID'	=> $HostingOrder['UserID'],
				  'PriorityID'	=> 'Billing',
				  'Text'	=> SPrintF('Заказ хостинга тариф (%s), логин (%s), домен (%s) успешно оплачен на период %u дн.',$HostingOrder['SchemeName'],$HostingOrder['Login'],$HostingOrder['Domain'],$DaysPay)
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
