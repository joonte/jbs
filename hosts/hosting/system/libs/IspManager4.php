<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------
function IspManager4_Logon($Settings,$Params){
  /****************************************************************************/
  $__args_types = Array('array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('Url'=>$Params['Url'],'Args'=>Array('lang'=>$Settings['Language'],'theme'=>$Settings['Theme'],'checkcookie'=>'no','username'=>$Params['Login'],'password'=>$Params['Password'],'func'=>'auth'));
}
#-------------------------------------------------------------------------------
function IspManager4_Get_Domains($Settings){
	/****************************************************************************/
	$__args_types = Array('array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-----------------------------------------------------------------------------
	$Http = Array(
			#---------------------------------------------------------------------------
			'Address'  => $Settings['IP'],
			'Port'     => $Settings['Port'],
			'Host'     => $Settings['Address'],
			'Protocol' => $Settings['Protocol'],
			'Hidden'   => $authinfo,
			'IsLoggin' => FALSE
			);
	#-------------------------------------------------------------------------------
	# достаём список пользователей/реселлеров
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'user'));
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-------------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Elems = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(IsSet($Elems['error']))
		return new gException('GET_USERS_ERROR',$Elems['error']);
	#-----------------------------------------------------------------------------
	$Resellers = Array();
	#-----------------------------------------------------------------------------
	if(Is_Array($Elems)){
		#-------------------------------------------------------------------------------
		foreach($Elems as $Elem)
			if(IsSet($Elem['owner']) && !In_Array($Elem['owner'],$Resellers))
				$Resellers[] = $Elem['owner'];
		#-------------------------------------------------------------------------------
	}
	#Debug(SPrintF('[system/libs/IspManager4.php]: Resellers = %s',print_r($Resellers,true)));
	#-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	$Owners = Array();
	#-----------------------------------------------------------------------------
	if(Is_Array($Elems)){
		#-------------------------------------------------------------------------------
		foreach($Elems as $Elem)
			if(In_Array($Elem['owner'],$Resellers))
				$Owners[$Elem['name']] = $Elem['owner'];
		#-------------------------------------------------------------------------------
	}
	#Debug(SPrintF('[system/libs/IspManager4.php]: Owners = %s',print_r($Owners,true)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# строим выхлопной массив
	$Users = Array();
	#-------------------------------------------------------------------------------
	# достаём список www доменов
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'wwwdomain'));
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Domains = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(!Is_Array($Domains))
		return new gException('WWW_DOMAINS_NOT_FOUND','WWW доменов не обнаружено');
	#-----------------------------------------------------------------------------
	if(IsSet($Domains['error']))
		return new gException('GET_WWW_DOMAINS_ERROR',$Domains['error']);
	#-----------------------------------------------------------------------------
	foreach($Domains as $Domain){
		#---------------------------------------------------------------------------
		if(!IsSet($Domain['owner']))
			continue;
		#---------------------------------------------------------------------------
		$Owner = $Domain['owner'];
		#---------------------------------------------------------------------------
		if(!IsSet($Users[$Owner]))
			$Users[$Owner] = Array();
		#---------------------------------------------------------------------------
		$Users[$Owner][] = $Domain['name'];
		#---------------------------------------------------------------------------
		# домены юзеров реселлеров
		if(IsSet($Owners[$Owner])){
			#---------------------------------------------------------------------------
			if(!IsSet($Users[$Owners[$Owner]]))
				$Users[$Owners[$Owner]] = Array();
			#---------------------------------------------------------------------------
			$Users[$Owners[$Owner]][] = $Domain['name'];
		}
		#---------------------------------------------------------------------------
	}
	#-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	# достаём DNS домены
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'domain'));
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Domains = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(!Is_Array($Domains))
		return new gException('DNS_DOMAINS_NOT_FOUND','DNS доменов не обнаружено');
	#-----------------------------------------------------------------------------
	if(IsSet($Domains['error']))
		return new gException('GET_DNS_DOMAINS_ERROR',$Domains['error']);
	#-----------------------------------------------------------------------------
	foreach($Domains as $Domain){
		#---------------------------------------------------------------------------
		if(!IsSet($Domain['owner']))
			continue;
		#---------------------------------------------------------------------------
		$Owner = $Domain['owner'];
		#---------------------------------------------------------------------------
		if(!IsSet($Users[$Owner]))
			$Users[$Owner] = Array();
		#---------------------------------------------------------------------------
		if(!In_Array($Domain['name'],$Users[$Owner]))
			$Users[$Owner][] = $Domain['name'];
		#---------------------------------------------------------------------------
		# домены юзеров реселлеров
		if(IsSet($Owners[$Owner])){
			#---------------------------------------------------------------------------
			if(!IsSet($Users[$Owners[$Owner]]))
				$Users[$Owners[$Owner]] = Array();
			#---------------------------------------------------------------------------
			if(!In_Array($Domain['name'],$Users[$Owners[$Owner]]))
				$Users[$Owners[$Owner]][] = $Domain['name'];
		}
		#---------------------------------------------------------------------------
	}
	#-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	# достаём Email домены
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'emaildomain'));
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Domains = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(!Is_Array($Domains))
		return new gException('EMAIL_DOMAINS_NOT_FOUND','Email доменов не обнаружено');
	#-----------------------------------------------------------------------------
	if(IsSet($Domains['error']))
		return new gException('GET_EMAIL_DOMAINS_ERROR',$Domains['error']);
	#-----------------------------------------------------------------------------
	foreach($Domains as $Domain){
		#---------------------------------------------------------------------------
		if(!IsSet($Domain['owner']))
			continue;
		#---------------------------------------------------------------------------
		$Owner = $Domain['owner'];
		#---------------------------------------------------------------------------
		if(!IsSet($Users[$Owner]))
			$Users[$Owner] = Array();
		#---------------------------------------------------------------------------
		if(!In_Array($Domain['name'],$Users[$Owner]))
			$Users[$Owner][] = $Domain['name'];
		#---------------------------------------------------------------------------
		# домены юзеров реселлеров
		if(IsSet($Owners[$Owner])){
			#---------------------------------------------------------------------------
			if(!IsSet($Users[$Owners[$Owner]]))
				$Users[$Owners[$Owner]] = Array();
			#---------------------------------------------------------------------------
			if(!In_Array($Domain['name'],$Users[$Owners[$Owner]]))
				$Users[$Owners[$Owner]][] = $Domain['name'];
		}
		#---------------------------------------------------------------------------
	}

	#Debug(SPrintF('[system/libs/IspManager4.php]: UsersList = %s',print_r($Users,true)));
	return $Users;
	#-----------------------------------------------------------------------------
}
#-----------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspManager4_Get_Users($Settings){
  /****************************************************************************/
  $__args_types = Array('array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo,
    'IsLoggin' => FALSE
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'user'));
  if(Is_Error($Response))
    return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray('elem');
  #-----------------------------------------------------------------------------
  $Users = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Users['error']))
    return new gException('GET_USERS_ERROR',$Users['error']);
  #-----------------------------------------------------------------------------
  $Result = Array();
  #-----------------------------------------------------------------------------
  foreach($Users as $User){
    #---------------------------------------------------------------------------
    if(!IsSet($User['name']))
      continue;
    #---------------------------------------------------------------------------
    if(!IsSet($User['owner']))
      continue;
    #---------------------------------------------------------------------------
    if($User['owner'] == $Settings['Login'])
      $Result[] = $User['name'];
  }
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'reseller'));
  if(Is_Error($Response))
    return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray('elem');
  #-----------------------------------------------------------------------------
  $Users = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Users['error']))
    return new gException('GET_USERS_ERROR',$Users['error']);
  #-----------------------------------------------------------------------------
  if(Is_Array($Users)){
    foreach($Users as $User){
      #---------------------------------------------------------------------------
      if(!IsSet($User['name']))
        continue;
      #---------------------------------------------------------------------------
      $Result[] = $User['name'];
    }
  }
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'mgradmin'));
  if(Is_Error($Response))
    return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray('elem');
  #-----------------------------------------------------------------------------
  $Users = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Users['error']))
    return new gException('GET_USERS_ERROR',$Users['error']);
  #-----------------------------------------------------------------------------
  if(Is_Array($Users)){
    foreach($Users as $User){
      #---------------------------------------------------------------------------
      if(!IsSet($User['name']))
        continue;
      #---------------------------------------------------------------------------
      if($User['name'] != $Settings['Login'])
        $Result[] = $User['name'];
    }
  }
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  return $Result;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspManager4_Create($Settings,$Login,$Password,$Domain,$IP,$HostingScheme,$Email,$PersonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','string','string','array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $IsReselling = $HostingScheme['IsReselling'];
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # JBS-543, проверяем наличие такого юзера
  $Request = Array(
                    'authinfo'      => $authinfo,
                    'func'          => $IsReselling?'reseller.edit':'user.edit',
                    'out'           => 'xml',
                    'elid'          => $Login
		   );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(!IsSet($Doc['error']))
    return TRUE;
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  $IDNA = new Net_IDNA_php5();
  $Domain = $IDNA->encode($Domain);
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo'           => $authinfo,
    'out'                => 'xml', # Формат вывода
    'func'            => ($IsReselling?'reseller.edit':'user.edit'), # Целевая функция
    'sok'             => 'yes', # Значение параметра должно быть равно "yes"
    'name'            => $Login, # Имя пользователя (реселлера)
    'passwd'          => $Password, # Пароль
    'confirm'         => $Password, # Подтверждение
    #'domain'          => $Domain, # Домен
    'ip'              => ($IsReselling?'noassign':$IP), # IP-адрес
    'preset'          => $HostingScheme['PackageID'], # Шаблон
    'email'           => $Email,
    #---------------------------------------------------------------------------
    'disklimit'       => $HostingScheme['QuotaDisk'], # Диск
    'ftplimit'        => $HostingScheme['QuotaFTP'], # FTP аккаунты
    'maillimit'       => $HostingScheme['QuotaEmail'], # Почтовые ящики
    'domainlimit'     => $HostingScheme['QuotaDomains'], # Домены
    'webdomainlimit'  => $HostingScheme['QuotaWWWDomains'], # WWW домены
    'maildomainlimit' => $HostingScheme['QuotaEmailDomains'], # Почтовые домены
    'baselimit'       => $HostingScheme['QuotaDBs'], # Базы данных
    'baseuserlimit'   => $HostingScheme['QuotaUsersDBs'], # Пользователи баз данных
    'bandwidthlimit'  => $HostingScheme['QuotaTraffic'], # Трафик
    #---------------------------------------------------------------------------
    'shell'                 => ($HostingScheme['IsShellAccess']?'on':'off'), # Доступ к shell
    'ssl'                   => ($HostingScheme['IsSSLAccess']?'on':'off'), # SSL
    'cgi'                   => ($HostingScheme['IsCGIAccess']?'on':'off'), # CGI
    'ssi'                   => ($HostingScheme['IsSSIAccess']?'on':'off'), # SSI
    'phpmod'                => ($HostingScheme['IsPHPModAccess']?'on':'off'), # PHP как модуль Apache
    'phpcgi'                => ($HostingScheme['IsPHPCGIAccess']?'on':'off'), # PHP как CGI
    'phpfcgi'               => ($HostingScheme['IsPHPFastCGIAccess']?'on':'off'), # PHP как FastCGI
    'safemode'              => ($HostingScheme['IsPHPSafeMode']?'on':'off'), # Безопасный режим
    'cpulimit'              => $HostingScheme['MaxExecutionTime'], # Ограничение на CPU
    'memlimit'              => Ceil($HostingScheme['QuotaMEM']), # Ограничение на память
    'proclimit'             => $HostingScheme['QuotaPROC'], # Кол-во процессов
    'maxclientsvhost'       => $HostingScheme['QuotaMPMworkers'], # mpm-itk
    'mysqlquerieslimit'     => $HostingScheme['mysqlquerieslimit'],      # Запросов к MySQL
    'mysqlupdateslimit'     => $HostingScheme['mysqlupdateslimit'],      # Обновлений MySQL
    'mysqlconnectlimit'     => $HostingScheme['mysqlconnectlimit'],      # Соединений к MySQL
    'mysqluserconnectlimit' => $HostingScheme['mysqluserconnectlimit'],   # Одновременных соединений к MySQL
    'mailrate'              => $HostingScheme['mailrate']       # ограничение на письма, в час
  );
  #-------------------------------------------------------------------------------
  if($HostingScheme['QuotaWWWDomains'])
  	$Request['domain'] = $Domain; # Домен
	#-------------------------------------------------------------------------------
  if(!$IsReselling) {
    $Request['owner'] = $Settings['Login']; # Владелец
  }
  else {
    $Request['userlimit'] = $HostingScheme['QuotaUsers']; # Пользователи
  }
  
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager4_Create]: не удалось соедениться с сервером');
  
  $Response = Trim($Response['Body']);
  
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return new gException('ACCOUNT_CREATE_ERROR','Не удалось создать заказ хостинга');
  #-----------------------------------------------------------------------------
  if(!$Settings['NoRestartCreate']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[IspManager4_Create]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager4_Active($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.enable':'user.enable','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager4_Activate]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return new gException('ACCOUNT_ACTIVATE_ERROR','Не удалось активировать заказ хостинга');
  #-----------------------------------------------------------------------------
  if(!$Settings['NoRestartActive']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[IspManager4_Activate]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager4_Suspend($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.disable':'user.disable','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager4_Suspend]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return new gException('ACCOUNT_SUSPEND_ERROR','Не удалось заблокировать заказ хостинга');
  #-----------------------------------------------------------------------------
  if(!$Settings['NoRestartSuspend']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[IspManager4_Suspend]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager4_Delete($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # JBS-543, проверяем наличие такого юзера
  $Request = Array(
                    'authinfo'      => $authinfo,
                    'func'          => $IsReseller?'reseller.edit':'user.edit',
                    'out'           => 'xml',
                    'elid'          => $Login
		   );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return TRUE;
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # проверка что это реселлер, если так - надо дропать его юзеров
  if($IsReseller){
  	# достаём список всех юзеров
	$Request = Array(
			'authinfo'      => $authinfo,
			'func'          => 'user',
			'out'           => 'xml',
			'su'            => $Login
		);
	#-----------------------------------------------------------------------------
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	$Users = $XML['doc'];
	if(Is_Array($Users)){
		#-----------------------------------------------------------------------------
		# дропаем юзеров
		foreach($Users as $User){
			#-----------------------------------------------------------------------------
			$Request = Array(
					'authinfo'      => $authinfo,
					'func'		=> 'user.delete',
					'out'		=> 'xml',
					'su'		=> $Login,
					'elid'		=> $User['name']
				);
			#-----------------------------------------------------------------------------
			$Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
			if(Is_Error($Response))
				return ERROR | @Trigger_Error('[IspManager4_Delete]: не удалось соедениться с сервером');
			# я так думаю, неважно чё он там ответил, если ответил...
			#-----------------------------------------------------------------------------
		}
		#-----------------------------------------------------------------------------
	}
	#-----------------------------------------------------------------------------
  }
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.delete':'user.delete','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager4_Delete]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return new gException('ACCOUNT_DELETE_ERROR','Не удалось удалить заказ хостинга');
  #-----------------------------------------------------------------------------
  if(!$Settings['NoRestartDelete']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[IspManager4_Delete]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager4_Scheme_Change($Settings,$Login,$HostingScheme){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $IsReselling = $HostingScheme['IsReselling'];
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo'        => $authinfo,
    'out'             => 'xml', # Формат вывода
    'func'            => ($IsReselling?'reseller.edit':'user.edit'), # Целевая функция
    'elid'            => $Login, # Уникальный идентификатор
    'sok'             => 'yes', # Значение параметра должно быть равно "yes"
    'name'            => $Login, # Имя пользователя (реселлера)
    'ip'              => ($IsReselling?'noassign':$Settings['IP']), # IP-адрес
    'preset'          => $HostingScheme['PackageID'], # Шаблон
    #---------------------------------------------------------------------------
    'disklimit'       => $HostingScheme['QuotaDisk'], # Диск
    'ftplimit'        => $HostingScheme['QuotaFTP'], # FTP аккаунты
    'maillimit'       => $HostingScheme['QuotaEmail'], # Почтовые ящики
    'domainlimit'     => $HostingScheme['QuotaDomains'], # Домены
    'webdomainlimit'  => $HostingScheme['QuotaWWWDomains'], # WWW домены
    'maildomainlimit' => $HostingScheme['QuotaEmailDomains'], # Почтовые домены
    'baselimit'       => $HostingScheme['QuotaDBs'], # Базы данных
    'baseuserlimit'   => $HostingScheme['QuotaUsersDBs'], # Пользователи баз данных
    'bandwidthlimit'  => $HostingScheme['QuotaTraffic'],  # Трафик
    'email'           => $HostingScheme['Email'],         # мыло юзера
    #---------------------------------------------------------------------------
    'shell'           => ($HostingScheme['IsShellAccess']?'on':'off'), # Доступ к shell
    'ssl'             => ($HostingScheme['IsSSLAccess']?'on':'off'), # SSL
    'cgi'             => ($HostingScheme['IsCGIAccess']?'on':'off'), # CGI
    'ssi'             => ($HostingScheme['IsSSIAccess']?'on':'off'), # SSI
    'phpmod'          => ($HostingScheme['IsPHPModAccess']?'on':'off'), # PHP как модуль Apache
    'phpcgi'          => ($HostingScheme['IsPHPCGIAccess']?'on':'off'), # PHP как CGI
    'phpfcgi'         => ($HostingScheme['IsPHPFastCGIAccess']?'on':'off'), # PHP как FastCGI
    'safemode'        => ($HostingScheme['IsPHPSafeMode']?'on':'off'), # Безопасный режим
    'cpulimit'        => $HostingScheme['MaxExecutionTime'], # Ограничение на CPU
    'memlimit'        => Ceil($HostingScheme['QuotaMEM']), # Ограничение на память
    'proclimit'       => $HostingScheme['QuotaPROC'], # Кол-во процессов
    'maxclientsvhost' => $HostingScheme['QuotaMPMworkers'], # mpm-itk
    'mysqlquerieslimit'     => $HostingScheme['mysqlquerieslimit'],      # Запросов к MySQL
    'mysqlupdateslimit'     => $HostingScheme['mysqlupdateslimit'],      # Обновлений MySQL
    'mysqlconnectlimit'     => $HostingScheme['mysqlconnectlimit'],      # Соединений к MySQL
    'mysqluserconnectlimit' => $HostingScheme['mysqluserconnectlimit'],   # Одновременных соединений к MySQL
    'mailrate'              => $HostingScheme['mailrate']       # ограничение на письма, в час

  );
  #-----------------------------------------------------------------------------
  if(!$IsReselling)
    $Request['owner'] = $Settings['Login']; # Владелец
  else
    $Request['userlimit'] = $HostingScheme['QuotaUsers']; # Пользователи
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager4_Scheme_Change]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return new gException('SCHEME_CHANGE_ERROR','Не удалось изменить тарифный план для заказа хостинга');
  #-----------------------------------------------------------------------------
  if(!$Settings['NoRestartSchemeChange']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[IspManager4_Scheme_Change]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager4_Password_Change($Settings,$Login,$Password,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # /usr/local/ispmgr/sbin/mgrctl -m ispmgr -o xml usrparam sok=ok atype=atany su=h33502
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => $authinfo,
    'out'      => 'xml',
    'func'     => 'usrparam',
    'su'       => $Login,
    'sok'      => 'ok',
    'atype'    => 'atany',         # разрешаем доступ к панели с любого IP
    'passwd'   => $Password,
    'confirm'  => $Password,
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager4_Password_Change]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return ERROR | @Trigger_Error('[IspManager4_Password_Change]: неверный ответ от сервера');
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return new gException('PASSWORD_CHANGE_ERROR','Не удалось изменить пароль для заказа хостинга');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager4_Get_Email_Boxes($Settings){
  /****************************************************************************/
  $__args_types = Array('array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'emaildomain'));
  if(Is_Error($Response))
    return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray('elem');
  #-----------------------------------------------------------------------------
  $Result = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(!Is_Array($Result))
    return new gException('BOXES_NOT_FOUND','Почтовых доменов не обнаружено');
  #-----------------------------------------------------------------------------
  if(IsSet($Result['error']))
    return ERROR | @Trigger_Error('[IspManager4_Get_Email_Boxes]: не удалось получить список почтовых ящиков');
  #-----------------------------------------------------------------------------
  $Domains = Array();
  #-----------------------------------------------------------------------------
  foreach($Result as $Domain){
    #---------------------------------------------------------------------------
    if(!Is_Array($Domain))
      continue;
    #---------------------------------------------------------------------------
    $Owner = $Domain['owner'];
    #---------------------------------------------------------------------------
    if(!IsSet($Users[$Owner]))
      $Users[$Owner] = Array();
    #---------------------------------------------------------------------------
    $Domains[$Domain['name']] = Array('Owner'=>$Owner,'Boxes'=>Array());
  }
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'email'));
  if(Is_Error($Response))
    return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = $Response['Body'];
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER','Неверный ответ от сервера');
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray('elem',Array('used','limit'));
  #-----------------------------------------------------------------------------
  $Result = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(!Is_Array($Result))
    return new gException('BOXES_NOT_FOUND','Почтовых ящиков не обнаружено');
  #-----------------------------------------------------------------------------
  if(IsSet($Result['error']))
    return ERROR | @Trigger_Error('[IspManager4_Get_Email_Boxes]: не удалось получить список почтовых ящиков');
  #-----------------------------------------------------------------------------
  foreach($Result as $Box){
    #---------------------------------------------------------------------------
    if(!Is_Array($Box))
      continue;
    #---------------------------------------------------------------------------
    $Name = Explode('@',$Box['name']);
    #---------------------------------------------------------------------------
    if(!IsSet($Domains[$Domain = Next($Name)]))
      continue;
    #---------------------------------------------------------------------------
    $Domains[$Domain]['Boxes'][$Box['name']] = Array_Values($Box['size']);
  }
  #-----------------------------------------------------------------------------
  $Users = Array();
  #-----------------------------------------------------------------------------
  foreach($Domains as $DomainID=>$Domain){
    #---------------------------------------------------------------------------
    $Owner = $Domain['Owner'];
    #---------------------------------------------------------------------------
    if(!IsSet($Users[$Owner]))
      $Users[$Owner] = Array();
    #---------------------------------------------------------------------------
    foreach($Domain['Boxes'] as $Email=>$Box)
      $Users[$Owner][$Email] = $Box;
  }
  #-----------------------------------------------------------------------------
  return $Users;
}
#-------------------------------------------------------------------------------




# added by lissyara 2011-08-08 in 11:25 MSK

function IspManager4_AddIP($Settings,$Login,$ID,$Domain,$IP,$AddressType){
        /****************************************************************************/
        $__args_types = Array('array','string','string','string','string','string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
        /****************************************************************************/
        $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
        #-----------------------------------------------------------------------------
        $Http = Array(
                'Address'  => $Settings['IP'],
                'Port'     => $Settings['Port'],
                'Host'     => $Settings['Address'],
                'Protocol' => $Settings['Protocol'],
                'Hidden'   => $authinfo,
                'IsLoggin' => FALSE
        );
        #-----------------------------------------------------------------------------
        $Request = Array(
                'authinfo'      => $authinfo,
                'out'           => 'xml',
                'func'          => 'iplist.edit',
                'elid'          => '',
                'sok'           => 'ok',
                'stat'          => 'assigned',
                'rname'         => $Domain,
                'owner'         => $Login,
                'type'          => StrToLower($AddressType)
        );
        $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
        if(Is_Error($Response))
                return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
        #-----------------------------------------------------------------------------
        $Response = Trim($Response['Body']);
        #-----------------------------------------------------------------------------
        $XML = String_XML_Parse($Response);
        if(Is_Exception($XML))
                return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
        $XML = $XML->ToArray();
        #-----------------------------------------------------------------------------
        $Doc = $XML['doc'];
        #-----------------------------------------------------------------------------
        if(IsSet($Doc['error']))
                return new gException('IP_ADD_CREATE_ERROR','Не удалось добавить IP адрес');
        #-----------------------------------------------------------------------------
        #-----------------------------------------------------------------------------
        #Debug("[system/libs/IspManager4]: to hosting order added IP = " . $Doc['ip']);
        #-----------------------------------------------------------------------------
	$IsUpdate = DB_Update('ExtraIPOrders',Array('Login'=>$Doc['ip']),Array('ID'=>$ID));
        if(Is_Error($IsUpdate))
                return ERROR | @Trigger_Error('[IspManager4_AddIP]: не удалось прописать IP адрес для заказа хостинга ' . $Login);
        #-----------------------------------------------------------------------------
        return TRUE;
}

# added by lissyara 2011-08-10 in 10:13 MSK
#-------------------------------------------------------------------------------
function IspManager4_DeleteIP($Settings,$ExtraIP){
	/****************************************************************************/
        $__args_types = Array('array','string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
        #Debug("ExtraIP order ID = " . $ID);
	/****************************************************************************/
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-----------------------------------------------------------------------------
	$Http = Array(
		#---------------------------------------------------------------------------
		'Address'  => $Settings['IP'],
		'Port'     => $Settings['Port'],
		'Host'     => $Settings['Address'],
		'Protocol' => $Settings['Protocol'],
		'Hidden'   => $authinfo
	);
        # Логика.
        # 1. определяем число доменов на этом адресе.
        # 2. если доменов больше нуля - переносим их на шаред адрес
        # 3. удаляем IP
        #
        # func=iplist.edit&elid=91.227.16.38
        # func=iplist&clickstat=yes
        $Request = Array(
                'authinfo'      => $authinfo,
                'func'          => 'iplist.edit',
                'out'           => 'xml',
                'elid'          => $ExtraIP
        );
        $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
        if(Is_Error($Response))
                return ERROR | @Trigger_Error('[IspManager4_DeleteIP]: не удалось соедениться с сервером');
        $Response = Trim($Response['Body']);
        $XML = String_XML_Parse($Response);
        if(Is_Exception($XML))
                return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
        $XML = $XML->ToArray();
        $Doc = $XML['doc'];
        if(IsSet($Doc['error']))
                return new gException('CHECK_IP_STATUS_ERROR','Не удалось проверить статус IP');
        #-----------------------------------------------------------------------------
        #-----------------------------------------------------------------------------
        if(IsSet($Doc['domain'])){
                # IP in use, have some domains
                # func=wwwdomain
                $Request = Array(
                        'authinfo'      => $authinfo,
                        'func'          => 'wwwdomain',
                        'out'           => 'xml',
                        'su'            => $Settings['UserLogin']
                );
                $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
                if(Is_Error($Response))
                        return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
		#-----------------------------------------------------------------------------
                $Response = Trim($Response['Body']);
		#-----------------------------------------------------------------------------
                $XML = String_XML_Parse($Response);
                if(Is_Exception($XML))
                        return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
		#-----------------------------------------------------------------------------
                $XML = $XML->ToArray('elem');
		#-----------------------------------------------------------------------------
                $Domains = $XML['doc'];
		#-----------------------------------------------------------------------------
                if(!Is_Array($Domains))
                        return new gException('DOMAINS_NOT_FOUND','Доменов не обнаружено');
		#-----------------------------------------------------------------------------
                if(IsSet($Domains['error']))
                        return new gException('GET_DOMAINS_ERROR',$Domains['error']);
		#-----------------------------------------------------------------------------
                foreach($Domains as $Domain){
			#-----------------------------------------------------------------------------
                        if($Domain['ip'] == $ExtraIP){
				#-----------------------------------------------------------------------------
                                #Debug("[system/libs/IspManager4.php]: on IP " . $ExtraIP . " found domain " . $Domain['name']);
                                # get domain settings
                                # func=wwwdomain.edit&elid=ffffff.ru
                                $Request = Array(
                                        'authinfo'      => $authinfo,
                                        'func'          => 'wwwdomain.edit',
                                        'elid'          => $Domain['name'],
                                        'out'           => 'xml',
                                        'su'            => $Settings['UserLogin']
                                );
				#-----------------------------------------------------------------------------
                                $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
                                if(Is_Error($Response))
                                        return ERROR | @Trigger_Error('[IspManager4_DeleteIP]: не удалось соедениться с сервером');
				#-----------------------------------------------------------------------------
                                $Response = Trim($Response['Body']);
				#-----------------------------------------------------------------------------
                                $XML = String_XML_Parse($Response);
				#-----------------------------------------------------------------------------
                                if(Is_Exception($XML))
                                        return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
				#-----------------------------------------------------------------------------
                                $XML = $XML->ToArray();
				#-----------------------------------------------------------------------------
                                $Doc = $XML['doc'];
				#-----------------------------------------------------------------------------
                                if(IsSet($Doc['error']))
                                        return new gException('USER_DATA_TAKE_ERROR','Ошибка получения данных пользователя из системы управления');
				#-----------------------------------------------------------------------------
                                # change settings
                                $Request = Array(
                                        'authinfo'      => $authinfo,
                                        'out'           => 'xml',
                                        'func'          => 'wwwdomain.edit',
                                        'elid'          => $Domain['name'],
                                        'sok'           => 'ok'
                                );
				#-----------------------------------------------------------------------------
                                foreach(Array_Keys($Doc) as $ParamID)
                                        $Request[$ParamID] = $Doc[$ParamID];
				#-----------------------------------------------------------------------------
                                # change IP to shared
                                $Request['ip']  = $Settings['IP'];
				#-----------------------------------------------------------------------------
                                $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
                                if(Is_Error($Response))
                                        return ERROR | @Trigger_Error('[IspManager4_DeleteIP]: не удалось соедениться с сервером');
				#-----------------------------------------------------------------------------
                                $Response = $Response['Body'];
				#-----------------------------------------------------------------------------
                                $XML = String_XML_Parse($Response);
				#-----------------------------------------------------------------------------
                                if(Is_Exception($XML))
                                        return ERROR | @Trigger_Error('[IspManager4_DeleteIP]: неверный ответ от сервера');
				#-----------------------------------------------------------------------------
                                $XML = $XML->ToArray();
				#-----------------------------------------------------------------------------
                                $Doc = $XML['doc'];
				#-----------------------------------------------------------------------------
                                if(IsSet($Doc['error']))
                                        return new gException('IspManager4_DeleteIP','Не удалось изменить IP для домена' . $Domain['name']);
				#-----------------------------------------------------------------------------
                        }
			#-----------------------------------------------------------------------------
                }
		#-----------------------------------------------------------------------------
        }
        #-----------------------------------------------------------------------------
        #-----------------------------------------------------------------------------
	# added by lissyara, 2014-09-19 in 09:02 MSK, for JBS-720
	# 1. достаём все почтовые домены
	# 2. перебираем их, проверяем IP адреса
	# 3. при совпадении с удаляемым - проставляем почтовому домену шареш адрес
	$Request = Array(
			'authinfo'	=> $authinfo,
			'func'		=> 'emaildomain',
			'out'		=> 'xml',
			'su'		=> $Settings['UserLogin']
			);
	#-----------------------------------------------------------------------------
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Domains = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(!Is_Array($Domains)){
		#-----------------------------------------------------------------------------
		Debug('[system/libs/IspManager4.php]: Почтовых доменов не обнаружено');
		#-----------------------------------------------------------------------------
	}else{
		#-----------------------------------------------------------------------------
		if(IsSet($Domains['error']))
			return new gException('GET_DOMAINS_ERROR',$Domains['error']);
		#-----------------------------------------------------------------------------
		foreach($Domains as $Domain){
			#-----------------------------------------------------------------------------
			$Request = Array(
					'authinfo'	=> $authinfo,
					'func'		=> 'emaildomain.edit',
					'elid'		=> $Domain['name'],
					'out'		=> 'xml',
					'su'		=> $Settings['UserLogin']
					);
			#-----------------------------------------------------------------------------
			$Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
			if(Is_Error($Response))
				return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
			#-----------------------------------------------------------------------------
			$Response = Trim($Response['Body']);
			#-----------------------------------------------------------------------------
			$XML = String_XML_Parse($Response);
			if(Is_Exception($XML))
				return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
			#-----------------------------------------------------------------------------
			$XML = $XML->ToArray();
			#-----------------------------------------------------------------------------
			$Doc = $XML['doc'];
			#-----------------------------------------------------------------------------
			if(IsSet($Doc['error']))
				return new gException('IspManager4_DeleteIP',SPrintF('Не удалось изменить IP для email домена %s',$Domain['name']));
			#-----------------------------------------------------------------------------
			if(IsSet($Doc['ip']) && $Doc['ip'] == $ExtraIP){
				#-----------------------------------------------------------------------------
                                # change settings
				$Request = Array(
						'authinfo'      => $authinfo,
						'out'           => 'xml',
						'func'          => 'emaildomain.edit',
						'sok'           => 'ok'
						);
				#-----------------------------------------------------------------------------
				foreach(Array_Keys($Doc) as $ParamID)
					$Request[$ParamID] = $Doc[$ParamID];
				#-----------------------------------------------------------------------------
				# change IP to shared
				$Request['ip']  = $Settings['IP'];
				#-----------------------------------------------------------------------------
				$Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
				if(Is_Error($Response))
					return ERROR | @Trigger_Error('[IspManager4_DeleteIP]: не удалось соедениться с сервером');
				#-----------------------------------------------------------------------------
				$Response = $Response['Body'];
				#-----------------------------------------------------------------------------
				$XML = String_XML_Parse($Response);
				#-----------------------------------------------------------------------------
				if(Is_Exception($XML))
					return ERROR | @Trigger_Error('[IspManager4_DeleteIP]: неверный ответ от сервера');
				#-----------------------------------------------------------------------------
				$XML = $XML->ToArray();
				#-----------------------------------------------------------------------------
				$Doc = $XML['doc'];
				#-----------------------------------------------------------------------------
				if(IsSet($Doc['error']))
					return new gException('IspManager4_DeleteIP',SPrintF('Не удалось изменить IP для email домена %s',$Domain['name']));
				#-----------------------------------------------------------------------------
			}
			#-----------------------------------------------------------------------------
		}
		#-----------------------------------------------------------------------------
	}
	#-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
        # func=iplist.delete&elid=91.227.16.36
        $Request = Array(
                'authinfo'      => $authinfo,
                'func'          => 'iplist.delete',
                'out'           => 'xml',
                'elid'          => $ExtraIP,
                'sok'           => 'ok'
        );
        #Debug(var_export($Settings, true));
	#-----------------------------------------------------------------------------
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[IspManager4_DeleteIP]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
        $Response = Trim($Response['Body']);
        $XML = String_XML_Parse($Response);
        if(Is_Exception($XML))
                return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
        #-----------------------------------------------------------------------------
        $XML = $XML->ToArray();
        #-----------------------------------------------------------------------------
        $Doc = $XML['doc'];
        #-----------------------------------------------------------------------------
        if(IsSet($Doc['error']))
                return new gException('DeleteIP_ERROR','Не удалось удалить IP у заказа хостинга');
        #-----------------------------------------------------------------------------
        #-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	return TRUE;
}
#-------------------------------------------------------------------------------
# added by lissyara 2013-03-07 in 13:47 MSK
#-------------------------------------------------------------------------------
function IspManager4_Get_CPU_Usage($Settings,$TFilter){
	/****************************************************************************/
        $__args_types = Array('array','string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-----------------------------------------------------------------------------
	$Http = Array(
			'Address'  => $Settings['IP'],
			'Port'     => $Settings['Port'],
			'Host'     => $Settings['Address'],
			'Protocol' => $Settings['Protocol'],
			'Hidden'   => $authinfo
			);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# достаём список пользователей/реселлеров
	$Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'user'));
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-------------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Elems = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(IsSet($Elems['error']))
		return new gException('GET_USERS_ERROR',$Elems['error']);
	#-----------------------------------------------------------------------------
	$Resellers = Array();
	#-----------------------------------------------------------------------------
	if(Is_Array($Elems)){
		#-------------------------------------------------------------------------------
		foreach($Elems as $Elem)
			if(!In_Array($Elem['owner'],$Resellers))
				$Resellers[] = $Elem['owner'];
		#-------------------------------------------------------------------------------
	}
	#Debug(SPrintF('[system/libs/IspManager4.php]: Resellers = %s',print_r($Resellers,true)));
	#-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	$Owners = Array();
	#-----------------------------------------------------------------------------
	if(Is_Array($Elems)){
		#-------------------------------------------------------------------------------
		foreach($Elems as $Elem)
			if(In_Array($Elem['owner'],$Resellers))
				$Owners[$Elem['name']] = $Elem['owner'];
		#-------------------------------------------------------------------------------
	}
	#Debug(SPrintF('[system/libs/IspManager4.php]: Owners = %s',print_r($Owners,true)));
	#-------------------------------------------------------------------------------
	# /manager/ispmgr?func=totalresourceusage&tfilter=2013-03-01%20-%202013-03-07&out=xml
	$Request = Array(
			'authinfo'	=> $authinfo,
			'func'		=> 'totalresourceusage',
			'out'		=> 'xml',
			'tfilter'	=> $TFilter
			);
	#-------------------------------------------------------------------------------
        $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
        if(Is_Error($Response))
                return ERROR | @Trigger_Error('[IspManager4_Get_CPU_Usage]: не удалось соедениться с сервером');
	#-------------------------------------------------------------------------------
        $Response = Trim($Response['Body']);
	#-------------------------------------------------------------------------------
        $XML = String_XML_Parse($Response);
        if(Is_Exception($XML))
                return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-------------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Elems = $XML['doc'];
	#-------------------------------------------------------------------------------
	if(IsSet($Elems['error']))
		return new gException('GET_TOTALRESOURCEUSAGE_ERROR',$Elems['error']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# создаём выходной массив
	$Out = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!Is_Array($Elems))
		return $Out;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# массив с полями
	$Fields = Array('memory','utime','stime','etime','io');
	#-------------------------------------------------------------------------------
	foreach($Resellers as $Reseller){
		#-------------------------------------------------------------------------------
		$Out[$Reseller] = Array();
		#-------------------------------------------------------------------------------
		foreach($Fields as $Key)
			$Out[$Reseller][$Key] = 0;
		#-------------------------------------------------------------------------------
	}
	#Debug(SPrintF('[system/libs/IspManager4.php]: Elem = %s',print_r($Out,true)));
	#-------------------------------------------------------------------------------
	# перебираем все данные по нагрузке
	foreach($Elems as $Elem){
		#-------------------------------------------------------------------------------
		if(Array_Key_Exists($Elem['account'], $Out)){
			#-------------------------------------------------------------------------------
			foreach($Fields as $Key)
				$Out[$Elem['account']][$Key] = $Out[$Elem['account']][$Key] + $Elem[$Key];
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			foreach($Fields as $Key)
				$Out[$Elem['account']][$Key] = $Elem[$Key];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(IsSet($Owners[$Elem['account']]))
			foreach($Fields as $Key)
				$Out[$Owners[$Elem['account']]][$Key] = $Out[$Owners[$Elem['account']]][$Key] + $Elem[$Key];
		#------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}

# added by lissyara 2014-01-16 in 10:46 MSK, for JBS-764
#-----------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspManager4_Get_Disk_Usage($Settings){
	/****************************************************************************/
	$__args_types = Array('array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-----------------------------------------------------------------------------
	$Http = Array(
			#---------------------------------------------------------------------------
			'Address'  => $Settings['IP'],
			'Port'     => $Settings['Port'],
			'Host'     => $Settings['Address'],
			'Protocol' => $Settings['Protocol'],
			'Hidden'   => $authinfo,
			'IsLoggin' => FALSE
			);
	#-----------------------------------------------------------------------------
	$Out = Array();
	#-----------------------------------------------------------------------------
	# перебираем функции
	foreach(Array('user','reseller') as $Func){
		#-----------------------------------------------------------------------------
		$Response = Http_Send('/manager/ispmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$Func));
		if(Is_Error($Response))
			return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
		#-----------------------------------------------------------------------------
		$Response = Trim($Response['Body']);
		#-----------------------------------------------------------------------------
		$XML = String_XML_Parse($Response);
		if(Is_Exception($XML))
			return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
		#-----------------------------------------------------------------------------
		$XML = $XML->ToArray('elem',Array('used','limit'));
		#-----------------------------------------------------------------------------
		$Users = $XML['doc'];
		#-----------------------------------------------------------------------------
		if(IsSet($Users['error']))
			return new gException('GET_USERS_ERROR',$Users['error']);
		#-----------------------------------------------------------------------------
		$Result = Array();
		#-----------------------------------------------------------------------------
		if(IsSet($Users) && Is_Array($Users)){
			#-----------------------------------------------------------------------------
			foreach($Users as $User){
				#---------------------------------------------------------------------------
				if(!IsSet($User['name']))
					continue;
				#-----------------------------------------------------------------------------
				if(!IsSet($User['disk'])){
					#-----------------------------------------------------------------------------
					Debug(SPrintF('[system/libs/IspManager4.php]: user (%s) does not have disk limit',$User['name']));
					$User['disk']['limit'] = -1;
					$User['disk']['used'] = -1;
					#-----------------------------------------------------------------------------
				}
				#-----------------------------------------------------------------------------
				$Out[$User['name']] = Array('Limit'=>$User['disk']['limit'],'Used'=>$User['disk']['used'],'Disabled'=>(IsSet($User['disabled'])?TRUE:FALSE));
				#-----------------------------------------------------------------------------
			}
			#-----------------------------------------------------------------------------
		}

#-----------------------------------------------------------------------------
	}
#-----------------------------------------------------------------------------

  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  return $Out;
}












#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------








?>
