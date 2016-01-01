<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
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
$DomainSchemeID		= (integer) @$Args['DomainSchemeID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$Name			=  (string) @$Args['Name'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$IsTransfer		= (boolean) @$Args['IsTransfer'];
$CostOrder		=   (float) @$Args['CostOrder'];
$CostProlong		=   (float) @$Args['CostProlong'];
$CostTransfer		=   (float) @$Args['CostTransfer'];
$ServerID		= (integer) @$Args['ServerID'];
$SortID			= (integer) @$Args['SortID'];
$MinOrderYears		= (integer) @$Args['MinOrderYears'];
$MaxActionYears		= (integer) @$Args['MaxActionYears'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$MinOrdersPeriod	= (integer) @$Args['MinOrdersPeriod'];
$DaysToProlong		= (integer) @$Args['DaysToProlong'];
$DaysBeforeTransfer	= (integer) @$Args['DaysBeforeTransfer'];
$DaysAfterTransfer	= (integer) @$Args['DaysAfterTransfer'];
#-------------------------------------------------------------------------------
$Name = Trim($Name,'.');
#-------------------------------------------------------------------------------
if(!Preg_Match('/^[\p{L}0-9\-\.]+$/u',$Name))
	return new gException('WRONG_DOMAIN_ZONE','Неверное имя доменной зоны');
#-------------------------------------------------------------------------------
$Count = DB_Count('Servers',Array('ID'=>$ServerID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('WRONG_REGISTRATOR','Неверный регистратор');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($IsActive || $IsProlong || $IsTransfer){
	#-------------------------------------------------------------------------------
	$DomainZones = System_XML('config/DomainZones.xml');
	if(Is_Error($DomainZones))
		return ERROR | @Trigger_Error('[comp/www/API/WhoIs]: не удалось загрузить базу WhoIs серверов');
	#-------------------------------------------------------------------------------
	# перебираем зоны, сравниваем имя с редактируемой
	foreach($DomainZones as $DomainZone)
		if($DomainZone['Name'] == $Name)
			$IsExist = TRUE;
	#-------------------------------------------------------------------------------
	if(!IsSet($IsExist))
		return new gException('SCHEME_NOT_FOUND_IN_WHOIS_DATABASE',SPrintF('Доменная зона "%s" не найдена в базе WhoIs серверов, активация тарифа невозможна. Добавьте описание доменной зоны в файл "hosts/%s/config/DomainZones.xml", или снимите все галочки с активен/перенос/продление',$Name,HOST_ID));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IDomainScheme = Array(
			'GroupID'		=> $GroupID,
			'UserID'		=> $UserID,
			'Name'			=> $Name,
			'IsActive'		=> $IsActive,
			'IsProlong'		=> $IsProlong,
			'IsTransfer'		=> $IsTransfer,
			'CostOrder'		=> $CostOrder,
			'CostProlong'		=> $CostProlong,
			'CostTransfer'		=> $CostTransfer,
			'ServerID'		=> $ServerID,
			'SortID'		=> $SortID,
			'MinOrderYears'		=> $MinOrderYears,
			'MaxActionYears'	=> $MaxActionYears,
			'MaxOrders'		=> $MaxOrders,
			'MinOrdersPeriod'	=> $MinOrdersPeriod,
			'DaysToProlong'		=> $DaysToProlong,
			'DaysBeforeTransfer'	=> $DaysBeforeTransfer,
			'DaysAfterTransfer'	=> $DaysAfterTransfer
);

#-------------------------------------------------------------------------------
if($DomainSchemeID){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('DomainSchemes',$IDomainScheme,Array('ID'=>$DomainSchemeID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('DomainSchemes',$IDomainScheme);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
