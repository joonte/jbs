<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/WhoIs.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['DomainOrdersWhoIsUpdate'];
#------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return 24*3600;
#------------------------------------------------------------------------------
$ExecutePeriod = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod'],'DefaultTime'=>120));
if(Is_Error($ExecutePeriod))
	return ERROR | @Trigger_Error(500);
#------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecuteTime'=>$Settings['ExecuteTime'],'DefaultTime'=>MkTime(5,0,0,Date('n'),Date('j')+1,Date('Y'))));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		"`StatusID` = 'Active' OR `StatusID` = 'ForTransfer' OR `StatusID` = 'OnTransfer'",
		# как часто обновляем информацию WhoIs
		SPRintF('UNIX_TIMESTAMP() - %u * 86400 > `UpdateDate`',$Settings['WhoIsUpdatePeriod']),
		# через сколько начинаем обновлять WhoIs, после установки статуса
		SPrintF('UNIX_TIMESTAMP() - %u * 86400 > `StatusDate`',$Settings['WhoIsBeginUpdate'])
		);
#-------------------------------------------------------------------------------
$Columns = Array('ID','DomainName','(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `SchemeID`) AS `DomainZone`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `ServerID`) AS `Params`');
$DomainOrders = DB_Select('DomainOrdersOwners',$Columns,Array('Where'=>$Where,'Limits'=>Array(0,$Settings['Limit']),'SortOn'=>Array('UpdateDate','DomainName')));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = Array();
	#-------------------------------------------------------------------------------
	foreach($DomainOrders as $DomainOrder){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/Administrator/API/DomainOrderWhoIsUpdate',Array('DomainOrderID'=>$DomainOrder['ID']));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!IsSet($GLOBALS['TaskReturnInfo'][$DomainOrder['Params']['Name']]))
			$GLOBALS['TaskReturnInfo'][$DomainOrder['Params']['Name']] = Array();
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'][$DomainOrder['Params']['Name']][] = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone']);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'true':
	#-------------------------------------------------------------------------------

	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('DomainOrders',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	$GLOBALS['TaskReturnInfo']['Estimated'] = Array($Count);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return ($Count?$ExecutePeriod:$ExecuteTime);
#-------------------------------------------------------------------------------

?>
