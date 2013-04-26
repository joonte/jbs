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
$Where = Array(
		"`StatusID` = 'Active' OR `StatusID` = 'ForTransfer' OR `StatusID` = 'OnTransfer'",
		"UNIX_TIMESTAMP() - 86400 > `UpdateDate`",
		"UNIX_TIMESTAMP() - 3 * 86400 > `StatusDate`"
		);
#-------------------------------------------------------------------------------
$Columns = Array('ID','DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`) AS `DomainZone`');
$DomainOrders = DB_Select('DomainsOrders',$Columns,Array('Where'=>$Where,'Limits'=>Array(0,5),'SortOn'=>'UpdateDate'));
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
		$GLOBALS['TaskReturnInfo'][] = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone']);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'true':
	#-------------------------------------------------------------------------------
	# domain not found ....
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('DomainsOrders',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#Debug("[comp/Tasks/DomainsOrdersWhoIsUpdate]: TaskReturnInfo = " . print_r($GLOBALS['TaskReturnInfo'], true));
$GLOBALS['TaskReturnInfo'][] = SPrintF('estimated: %s domains',$Count);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return ($Count?120:MkTime(5,0,0,Date('n'),Date('j')+1,Date('Y')));
#-------------------------------------------------------------------------------

?>
