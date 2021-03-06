<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['DomainSchemeCheckWhoIsDatabaseSettings'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$WhoIsZones = Comp_Load('Formats/DomainOrder/DomainZones',FALSE,'List');
if(Is_Error($WhoIsZones))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выбираем все тарифы, которые предположительно, надо отключать
$DomainSchemes = DB_Select('DomainSchemesOwners',Array('ID','Name'),Array('Where'=>Array('`IsActive` = "yes" OR `IsProlong` = "yes" OR `IsTransfer` = "yes"')));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	Debug('[comp/Tasks/GC/DomainSchemeCheckWhoIsDatabase]: нет тарифных планов на домены');
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($DomainSchemes as $DomainScheme){
	#-------------------------------------------------------------------------------
	if(!In_Array($DomainScheme['Name'],$WhoIsZones)){
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('DomainSchemes',Array('IsActive'=>FALSE,'IsProlong'=>FALSE,'IsTransfer'=>FALSE),Array('ID'=>$DomainScheme['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!$Settings['IsEvent'])
			continue;
		#-------------------------------------------------------------------------------
		$Event = Array('Text'=>SPrintF('Доменная зона "%s" не обнаружена в базе данных WhoIs, автоматически отключена',$DomainScheme['Name']),'PriorityID'=>'Warning','IsReaded'=>FALSE);
		$Event = Comp_Load('Events/EventInsert', $Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
