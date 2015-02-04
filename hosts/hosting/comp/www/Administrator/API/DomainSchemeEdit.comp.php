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
$CostOrder		=   (float) @$Args['CostOrder'];
$CostProlong		=   (float) @$Args['CostProlong'];
$CostTransfer		=   (float) @$Args['CostTransfer'];
$ServerID		= (integer) @$Args['ServerID'];
$SortID			= (integer) @$Args['SortID'];
$MinOrderYears		= (integer) @$Args['MinOrderYears'];
$MaxActionYears		= (integer) @$Args['MaxActionYears'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$DaysToProlong		= (integer) @$Args['DaysToProlong'];
$DaysBeforeTransfer	= (integer) @$Args['DaysBeforeTransfer'];
$DaysAfterTransfer	= (integer) @$Args['DaysAfterTransfer'];
#-------------------------------------------------------------------------------
$Name = Trim($Name,'.');
#-------------------------------------------------------------------------------
if(!Preg_Match('/^[A-Za-zРФрф0-9\-\.]+$/i',$Name))
	return new gException('WRONG_DOMAIN_ZONE','Неверное имя доменной зоны');
#-------------------------------------------------------------------------------
$Count = DB_Count('Servers',Array('ID'=>$ServerID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('WRONG_REGISTRATOR','Неверный регистратор');
#-------------------------------------------------------------------------------
$IDomainScheme = Array(
			'GroupID'		=> $GroupID,
			'UserID'		=> $UserID,
			'Name'			=> $Name,
			'IsActive'		=> $IsActive,
			'IsProlong'		=> $IsProlong,
			'CostOrder'		=> $CostOrder,
			'CostProlong'		=> $CostProlong,
			'CostTransfer'		=> $CostTransfer,
			'ServerID'		=> $ServerID,
			'SortID'		=> $SortID,
			'MinOrderYears'		=> $MinOrderYears,
			'MaxActionYears'	=> $MaxActionYears,
			'MaxOrders'		=> $MaxOrders,
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
