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
$HostingSchemeID       = (integer) @$Args['HostingSchemeID'];
$GroupID               = (integer) @$Args['GroupID'];
$UserID                = (integer) @$Args['UserID'];
$Name                  =  (string) @$Args['Name'];
$PackageID             =  (string) @$Args['PackageID'];
$CostDay               =   (float) @$Args['CostDay'];
$CostMonth             =   (float) @$Args['CostMonth'];
$Discount	       =  (double) @$Args['Discount'];
$ServersGroupID        = (integer) @$Args['ServersGroupID'];
$HardServerID          = (integer) @$Args['HardServerID'];
$Comment               =  (string) @$Args['Comment'];
$IsReselling           = (boolean) @$Args['IsReselling'];
$IsActive              = (boolean) @$Args['IsActive'];
$IsProlong             = (boolean) @$Args['IsProlong'];
$IsSchemeChangeable    = (boolean) @$Args['IsSchemeChangeable'];
$IsSchemeChange        = (boolean) @$Args['IsSchemeChange'];
$MinDaysPay            = (integer) @$Args['MinDaysPay'];
$MinDaysProlong        = (integer) @$Args['MinDaysProlong'];
$MaxDaysPay            = (integer) @$Args['MaxDaysPay'];
$MaxOrders             = (integer) @$Args['MaxOrders'];
$MinOrdersPeriod	= (integer) @$Args['MinOrdersPeriod'];
$SortID                = (integer) @$Args['SortID'];
$QuotaDisk             = (integer) @$Args['QuotaDisk'];
$QuotaEmail            = (integer) @$Args['QuotaEmail'];
$QuotaDomains          = (integer) @$Args['QuotaDomains'];
$QuotaFTP              = (integer) @$Args['QuotaFTP'];
$QuotaParkDomains      = (integer) @$Args['QuotaParkDomains'];
$QuotaSubDomains       = (integer) @$Args['QuotaSubDomains'];
$QuotaDBs              = (integer) @$Args['QuotaDBs'];
$QuotaTraffic          = (integer) @$Args['QuotaTraffic'];
$QuotaEmailAutoResp    = (integer) @$Args['QuotaEmailAutoResp'];
$QuotaEmailLists       = (integer) @$Args['QuotaEmailLists'];
$QuotaEmailForwards    = (integer) @$Args['QuotaEmailForwards'];
$QuotaUsers            = (integer) @$Args['QuotaUsers'];
$IsShellAccess         = (boolean) @$Args['IsShellAccess'];
$IsSSLAccess           = (boolean) @$Args['IsSSLAccess'];
$IsCGIAccess           = (boolean) @$Args['IsCGIAccess'];
$IsDnsControll         = (boolean) @$Args['IsDnsControll'];
$QuotaWWWDomains       = (integer) @$Args['QuotaWWWDomains'];
$QuotaEmailDomains     = (integer) @$Args['QuotaEmailDomains'];
$QuotaUsersDBs         = (integer) @$Args['QuotaUsersDBs'];
$QuotaCPU              =   (float) @$Args['QuotaCPU'];
$MaxExecutionTime      = (integer) @$Args['MaxExecutionTime'];
$QuotaMEM              =   (float) @$Args['QuotaMEM'];
$QuotaPROC             = (integer) @$Args['QuotaPROC'];
$QuotaMPMworkers       = (integer) @$Args['QuotaMPMworkers'];
$mysqlquerieslimit     = (integer) @$Args['mysqlquerieslimit'];
$mysqlupdateslimit     = (integer) @$Args['mysqlupdateslimit'];
$mysqlconnectlimit     = (integer) @$Args['mysqlconnectlimit'];
$mysqluserconnectlimit = (integer) @$Args['mysqluserconnectlimit'];
$mailrate	       = (integer) @$Args['mailrate'];
$IsSSIAccess           = (boolean) @$Args['IsSSIAccess'];
$IsPHPModAccess        = (boolean) @$Args['IsPHPModAccess'];
$IsPHPCGIAccess        = (boolean) @$Args['IsPHPCGIAccess'];
$IsPHPFastCGIAccess    = (boolean) @$Args['IsPHPFastCGIAccess'];
$IsPHPSafeMode         = (boolean) @$Args['IsPHPSafeMode'];
$QuotaAddonDomains     = (integer) @$Args['QuotaAddonDomains'];
$QuotaWebUsers         = (integer) @$Args['QuotaWebUsers'];
$QuotaEmailBox         = (integer) @$Args['QuotaEmailBox'];
$QuotaEmailGroups      = (integer) @$Args['QuotaEmailGroups'];
$QuotaWebApp           = (integer) @$Args['QuotaWebApp'];
$IsCreateDomains       = (boolean) @$Args['IsCreateDomains'];
$IsManageHosting       = (boolean) @$Args['IsManageHosting'];
$IsManageQuota         = (boolean) @$Args['IsManageQuota'];
$IsManageSubdomains    = (boolean) @$Args['IsManageSubdomains'];
$IsChangeLimits        = (boolean) @$Args['IsChangeLimits'];
$IsManageLog           = (boolean) @$Args['IsManageLog'];
$IsManageCrontab       = (boolean) @$Args['IsManageCrontab'];
$IsManageAnonFtp       = (boolean) @$Args['IsManageAnonFtp'];
$IsManageWebapps       = (boolean) @$Args['IsManageWebapps'];
$IsManageMaillists     = (boolean) @$Args['IsManageMaillists'];
$IsManageDrWeb         = (boolean) @$Args['IsManageDrWeb'];
$IsMakeDumps           = (boolean) @$Args['IsMakeDumps'];
$IsSiteBuilder         = (boolean) @$Args['IsSiteBuilder'];
$IsRemoteInterface     = (boolean) @$Args['IsRemoteInterface'];
$IsManagePerformance   = (boolean) @$Args['IsManagePerformance'];
$IsCpAccess            = (boolean) @$Args['IsCpAccess'];
$IsManageDomainAliases = (boolean) @$Args['IsManageDomainAliases'];
$IsManageIISAppPool    = (boolean) @$Args['IsManageIISAppPool'];
$IsDashBoard           = (boolean) @$Args['IsDashBoard'];
$IsStdGIU              = (boolean) @$Args['IsStdGIU'];
$IsManageDashboard     = (boolean) @$Args['IsManageDashboard'];
$IsManageSubFtp        = (boolean) @$Args['IsManageSubFtp'];
$ISManageSpamFilter    = (boolean) @$Args['ISManageSpamFilter'];
$IsLocalBackups        = (boolean) @$Args['IsLocalBackups'];
$IsFtpBackups          = (boolean) @$Args['IsFtpBackups'];
$IsAnonimousFTP        = (boolean) @$Args['IsAnonimousFTP'];
$IsSpamAssasing        = (boolean) @$Args['IsSpamAssasing'];
$IsPHPAccess           = (boolean) @$Args['IsPHPAccess'];
$IsCatchAll            = (boolean) @$Args['IsCatchAll'];
$IsSystemInfo          = (boolean) @$Args['IsSystemInfo'];
$field1                =  (string) @$Args['field1'];
$field2                =  (string) @$Args['field2'];
$field3                =  (string) @$Args['field3'];
#-------------------------------------------------------------------------------
$Count = DB_Count('Groups',Array('ID'=>$GroupID));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
  return new gException('GROUP_NOT_FOUND','Группа не найдена');
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
$IHostingScheme = Array(
  #-----------------------------------------------------------------------------
  'GroupID'               => $GroupID,
  'UserID'                => $UserID,
  'Name'                  => $Name,
  'PackageID'             => $PackageID,
  'CostDay'               => $CostDay,
  'CostMonth'             => $CostMonth,
  'Discount'		  => $Discount,
  'ServersGroupID'        => $ServersGroupID,
  'HardServerID'          => $HardServerID,
  'Comment'               => $Comment,
  'IsReselling'           => $IsReselling,
  'IsActive'              => $IsActive,
  'IsProlong'             => $IsProlong,
  'IsSchemeChangeable'    => $IsSchemeChangeable,
  'IsSchemeChange'        => $IsSchemeChange,
  'MinDaysPay'            => $MinDaysPay,
  'MinDaysProlong'        => $MinDaysProlong,
  'MaxDaysPay'            => $MaxDaysPay,
  'MaxOrders'             => $MaxOrders,
  'MinOrdersPeriod'		=> $MinOrdersPeriod,
  'SortID'                => $SortID,
  'QuotaDisk'             => $QuotaDisk,
  'QuotaEmail'            => $QuotaEmail,
  'QuotaDomains'          => $QuotaDomains,
  'QuotaFTP'              => $QuotaFTP,
  'QuotaParkDomains'      => $QuotaParkDomains,
  'QuotaSubDomains'       => $QuotaSubDomains,
  'QuotaDBs'              => $QuotaDBs,
  'QuotaTraffic'          => $QuotaTraffic,
  'QuotaEmailAutoResp'    => $QuotaEmailAutoResp,
  'QuotaEmailLists'       => $QuotaEmailLists,
  'QuotaEmailForwards'    => $QuotaEmailForwards,
  'QuotaUsers'            => $QuotaUsers,
  'IsShellAccess'         => $IsShellAccess,
  'IsSSLAccess'           => $IsSSLAccess,
  'IsCGIAccess'           => $IsCGIAccess,
  'IsDnsControll'         => $IsDnsControll,
  'QuotaWWWDomains'       => $QuotaWWWDomains,
  'QuotaEmailDomains'     => $QuotaEmailDomains,
  'QuotaUsersDBs'         => $QuotaUsersDBs,
  'QuotaCPU'              => $QuotaCPU,
  'MaxExecutionTime'      => $MaxExecutionTime,
  'QuotaMEM'              => $QuotaMEM,
  'QuotaPROC'             => $QuotaPROC,
  'QuotaMPMworkers'       => $QuotaMPMworkers,
  'mysqlquerieslimit'     => $mysqlquerieslimit,
  'mysqlupdateslimit'     => $mysqlupdateslimit,
  'mysqlconnectlimit'     => $mysqlconnectlimit,
  'mysqluserconnectlimit' => $mysqluserconnectlimit,
  'mailrate'		  => $mailrate,
  'IsSSIAccess'           => $IsSSIAccess,
  'IsPHPModAccess'        => $IsPHPModAccess,
  'IsPHPCGIAccess'        => $IsPHPCGIAccess,
  'IsPHPFastCGIAccess'    => $IsPHPFastCGIAccess,
  'IsPHPSafeMode'         => $IsPHPSafeMode,
  'QuotaAddonDomains'     => $QuotaAddonDomains,
  'QuotaWebUsers'         => $QuotaWebUsers,
  'QuotaEmailBox'         => $QuotaEmailBox,
  'QuotaEmailGroups'      => $QuotaEmailGroups,
  'QuotaWebApp'           => $QuotaWebApp,
  'IsCreateDomains'       => $IsCreateDomains,
  'IsManageHosting'       => $IsManageHosting,
  'IsManageQuota'         => $IsManageQuota,
  'IsManageSubdomains'    => $IsManageSubdomains,
  'IsChangeLimits'        => $IsChangeLimits,
  'IsManageLog'           => $IsManageLog,
  'IsManageCrontab'       => $IsManageCrontab,
  'IsManageAnonFtp'       => $IsManageAnonFtp,
  'IsManageWebapps'       => $IsManageWebapps,
  'IsManageMaillists'     => $IsManageMaillists,
  'IsManageDrWeb'         => $IsManageDrWeb,
  'IsMakeDumps'           => $IsMakeDumps,
  'IsSiteBuilder'         => $IsSiteBuilder,
  'IsRemoteInterface'     => $IsRemoteInterface,
  'IsManagePerformance'   => $IsManagePerformance,
  'IsCpAccess'            => $IsCpAccess,
  'IsManageDomainAliases' => $IsManageDomainAliases,
  'IsManageIISAppPool'    => $IsManageIISAppPool,
  'IsDashBoard'           => $IsDashBoard,
  'IsStdGIU'              => $IsStdGIU,
  'IsManageDashboard'     => $IsManageDashboard,
  'IsManageSubFtp'        => $IsManageSubFtp,
  'ISManageSpamFilter'    => $ISManageSpamFilter,
  'IsLocalBackups'        => $IsLocalBackups,
  'IsFtpBackups'          => $IsFtpBackups,
  'IsAnonimousFTP'        => $IsAnonimousFTP,
  'IsPHPAccess'           => $IsPHPAccess,
  'IsSpamAssasing'        => $IsSpamAssasing,
  'IsCatchAll'            => $IsCatchAll,
  'IsSystemInfo'          => $IsSystemInfo,
  'field1'                => $field1,
  'field2'                => $field2,
  'field3'                => $field3
);
#-------------------------------------------------------------------------------
if($HostingSchemeID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('HostingSchemes',$IHostingScheme,Array('ID'=>$HostingSchemeID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('HostingSchemes',$IHostingScheme);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
