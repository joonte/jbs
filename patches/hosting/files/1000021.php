<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$RS = Array();
#-------------------------------------------------------------------------------
#---------------------------TRANSACTION-----------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('Registrators'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServersGroups = DB_Select('ServersGroups','ID',Array('Where'=>'`ServiceID` = 20000'));
#-------------------------------------------------------------------------------
switch(ValueOf($ServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($ServersGroups as $ServersGroup){
		#-------------------------------------------------------------------------------
		$IsDelete = DB_Delete('Servers',Array('Where'=>SPrintF('`ServersGroupID` = %u',$ServersGroup['ID'])));
		if(Is_Error($IsDelete))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsDelete = DB_Delete('ServersGroups',Array('ID'=>$ServersGroup['ID']));
		if(Is_Error($IsDelete))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServersGroup			= Array();
$ServersGroup['Name']		= 'Регистраторы';
$ServersGroup['ServiceID']	= 20000;
$ServersGroup['FunctionID']	= 'NotDefined';
$ServersGroup['IsCheckUsers']	= FALSE;
$ServersGroup['Params']		= Array('Count'=>0);
$ServersGroup['Comment']	= 'Группа регистраторов доменных имён';
$ServersGroup['SortID']		= 20000;
#-------------------------------------------------------------------------------
$ServersGroupID = DB_Insert('ServersGroups',$ServersGroup);
if(Is_Error($ServersGroupID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Registrators = DB_Select('Registrators','*',Array('SortOn'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($Registrators)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Registrators as $Registrator){
	#-------------------------------------------------------------------------------
	$Server = Array(
			'TemplateID'		=> 'Domains',
			'ServersGroupID'	=> $ServersGroupID,
			'IsActive'		=> TRUE,
			'IsDefault'		=> TRUE,
			'Protocol'		=> $Registrator['Protocol'],
			'Address'		=> $Registrator['Address'],
			'Port'			=> $Registrator['Port'],
			'Login'			=> $Registrator['Login'],
			'Password'		=> $Registrator['Password'],
			'Params'		=> Array(
							'Name'			=> $Registrator['Name'],
							'SystemID'		=> $Registrator['TypeID'],
							'Comment'		=> $Registrator['Comment'],
							'PrefixAPI'		=> $Registrator['PrefixAPI'],
							'Ns1Name'		=> $Registrator['Ns1Name'],
							'Ns2Name'		=> $Registrator['Ns2Name'],
							'Ns3Name'		=> $Registrator['Ns3Name'],
							'Ns4Name'		=> $Registrator['Ns4Name'],
							'ParentID'		=> $Registrator['ParentID'],
							'PrefixNic'		=> $Registrator['PrefixNic'],
							'PartnerLogin'		=> $Registrator['PartnerLogin'],
							'PartnerContract'	=> $Registrator['PartnerContract'],
							'JurName'		=> $Registrator['JurName'],
							'BalanceLowLimit'	=> $Registrator['BalanceLowLimit'],
							),
			'SortID'		=> 20000 + $Registrator['SortID'],
			'Monitoring'		=> SPrintF('HTTP=%s',$Registrator['Port'])
			);
	#-------------------------------------------------------------------------------
	$ServerID = DB_Insert('Servers',$Server);
	if(Is_Error($ServerID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# соответствие старых идентификаторов новым
	$RS[$Registrator['ID']] = $ServerID;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DomainsOrders = DB_Select('DomainsOrdersOwners',Array('ID','OrderID'),Array('Where'=>SPrintF('`RegistratorID` = %u',$Registrator['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DomainsOrders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($DomainsOrders as $DomainsOrder){
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('Orders',Array('ServerID'=>$ServerID),Array('ID'=>$DomainsOrder['OrderID']));
			if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DomainsSchemes` DROP FOREIGN KEY `DomainsSchemesRegistratorID`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DomainsSchemes` DROP KEY `DomainsSchemesRegistratorID`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DomainsSchemes` CHANGE `RegistratorID` `ServerID` INT(11) NOT NULL');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DomainsSchemes` ADD `tmpServerID` int(11) NOT NULL');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('UPDATE `DomainsSchemes` SET `tmpServerID` = `ServerID`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainsSchemes = DB_Select('DomainsSchemes',Array('ID','ServerID','tmpServerID'),Array('Where'=>'`ServerID` > 0'));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainsSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($DomainsSchemes as $DomainsScheme){
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('DomainsSchemes',Array('ServerID'=>$RS[$DomainsScheme['tmpServerID']]),Array('ID'=>$DomainsScheme['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
        break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# удаляем Registrators
$IsQuery = DB_Query('DROP TABLE `Registrators`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);

#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DomainsSchemes` DROP `tmpServerID`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DomainsSchemes` ADD KEY `DomainsSchemesServerID` (`ServerID`)');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DomainsSchemes` ADD CONSTRAINT `DomainsSchemesServerID` FOREIGN KEY (`ServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsFlush = CacheManager::flush();
if(!$IsFlush)
	@Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
