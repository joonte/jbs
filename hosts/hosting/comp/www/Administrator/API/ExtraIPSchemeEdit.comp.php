<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ExtraIPSchemeID	= (integer) @$Args['ExtraIPSchemeID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$Name			=  (string) @$Args['Name'];
$PackageID		=  (string) @$Args['PackageID'];
$CostDay		=   (float) @$Args['CostDay'];
$CostMonth		=   (float) @$Args['CostMonth'];
$CostInstall		=   (float) @$Args['CostInstall'];
$AddressType		=  (string) @$Args['AddressType'];
$Servers		=   (array) @$Args['Servers'];
$Comment		=  (string) @$Args['Comment'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$MinDaysPay		= (integer) @$Args['MinDaysPay'];
$MinDaysProlong		= (integer) @$Args['MinDaysProlong'];
$MaxDaysPay		= (integer) @$Args['MaxDaysPay'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$MinOrdersPeriod	= (integer) @$Args['MinOrdersPeriod'];
$SortID			= (integer) @$Args['SortID'];
#-------------------------------------------------------------------------------
$Count = DB_Count('Groups',Array('ID'=>$GroupID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('GROUP_NOT_FOUND','Группа не найден');
#-------------------------------------------------------------------------------
$Count = DB_Count('Users',Array('ID'=>$UserID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('USER_NOT_FOUND','Пользователь не найден');
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match('/^[A-Za-zА-ЯёЁа-я0-9\s\.\-]+$/u',$Name))
	return new gException('WRONG_SCHEME_NAME','Неверное имя тарифа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$MinDaysPay)
	return new gException('MIN_DAYS_PAY_NOT_DEFINED','Минимальное кол-во дней оплаты не указано');
#-------------------------------------------------------------------------------
if($MinDaysProlong > $MinDaysPay)
	return new gException('WRONG_MIN_DAYS_PROLONG','Минимальное число дней продления не может быть больше минимального числа дней оплаты');
#-------------------------------------------------------------------------------
if($MinDaysPay > $MaxDaysPay)
	return new gException('WRONG_MIN_DAYS_PAY','Минимальное кол-во дней оплаты не можеть быть больше максимального');
#-------------------------------------------------------------------------------
$IExtraIPScheme = Array(
			'GroupID'		=> $GroupID,
			'UserID'		=> $UserID,
			'Name'			=> $Name,
			'PackageID'		=> $PackageID,
			'CostDay'		=> $CostDay,
			'CostMonth'		=> $CostMonth,
			'CostInstall'		=> $CostInstall,
			'AddressType'		=> $AddressType,
			'Params'		=> Array('Servers'=>$Servers),
			'Comment'		=> $Comment,
			'IsActive'		=> $IsActive,
			'IsProlong'		=> $IsProlong,
			'MinDaysPay'		=> $MinDaysPay,
			'MinDaysProlong'	=> $MinDaysProlong,
			'MaxDaysPay'		=> $MaxDaysPay,
			'MaxOrders'		=> $MaxOrders,
			'MinOrdersPeriod'	=> $MinOrdersPeriod,
			'SortID'		=> $SortID
			);
#-------------------------------------------------------------------------------
if($ExtraIPSchemeID){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('ExtraIPSchemes',$IExtraIPScheme,Array('ID'=>$ExtraIPSchemeID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('ExtraIPSchemes',$IExtraIPScheme);
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
