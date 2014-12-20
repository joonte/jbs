<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$HostingServersGroups = DB_Select('HostingServersGroups',Array('*'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServersGroups)){
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
if(Is_Array($HostingServersGroups)){
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$HS = Array();
	#-------------------------------------------------------------------------------
	#---------------------------TRANSACTION-----------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('HostingServers'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$iServersGroups = Array();
	#-------------------------------------------------------------------------------
	$ServersGroups = DB_Select('ServersGroups','ID',Array('Where'=>'`ServiceID` = 10000'));
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
	foreach($HostingServersGroups as $HostingServersGroup){
		#-------------------------------------------------------------------------------
		$ServersGroup			= $HostingServersGroup;
		$ServersGroup['Name']		= $HostingServersGroup['Name'];
		$ServersGroup['ServiceID']	= 10000;
		$ServersGroup['FunctionID']	= 'ByAllCapacity';
		#-------------------------------------------------------------------------------
		UnSet($ServersGroup['ID']);
		#-------------------------------------------------------------------------------
		$ServersGroupID = DB_Insert('ServersGroups',$ServersGroup);
		if(Is_Error($ServersGroupID))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$iServersGroups[$HostingServersGroup['ID']] = $ServersGroupID;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$HostingServers = DB_Select('HostingServers','*',Array('Where'=>SPrintF('`ServersGroupID` = %u',$HostingServersGroup['ID']),'SortOn'=>'Address'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($HostingServers)){
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
		foreach($HostingServers as $HostingServer){
			#-------------------------------------------------------------------------------
			$Server = Array(
					'TemplateID'		=> 'Hosting',
					'ServersGroupID'	=> $ServersGroupID,
					'IsActive'		=> TRUE,
					'IsDefault'		=> $HostingServer['IsDefault'],
					'Protocol'		=> $HostingServer['Protocol'],
					'Address'		=> $HostingServer['Address'],
					'Port'			=> $HostingServer['Port'],
					'Login'			=> $HostingServer['Login'],
					'Password'		=> $HostingServer['Password'],
					'Params'		=> Array(
									'SystemID'		=> $HostingServer['SystemID'],
									'IP'			=> $HostingServer['IP'],
									'BalancingFactor'	=> $HostingServer['BalancingFactor'],
									'IsAutoBalancing'	=> $HostingServer['IsAutoBalancing'],
									'Domain'		=> $HostingServer['Domain'],
									'Prefix'		=> $HostingServer['Prefix'],
									'Theme'			=> $HostingServer['Theme'],
									'Language'		=> $HostingServer['Language'],
									'Url'			=> $HostingServer['Url'],
									'Ns1Name'		=> $HostingServer['Ns1Name'],
									'Ns2Name'		=> $HostingServer['Ns2Name'],
									'Ns3Name'		=> $HostingServer['Ns3Name'],
									'Ns4Name'		=> $HostingServer['Ns4Name'],
									'IPsPool'		=> $HostingServer['IPsPool'],
									'MySQL'			=> $HostingServer['MySQL'],

									),
					'AdminNotice'		=> $HostingServer['Notice'],
					'SortID'		=> 10000,
					'IsOK'			=> $HostingServer['IsOK'],
					'Monitoring'		=> $HostingServer['Services'],
					);
			#-------------------------------------------------------------------------------
			$ServerID = DB_Insert('Servers',$Server);
			if(Is_Error($ServerID))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# соответствие старых идентификаторов новым
			$HS[$HostingServer['ID']] = $ServerID;
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Orders = DB_Select('HostingOrders',Array('ID','OrderID'),Array('Where'=>SPrintF('`ServerID` = %u',$HostingServer['ID'])));
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
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` DROP FOREIGN KEY `HostingSchemesServersGroupID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` DROP FOREIGN KEY `HostingSchemesHardServerID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` ADD `tmpServersGroupID` int(11) NOT NULL');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` ADD `tmpHardServerID` int(11) NOT NULL');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('UPDATE `HostingSchemes` SET `tmpServersGroupID` = `ServersGroupID`,`tmpHardServerID` = `HardServerID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($iServersGroups) as $iServersGroup){
		#-------------------------------------------------------------------------------
		$IsQuery = DB_Query(SPrintF('UPDATE `HostingSchemes` SET `ServersGroupID` = %u WHERE `tmpServersGroupID` = %u',$iServersGroups[$iServersGroup],$iServersGroup));
		if(Is_Error($IsQuery))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#обновляем HostingSchemes.HardServerID
	$HostingSchemes = DB_Select('HostingSchemes',Array('ID','HardServerID','tmpHardServerID'),Array('Where'=>'`HardServerID` > 0'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingSchemes)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($HostingSchemes as $HostingScheme){
			#------------------------------------------------------------------------------- $HS[$HostingServer['ID']] = $ServerID;
			$IsUpdate = DB_Update('HostingSchemes',Array('HardServerID'=>$HS[$HostingScheme['tmpHardServerID']]),Array('ID'=>$HostingScheme['ID']));
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
	$IsQuery = DB_Query('ALTER TABLE `HostingOrders` DROP FOREIGN KEY `HostingOrdersServerID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# удаляем HostingServers
	$IsQuery = DB_Query('DROP TABLE `HostingServers`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);

	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` DROP `tmpServersGroupID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` DROP `tmpHardServerID`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` ADD CONSTRAINT `HostingSchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` ADD CONSTRAINT `HostingSchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('DROP TABLE `HostingServersGroups`');
	if(Is_Error($IsQuery))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsQuery = DB_Query('ALTER TABLE `HostingOrders` DROP `ServerID`');
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
$IsFlush = CacheManager::flush();
if(!$IsFlush)
	@Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
