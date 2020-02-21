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
$ProxySchemeID	= (integer) @$Args['ProxySchemeID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$Name			=  (string) @$Args['Name'];
$PackageID		=  (string) @$Args['PackageID'];
$CostDay		=   (float) @$Args['CostDay'];
$CostMonth		=   (float) @$Args['CostMonth'];
$ServersGroupID		= (integer) @$Args['ServersGroupID'];
$HardServerID		= (integer) @$Args['HardServerID'];
$Comment		=  (string) @$Args['Comment'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$IsSchemeChangeable	= (boolean) @$Args['IsSchemeChangeable'];
$IsSchemeChange		= (boolean) @$Args['IsSchemeChange'];
$MinDaysPay		= (integer) @$Args['MinDaysPay'];
$MinDaysProlong		= (integer) @$Args['MinDaysProlong'];
$MaxDaysPay		= (integer) @$Args['MaxDaysPay'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$MinOrdersPeriod	= (integer) @$Args['MinOrdersPeriod'];
$IPtype			=  (string) @$Args['IPtype'];
$Country		=  (string) @$Args['Country'];
$SortID			= (integer) @$Args['SortID'];
#-------------------------------------------------------------------------------
$Count = DB_Count('Groups',Array('ID'=>$GroupID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('GROUP_NOT_FOUND','Группа не найдена');
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
$Count = DB_Count('ServersGroups',Array('ID'=>$ServersGroupID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('SERVERS_GROUP_NOT_FOUND','Группа серверов не найдена');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($HardServerID > 0){
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Servers',Array('ID'=>$HardServerID));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(!$Count)
		return new gException('WRONG_HardServerID','Указанного сервера не существует');
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Servers',Array('Where'=>SPrintF('`ID` = %u AND `ServersGroupID` = %u',$HardServerID,$ServersGroupID)));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(!$Count)
		return new gException('WRONG_HardServerID_Group','Указанный сервер размещения относится к другой группе серверов');

	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$HardServerID = NULL;
	#-------------------------------------------------------------------------------
}
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
$IProxyScheme = Array(
				'GroupID'		=> $GroupID,
				'UserID'		=> $UserID,
				'Name'			=> $Name,
				'PackageID'		=> $PackageID,
				'CostDay'		=> $CostDay,
				'CostMonth'		=> $CostMonth,
				'ServersGroupID'	=> $ServersGroupID,
				'HardServerID'		=> $HardServerID,
				'Comment'		=> $Comment,
				'IsActive'		=> $IsActive,
				'IsProlong'		=> $IsProlong,
				'IsSchemeChangeable'	=> $IsSchemeChangeable,
				'IsSchemeChange'	=> $IsSchemeChange,
				'MinDaysPay'		=> $MinDaysPay,
				'MinDaysProlong'	=> $MinDaysProlong,
				'MaxDaysPay'		=> $MaxDaysPay,
				'MaxOrders'		=> $MaxOrders,
				'MinOrdersPeriod'	=> $MinOrdersPeriod,
				'SortID'		=> $SortID,
				'IPtype'		=> $IPtype,
				'Country'		=> $Country,
			);
#-------------------------------------------------------------------------------
if($ProxySchemeID){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('ProxySchemes',$IProxyScheme,Array('ID'=>$ProxySchemeID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('ProxySchemes',$IProxyScheme);
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
