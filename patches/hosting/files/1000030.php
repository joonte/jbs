<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
//Debug(SPrintF('[files/1000030.php]: HOST_ID = %s',HOST_ID));
if(HOST_ID == 'manager.host-food.ru')
	return TRUE;
#-------------------------------------------------------------------------------
// добавляем колонку для информации о тарифе
$IsQuery = DB_Query('ALTER TABLE `HostingSchemes` ADD `SchemeParams` VARCHAR(16384) NOT NULL AFTER `SortID`;');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём список тарифных планов
$HostingSchemes = DB_Select('HostingSchemes',Array('*','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ServersGroupID` = `HostingSchemes`.`ServersGroupID` LIMIT 1) AS `Params`','(SELECT `Address` FROM `Servers` WHERE `Servers`.`ServersGroupID` = `HostingSchemes`.`ServersGroupID` LIMIT 1) AS `Address`'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	// массив сопоставления
	$Collation = Array(
			/* ISPmanager 4 */
			'disklimit'	=> 'QuotaDisk',
			'ftplimit'	=> 'QuotaFTP',
			'maillimit'	=> 'QuotaEmail',
			'domainlimit'	=> 'QuotaDomains',
			'webdomainlimit'=> 'QuotaWWWDomains',
			'maildomainlimit'=> 'QuotaEmailDomains',
			'baselimit'	=> 'QuotaDBs',
			'baseuserlimit'	=> 'QuotaUsersDBs',
			'shell'		=> 'IsShellAccess',
			'ssl'		=> 'IsSSLAccess',
			'cgi'		=> 'IsCGIAccess',
			'ssi'		=> 'IsSSIAccess',
			'phpmod'	=> 'IsPHPModAccess',
			'phpcgi'	=> 'IsPHPCGIAccess',
			'cpulimit'	=> 'MaxExecutionTime',
			'memlimit'	=> 'QuotaMEM',
			'proclimit'	=> 'QuotaPROC',
			'maxclientsvhost'=> 'QuotaMPMworkers',
			'mysqlquerieslimit'=> 'mysqlquerieslimit',
			'mysqlupdateslimit'=> 'mysqlupdateslimit',
			'mysqlconnectlimit'=> 'mysqlconnectlimit',
			'mysqluserconnectlimit'=> 'mysqluserconnectlimit',
			'mailrate'	=> 'QuotaEmail',
			/* ISPmanager 5 */
			'limit_quota'	=> 'QuotaDisk',
			'limit_ftp_users'=> 'QuotaFTP',
			'limit_emails'	=> 'QuotaEmail',
			'limit_webdomains'=> 'QuotaWWWDomains',
			'limit_domains'	=> 'QuotaDomains',
			'limit_emaildomains'=> 'QuotaEmailDomains',
			'limit_db'	=> 'QuotaDBs',
			'limit_db_users'=> 'QuotaUsersDBs',
			'limit_cpu'	=> 'MaxExecutionTime',
			'limit_memory'	=> 'QuotaMEM',
			'limit_process'	=> 'QuotaPROC',
			'limit_mailrate'=> 'QuotaEmail',
			'limit_maxclientsvhost'=> 'QuotaMPMworkers',
			'limit_mysql_maxuserconn'=> 'mysqluserconnectlimit',
			'limit_mysql_maxconn'=> 'mysqlconnectlimit',
			'limit_mysql_query'=> 'mysqlquerieslimit',
			'limit_mysql_update'=> 'mysqlupdateslimit',
			'limit_users'	=> 'QuotaUsers',
			'php_enable'	=> 'IsPHPModAccess',
			'limit_cgi'	=> 'IsCGIAccess',
			'limit_php_mode_mod'=> 'IsPHPModAccess',
			'limit_shell'	=> 'IsShellAccess',
			'limit_ssl'	=> 'IsSSLAccess',
			/* brainy */
			'emailboxes'	=> 'QuotaEmail',
			'sites'		=> 'QuotaWWWDomains',
			'databases'	=> 'QuotaDBs',
			'subdomains'	=> 'QuotaParkDomains',
			'mailperhour'	=> 'mailrate',
			'disk'		=> 'QuotaDisk',
			'ftp_accounts'	=> 'QuotaFTP',
			'shell_access'	=> 'IsShellAccess',
			'shell'		=> 'IsShellAccess',
			'databases_max_updates'=> 'mysqlupdateslimit',
			'databases_max_user_connections'=> 'mysqluserconnectlimit',
			'databases_max_queries'=> 'mysqlquerieslimit',
			'databases_max_connections'=> 'limit_mysql_maxconn',
			'ctl_max_user_memory'=> 'QuotaMEM',
			'ctl_max_user_cpu'=> 'QuotaCPU',
			'dns_zones'	=> 'QuotaDomains',
			);
	#-------------------------------------------------------------------------------
	foreach($HostingSchemes as $HostingScheme){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[files/1000030.php]: Name = %s; Address = %s; SystemID = %s',$HostingScheme['Name'],$HostingScheme['Address'],$HostingScheme['Params']['SystemID']));
		// считываем шаблон XML
		$Fields = System_XML(SPrintF('config/Schemes.%s.xml',$HostingScheme['Params']['SystemID']));
		if(Is_Error($Fields))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		//Debug(SPrintF('[files/1000030.php]: %s',print_r($HostingScheme['SchemeParams'],true)));
		//#-------------------------------------------------------------------------------
		$SchemeParams = $Internal = Array();
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($Fields) as $Key){
			#-------------------------------------------------------------------------------
			$Field = $Fields[$Key];
			#-------------------------------------------------------------------------------
			//Debug(SPrintF('[files/1000030.php]: Key = %s; Field = %s',$Key,print_r($Field,true)));
			#-------------------------------------------------------------------------------
			// задаём дефолт - то что в XML
			$SchemeParams[$Key] = $Field['Value'];
			#-------------------------------------------------------------------------------
			// перекладываем из текущих полей в новые
			if(IsSet($Collation[$Key]))
				if(IsSet($HostingScheme[$Collation[$Key]]))
					$SchemeParams[$Key] = $HostingScheme[$Collation[$Key]];
			#-------------------------------------------------------------------------------
			if(IsSet($Field['InternalName']))
				$Internal[$Field['InternalName']] = $SchemeParams[$Key];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// для минимализации поиска по базе при отображении тарифа
		$SchemeParams['SystemID']       = $HostingScheme['Params']['SystemID'];
		$SchemeParams['InternalName']   = $Internal;
		#-------------------------------------------------------------------------------

		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('HostingSchemes',Array('SchemeParams'=>$SchemeParams),Array('ID'=>$HostingScheme['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
