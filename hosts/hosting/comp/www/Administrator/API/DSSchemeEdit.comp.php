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
$ServerID		= (integer) @$Args['ServerID'];
$NumServers		= (integer) @$Args['NumServers'];
$RemainServers		= (integer) @$Args['RemainServers'];
$IsCalculateNumServers	= (boolean) @$Args['IsCalculateNumServers'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$MinDaysPay		= (integer) @$Args['MinDaysPay'];
$MinDaysProlong         = (integer) @$Args['MinDaysProlong'];
$MaxDaysPay		= (integer) @$Args['MaxDaysPay'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$SortID			= (integer) @$Args['SortID'];
$cputype		=  (string) @$Args['cputype'];
$cpuarch		=  (string) @$Args['cpuarch'];
$numcpu			= (integer) @$Args['numcpu'];
$numcores		= (integer) @$Args['numcores'];
$cpufreq		= (integer) @$Args['cpufreq'];
$ram			= (integer) @$Args['ram'];
$raid			=  (string) @$Args['raid'];
$disk1			=  (string) @$Args['disk1'];
$disk2			=  (string) @$Args['disk2'];
$disk3			=  (string) @$Args['disk3'];
$disk4			=  (string) @$Args['disk4'];
$chrate			=   (float) @$Args['chrate'];
$trafflimit		=   (float) @$Args['trafflimit'];
$traffcorrelation	=  (string) @$Args['traffcorrelation'];
$OS			=  (string) @$Args['OS'];
$UserComment		=  (string) @$Args['UserComment'];
$AdminComment		=  (string) @$Args['AdminComment'];
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
$IDSScheme = Array(
			'GroupID'		=> $GroupID,
			'UserID'		=> $UserID,
			'Name'			=> $Name,
			'PackageID'		=> $PackageID,
			'CostDay'		=> $CostDay,
			'CostMonth'		=> $CostMonth,
			'CostInstall'		=> $CostInstall,
			'ServerID'		=> $ServerID,
			'NumServers'		=> $NumServers,
			'RemainServers'		=> $RemainServers,
			'IsCalculateNumServers'	=> $IsCalculateNumServers,
			'IsActive'		=> $IsActive,
			'IsProlong'		=> $IsProlong,
			'MinDaysPay'		=> $MinDaysPay,
			'MinDaysProlong'	=> $MinDaysProlong,
			'MaxDaysPay'		=> $MaxDaysPay,
			'MaxOrders'		=> $MaxOrders,
			'SortID'		=> $SortID,
			'cputype'		=> $cputype,
			'cpuarch'		=> $cpuarch,
			'numcpu'		=> $numcpu,
			'numcores'		=> $numcores,
			'cpufreq'		=> $cpufreq,
			'ram'			=> $ram,
			'raid'			=> $raid,
			'disk1'			=> $disk1,
			'disk2'			=> $disk2,
			'disk3'			=> $disk3,
			'disk4'			=> $disk4,
			'chrate'		=> $chrate,
			'trafflimit'		=> $trafflimit,
			'traffcorrelation'	=> $traffcorrelation,
			'OS'			=> $OS,
			'UserComment'		=> $UserComment,
			'AdminComment'		=> $AdminComment
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
