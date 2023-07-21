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
#-------------------------------------------------------------------------------
$DomainName     =  (string) @$Args['DomainName'];
$DomainSchemeID = (integer) @$Args['DomainSchemeID'];
$ContractID     = (integer) @$Args['ContractID'];
$HostingOrderID = (integer) @$Args['HostingOrderID'];
$IsPrivateWhoIs = (boolean) @$Args['IsPrivateWhoIs'];
$Comment	=  (string) @$Args['Comment'];
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
if(Is_Error(System_Load('modules/Authorisation.mod','libs/WhoIs.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ContractID)
	return new gException('CONTRACT_NOT_DEFINED','Не выбран договор');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainName = Mb_StrToLower($DomainName,'UTF-8');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Ns1Name)
	if(Mb_StrToLower($Ns1Name,'UTF-8') == Mb_StrToLower($Ns2Name,'UTF-8'))
		return new gException('DNS_SERVERS_CANNOT_BE_EQUAL','Имена DNS серверов должны быть разными');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Ns1IP)
	if($Ns1IP == $Ns2IP)
		return new gException('IP_DNS_SERVERS_CANNOT_BE_EQUAL','IP адреса DNS серверов должны быть разными');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Mb_StrToLower($Ns1Name,'UTF-8') == $DomainName || Mb_StrToLower($Ns2Name,'UTF-8') == $DomainName || Mb_StrToLower($Ns3Name,'UTF-8') == $DomainName || Mb_StrToLower($Ns4Name,'UTF-8') == $DomainName)
	return new gException('NS_HOSTNAME_CANT_BE_EQUAL_TO_DOMAIN','Имя DNS сервера не может совпадать с именем домена');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainScheme = DB_Select('DomainSchemes',Array('ID','Name','ServerID','IsActive'),Array('UNIQ','ID'=>$DomainSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('DOMAIN_SCHEME_NOT_FOUND','Выбранный тарифный план не найден');
case 'array':
	break;	
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainZone = Comp_Load('Formats/DomainOrder/DomainZones',$DomainScheme['Name'],FALSE);
if(Is_Error($DomainZone))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!Preg_Match($DomainZone['Regular'],$DomainName))
	return new gException('WRONG_DOMAIN_NAME','Неверное имя домена');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DomainScheme['IsActive'])
	return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа домена не активен');
