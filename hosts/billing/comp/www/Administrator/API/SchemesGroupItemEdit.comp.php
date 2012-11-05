<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$SchemesGroupItemID	= (integer) @$Args['SchemesGroupItemID'];
$SchemesGroupID		= (integer) @$Args['SchemesGroupID'];
$ServiceID		= (integer) @$Args['ServiceID'];
$SchemeID		= (integer) @$Args['SchemeID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ServiceID)
	return new gException('SERVICE_NOT_SELECTED','Выберите сервис');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, что такой тариф уже не добавлен
$Where = Array(
		SPrintF("`SchemesGroupID` = %u",$SchemesGroupID),
		SPrintF("`ServiceID` = '%u'",$ServiceID),
		SPrintF("`SchemeID` IS NULL OR `SchemeID` = %s",$SchemeID)
		);
#-------------------------------------------------------------------------------
$Count = DB_Count('SchemesGroupsItems',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('DUPLICATE_SERVCE_OR_SCHEME','Данная услуга/тариф уже добавлен в группу');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# если вариант "Все тарифы", то проверяем что нет какого-то конкретного тарифа
if(!$SchemeID){
	#-----------------------------------------------------------------------
	$Where = Array(
			SPrintF("`SchemesGroupID` = %u",$SchemesGroupID),
			SPrintF("`ServiceID` = '%u'",$ServiceID)
			);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('SchemesGroupsItems',Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count)
		return new gException('DUPLICATE_SERVCE','Данная услуга уже добавлена в группу');
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, что тип учёта тот же самый, что и у уже добавленных услуг
# достаём текущий добавленный сервис
$Consider =  DB_Select('ServicesOwners','*',Array('UNIQ','ID'=>$ServiceID));
switch(ValueOf($Consider)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVICE_NOT_FOUND','Выбранный серис не найден');
	break;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
# достаём ранее добавленные сервисы
$Considers =  DB_Select('SchemesGroupsItems',Array('*','(SELECT `ConsiderTypeID` FROM `ServicesOwners` WHERE `ServicesOwners`.`ID`=`SchemesGroupsItems`.`ServiceID`) AS `ConsiderTypeID`'),Array('Where'=>SPrintF('`SchemesGroupID` = %u',$SchemesGroupID)));
switch(ValueOf($Considers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	foreach($Considers as $iConsider){
		if($iConsider['ConsiderTypeID'] != $Consider['ConsiderTypeID'])
			return new gException('CONSIDER_TYPE_DOES_NOT_MATCH','Тип учёта добавляемого в группу сервиса, отличается от типа учёта ранее добавленных серисов');
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ISchemesGroup = Array(
	#-----------------------------------------------------------------------------
	'ServiceID'	=> $ServiceID?$ServiceID:NULL,
	'SchemeID'	=> $SchemeID?$SchemeID:NULL,
	
);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($SchemesGroupItemID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('SchemesGroupsItems',$ISchemesGroup,Array('ID'=>$SchemesGroupItemID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
}else{
  #-----------------------------------------------------------------------------
  $ISchemesGroup['SchemesGroupID'] = $SchemesGroupID;
  #-----------------------------------------------------------------------------
  $SchemesGroupID = DB_Insert('SchemesGroupsItems',$ISchemesGroup);
  if(Is_Error($SchemesGroupID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Answer['SchemesGroupID'] = $SchemesGroupID;
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
