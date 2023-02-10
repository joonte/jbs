<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
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
$VPSSchemeID		= (integer) @$Args['VPSSchemeID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$Name			=  (string) @$Args['Name'];
$PackageID		=  (string) @$Args['PackageID'];
$CostDay		=   (float) @$Args['CostDay'];
$CostMonth		=   (float) @$Args['CostMonth'];
$CostInstall		=   (float) @$Args['CostInstall'];
$Discount		=  (double) @$Args['Discount'];
$ServersGroupID		= (integer) @$Args['ServersGroupID'];
$Node			=   (array) @$Args['Node'];
$Comment		=  (string) @$Args['Comment'];
$IsReselling		= (boolean) @$Args['IsReselling'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$IsSchemeChangeable	= (boolean) @$Args['IsSchemeChangeable'];
$IsSchemeChange		= (boolean) @$Args['IsSchemeChange'];
$MinDaysPay		= (integer) @$Args['MinDaysPay'];
$MinDaysProlong         = (integer) @$Args['MinDaysProlong'];
$MaxDaysPay		= (integer) @$Args['MaxDaysPay'];
$MaxOrders		= (integer) @$Args['MaxOrders'];
$MinOrdersPeriod	= (integer) @$Args['MinOrdersPeriod'];
$SortID			= (integer) @$Args['SortID'];
$vdslimit		= (integer) @$Args['vdslimit'];
$disklimit		= (integer) @$Args['disklimit'];
$preset			=  (string) @$Args['preset'];
$blkiotune		= (integer) @$Args['blkiotune'];
$isolimitsize		= (integer) @$Args['isolimitsize'];
$isolimitnum		= (integer) @$Args['isolimitnum'];
$snapshot_limit		= (integer) @$Args['snapshot_limit'];
$chrate			= (integer) @$Args['chrate'];
$QuotaUsers		= (integer) @$Args['QuotaUsers'];
$cpu			= (integer) @$Args['cpu'];
$ncpu			= (integer) @$Args['ncpu'];
$mem			= (integer) @$Args['mem'];
$ipalias		= (integer) @$Args['ipalias'];
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
$Count = DB_Count('ServersGroups',Array('ID'=>$ServersGroupID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('SERVERS_GROUP_NOT_FOUND','Группа серверов не найдена');
#-------------------------------------------------------------------------------
// убираем пустые значения
$Nodes = Array();
#-------------------------------------------------------------------------------
Debug(print_r($Node,true));
foreach($Node as $Value)
	if($Value)
		$Nodes[] = $Value;
Debug(print_r($Nodes,true));
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
$IVPSScheme = Array(
  #-----------------------------------------------------------------------------
  'GroupID'             => $GroupID,
  'UserID'              => $UserID,
  'Name'                => $Name,
  'PackageID'           => $PackageID,
  'CostDay'             => $CostDay,
  'CostMonth'           => $CostMonth,
  'CostInstall'		=> $CostInstall,
  'Discount'            => $Discount,
  'ServersGroupID'      => $ServersGroupID,
  'Node'		=> (SizeOf($Nodes) > 0)?Implode(',',$Node):'',
  'Comment'             => $Comment,
  'IsReselling'         => $IsReselling,
  'IsActive'            => $IsActive,
  'IsProlong'           => $IsProlong,
  'IsSchemeChangeable'  => $IsSchemeChangeable,
  'IsSchemeChange'      => $IsSchemeChange,
  'MinDaysPay'          => $MinDaysPay,
  'MinDaysProlong'      => $MinDaysProlong,
  'MaxDaysPay'          => $MaxDaysPay,
  'MaxOrders'		=> $MaxOrders,
  'MinOrdersPeriod'	=> $MinOrdersPeriod,
  'SortID'              => $SortID,
  'vdslimit'            => $vdslimit,
  'disklimit'           => $disklimit,
  'preset'		=> $preset,
  'blkiotune'		=> $blkiotune,
  'isolimitsize'	=> $isolimitsize,
  'isolimitnum'		=> $isolimitnum,
  'snapshot_limit'	=> $snapshot_limit,
  'chrate'              => $chrate,
  'QuotaUsers'          => $QuotaUsers,
  'cpu'                 => $cpu,
  'ncpu'                => $ncpu,
  'mem'                 => $mem,
  'ipalias'             => $ipalias,
);
#-------------------------------------------------------------------------------
if($VPSSchemeID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('VPSSchemes',$IVPSScheme,Array('ID'=>$VPSSchemeID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('VPSSchemes',$IVPSScheme);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
