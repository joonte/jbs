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
$IsUseBasket    = (boolean) @$Args['IsUseBasket'];
$PayMessage	=  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# реализация JBS-1047: проверяем не продлён ли домен до этого напрямую у регистратора
$Comp = Comp_Load('www/Administrator/API/DomainOrderWhoIsUpdate',Array('DomainOrderID'=>$DomainOrderID));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','ContractID','OrderID','ServiceID','UserID','DomainName','ExpirationDate','AuthInfo','StatusID','SchemeID',
		'CONCAT(`Ns1Name`,",",`Ns2Name`,",",`Ns3Name`,",",`Ns4Name`) AS `DNSs`',	// DNS for JBS-1337
		'(SELECT `GroupID` FROM `Users` WHERE `DomainOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`',
		'(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `DomainOrdersOwners`.`OrderID`) as `IsPayed`',
		'(SELECT `Balance` FROM `Contracts` WHERE `DomainOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`',
		'(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `DomainOrdersOwners`.`ServerID`) AS `Params`'
		);
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('DOMAIN_ORDER_NOT_FOUND','Выбранный заказ не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UserID = (integer)$DomainOrder['UserID'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('DomainOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['Orders']['Domain']['Prolong'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$StatusID = $DomainOrder['StatusID'];
#-------------------------------------------------------------------------------
if(!In_Array($StatusID,Array('Waiting','Active','Suspended','ForTransfer')))
	return new gException('ORDER_CAN_NOT_PAY','Заказ домена не может быть оплачен');
#-------------------------------------------------------------------------------
$DomainScheme = DB_Select('DomainSchemes','*',Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# JBS-1101
$IsPayed = $DomainOrder['IsPayed'];
#-------------------------------------------------------------------------------
if(!$IsPayed)
	$DomainScheme['MaxActionYears'] = 1;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ExpirationDate = $DomainOrder['ExpirationDate'];
#-------------------------------------------------------------------------------
if($IsPayed){
	#-------------------------------------------------------------------------------
	if(!$DomainScheme['IsProlong'])
		return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа домена не позволяет продление');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DaysToProlong = $DomainScheme['DaysToProlong'];
	#-------------------------------------------------------------------------------
	if(($ExpirationDate - Time())/86400 > $DaysToProlong && $StatusID != 'ForTransfer')
		return new gException('PROLONG_IS_EARLY',SPrintF('Заказ домена может быть продлен только за %u дн. до окончания',$DaysToProlong));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($YearsPay < $DomainScheme['MinOrderYears'])
		return new gException('YEARS_PAY_MIN_ORDER_YEARS','Кол-во лет оплаты меньше, чем допустимое значение лет заказа, определённое в тарифном плане');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($YearsPay > $DomainScheme['MaxActionYears'])
		return new gException('YEARS_PAY_MAX_ACTION_YEARS','Кол-во лет оплаты больше, чем допустимое значение, определённое в тарифном плане');
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$YearsRemainder = Date('Y',$ExpirationDate) - Date('Y') - 1;
	#-------------------------------------------------------------------------------
	if($YearsRemainder >= $DomainScheme['MaxActionYears'])
		return new gException('DOMAIN_ORDER_ON_MAX_YEARS','Доменное имя уже зарегистрировано на максимальное кол-во лет');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#--------------------------TRANSACTION------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainOrderPay'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainOrderID = (integer)$DomainOrder['ID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(FALSE){
            #-------------------------------------------------------------------
            $Columns = Array('(SELECT `SchemeID` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID` = `Basket`.`OrderID`) as `SchemeID`','Amount');
            #-------------------------------------------------------------------
#            $IsUseBasket = FALSE;
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
			# HostingDomainPolitics deleted
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
#            $Comp = Comp_Load('Services/Bonuses',$YearsRemainded,$DomainOrder['ServiceID'],$DomainScheme['ID'],$UserID,$CostPay,$DomainScheme[(!$IsPayed && $YearsPay - $YearsRemainded < $DomainScheme['MinOrderYears']?'CostOrder':'CostProlong')],$DomainOrderID);
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
              $IDomainConsider = Array('DomainOrderID'=>$DomainOrderID,'Cost'=>$CurrentCost);
              #-----------------------------------------------------------------
              $Where = SPrintF("`UserID` = %u AND ((`SchemeID` = %u OR %u IN (SELECT `SchemeID` FROM `DomainSchemesGroupsItems` WHERE `DomainSchemesGroupsItems`.`DomainSchemesGroupID` = `DomainBonuses`.`DomainSchemesGroupID`)) OR ISNULL(`SchemeID`) AND ISNULL(`DomainSchemesGroupID`)) AND `YearsRemainded` > 0",$UserID,$DomainScheme['ID'],$DomainScheme['ID']);
              #-----------------------------------------------------------------
              $DomainBonus = DB_Select('DomainBonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
              #-----------------------------------------------------------------
              switch(ValueOf($DomainBonus)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  #-------------------------------------------------------------
                  $CostPay += $YearsRemainded*$CurrentCost;
                  #-------------------------------------------------------------
                  $IDomainConsider['YearsReserved'] = $YearsRemainded;
                  #-------------------------------------------------------------
                  $YearsRemainded = 0;
                break;
                case 'array':
                  #-------------------------------------------------------------
                  $DomainBonus = Current($DomainBonus);
                  #-------------------------------------------------------------
                  $Discont = (1 - $DomainBonus['Discont']);
                  #-------------------------------------------------------------
                  $IDomainConsider['Discont'] = $DomainBonus['Discont'];
                  #-------------------------------------------------------------
                  if($DomainBonus['YearsRemainded'] - $YearsRemainded < 0){
                    #-----------------------------------------------------------
                    $CostPay += $DomainBonus['YearsRemainded']*$CurrentCost*$Discont;
                    #-----------------------------------------------------------
                    $IDomainConsider['YearsReserved'] = $DomainBonus['YearsRemainded'];
                    #-----------------------------------------------------------
                    $UDomainBonus = Array('YearsRemainded'=>0);
                    #-----------------------------------------------------------
                    $YearsRemainded -= $DomainBonus['YearsRemainded'];
                  }else{
                    #-----------------------------------------------------------
                    $CostPay += $YearsRemainded*$CurrentCost*$Discont;
                    #-----------------------------------------------------------
                    $IDomainConsider['YearsReserved'] = $YearsRemainded;
                    #-----------------------------------------------------------
                    $UDomainBonus = Array('YearsRemainded'=>$DomainBonus['YearsRemainded'] - $YearsRemainded);
                    #-----------------------------------------------------------
                    $YearsRemainded = 0;
                  }
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('DomainBonuses',$UDomainBonus,Array('ID'=>$DomainBonus['ID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              $IsInsert = DB_Insert('DomainConsider',$IDomainConsider);
              if(Is_Error($IsInsert))
                return ERROR | @Trigger_Error(500);
            }


}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// начальная стоимость - либо ноль, либо наценка за использование не-наших ДНС серверов
$CostPay = 0.00;
#-------------------------------------------------------------------------------
if($Settings['ExternalDnsMarkUp'] > 0){
	#-------------------------------------------------------------------------------
	// составляем список ДНС серверов, заданных в общих настройках
	$ExternalDnsList = Explode(',',StrToLower($Settings['ExternalDnsList']));
	#-------------------------------------------------------------------------------
	if($DomainOrder['Params']['Ns1Name'])
		$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns1Name']);
	#-------------------------------------------------------------------------------
	if($DomainOrder['Params']['Ns2Name'])
		$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns2Name']);
	#-------------------------------------------------------------------------------
	if($DomainOrder['Params']['Ns3Name'])
		$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns3Name']);
	#-------------------------------------------------------------------------------
	if($DomainOrder['Params']['Ns4Name'])
		$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns4Name']);
	#-------------------------------------------------------------------------------
	// перебираем ДНС сервера установленные для этого домена
	foreach(Explode(',',StrToLower($DomainOrder['DNSs'])) as $DNS){
		#-------------------------------------------------------------------------------
		if(!$DNS)
			continue;
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/DomainOrderPay]: проверка DNS: %s',$DNS));
		#-------------------------------------------------------------------------------
		if(!In_Array($DNS,$ExternalDnsList)){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/DomainOrderPay]: DNS (%s) not in list (%s)',$DNS,Implode(',',$ExternalDnsList)));
			#-------------------------------------------------------------------------------
			$CostPay = (double) $Settings['ExternalDnsMarkUp'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$YearsRemainded = $YearsPay;
#-------------------------------------------------------------------------------
while($YearsRemainded){
	#-------------------------------------------------------------------------------
	if($StatusID == 'ForTransfer'){
		#-------------------------------------------------------------------------------
		$CurrentCost = $DomainScheme['CostTransfer'];
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$CurrentCost = $DomainScheme[(!$IsPayed && $YearsPay - $YearsRemainded < $DomainScheme['MinOrderYears']?'CostOrder':'CostProlong')];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$IDomainConsider = Array('DomainOrderID'=>$DomainOrderID,'Cost'=>$CurrentCost);
	#-------------------------------------------------------------------------------
	$CostPay += $YearsRemainded*$CurrentCost;
	#-------------------------------------------------------------------------------
	$IDomainConsider['YearsReserved'] = $YearsRemainded;
	#-------------------------------------------------------------------------------
	$YearsRemainded = 0;
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('DomainConsider',$IDomainConsider);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$CostPay = Round($CostPay,2);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara, 2012-01-30 in 12:22 MSK, as part of JBS-18
Debug(SPrintF("[comp/www/API/DomainOrderPay]: Domain = %s.%s; CostPay = %s; ContractBalance = %s",$DomainOrder['DomainName'],$DomainScheme['Name'],$CostPay,$DomainOrder['ContractBalance']));
#-------------------------------------------------------------------------------
#if($IsUseBasket || $CostPay > $DomainOrder['ContractBalance']){
#if((!$IsNoBasket && $CostPay > $DomainOrder['ContractBalance']) && ($IsUseBasket || $CostPay > $DomainOrder['ContractBalance'])){
if($IsUseBasket || (!$IsNoBasket && $CostPay > $DomainOrder['ContractBalance'])){
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Roll($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IBasket = Array('OrderID'=>$DomainOrder['OrderID'],'Comment'=>SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainScheme['Name']),'Amount'=>$YearsPay,'Summ'=>$CostPay);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$DomainOrder['OrderID'])));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count){
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$DomainOrder['OrderID'])));
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Basket',$IBasket);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Basket/Update',$DomainOrder['UserID'],$DomainOrder['OrderID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return Array('Status'=>'UseBasket');
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Order/Number',$DomainOrder['OrderID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$DomainOrder['Number'] = $Comp;
	#-------------------------------------------------------------------------------
	$IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$DomainOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>$DomainOrder['ServiceID'],'Comment'=>SPrintF('№%s на %s лет.',$Comp,$YearsPay)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsUpdate)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Roll($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return $IsUpdate;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$DomainOrder['OrderID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$NewStatusID = 'ForProlong';
		#-------------------------------------------------------------------------------
		if($StatusID == 'Waiting')
			$NewStatusID = 'ClaimForRegister';
		#-------------------------------------------------------------------------------
		if($StatusID == 'ForTransfer')
			$NewStatusID = 'OnTransfer';
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>$NewStatusID,'RowsIDs'=>$DomainOrder['ID'],'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Comp)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			$Text = SPrintF('Заказ домена (%s.%s) оплачен на период %u лет',$DomainOrder['DomainName'],$DomainScheme['Name'],$YearsPay);
			#-------------------------------------------------------------------------------
			if($StatusID == 'ForTransfer')
				$Text = SPrintF('Заявка на перенос домена (%s.%s) оплачена',$DomainOrder['DomainName'],$DomainScheme['Name']);
			#-------------------------------------------------------------------------------
			$Event = Array('UserID'=>$DomainOrder['UserID'],'PriorityID'=>'Billing','Text'=>$Text);
			#-------------------------------------------------------------------------------
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if(Is_Error(DB_Commit($TransactionID)))
				return ERROR | @Trigger_Error(500);
			#---------------------END TRANSACTION-------------------------------------------
			#-------------------------------------------------------------------------------
			return Array('Status'=>'Ok');
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
