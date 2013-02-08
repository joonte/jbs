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
$DomainOrderID	= (integer) @$Args['DomainOrderID'];
$YearsPay	= (integer) @$Args['YearsPay'];
$IsNoBasket	= (boolean) @$Args['IsNoBasket'];
$PayMessage	=  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','ContractID','OrderID','UserID','DomainName','ExpirationDate','AuthInfo','StatusID','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `DomainsOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `DomainsOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT `Balance` FROM `Contracts` WHERE `DomainsOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`');
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DOMAIN_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$DomainOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DomainsOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
        $StatusID = $DomainOrder['StatusID'];
        #-----------------------------------------------------------------------
#        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
#          return ($StatusID != 'OnTransfer'?new gException('DOMAIN_ORDER_CAN_NOT_PAY','Заказ домена не может быть оплачен'):new gException('DOMAIN_ORDER_ON_TRANSFERING','Заказ не может быть оплачен до завершения ручной обработки оператором, осуществляющим перенос Вашего домена'));
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended','ForTransfer')))
          return new gException('ORDER_CAN_NOT_PAY','Заказ домена не может быть оплачен');
        #-----------------------------------------------------------------------
        $DomainScheme = DB_Select('DomainsSchemes','*',Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($DomainScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $ExpirationDate = $DomainOrder['ExpirationDate'];
            #-------------------------------------------------------------------
            if($IsPayed = $DomainOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$DomainScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа домена не позволяет продление');
              #-----------------------------------------------------------------
              if($YearsPay < $DomainScheme['MinOrderYears'])
                return new gException('YEARS_PAY_MIN_ORDER_YEARS','Кол-во лет оплаты меньше, чем допустимое значение лет заказа, определённое в тарифном плане');
              #-----------------------------------------------------------------
              if($YearsPay > $DomainScheme['MaxActionYears'])
                return new gException('YEARS_PAY_MAX_ACTION_YEARS','Кол-во лет оплаты больше, чем допустимое значение, определённое в тарифном плане');
            }else{
              #-----------------------------------------------------------------
              $YearsRemainder = Date('Y',$ExpirationDate) - Date('Y') - 1;
              #-----------------------------------------------------------------
              if($YearsRemainder >= $DomainScheme['MaxActionYears'])
                return new gException('DOMAIN_ORDER_ON_MAX_YEARS','Доменное имя уже зарегистрировано на максимальное кол-во лет');
            }
            #--------------------------TRANSACTION------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DomainOrderID = (integer)$DomainOrder['ID'];
            #-------------------------------------------------------------------
            $Columns = Array('(SELECT `SchemeID` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID` = `Basket`.`OrderID`) as `SchemeID`','Amount');
            #-------------------------------------------------------------------
            $IsUseBasket = FALSE;
            #-------------------------------------------------------------------
            $Basket = DB_Select('Basket',$Columns,Array('Where'=>SPrintF('(SELECT `ServiceID` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) = 10000 AND (SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) = %u',$DomainOrder['ContractID'])));
            #-------------------------------------------------------------------
            switch(ValueOf($Basket)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                # No more...
              break;
              case 'array':
                #---------------------------------------------------------------
                $Entrance = Tree_Path('Groups',(integer)$DomainOrder['GroupID']);
                #---------------------------------------------------------------
                switch(ValueOf($Entrance)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    foreach($Basket as $Order){
                      #---------------------------------------------------------
                      $HostingDomainPolitic = DB_Select('HostingDomainsPolitics','*',Array('IsDesc'=>TRUE,'SortOn'=>'DaysPay','Where'=>SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR `SchemeID` IS NULL) AND `DaysPay` <= %u AND EXISTS(SELECT * FROM `DomainsSchemesGroupsItems` WHERE `DomainsSchemesGroupsItems`.`DomainsSchemesGroupID` = `DomainsSchemesGroupID` AND `SchemeID` = %u)',Implode(',',$Entrance),$DomainOrder['UserID'],$Order['SchemeID'],$Order['Amount'],$DomainOrder['SchemeID'])));
                      #---------------------------------------------------------
                      switch(ValueOf($HostingDomainPolitic)){
                        case 'error':
                          return ERROR | @Trigger_Error(500);
                        case 'exception':
                          # No more...
                        break;
                        case 'array':
                          #-----------------------------------------------------
                          $HostingDomainPolitic = Current($HostingDomainPolitic);
                          #-----------------------------------------------------
                          $IDomainBonus = Array(
                            #---------------------------------------------------
                            'UserID'        => $DomainOrder['UserID'],
                            'SchemeID'      => $DomainOrder['SchemeID'],
                            'YearsReserved' => 1,
                            'OperationID'   => 'Order',
                            'Discont'       => $HostingDomainPolitic['Discont'],
                            'Comment'       => 'Назначен доменной политикой'
                          );
                          #-----------------------------------------------------
                          $IsInsert = DB_Insert('DomainsBonuses',$IDomainBonus);
                          if(Is_Error($IsInsert))
                            return ERROR | @Trigger_Error(500);
                          #-----------------------------------------------------
                          $IsUseBasket = TRUE;
                        break;
                        default:
                          return ERROR | @Trigger_Error(101);
                      }
                    }
                    #-----------------------------------------------------------
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
            $YearsRemainded = $YearsPay;
	    #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
#            $Comp = Comp_Load('Services/Bonuses',$YearsRemainded,20000,$DomainScheme['ID'],$UserID,$CostPay,$DomainScheme[(!$IsPayed && $YearsPay - $YearsRemainded < $DomainScheme['MinOrderYears']?'CostOrder':'CostProlong')],$DomainOrderID);
#            if(Is_Error($Comp))
#              return ERROR | @Trigger_Error(500);
#            #-----------------------------------------------------------------
#            $CostPay = $Comp['CostPay'];
#            $Bonuses = $Comp['Bonuses'];

            #-------------------------------------------------------------------
            while($YearsRemainded){
              #-----------------------------------------------------------------
	      if($StatusID == 'ForTransfer'){
                $CurrentCost = $DomainScheme['CostTransfer'];
              }else{
                $CurrentCost = $DomainScheme[(!$IsPayed && $YearsPay - $YearsRemainded < $DomainScheme['MinOrderYears']?'CostOrder':'CostProlong')];
	      }
              #-----------------------------------------------------------------
              $IDomainsConsider = Array('DomainOrderID'=>$DomainOrderID,'Cost'=>$CurrentCost);
              #-----------------------------------------------------------------
              $Where = SPrintF("`UserID` = %u AND ((`SchemeID` = %u OR %u IN (SELECT `SchemeID` FROM `DomainsSchemesGroupsItems` WHERE `DomainsSchemesGroupsItems`.`DomainsSchemesGroupID` = `DomainsBonuses`.`DomainsSchemesGroupID`)) OR ISNULL(`SchemeID`) AND ISNULL(`DomainsSchemesGroupID`)) AND `YearsRemainded` > 0",$UserID,$DomainScheme['ID'],$DomainScheme['ID']);
              #-----------------------------------------------------------------
              $DomainBonus = DB_Select('DomainsBonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
              #-----------------------------------------------------------------
              switch(ValueOf($DomainBonus)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  #-------------------------------------------------------------
                  $CostPay += $YearsRemainded*$CurrentCost;
                  #-------------------------------------------------------------
                  $IDomainsConsider['YearsReserved'] = $YearsRemainded;
                  #-------------------------------------------------------------
                  $YearsRemainded = 0;
                break;
                case 'array':
                  #-------------------------------------------------------------
                  $DomainBonus = Current($DomainBonus);
                  #-------------------------------------------------------------
                  $Discont = (1 - $DomainBonus['Discont']);
                  #-------------------------------------------------------------
                  $IDomainsConsider['Discont'] = $DomainBonus['Discont'];
                  #-------------------------------------------------------------
                  if($DomainBonus['YearsRemainded'] - $YearsRemainded < 0){
                    #-----------------------------------------------------------
                    $CostPay += $DomainBonus['YearsRemainded']*$CurrentCost*$Discont;
                    #-----------------------------------------------------------
                    $IDomainsConsider['YearsReserved'] = $DomainBonus['YearsRemainded'];
                    #-----------------------------------------------------------
                    $UDomainBonus = Array('YearsRemainded'=>0);
                    #-----------------------------------------------------------
                    $YearsRemainded -= $DomainBonus['YearsRemainded'];
                  }else{
                    #-----------------------------------------------------------
                    $CostPay += $YearsRemainded*$CurrentCost*$Discont;
                    #-----------------------------------------------------------
                    $IDomainsConsider['YearsReserved'] = $YearsRemainded;
                    #-----------------------------------------------------------
                    $UDomainBonus = Array('YearsRemainded'=>$DomainBonus['YearsRemainded'] - $YearsRemainded);
                    #-----------------------------------------------------------
                    $YearsRemainded = 0;
                  }
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('DomainsBonuses',$UDomainBonus,Array('ID'=>$DomainBonus['ID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              $IsInsert = DB_Insert('DomainsConsider',$IDomainsConsider);
              if(Is_Error($IsInsert))
                return ERROR | @Trigger_Error(500);
            }
            #-------------------------------------------------------------------
            $CostPay = Round($CostPay,2);
            #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
	    # added by lissyara, 2012-01-30 in 12:22 MSK, as part of JBS-18
	    Debug(SPrintF("[comp/www/API/DomainOrderPay]: Domain = %s.%s; CostPay = %s; ContractBalance = %s",$DomainOrder['DomainName'],$DomainScheme['Name'],$CostPay,$DomainOrder['ContractBalance']));
            #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
            #if($IsUseBasket || $CostPay > $DomainOrder['ContractBalance']){
	    if((!$IsNoBasket && $CostPay > $DomainOrder['ContractBalance']) && ($IsUseBasket || $CostPay > $DomainOrder['ContractBalance'])){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IBasket = Array('OrderID'=>$DomainOrder['OrderID'],'Comment'=>SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainScheme['Name']),'Amount'=>$YearsPay,'Summ'=>$CostPay);
              #-----------------------------------------------------------------
              $Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$DomainOrder['OrderID'])));
              if(Is_Error($Count))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($Count){
                #---------------------------------------------------------------
                $IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$DomainOrder['OrderID'])));
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }else{
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('Basket',$IBasket);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Basket/Update',$DomainOrder['UserID'],$DomainOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              return Array('Status'=>'UseBasket');
            }else{
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Order/Number',$DomainOrder['OrderID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $DomainOrder['Number'] = $Comp;
              #-----------------------------------------------------------------
              $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$DomainOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>20000,'Comment'=>SPrintF('№%s на %s лет.',$Comp,$YearsPay)));
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
                  $IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$DomainOrder['OrderID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
		  if(!$PayMessage)
                    $PayMessage = "Заказ успешно оплачен";
		  #-------------------------------------------------------------
		  #-------------------------------------------------------------
		  $NewStatusID = 'ForProlong';
		  #-------------------------------------------------------------
		  if($StatusID == 'Waiting')
		    $NewStatusID = 'ClaimForRegister';
		  #-------------------------------------------------------------
		  if($StatusID == 'ForTransfer')
		    $NewStatusID = 'OnTransfer';
                  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>$NewStatusID,'RowsIDs'=>$DomainOrder['ID'],'Comment'=>$PayMessage));
                  #-------------------------------------------------------------
                  switch(ValueOf($Comp)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'array':
                      #---------------------------------------------------------
		      $Event = Array(
		                       'UserID'		=> $DomainOrder['UserID'],
				       'PriorityID'	=> 'Billing',
				       'Text'		=> SPrintF('Заказ домена (%s.%s) успешно оплачен на период %u лет',$DomainOrder['DomainName'],$DomainScheme['Name'],$YearsPay)
				     );
                      $Event = Comp_Load('Events/EventInsert',$Event);
                      if(!$Event)
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      if(Is_Error(DB_Commit($TransactionID)))
                        return ERROR | @Trigger_Error(500);
                      #---------------------END TRANSACTION---------------------
                      return Array('Status'=>'Ok');
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
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
