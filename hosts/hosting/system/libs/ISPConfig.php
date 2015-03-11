<?php
  /*-- ------------------------------------------------------*/
 /* @author Aleksey Babanin aleksey@alezhen.ru (AleZhen.RU) */
/*--- -----------------------------------------------------*/
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# Функция входа в панель управления ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_Logon($Settings,$Params){
	/****************************************************************************/
	$__args_types = Array('array','array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	# Автологин пока не реализован. В процессе разработки.
	return Array('Url'=>$Settings['Params']['Url'],'Args'=>Array('username'=>$Params['Login'],'passwort'=>$Params['Password']));
}

#-------------------------------------------------------------------------------
# Функция создания клиента в панели ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_Create($Settings,$Login,$Password,$Domain,$IP,$HostingScheme,$Email,$PersonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','string','string','array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
//  $IDNA = new Net_IDNA_php5();
//  $Domain = $IDNA->encode($Domain);
  #-----------------------------------------------------------------------------
  if ($HostingScheme['PackageID'] == '') $HostingScheme['PackageID'] = 0;
  $ISPConfigPHP = "no";
  if ($HostingScheme['IsPHPFastCGIAccess']=='yes') { $ISPConfigPHP = $ISPConfigPHP.",fast-cgi"; }
  if ($HostingScheme['IsPHPCGIAccess']=='yes') { $ISPConfigPHP = $ISPConfigPHP.",cgi"; }
  if ($HostingScheme['IsPHPModAccess']=='yes') { $ISPConfigPHP = $ISPConfigPHP.",mod"; }
