<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------
function DNSmanager4_Logon($Settings,$Params){
	/******************************************************************************/
	$__args_types = Array('array','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	return Array('Url'=>$Settings['Params']['Url'],'Args'=>Array('lang'=>$Settings['Params']['Language'],'theme'=>$Settings['Params']['Theme'],'checkcookie'=>'no','username'=>$Params['Login'],'password'=>$Params['Password'],'func'=>'auth'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function DNSmanager4_Get_Users($Settings){
  /****************************************************************************/
  $__args_types = Array('array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo,
    'IsLoggin' => FALSE
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'user'));
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
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'reseller'));
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
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'mgradmin'));
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
function DNSmanager4_Create($Settings,$Login,$Password,$DNSmanagerScheme){
	/******************************************************************************/
	$__args_types = Array('array','string','string','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-------------------------------------------------------------------------------
	$Http = Array(
			'Address'  => $Settings['Address'],
			'Port'     => $Settings['Port'],
			'Host'     => $Settings['Address'],
			'Protocol' => $Settings['Protocol'],
			'Hidden'   => $authinfo
			);
	#-------------------------------------------------------------------------------
	$IsReselling = $DNSmanagerScheme['IsReselling'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# JBS-543, проверяем наличие такого юзера
	$Request = Array(
			'authinfo'      => $authinfo,
			'func'          => $IsReselling?'reseller.edit':'user.edit',
			'out'           => 'xml',
			'elid'          => $Login
			);
	#-------------------------------------------------------------------------------
	$Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
	if(Is_Error($Response))
		return new gException('NOT_CONNECTED_TO_SERVER','Не удалось соедениться с сервером');
	#-------------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-------------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	#-------------------------------------------------------------------------------
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-------------------------------------------------------------------------------
	$XML = $XML->ToArray();
	#-------------------------------------------------------------------------------
	$Doc = $XML['doc'];
	#-------------------------------------------------------------------------------
	if(!IsSet($Doc['error']))
		return TRUE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Request = Array(
			'authinfo'	=> $authinfo,
			'out'		=> 'xml', # Формат вывода
			'func'		=> ($IsReselling?'reseller.edit':'user.edit'), # Целевая функция
			'sok'		=> 'yes', # Значение параметра должно быть равно "yes"
			'name'		=> $Login, # Имя пользователя (реселлера)
			'passwd'	=> $Password, # Пароль
			'confirm'	=> $Password, # Подтверждение
			'domlimit'	=> $DNSmanagerScheme['DomainLimit'],
			'view'		=> $DNSmanagerScheme['ViewArea']
			);
	#-------------------------------------------------------------------------------
	if($DNSmanagerScheme['Reseller'])
		$Request['su'] = $DNSmanagerScheme['Reseller'];
	#-------------------------------------------------------------------------------
	$Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[DNSmanager4_Create]: не удалось соедениться с сервером');
	#-------------------------------------------------------------------------------  
	$Response = Trim($Response['Body']);
	#-------------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-------------------------------------------------------------------------------
	$XML = $XML->ToArray();
	#-------------------------------------------------------------------------------
	$Doc = $XML['doc'];
	#-------------------------------------------------------------------------------
	if(IsSet($Doc['error']))
		return new gException('ACCOUNT_CREATE_ERROR','Не удалось создать заказ вторичного DNS');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
function DNSmanager4_Active($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.enable':'user.enable','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DNSmanager4_Activate]: не удалось соедениться с сервером');
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
    return new gException('ACCOUNT_ACTIVATE_ERROR','Не удалось активировать заказ вторичного DNS');
  #-----------------------------------------------------------------------------
  if(!$Settings['Params']['NoRestartActive']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[DNSmanager4_Activate]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function DNSmanager4_Suspend($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.disable':'user.disable','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DNSmanager4_Suspend]: не удалось соедениться с сервером');
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
    return new gException('ACCOUNT_SUSPEND_ERROR','Не удалось заблокировать заказ вторичного DNS');
  #-----------------------------------------------------------------------------
  if(!$Settings['Params']['NoRestartSuspend']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[DNSmanager4_Suspend]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function DNSmanager4_Delete($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
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
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
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
	$Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
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
			$Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
			if(Is_Error($Response))
				return ERROR | @Trigger_Error('[DNSmanager4_Delete]: не удалось соедениться с сервером');
			# я так думаю, неважно чё он там ответил, если ответил...
			#-----------------------------------------------------------------------------
		}
		#-----------------------------------------------------------------------------
	}
	#-----------------------------------------------------------------------------
  }
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.delete':'user.delete','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DNSmanager4_Delete]: не удалось соедениться с сервером');
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
    return new gException('ACCOUNT_DELETE_ERROR','Не удалось удалить заказ вторичного DNS');
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function DNSmanager4_Scheme_Change($Settings,$Login,$DNSmanagerScheme){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  $IsReselling = $DNSmanagerScheme['IsReselling'];
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo'        => $authinfo,
    'out'             => 'xml', # Формат вывода
    'func'            => ($IsReselling?'reseller.edit':'user.edit'), # Целевая функция
    'elid'            => $Login, # Уникальный идентификатор
    'sok'             => 'yes', # Значение параметра должно быть равно "yes"
    'name'            => $Login, # Имя пользователя (реселлера)
    'ip'              => ($IsReselling?'noassign':$Settings['Params']['IP']), # IP-адрес
    'preset'          => $DNSmanagerScheme['PackageID'], # Шаблон
    #---------------------------------------------------------------------------
    'disklimit'       => $DNSmanagerScheme['QuotaDisk'], # Диск
    'ftplimit'        => $DNSmanagerScheme['QuotaFTP'], # FTP аккаунты
    'maillimit'       => $DNSmanagerScheme['QuotaEmail'], # Почтовые ящики
    'domainlimit'     => $DNSmanagerScheme['QuotaDomains'], # Домены
    'webdomainlimit'  => $DNSmanagerScheme['QuotaWWWDomains'], # WWW домены
    'maildomainlimit' => $DNSmanagerScheme['QuotaEmailDomains'], # Почтовые домены
    'baselimit'       => $DNSmanagerScheme['QuotaDBs'], # Базы данных
    'baseuserlimit'   => $DNSmanagerScheme['QuotaUsersDBs'], # Пользователи баз данных
    'bandwidthlimit'  => $DNSmanagerScheme['QuotaTraffic'],  # Трафик
    'email'           => $DNSmanagerScheme['Email'],         # мыло юзера
    #---------------------------------------------------------------------------
    'shell'           => ($DNSmanagerScheme['IsShellAccess']?'on':'off'), # Доступ к shell
    'ssl'             => ($DNSmanagerScheme['IsSSLAccess']?'on':'off'), # SSL
    'cgi'             => ($DNSmanagerScheme['IsCGIAccess']?'on':'off'), # CGI
    'ssi'             => ($DNSmanagerScheme['IsSSIAccess']?'on':'off'), # SSI
    'phpmod'          => ($DNSmanagerScheme['IsPHPModAccess']?'on':'off'), # PHP как модуль Apache
    'phpcgi'          => ($DNSmanagerScheme['IsPHPCGIAccess']?'on':'off'), # PHP как CGI
    'phpfcgi'         => ($DNSmanagerScheme['IsPHPFastCGIAccess']?'on':'off'), # PHP как FastCGI
    'safemode'        => ($DNSmanagerScheme['IsPHPSafeMode']?'on':'off'), # Безопасный режим
    'cpulimit'        => $DNSmanagerScheme['MaxExecutionTime'], # Ограничение на CPU
    'memlimit'        => Ceil($DNSmanagerScheme['QuotaMEM']), # Ограничение на память
    'proclimit'       => $DNSmanagerScheme['QuotaPROC'], # Кол-во процессов
    'maxclientsvhost' => $DNSmanagerScheme['QuotaMPMworkers'], # mpm-itk
    'mysqlquerieslimit'     => $DNSmanagerScheme['mysqlquerieslimit'],      # Запросов к MySQL
    'mysqlupdateslimit'     => $DNSmanagerScheme['mysqlupdateslimit'],      # Обновлений MySQL
    'mysqlconnectlimit'     => $DNSmanagerScheme['mysqlconnectlimit'],      # Соединений к MySQL
    'mysqluserconnectlimit' => $DNSmanagerScheme['mysqluserconnectlimit'],   # Одновременных соединений к MySQL
    'mailrate'              => $DNSmanagerScheme['mailrate']       # ограничение на письма, в час

  );
  #-----------------------------------------------------------------------------
  if(!$IsReselling)
    $Request['owner'] = $Settings['Login']; # Владелец
  else
    $Request['userlimit'] = $DNSmanagerScheme['QuotaUsers']; # Пользователи
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DNSmanager4_Scheme_Change]: не удалось соедениться с сервером');
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
    return new gException('SCHEME_CHANGE_ERROR','Не удалось изменить тарифный план для заказа вторичного DNS');
  #-----------------------------------------------------------------------------
  if(!$Settings['Params']['NoRestartSchemeChange']){
          $Request = Array(
            #---------------------------------------------------------------------------
            'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
            'out'      => 'xml',
            'func'     => 'restart'
          );
          #-----------------------------------------------------------------------------
          $Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
          if(Is_Error($Response))
            return ERROR | @Trigger_Error('[DNSmanager4_Scheme_Change]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function DNSmanager4_Password_Change($Settings,$Login,$Password,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $authinfo
  );
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # /usr/local/dnsmgr/sbin/mgrctl -m dnsmgr -o xml usrparam sok=ok atype=atany su=h33502
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
  $Response = Http_Send('/manager/dnsmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DNSmanager4_Password_Change]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return ERROR | @Trigger_Error('[DNSmanager4_Password_Change]: неверный ответ от сервера');
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Doc = $XML['doc'];
  #-----------------------------------------------------------------------------
  if(IsSet($Doc['error']))
    return new gException('PASSWORD_CHANGE_ERROR','Не удалось изменить пароль для заказа вторичного DNS');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------








?>
