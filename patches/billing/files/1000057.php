<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/

if(Is_Error(System_Load('libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Files = DB_Select('Contracts',Array('ID','Document'),Array('Where'=>"`Document` IS NOT NULL"));
#-------------------------------------------------------------------------------
switch(ValueOf($Files)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#---------------------------------------------------------------------------
	foreach($Files as $File){
		#---------------------------------------------------------------------------
		Debug(SPrintF("[patches/billing/files/1000057]: save file #%u ",$File['ID']));
		#-------------------------------------------------------------------------
		if(!SaveUploadedFile('Contracts', $File['ID'], $File['Document']))
			Debug("[patches/billing/files/1000057]: cannot save file " . $File['ID']);
		#-------------------------------------------------------------------------
		$Erase = DB_Query("UPDATE `Contracts` SET `Document` = NULL WHERE ID = " . $File['ID']);
		if(Is_Error($Erase))
			return ERROR | @Trigger_Error('101');
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>
