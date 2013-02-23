<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/

if(Is_Error(System_Load('libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Files = DB_Select('Users',Array('ID','Foto'),Array('Where'=>"`Foto` IS NOT NULL"));
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
		Debug(SPrintF("[patches/billing/files/1000058]: save file #%u ",$File['ID']));
		#-------------------------------------------------------------------------
		if(!SaveUploadedFile('Users', $File['ID'], $File['Foto']))
			Debug("[patches/billing/files/1000058]: cannot save file " . $File['ID']);
		#-------------------------------------------------------------------------
		$Erase = DB_Query("UPDATE `Users` SET `Foto` = NULL WHERE ID = " . $File['ID']);
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
