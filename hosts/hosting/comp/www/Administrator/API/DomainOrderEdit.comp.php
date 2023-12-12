<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$DomainOrderID  = (integer) @$Args['DomainOrderID'];
$ContractID     = (integer) @$Args['ContractID'];
$DomainName     =  (string) @$Args['DomainName'];
$SchemeID       = (integer) @$Args['SchemeID'];
$ExpirationDate =  (string) @$Args['ExpirationDate'];
$PersonID       =  (string) @$Args['PersonID'];
$Ns1Name        =  (string) @$Args['Ns1Name'];
$Ns1IP          =  (string) @$Args['Ns1IP'];
$Ns2Name        =  (string) @$Args['Ns2Name'];
$Ns2IP          =  (string) @$Args['Ns2IP'];
$Ns3Name        =  (string) @$Args['Ns3Name'];
$Ns3IP          =  (string) @$Args['Ns3IP'];
$Ns4Name        =  (string) @$Args['Ns4Name'];
$Ns4IP          =  (string) @$Args['Ns4IP'];
$DependOrderID	= (integer) @$Args['DependOrderID'];
#-------------------------------------------------------------------------------
if(!$ContractID)
	return new gException('CONTRACT_NOT_FILLED','Договор клиента не указан');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DomainOrderID){
	#-------------------------------------------------------------------------------
	# ищщем старый контракт - сравниваем номерки
	$OldContractID = DB_Select('DomainOrders','(SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID`=`DomainOrders`.`OrderID`) AS `ContractID`',Array('UNIQ','ID'=>$DomainOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($OldContractID)){
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
	if($OldContractID['ContractID'] != $ContractID){
		#-------------------------------------------------------------------------------
		#return new gException('CONTRACT_DOES_NOT_MATCH','Договора не совпадают');
		# проверяем есть ли профиль у нового контракта
		$Count = DB_Count('Contracts',Array('Where'=>SPrintF('`ID` = %u AND `ProfileID` IS NOT NULL',$ContractID)));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!$Count)
			return new gException('CONTRACT_WITHOUT_PROFILE','У выбранного договора отсутствует профиль. Выберите другой договор, или, пусть клиент создаст и назначит профиль для этого договора.');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['DomainName'],$DomainName))
	return new gException('WRONG_DOMAIN_NAME','Неверное имя домена');
#-------------------------------------------------------------------------------
$DomainScheme = DB_Select('DomainSchemes','*',Array('UNIQ','ID'=>$SchemeID));
switch(ValueOf($DomainScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('DOMAIN_SCHEME_NOT_FOUND','Тарифный план на домен не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($PersonID)
	if(!Preg_Match($Regulars['Char'],$PersonID))
		return new gException('WRONG_PERSON_ID','Договор регистратора указан не верно');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Ns1Name = Trim(Mb_StrToLower($Ns1Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if($Ns1Name && !Preg_Match($Regulars['Domain'],$Ns1Name))
	return new gException('WRONG_NAME_NS1','Неверное имя первого сервера имен');
#-------------------------------------------------------------------------------
if($Ns1IP && !Preg_Match($Regulars['IP'],$Ns1IP))
	return new gException('WRONG_IP_NS1','Неверный IP адрес первого сервера имен');
#-------------------------------------------------------------------------------
$Ns2Name = Trim(Mb_StrToLower($Ns2Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if($Ns2Name && !Preg_Match($Regulars['Domain'],$Ns2Name))
	return new gException('WRONG_NAME_NS2','Неверное имя второго сервера имен');
#-------------------------------------------------------------------------------
if($Ns2IP && !Preg_Match($Regulars['IP'],$Ns2IP))
	return new gException('WRONG_IP_NS2','Неверный IP адрес второго сервера имен');
#-------------------------------------------------------------------------------
$Ns3Name = Trim(Mb_StrToLower($Ns3Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if($Ns3Name && !Preg_Match($Regulars['Domain'],$Ns3Name))
	return new gException('WRONG_NAME_NS3','Неверное имя дополнительного сервера имен');
#-------------------------------------------------------------------------------
if($Ns3IP && !Preg_Match($Regulars['IP'],$Ns3IP))
	return new gException('WRONG_IP_NS3','Неверный IP адрес дополнительного сервера имен');
#-------------------------------------------------------------------------------
$Ns4Name = Trim(Mb_StrToLower($Ns4Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if($Ns4Name && !Preg_Match($Regulars['Domain'],$Ns4Name))
	return new gException('WRONG_NAME_NS3','Неверное имя расширенного сервера имен');
#-------------------------------------------------------------------------------
if($Ns4IP && !Preg_Match($Regulars['IP'],$Ns4IP))
	return new gException('WRONG_IP_NS3','Неверный IP адрес расширенного сервера имен');
#-------------------------------------------------------------------------------
#-----------------------------TRANSACTION---------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainOrderEdit'))))
        return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UDomainOrder = Array(
			'DomainName'     => $DomainName,
			'SchemeID'       => $SchemeID,
			'ExpirationDate' => $ExpirationDate,
			'PersonID'       => $PersonID,
			'Ns1Name'        => $Ns1Name,
			'Ns1IP'          => $Ns1IP,
			'Ns2Name'        => $Ns2Name,
			'Ns2IP'          => $Ns2IP,
			'Ns3Name'        => $Ns3Name,
			'Ns3IP'          => $Ns3IP,
			'Ns4Name'        => $Ns4Name,
			'Ns4IP'          => $Ns4IP
			);
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('DomainOrders',$UDomainOrder,Array('ID'=>$DomainOrderID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrders','OrderID',Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
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
$IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$DomainScheme['ServerID'],'DependOrderID'=>$DependOrderID),Array('ID'=>$DomainOrder['OrderID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
        return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
