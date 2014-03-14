<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$ConfigPath = SPrintF('%s/hosts/%s/config/Config.xml',SYSTEM_PATH,HOST_ID);
#-------------------------------------------------------------------------------
if(File_Exists($ConfigPath)){
	#-------------------------------------------------------------------------------
	$File = IO_Read($ConfigPath);
	if(Is_Error($File))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$XML = String_XML_Parse($File);
	if(Is_Exception($XML))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Config = $XML->ToArray();
	#-------------------------------------------------------------------------------
	$Config = $Config['XML'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Config = Array();
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VPSServersGroups = DB_Select('VPSServersGroups',Array('*'));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSServersGroups)){
case 'error':
	# нет таблицы
	break;
case 'exception':
	# нет групп, нечего делать
	break;
case 'array':
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Array($VPSServersGroups)){
	#-------------------------------------------------------------------------------
	#---------------------------TRANSACTION-----------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('VPSServers'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$iServersGroups = Array();
	#-------------------------------------------------------------------------------
	$ServersGroups = DB_Select('ServersGroups','ID',Array('Where'=>'`ServiceID` = 30000'));
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
	foreach($VPSServersGroups as $VPSServersGroup){
		#-------------------------------------------------------------------------------
		$ServersGroup			= $VPSServersGroup;
		$ServersGroup['Name']		= $VPSServersGroup['Name'];
		$ServersGroup['ServiceID']	= 30000;
		$ServersGroup['FunctionID']	= 'ByAllCapacity';
		#-------------------------------------------------------------------------------
		UnSet($ServersGroup['ID']);
		#-------------------------------------------------------------------------------
		$ServersGroupID = DB_Insert('ServersGroups',$ServersGroup);
		if(Is_Error($ServersGroupID))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$iServersGroups[$VPSServersGroup['ID']] = $ServersGroupID;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$VPSServers = DB_Select('VPSServers','*',Array('Where'=>SPrintF('`ServersGroupID` = %u',$VPSServersGroup['ID']),'SortOn'=>'Address'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($VPSServers)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			continue 2;
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		foreach($VPSServers as $VPSServer){
			#-------------------------------------------------------------------------------
			$Server = Array(
					'TemplateID'		=> 'VPS',
					'ServersGroupID'	=> $ServersGroupID,
					'IsActive'		=> TRUE,
					'IsDefault'		=> $VPSServer['IsDefault'],
					'Protocol'		=> $VPSServer['Protocol'],
					'Address'		=> $VPSServer['Address'],
					'Port'			=> $VPSServer['Port'],
					'Login'			=> $VPSServer['Login'],
					'Password'		=> $VPSServer['Password'],
					'Params'		=> Array(
									'SystemID'		=> $VPSServer['SystemID'],
									'IP'			=> $VPSServer['IP'],
									'BalancingFactor'	=> $VPSServer['BalancingFactor'],
									'IsAutoBalancing'	=> $VPSServer['IsAutoBalancing'],
									'Domain'		=> $VPSServer['Domain'],
									'Prefix'		=> $VPSServer['Prefix'],
									'DiskTemplate'		=> @$VPSServer['disktempl'],
									'Theme'			=> $VPSServer['Theme'],
									'Language'		=> $VPSServer['Language'],
									'Url'			=> $VPSServer['Url'],
									'Ns1Name'		=> $VPSServer['Ns1Name'],
									'Ns2Name'		=> $VPSServer['Ns2Name'],
									'Ns3Name'		=> $VPSServer['Ns3Name'],
									'Ns4Name'		=> $VPSServer['Ns4Name'],
									'IPsPool'		=> ''
									),
					'AdminNotice'		=> $VPSServer['Notice'],
					'SortID'		=> 30000,
					'IsOK'			=> $VPSServer['IsOK'],
					'Monitoring'		=> $VPSServer['Services'],
					);
			#-------------------------------------------------------------------------------
			$ServerID = DB_Insert('Servers',$Server);
			if(Is_Error($ServerID))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Orders = DB_Select('VPSOrders',Array('ID','OrderID'),Array('Where'=>SPrintF('`ServerID` = %u',$VPSServer['ID'])));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Orders)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				break;
			case 'array':
				#-------------------------------------------------------------------------------
				foreach($Orders as $Order){
					#-------------------------------------------------------------------------------
					$IsUpdate = DB_Update('Orders',Array('ServerID'=>$ServerID),Array('ID'=>$Order['OrderID']));
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
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `VPSOrders` DROP FOREIGN KEY `VPSOrdersServerID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# TODO: удаляем VPSServers
	$IsQuery = DB_Query('DROP TABLE `VPSServers`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	#$IsQuery = DB_Query('ALTER TABLE `VPSSchemes` DROP FOREIGN KEY `VPSSchemesServersGroupID`');
	#if(Is_Error($IsQuery))
	#	return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `VPSSchemes` ADD `tmpServersGroupID` int(11) NOT NULL');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('UPDATE `VPSSchemes`  SET `tmpServersGroupID` = `ServersGroupID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($iServersGroups) as $iServersGroup){
		#-------------------------------------------------------------------------------
		$IsQuery = DB_Query(SPrintF('UPDATE `VPSSchemes` SET `ServersGroupID` = %u WHERE `tmpServersGroupID` = %u',$iServersGroups[$iServersGroup],$iServersGroup));
		if(Is_Error($IsQuery))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `VPSSchemes` DROP `tmpServersGroupID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `VPSSchemes` ADD CONSTRAINT `VPSSchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('DROP TABLE `VPSServersGroups`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `VPSOrders` DROP `ServerID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$File = IO_Write($ConfigPath,To_XML_String($Config),TRUE);
if(Is_Error($File))
	return ERROR | @Trigger_Error(500);
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
