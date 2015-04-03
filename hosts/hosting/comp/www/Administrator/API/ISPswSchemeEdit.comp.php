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
$Config = Config();
#-------------------------------------------------------------------------------
$ISPswSchemeID		= (integer) @$Args['ISPswSchemeID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$SoftWareGroup		= (integer) @$Args['SoftWareGroup'];
$Name			=  (string) @$Args['Name'];
$PackageID		=  (string) @$Args['PackageID'];
$CostDay		=   (float) @$Args['CostDay'];
$CostMonth		=   (float) @$Args['CostMonth'];
$Comment		=  (string) @$Args['Comment'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$IsSchemeChangeable	= (boolean) @$Args['IsSchemeChangeable'];
$IsSchemeChange		= (boolean) @$Args['IsSchemeChange'];
$IsInternal		= (boolean) @$Args['IsInternal'];
$MinDaysPay		= (integer) @$Args['MinDaysPay'];
$MinDaysProlong		= (integer) @$Args['MinDaysProlong'];
$MaxDaysPay		= (integer) @$Args['MaxDaysPay'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$MinOrdersPeriod	= (integer) @$Args['MinOrdersPeriod'];
$ConsiderTypeID		=  (string) @$Args['ConsiderTypeID'];
$SortID			= (integer) @$Args['SortID'];
$pricelist_id		= (integer) @$Args['pricelist_id'];
$period			=  (string) @$Args['period'];
$addon			= (integer) @$Args['addon'];
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
#-------------------------------------------------------------------------------
$Count = DB_Count('ISPswGroups',Array('ID'=>$SoftWareGroup));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('SoftWareGroup_NOT_FOUND','Группа ПО не найдена');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match('/^[A-Za-zА-ЯёЁа-я0-9\s\.\-\[\]]+$/u',$Name))
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
if(!In_Array($ConsiderTypeID,Array('Upon','Daily')))
	return new gException('WRONG_CONSIDER_TYPE','Тип учёта может быть только Разово/Ежедневно');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IISPswScheme = Array(
			#-------------------------------------------------------------------------------
			'GroupID'		=> $GroupID,
			'UserID'		=> $UserID,
			'SoftWareGroup'		=> $SoftWareGroup,
			'Name'			=> $Name,
			'PackageID'		=> $PackageID,
			'CostDay'		=> $CostDay,
			'CostMonth'		=> $CostMonth,
			'Comment'		=> $Comment,
			'IsActive'		=> $IsActive,
			'IsProlong'		=> $IsProlong,
			'IsSchemeChangeable'	=> $IsSchemeChangeable,
			'IsSchemeChange'	=> $IsSchemeChange,
			'IsInternal'		=> $IsInternal,
			'MinDaysPay'		=> $MinDaysPay,
			'MinDaysProlong'	=> $MinDaysProlong,
			'MaxDaysPay'		=> $MaxDaysPay,
			'MaxOrders'		=> $MaxOrders,
			'MinOrdersPeriod'	=> $MinOrdersPeriod,
			'SortID'		=> $SortID,
			'ConsiderTypeID'	=> $ConsiderTypeID,
			'pricelist_id'		=> $pricelist_id,
			'period'		=> $period,
			'addon'			=> $addon
			#-------------------------------------------------------------------------------
			);
#-------------------------------------------------------------------------------
if($ISPswSchemeID){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('ISPswSchemes',$IISPswScheme,Array('ID'=>$ISPswSchemeID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('ISPswSchemes',$IISPswScheme);
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