#-------------------------------------------------------------------------------
$Count = DB_Count('DomainOrdersOwners',Array('Where'=>SPrintF("`DomainName` = '%s' AND (SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) = '%s' AND `UserID` = %u AND `StatusID` != 'Deleted'",$DomainName,$DomainScheme['Name'],$GLOBALS['__USER']['ID'])));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('DOMAIN_ORDER_EXISTS','Домен уже находится в вашем списке заказов');
#-------------------------------------------------------------------------------
$Count = DB_Count('DomainOrdersOwners',Array('Where'=>SPrintF("`DomainName` = '%s' AND (SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) = '%s' AND `StatusID` != 'Deleted' AND `StatusID` != 'Waiting'",$DomainName,$DomainScheme['Name'])));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('DOMAIN_ORDER_EXISTS','Домен уже находится в списке заказов другого пользователя');
#-------------------------------------------------------------------------------
$IsCheck = WhoIs_Check($DomainName,$DomainScheme['Name']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsCheck)){
case 'exception':
	return $IsCheck;
case 'array':
	return new gException('DOMAIN_IS_BORROWED','Выбранный Вами домен уже занят. Выберите другое имя.');
case 'error':
	# No more...
case 'false':
	# No more...
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Domain = SPrintF('%s.%s',$DomainName,$DomainScheme['Name']);
#-------------------------------------------------------------------------------
if($HostingOrderID){
	#-------------------------------------------------------------------------------
	$Columns = Array('ID','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `ServerID`) as `Params`');
	#-------------------------------------------------------------------------------
	$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_ORDER_NOT_FOUND','Заказ хостинга не найден');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Ns1Name = $HostingOrder['Params']['Ns1Name'];
	$Ns2Name = $HostingOrder['Params']['Ns2Name'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Ns1Name = Trim(Mb_StrToLower($Ns1Name,'UTF-8'),'.');
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['Domain'],$Ns1Name))
		return new gException('WRONG_NAME_NS1','Неверное имя первого сервера имен');
	#-------------------------------------------------------------------------------
	if(Mb_SubStr($Ns1Name,-Mb_StrLen($Domain)) == $Domain){
		#-------------------------------------------------------------------------------
		if(!Preg_Match($Regulars['IP'],$Ns1IP))
			return new gException('WRONG_IP_NS1','Неверный IP адрес первого сервера имен');
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		if($Ns1IP)
			return new gException('IP_NS1_CAN_NOT_FILL','IP адрес первого сервера имен не может быть указан');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Ns2Name = Trim(Mb_StrToLower($Ns2Name,'UTF-8'),'.');
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['Domain'],$Ns1Name))
		return new gException('WRONG_NAME_NS2','Неверное имя второго сервера имен');
	#-------------------------------------------------------------------------------
	if(Mb_SubStr($Ns2Name,-Mb_StrLen($Domain)) == $Domain){
		#-------------------------------------------------------------------------------
		if(!Preg_Match($Regulars['IP'],$Ns2IP))
			return new gException('WRONG_IP_NS2','Неверный IP адрес второго сервера имен');
			#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		if($Ns2IP)
			return new gException('IP_NS2_CAN_NOT_FILL','IP адрес второго сервера имен не может быть указан');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Ns3Name = Trim(Mb_StrToLower($Ns3Name,'UTF-8'),'.');
	#-------------------------------------------------------------------------------
	if($Ns3Name){
		#-------------------------------------------------------------------------------
		if(!Preg_Match($Regulars['Domain'],$Ns3Name))
			return new gException('WRONG_NAME_NS3','Неверное имя дополнительного сервера имен');
		#-------------------------------------------------------------------------------
		if(Mb_SubStr($Ns3Name,-Mb_StrLen($Domain)) == $Domain){
			#-------------------------------------------------------------------------------
			if(!Preg_Match($Regulars['IP'],$Ns3IP))
				return new gException('WRONG_IP_NS3','Неверный IP адрес дополнительного сервера имен');
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			if($Ns3IP)
				return new gException('IP_NS3_CAN_NOT_FILL','IP адрес дополнительного сервера имен не может быть указан');
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		if($Ns3IP)
			return new gException('NAME_NS3_NOT_FILL','Укажите имя дополнительного сервера имен');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Ns4Name = Trim(Mb_StrToLower($Ns4Name,'UTF-8'),'.');
	#-------------------------------------------------------------------------------
	if($Ns4Name){
		#-------------------------------------------------------------------------------
		if(!Preg_Match($Regulars['Domain'],$Ns4Name))
			return new gException('WRONG_NAME_NS4','Неверное имя расширенного сервера имен');
		#-------------------------------------------------------------------------------
		if(Mb_SubStr($Ns4Name,-Mb_StrLen($Domain)) == $Domain){
			#-------------------------------------------------------------------------------
			if(!Preg_Match($Regulars['IP'],$Ns4IP))
				return new gException('WRONG_IP_NS4','Неверный IP адрес расширенного сервера имен');
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			if($Ns4IP)
				return new gException('IP_NS4_CAN_NOT_FILL','IP адрес расширенного сервера имен не может быть указан');
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		if($Ns4IP)
			return new gException('NAME_NS4_NOT_FILL','Укажите имя расширенного сервера имен');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('CONTRACT_NOT_FOUND','Договор не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('ContractsRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
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
#-------------------------TRANSACTION-------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainOrder'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Where = SPrintF("`ContractID` = %u AND `TypeID` = 'DomainRules'",$Contract['ID']);
#-------------------------------------------------------------------------------
$Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count < 1){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'DomainRules'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'integer':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>20000,'ServerID'=>$DomainScheme['ServerID'],'Params'=>'','DependOrderID'=>$DependOrderID));
if(Is_Error($OrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IDomainOrder = Array(
			'OrderID'	=> $OrderID,
			'DomainName'	=> $DomainName,
			'SchemeID'	=> $DomainScheme['ID'],
			'IsPrivateWhoIs'=> $IsPrivateWhoIs,
			'Ns1Name'	=> $Ns1Name,
			'Ns1IP'		=> $Ns1IP,
			'Ns2Name'	=> $Ns2Name,
			'Ns2IP'		=> $Ns2IP,
			'Ns3Name'	=> $Ns3Name,
			'Ns3IP'		=> $Ns3IP,
			'Ns4Name'	=> $Ns4Name,
			'Ns4IP'		=> $Ns4IP
			);
#-------------------------------------------------------------------------------
$DomainOrderID = DB_Insert('DomainOrders',$IDomainOrder);
if(Is_Error($DomainOrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>'Waiting','RowsIDs'=>$DomainOrderID,'Comment'=>($Comment)?$Comment:'Заказ создан и ожидает оплаты'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
	#return ERROR | @Trigger_Error(500);
	return $Comp;
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#---------------------END TRANSACTION-------------------------------------------
return Array('Status'=>'Ok','DomainOrderID'=>$DomainOrderID,'ServiceOrderID'=>$DomainOrderID,'OrderID'=>$OrderID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
