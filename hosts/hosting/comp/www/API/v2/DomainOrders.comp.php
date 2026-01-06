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
$OrderID	= (integer) @$Args['OrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/Net_IDNA.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// список колонок которые юзеру не показываем
$Config = Config();
#-------------------------------------------------------------------------------
$Exclude = Array_Keys($Config['APIv2ExcludeColumns']);
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['Orders']['Domain']['Prolong'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
if($OrderID > 0)
	$Where[] = SPrintF('`OrderID` = %s',$OrderID);
#-------------------------------------------------------------------------------
$Columns = Array(
		'*',
		'(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `DomainZone`',
		'(SELECT `CostProlong` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `CostProlong`',
		'CONCAT(`Ns1Name`,",",`Ns2Name`,",",`Ns3Name`,",",`Ns4Name`) AS `DNSs`',        // DNS for JBS-1337
		'(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`)) as `Params`',
		'(SELECT `IsAutoProlong` FROM `Orders` WHERE `DomainOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `IsAutoProlong`',
		'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DomainOrdersOwners`.`OrderID`) AS `UserNotice`',
		'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DomainOrdersOwners`.`OrderID`) AS `AdminNotice`',
		'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `DomainOrdersOwners`.`ContractID`) AS `Customer`',
		'(SELECT `TypeID` FROM `Contracts` WHERE `DomainOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractTypeID`',
		'(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `DomainOrdersOwners`.`OrderID`) as `IsPayed`',
		);
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Out;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($DomainOrders as $DomainOrder){
	#-------------------------------------------------------------------------------
	$AdditionalCost = 0;
	#-------------------------------------------------------------------------------
	$DomainOrder['DNSs'] = Trim($DomainOrder['DNSs'],',');
	#-------------------------------------------------------------------------------
	if($Settings['ExternalDnsMarkUp'] > 0 && (!$Settings['JuridicalOnly'] || In_Array($DomainOrder['ContractTypeID'],Array('Juridical','Individual')))){
		#-------------------------------------------------------------------------------
		// составляем список ДНС серверов, заданных в общих настройках
		$ExternalDnsList = Explode(',',$Settings['ExternalDnsList']);
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
			Debug(SPrintF('[comp/www/API/v2/DomainOrders]: проверка DNS: %s',$DNS));
			#-------------------------------------------------------------------------------
			if(!In_Array($DNS,$ExternalDnsList)){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/www/API/v2/DomainOrders]: DNS (%s) not in list (%s)',$DNS,Implode(',',$ExternalDnsList)));
				#-------------------------------------------------------------------------------
				$AdditionalCost = (double) $Settings['ExternalDnsMarkUp'];
				#-------------------------------------------------------------------------------
				$Message = SPrintF($Settings['ExternalDnsMessage'],$Settings['ExternalDnsMarkUp']);
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
	$DomainOrder['Message'] = Str_Replace('%CostProlong%',$DomainOrder['CostProlong'],$DomainOrder['Params']['Message']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	UnSet($DomainOrder['Params']);
	UnSet($DomainOrder['CostProlong']);
	#-------------------------------------------------------------------------------
	// выпиливаем колонки
	foreach(Array_Keys($DomainOrder) as $Column)
		if(In_Array($Column,$Exclude))
			UnSet($DomainOrder[$Column]);
	#-------------------------------------------------------------------------------
	// полное имя домена
	$DomainOrder['Domain'] = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone']);
	#-------------------------------------------------------------------------------
	// дополнительная стоимость для не-наших ДНС
	if($AdditionalCost)
		$DomainOrder['AdditionalCost'] = Array('Summ'=>$AdditionalCost,'Message'=>$Message);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// дата окончания
	$ExpirationDate = Max($DomainOrder['ExpirationDate'],Time());
	#-------------------------------------------------------------------------------
	$DomainOrder['DaysRemainded'] = Ceil(($ExpirationDate-Time())/86400);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// кириллические домены
	$IDNA = new Net_IDNA();
	#-------------------------------------------------------------------------------
	$DomainOrder['IDNA_Domain'] = $IDNA->encode($DomainOrder['Domain']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Out[$DomainOrder['ID']] = $DomainOrder;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return ($OrderID > 0)?Current($Out):$Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

