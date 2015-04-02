<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$DS = Array();
#-------------------------------------------------------------------------------
#---------------------------TRANSACTION-----------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('Dedicated'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServersGroups = DB_Select('ServersGroups','ID',Array('Where'=>'`ServiceID` = 40000'));
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
$ServersGroup['Name']		= 'Выделенные сервера';
$ServersGroup['ServiceID']	= 40000;
$ServersGroup['FunctionID']	= 'NotDefined';
$ServersGroup['IsCheckUsers']	= FALSE;
$ServersGroup['Params']		= Array('Count'=>0);
$ServersGroup['Comment']	= 'Группа выделенных серверов';
$ServersGroup['SortID']		= 40000;
#-------------------------------------------------------------------------------
$ServersGroupID = DB_Insert('ServersGroups',$ServersGroup);
if(Is_Error($ServersGroupID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OldGroups = DB_Select('DSServersGroups','*',Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($OldGroups)){
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
foreach($OldGroups as $Group){
	#-------------------------------------------------------------------------------
	$Server = Array(
			'TemplateID'		=> 'DS',
			'ServersGroupID'	=> $ServersGroupID,
			'IsActive'		=> TRUE,
			'IsDefault'		=> TRUE,
			'Protocol'		=> 'ssl',
			'Address'		=> SPrintF('manual%u.isp.su',$ServersGroupID),
			'Port'			=> 1000,
			'Login'			=> 'root',
			'Password'		=> 'Default',
			'Params'		=> Array(
							'Name'			=> $Group['Name'],
							'SystemID'		=> 'NullSystem',
							'IP'			=> '127.0.0.127',
							'IsLogging'		=> TRUE,
							'DS'		=> 'test.su',
							'Prefix'		=> 'ds',
							'DiskTemplate'		=> 'FreeBSD-10.1',
							'Theme'			=> 'Orion',
							'Url'			=> SPrintF('http://manual%u.isp.su:1000/manage',$ServersGroupID),
							'Ns1Name'		=> 'dns0.isp.su',
							'Ns2Name'		=> 'dns1.isp.su',
							'Ns3Name'		=> '',
							'Ns4Name'		=> '',
							),
			'SortID'		=> 40000 + $Group['SortID'],
			'Monitoring'		=> '',
			'AdminNotice'		=> $Group['Comment']
			);
	#-------------------------------------------------------------------------------
	$ServerID = DB_Insert('Servers',$Server);
	if(Is_Error($ServerID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# соответствие старых идентификаторов новым
	$DS[$Group['ID']] = $ServerID;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DSOrders = DB_Select('DSOrders',Array('ID','OrderID'),Array('Where'=>SPrintF('(SELECT `ServersGroupID` FROM `DSSchemes` WHERE `ID` = `DSOrders`.`SchemeID`) = %u',$Group['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DSOrders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($DSOrders as $DSOrder){
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('Orders',Array('ServerID'=>$ServerID),Array('ID'=>$DSOrder['OrderID']));
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
$IsQuery = DB_Query('ALTER TABLE `DSSchemes` CHANGE `ServersGroupID` `ServerID` INT(11) NULL');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query(SPrintF('UPDATE `DSSchemes` SET `ServerID` = %u',$ServerID));
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DSSchemes` ADD KEY `DSSchemesServerID` (`ServerID`)');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `DSSchemes` ADD CONSTRAINT `DSSchemesServerID` FOREIGN KEY (`ServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('DROP TABLE IF EXISTS `DSServers`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('DROP TABLE IF EXISTS `DSServersGroups`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
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