//  if ($HostingScheme['']=='yes') { $ISPConfigPHP = $ISPConfigPHP.",suphp"; } #!!! нет в тарифах suphp
//  if ($HostingScheme['']=='yes') { $ISPConfigPerl = 'y'; }else{ $ISPConfigPerl = 'n'; }; # limit_perl
//  if ($HostingScheme['']=='yes') { $ISPConfigRuby = 'y'; }else{ $ISPConfigRuby = 'n'; }; # limit_ruby
//  if ($HostingScheme['']=='yes') { $ISPConfigPython = 'y'; }else{ $ISPConfigPython = 'n'; }; # limit_python
//  if ($HostingScheme['']=='yes') { $ISPConfigSuexec = 'y'; }else{ $ISPConfigSuexec = 'n'; }; #!!! force_suexec
//  if ($HostingScheme['']=='yes') { $ISPConfigHterror = 'y'; }else{ $ISPConfigHterror = 'n'; }; # limit_hterror Custom error docs available
//  if ($HostingScheme['']=='yes') { $ISPConfigWildcard = 'y'; }else{ $ISPConfigWildcard = 'n'; }; # limit_wildcard Wildcard subdomain available
//  if ($HostingScheme['']=='yes') { $ISPConfigBackup = 'y'; }else{ $ISPConfigBackup = 'n'; }; # limit_backup Разрешить резервное копирование
  $Request = Array(
            'contact_name' => $Login,
            'country' => 'RU',
            'default_mailserver' => 1,
            'limit_maildomain' => $HostingScheme['QuotaEmailDomains'], # Почтовые домены
            'limit_mailbox' => $HostingScheme['QuotaEmail'], # Почтовые ящики
            'limit_mailalias' => -1,
            'limit_mailaliasdomain' => -1,
            'limit_mailforward' => $HostingScheme['QuotaEmailForwards'],
            'limit_mailcatchall' => -1,
            'limit_mailrouting' => -1,
            'limit_mailfilter' => -1,
            'limit_fetchmail' => -1,
            'limit_mailquota' => $HostingScheme['QuotaEmailBox'],
            'limit_spamfilter_wblist' => 1,
            'limit_spamfilter_user' => 1,
            'limit_spamfilter_policy' => 1,
            'default_webserver' => 1,
            'limit_web_ip' => '',
            'limit_web_domain' => $HostingScheme['QuotaWWWDomains'], # Домены
            'limit_web_quota' => $HostingScheme['QuotaDisk'], # Диск
            'web_php_options' => $ISPConfigPHP,
            'limit_cgi' => ($HostingScheme['IsCGIAccess']?'y':'n'),
            'limit_ssi' => ($HostingScheme['IsSSIAccess']?'y':'n'),
            'limit_perl' => 'y',
            'limit_ruby' => 'y',
            'limit_python' => 'y',
            'force_suexec' => 'y',
            'limit_hterror' => 'y',
            'limit_wildcard' => 'y',
            'limit_ssl' => ($HostingScheme['IsSSLAccess']?'y':'n'),
            'limit_web_subdomain' => $HostingScheme['QuotaSubDomains'],
            'limit_web_aliasdomain' => $HostingScheme['QuotaParkDomains'], # Кол-во псевдонимов домена
            'limit_ftp_user' => $HostingScheme['QuotaFTP'], # FTP аккаунты
            'limit_shell_user' => 0,
            'ssh_chroot' => 'no',
            'limit_webdav_user' => 0,
            'limit_backup' => 'y',
            'limit_aps' => $HostingScheme['QuotaWebApp'],
            'default_dnsserver' => 1,
            'limit_dns_zone' => $HostingScheme['QuotaWWWDomains'],
            'default_slave_dnsserver' => 1,
            'limit_dns_slave_zone' => $HostingScheme['QuotaWWWDomains'],
            'limit_dns_record' => -1,
            'default_dbserver' => 1,
            'limit_database' => $HostingScheme['QuotaDBs'], # Базы данных
            'limit_database_quota' => -1,
            'limit_cron' => $HostingScheme['QuotaDomains'], # Домены
            'limit_cron_type' => 'full',
            'limit_cron_frequency' => 5,
            'limit_traffic_quota' => $HostingScheme['QuotaTraffic'], # Трафик
            'limit_client' => 0, // If this value is > 0, then the client is a reseller
            'limit_mailmailinglist' => $HostingScheme['QuotaEmailLists'],
            'parent_client_id' => 0,
            'username' => $Login,
            'password' => $Password,
            'language' => 'ru',
            'usertheme' => 'default',
            'template_master' => $HostingScheme['PackageID'],
            'template_additional' => '',
            'created_at' => 0
  );
  $reseller_id = 0; // this id has to be 0 if the client shall not be assigned to admin or if the client is a reseller

	#-----------------------------------------------------------------------------
	# Содаем подключение к панели ISPConfig
	$SoapLocation = SPrintF('%s://%s:%u/remote/index.php',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$SoapUri = SPrintF('%s://%s:%u/remote/',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$client = new SoapClient(null, array('location' => $SoapLocation,
	                                     'uri'      => $SoapUri,
	                                     'trace' => 1,
	                                     'exceptions' => 1));
	# Открываем сессию на панели ISPConfig
	$session_id = $client->login($Settings['Login'], $Settings['Password']);
	# Отправляем данные в панеь ISPConfig
	try {
		$client->client_add($session_id, $reseller_id, $Request);
		$Response = $client->__getLastResponse();
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
	}

	#-----------------------------------------------------------------------------
	# Закрываем сессию на сервере ISPConfig
	$client->logout($session_id);

	#-----------------------------------------------------------------------------
	# Проверяем ответ панели на ошибку запроса
	if(Preg_Match('/data_processing_error/',$Response)) {
		return new gException('WRONG_ANSWER',$Response);
	}else{
		return TRUE;
	}
}

#-------------------------------------------------------------------------------
# Функция активации клиента в панели ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_Active($Settings,$Login,$IsReseller = FALSE){
	/****************************************************************************/
	$__args_types = Array('array','string','boolean');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	$Request = Array(
		'locked' => 'n',
		'canceled' => 'n'
	);
	$reseller_id = 0; // this id has to be 0 if the client shall not be assigned to admin or if the client is a reseller

	#-----------------------------------------------------------------------------
	# Содаем подключение к панели ISPConfig
	$SoapLocation = SPrintF('%s://%s:%u/remote/index.php',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$SoapUri = SPrintF('%s://%s:%u/remote/',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$client = new SoapClient(null, array('location' => $SoapLocation,
                                     'uri'      => $SoapUri,
                                     'trace' => 1,
                                     'exceptions' => 1));
	# Открываем сессию на сервере ISPConfig
	$session_id = $client->login($Settings['Login'], $Settings['Password']);

	#-----------------------------------------------------------------------------
	# Запрашиваем ID клиента по его Имени пользователя
	$client_id = ISPConfig_ClientID($session_id,$client,$Login);

	#-----------------------------------------------------------------------------
	# Запрашиваем параметры клиента
	$ns1 = 'ns1:client_getResponse';
	$Request = ISPConfig_ClientGet($session_id, $client_id,$ns1);

	#-----------------------------------------------------------------------------
	# Выполняем запрос активации клиента на панели ISPConfig
	try {
		$client->client_update($session_id, $client_id, $reseller_id, $Request);
		$Response = $client->__getLastResponse();
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
	}
//Debug(print_r($Response,true));

	#-----------------------------------------------------------------------------
	# Закрываем сессию на сервере ISPConfig
	$client->logout($session_id);

	#-----------------------------------------------------------------------------
	# Проверяем на ошибку запроса ответ панели
	if(Preg_Match('/data_processing_error/',$Response)) {
		return new gException('WRONG_ANSWER',$Response);
	}else{
		return TRUE;
	}
}

#-------------------------------------------------------------------------------
# Функция блокировки клиента в панели ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_Suspend($Settings,$Login,$IsReseller = FALSE){
	/****************************************************************************/
	$__args_types = Array('array','string','boolean');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	$Request = Array(
		'locked' => 'y',
		'canceled' => 'y'
	);
	$reseller_id = 0; // this id has to be 0 if the client shall not be assigned to admin or if the client is a reseller

	#-----------------------------------------------------------------------------
	# Содаем подключение к панели ISPConfig
	$SoapLocation = SPrintF('%s://%s:%u/remote/index.php',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$SoapUri = SPrintF('%s://%s:%u/remote/',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$client = new SoapClient(null, array('location' => $SoapLocation,
                                     'uri'      => $SoapUri,
                                     'trace' => 1,
                                     'exceptions' => 1));
	# Открываем сессию на сервере ISPConfig
	$session_id = $client->login($Settings['Login'], $Settings['Password']);

	#-----------------------------------------------------------------------------
	# Запрашиваем ID клиента по его Имени пользователя
	$client_id = ISPConfig_ClientID($session_id,$client,$Login);

	#-----------------------------------------------------------------------------
	# Запрашиваем параметры клиента
	$ns1 = 'ns1:client_getResponse';
	$Request = ISPConfig_ClientGet($session_id, $client_id,$ns1);

	#-----------------------------------------------------------------------------
	# Выполняем запрос блокировки клиента на панели ISPConfig
	try {
		$client->client_update($session_id, $client_id, $reseller_id, $Request);
		$Response = $client->__getLastResponse();
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
	}
//Debug(print_r($Response,true));

	#-----------------------------------------------------------------------------
	# Закрываем сессию на сервере ISPConfig
	$client->logout($session_id);

	#-----------------------------------------------------------------------------
	# Проверяем на ошибку запроса ответ панели
	if(Preg_Match('/data_processing_error/',$Response)) {
		return new gException('WRONG_ANSWER',$Response);
	}else{
		return TRUE;
	}
}

#-------------------------------------------------------------------------------
# Функция удаления клиента в панели ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_Delete($Settings,$Login,$IsReseller = FALSE){
	/****************************************************************************/
	$__args_types = Array('array','string','boolean');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/

	#-----------------------------------------------------------------------------
	# Содаем подключение к панели ISPConfig
	$SoapLocation = SPrintF('%s://%s:%u/remote/index.php',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$SoapUri = SPrintF('%s://%s:%u/remote/',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$client = new SoapClient(null, array('location' => $SoapLocation,
                                     'uri'      => $SoapUri,
                                     'trace' => 1,
                                     'exceptions' => 1));
	# Открываем сессию на сервере ISPConfig
	$session_id = $client->login($Settings['Login'], $Settings['Password']);

	#-----------------------------------------------------------------------------
	# Запрашиваем ID клиента по его Имени пользователя
	$client_id = ISPConfig_ClientID($session_id,$client,$Login);

	#-----------------------------------------------------------------------------
	# Выполняем запрос удаления клиента на панели ISPConfig
	try {
		$client->client_delete($session_id, $client_id);
		$Response = $client->__getLastResponse();
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
	}
//Debug(print_r($Response,true));

	#-----------------------------------------------------------------------------
	# Закрываем сессию на сервере ISPConfig
	$client->logout($session_id);

	#-----------------------------------------------------------------------------
	# Проверяем на ошибку запроса ответ панели
	if(Preg_Match('/data_processing_error/',$Response)) {
		return new gException('WRONG_ANSWER',$Response);
	}else{
		return TRUE;
	}
}

#-------------------------------------------------------------------------------
# Функция смены пароля клиента в панели ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_Password_Change($Settings,$Login,$Password,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/

	#-----------------------------------------------------------------------------
	# Содаем подключение к панели ISPConfig
	$SoapLocation = SPrintF('%s://%s:%u/remote/index.php',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$SoapUri = SPrintF('%s://%s:%u/remote/',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$client = new SoapClient(null, array('location' => $SoapLocation,
                                     'uri'      => $SoapUri,
                                     'trace' => 1,
                                     'exceptions' => 1));
	# Открываем сессию на сервере ISPConfig
	$session_id = $client->login($Settings['Login'], $Settings['Password']);

	#-----------------------------------------------------------------------------
	# Запрашиваем ID клиента по его Имени пользователя
	$client_id = ISPConfig_ClientID($session_id,$client,$Login);

	#-----------------------------------------------------------------------------
	# Выполняем запрос смены пароля клиента на панели ISPConfig
	try {
		$client->client_change_password($session_id, $client_id, $Password);
		$Response = $client->__getLastResponse();
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
	}
//Debug(print_r($Response,true));

	#-----------------------------------------------------------------------------
	# Закрываем сессию на панели ISPConfig
	$client->logout($session_id);

	#-----------------------------------------------------------------------------
	# Проверяем на ошибку запроса ответ панели
	if(Preg_Match('/data_processing_error/',$Response)) {
		return new gException('WRONG_ANSWER',$Response);
	}else{
		return TRUE;
	}
}

#-------------------------------------------------------------------------------
# Функция смены тарифа клиенту в панели ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_Scheme_Change($Settings,$Login,$HostingScheme){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  if ($HostingScheme['PackageID'] == '') $HostingScheme['PackageID'] = 0;
  $ISPConfigPHP = "no";
  if ($HostingScheme['IsPHPFastCGIAccess']=='yes') { $ISPConfigPHP = $ISPConfigPHP.",fast-cgi"; }
  if ($HostingScheme['IsPHPCGIAccess']=='yes') { $ISPConfigPHP = $ISPConfigPHP.",cgi"; }
  if ($HostingScheme['IsPHPModAccess']=='yes') { $ISPConfigPHP = $ISPConfigPHP.",mod"; }
  $Request = Array(
            'limit_maildomain' => $HostingScheme['QuotaEmailDomains'], # Почтовые домены
            'limit_mailbox' => $HostingScheme['QuotaEmail'], # Почтовые ящики
            'limit_mailforward' => $HostingScheme['QuotaEmailForwards'],
            'limit_mailquota' => $HostingScheme['QuotaEmailBox'],
            'limit_web_domain' => $HostingScheme['QuotaWWWDomains'], # Домены
            'limit_web_quota' => $HostingScheme['QuotaDisk'], # Диск
            'web_php_options' => $ISPConfigPHP,
            'limit_cgi' => ($HostingScheme['IsCGIAccess']?'y':'n'),
            'limit_ssi' => ($HostingScheme['IsSSIAccess']?'y':'n'),
            'limit_ssl' => ($HostingScheme['IsSSLAccess']?'y':'n'),
            'limit_web_subdomain' => $HostingScheme['QuotaSubDomains'],
            'limit_web_aliasdomain' => $HostingScheme['QuotaParkDomains'], # Кол-во псевдонимов домена
            'limit_ftp_user' => $HostingScheme['QuotaFTP'], # FTP аккаунты
            'limit_aps' => $HostingScheme['QuotaWebApp'],
            'limit_dns_zone' => $HostingScheme['QuotaWWWDomains'],
            'limit_dns_slave_zone' => $HostingScheme['QuotaWWWDomains'],
            'limit_database' => $HostingScheme['QuotaDBs'], # Базы данных
            'limit_cron' => $HostingScheme['QuotaDomains'], # Домены
            'limit_traffic_quota' => $HostingScheme['QuotaTraffic'], # Трафик
            'limit_mailmailinglist' => $HostingScheme['QuotaEmailLists'],
            'template_master' => $HostingScheme['PackageID']
  );
	$reseller_id = 0; // this id has to be 0 if the client shall not be assigned to admin or if the client is a reseller

	#-----------------------------------------------------------------------------
	# Содаем подключение к панели ISPConfig
	$SoapLocation = SPrintF('%s://%s:%u/remote/index.php',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$SoapUri = SPrintF('%s://%s:%u/remote/',($Settings['Protocol'] == 'ssl')?'https':'http',$Settings['Address'],$Settings['Port']);
	$client = new SoapClient(null, array('location' => $SoapLocation,
                                     'uri'      => $SoapUri,
                                     'trace' => 1,
                                     'exceptions' => 1));
	# Открываем сессию на сервере ISPConfig
	$session_id = $client->login($Settings['Login'], $Settings['Password']);

	#-----------------------------------------------------------------------------
	# Запрашиваем ID клиента по его Имени пользователя
	$client_id = ISPConfig_ClientID($session_id,$client,$Login);

	#-----------------------------------------------------------------------------
	# Запрашиваем параметры клиента
	$ns1 = 'ns1:client_getResponse';
	$Request = ISPConfig_ClientGet($session_id, $client_id,$ns1);

	#-----------------------------------------------------------------------------
	# Выполняем отправку данных в панель ISPConfig
	try {
		$client->client_update($session_id, $client_id, $reseller_id, $Request);
		$Response = $client->__getLastResponse();
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
	}
	Debug(SPrintF('Ответ сервера => %s',print_r($Response,true)));

	#-----------------------------------------------------------------------------
	# Закрываем сессию на сервере ISPConfig
	$client->logout($session_id);

	#-----------------------------------------------------------------------------
	# Проверяем на ошибку запроса ответ панели
	if(Preg_Match('/data_processing_error/',$Response)) {
		return new gException('WRONG_ANSWER',$Response);
	}else{
		return TRUE;
	}
}

#-------------------------------------------------------------------------------
# Функция запроса данных клиента в панели ISPConfig
#-------------------------------------------------------------------------------
function ISPConfig_ClientGet($session_id, $client_id,$ns1){
	try {
		$client->client_get($session_id, $client_id);
		$Response = $client->__getLastResponse();
		$Body = Trim($Response);
		$XML = String_XML_Parse($Body);
		$XML = $XML->ToArray('item');
		$Array = $XML['SOAP-ENV:Envelope']['SOAP-ENV:Body'][$ns1]['return'];
		$ClientData = Array();
		foreach(Array_Keys($Array) as $Key){
			$ClientData[$Array[$Key]['key']] = $Array[$Key]['value'];
		}
		$Request = array_replace($ClientData, $Request);
		return $Request;
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
		return new gException('WRONG_ANSWER',$Response);
	}
	#-----------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
# Функция запроса ID клиента панели ISPConfig по его Имени пользователя
#-------------------------------------------------------------------------------
function ISPConfig_ClientID($session_id,$client,$Login){
	try {
		$client->client_get_by_username($session_id, $Login);
		$Response = $client->__getLastResponse();
		$Body = Trim($Response);
		$XML = String_XML_Parse($Body);
		$XML = $XML->ToArray('item');
		$Array = $XML['SOAP-ENV:Envelope']['SOAP-ENV:Body']['ns1:client_get_by_usernameResponse']['return'];
		foreach(Array_Keys($Array) as $Key){
			if ($Array[$Key]['key']=='client_id') break;
		}
		return $Array[$Key]['value'];
//		Debug(SPrintF('%s => %s',$Array[$Key]['key'],$Array[$Key]['value'],true));
	} catch (SoapFault $Result) {
		$Response = $client->__getLastResponse();
		$Response = Strip_Tags($Response);
		return new gException('WRONG_ANSWER',$Response);
	}
	#-----------------------------------------------------------------------------
}
?>
