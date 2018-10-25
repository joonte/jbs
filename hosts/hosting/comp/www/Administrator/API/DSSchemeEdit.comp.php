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
$DSSchemeID		= (integer) @$Args['DSSchemeID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$Name			=  (string) @$Args['Name'];
$PackageID		=  (string) @$Args['PackageID'];
$CostDay		=   (float) @$Args['CostDay'];
$CostMonth		=   (float) @$Args['CostMonth'];
$CostInstall		=   (float) @$Args['CostInstall'];
$Discount		=  (double) @$Args['Discount'];
$ServerID		= (integer) @$Args['ServerID'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsBroken		= (boolean) @$Args['IsBroken'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$MinDaysPay		= (integer) @$Args['MinDaysPay'];
$MinDaysProlong         = (integer) @$Args['MinDaysProlong'];
$MaxDaysPay		= (integer) @$Args['MaxDaysPay'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$MinOrdersPeriod	= (integer) @$Args['MinOrdersPeriod'];
$SortID			= (integer) @$Args['SortID'];
$CPU			=  (string) @$Args['CPU'];
$ram			= (integer) @$Args['ram'];
$raid			=  (string) @$Args['raid'];
$disks			=  (string) @$Args['disks'];
$chrate			=   (float) @$Args['chrate'];
$trafflimit		=   (float) @$Args['trafflimit'];
$traffcorrelation	=  (string) @$Args['traffcorrelation'];
$OS			=  (string) @$Args['OS'];
$Switch			=  (string) @$Args['Switch'];
$UserNotice		=  (string) @$Args['UserNotice'];
$AdminNotice		=  (string) @$Args['AdminNotice'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('Groups',Array('ID'=>$GroupID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('GROUP_NOT_FOUND','Группа не найден');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('Users',Array('ID'=>$UserID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('USER_NOT_FOUND','Пользователь не найден');
#-------------------------------------------------------------------------------
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
#-------------------------------------------------------------------------------
if($MinDaysProlong > $MinDaysPay)
	return new gException('WRONG_MIN_DAYS_PROLONG','Минимальное число дней продления не может быть больше минимального числа дней оплаты');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($MinDaysPay > $MaxDaysPay)
	return new gException('WRONG_MIN_DAYS_PAY','Минимальное кол-во дней оплаты не можеть быть больше максимального');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Discount < 0){
	#-------------------------------------------------------------------------------
	$Discount = -1;
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Discount = IntVal($Discount);
	#-------------------------------------------------------------------------------
	if($Discount > 100)
		$Discount = 100;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IDSScheme = Array(
			'GroupID'		=> $GroupID,
			'UserID'		=> $UserID,
			'Name'			=> $Name,
			'PackageID'		=> $PackageID,
			'CostDay'		=> $CostDay,
			'CostMonth'		=> $CostMonth,
			'CostInstall'		=> $CostInstall,
			'Discount'		=> $Discount,
			'ServerID'		=> $ServerID,
			'IsActive'		=> $IsActive,
			'IsBroken'		=> $IsBroken,
			'IsProlong'		=> $IsProlong,
			'MinDaysPay'		=> $MinDaysPay,
			'MinDaysProlong'	=> $MinDaysProlong,
			'MaxDaysPay'		=> $MaxDaysPay,
			'MaxOrders'		=> $MaxOrders,
			'MinOrdersPeriod'	=> $MinOrdersPeriod,
			'SortID'		=> $SortID,
			'CPU'			=> $CPU,
			'ram'			=> $ram,
			'raid'			=> $raid,
			'disks'			=> $disks,
			'chrate'		=> $chrate,
			'trafflimit'		=> $trafflimit,
			'traffcorrelation'	=> $traffcorrelation,
			'OS'			=> $OS,
			'Switch'		=> $Switch,
			'UserNotice'		=> $UserNotice,
			'AdminNotice'		=> $AdminNotice
);
#-------------------------------------------------------------------------------
if($DSSchemeID){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('DSSchemes',$IDSScheme,Array('ID'=>$DSSchemeID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('DSSchemes',$IDSScheme);
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
