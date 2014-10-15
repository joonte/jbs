<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Find_Free_License($ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  # на входе функции надо минимум два параметра
  # 1. тип лицензии что ищщем
  # 2. IP адрес лицензии который ищщем
  # второй парметр необязательный, если опущен - то ищщем абстрактно, без проверки что
  # на таком IP может быть лицензия (случай для внешних лицензий)

  # логика примерно такая
  # выбираем лицензии нужного типа по условиям:
  # - дата последнего изменения IP > 31 день
  # - лицензия внутренняя
  # - лицензия не заблокирована от изменений
  # 
  # выбираем лицензии подходящие под условие
  $Where = Array(
  			SPrintF("`ISPtype` = '%s'",$ISPswScheme['ISPtype']),
			SPrintF("`IP` = '%s'",$ISPswScheme['IP']),
/*			"`IsInternal` = 'yes'", */	/* какая разница, если IP совпадает? */
/*			"`IsUsed` = 'no'", */		/* какая разница - юзается она или нет, если IP совпадает? */
/*			"`Flag` != 'Locked'", */	/* какая разница, если IP совпадает? */
  		);
  $ISPswLicenses = DB_Select('ISPswLicenses','*',Array('UNIQ','Where'=>$Where,'Limits'=>Array(0,1)));
  #-------------------------------------------------------------------------------
  switch(ValueOf($ISPswLicenses)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    Debug("[system/libs/IspSoft.php]: no free licenses, type '" . $ISPswScheme['ISPtype'] . "', IP '" . $ISPswScheme['IP'] . "'");
    break;
  case 'array':
    Debug("[system/libs/IspSoft.php]: found free license , type '" . $ISPswScheme['ISPtype'] . "', elid '" . $ISPswLicenses['elid'] . "'");
    return Array('elid'=>$ISPswLicenses['elid'],'LicenseID'=>$ISPswLicenses['ID']);
  default:
    return ERROR | @Trigger_Error(101);
  }      # end of ISPswLicenses

  # делаем тот же запрос снова, только без части про IP и с датой
  $Where = Array(
  			SPrintF("`ISPtype` = '%s'",$ISPswScheme['ISPtype']),
			'UNIX_TIMESTAMP() - `StatusDate` > 24 * 3600 * 31',
			"`IsInternal` = 'yes'",
			"`IsUsed` = 'no'",
			"`Flag` != 'Locked'",
  		);

  $ISPswLicenses = DB_Select('ISPswLicenses','*',Array('UNIQ','Where'=>$Where,'Limits'=>Array(0,1)));
  switch(ValueOf($ISPswLicenses)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    Debug("[system/libs/IspSoft.php]: no free licenses, type '" . $ISPswScheme['ISPtype'] . "', IP not set");
    return FALSE;
  case 'array':
    Debug("[system/libs/IspSoft.php]: found free license, type '" . $ISPswScheme['ISPtype'] . "', elid '" . $ISPswLicenses['elid'] . "'");
    return Array('elid'=>$ISPswLicenses['elid'],'LicenseID'=>$ISPswLicenses['ID']);
  default:
    return ERROR | @Trigger_Error(101);
  }
  #-------------------------------------------------------------------------------
  return FALSE;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Create($Settings,$ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array','array');
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
  list($price, $period) = explode(":", $ISPswScheme['ISPtype']);
  #-----------------------------------------------------------------------------
  $Request = Array(
	#---------------------------------------------------------------------------
	'authinfo'	=> $authinfo,			# авторизационная информация
	'out'		=> 'xml',			# Формат вывода
	'func'		=> 'software.edit',		# Целевая функция
	'sok'		=> 'yes',			# Значение параметра должно быть равно "yes"
	'licname'	=> $ISPswScheme['LicComment'],	# имя лицензии
	'ip'		=> $ISPswScheme['IP'],		# IP на который цеплять лицензию
	'price'		=> $price,			# прайс
	'period'	=> $period,			# период на который заказываем
  );
  
  $Response = Http_Send($Settings['Params']['PrefixAPI'],$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspSoft_Create]: не удалось соедениться с сервером');
  
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
    return new gException('SOFT_CREATE_ERROR','Не удалось создать заказ ПО');
  #-----------------------------------------------------------------------------
  $License = Array(
  		'ISPtype'	=> $ISPswScheme['ISPtype'],
		'IP'		=> $ISPswScheme['IP'],
		'elid'		=> $Doc['id'],
		'IsInternal'	=> $ISPswScheme['IsInternal']?'yes':'no',
		'IsUsed'	=> 'yes',
		'StatusID'	=> 'Active',
		'CreateDate'	=> time(),	// дата создания лицензии
		'UpdateDate'	=> time(),	// дата последнего обновления инфы с биллинга ISPsystems
		'StatusDate'	=> time()	// поле по которому узнаётся дата смены IP
  	);
  $IsInsert = DB_Insert('ISPswLicenses',$License);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  return Array('elid'=>$Doc['id'],'LicenseID'=>$IsInsert);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Change_IP($Settings,$ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array','array');
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
  $Request = Array(
	#---------------------------------------------------------------------------
	'authinfo'	=> $authinfo,			# авторизационная информация
	'out'		=> 'xml',			# Формат вывода
	'func'		=> 'software.edit',		# Целевая функция
	'sok'		=> 'yes',			# Значение параметра должно быть равно "yes"
	'licname'	=> $ISPswScheme['LicComment'],	# имя лицензии
	'ip'		=> $ISPswScheme['IP'],		# IP на который цеплять лицензию
	'elid'		=> $ISPswScheme['elid'],	# идентификатор лицензии
  );
  
  $Response = Http_Send($Settings['Params']['PrefixAPI'],$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspSoft_Change_IP]: не удалось соедениться с сервером');
  
  $Response = Trim($Response['Body']);
  
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  $Doc = $XML['doc'];
  if(IsSet($Doc['error']))
    return new gException('IspSoft_Change_IP','Не удалось изменить IP для лицензии ' . $ISPswScheme['elid']);
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_UnLock($Settings,$ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array','array');
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
  $Request = Array(
	#---------------------------------------------------------------------------
	'authinfo'	=> $authinfo,			# авторизационная информация
	'out'		=> 'xml',			# Формат вывода
	'func'		=> 'software.enable',		# Целевая функция
	'sok'		=> 'yes',			# Значение параметра должно быть равно "yes"
	'elid'		=> $ISPswScheme['elid'],	# идентификатор лицензии
  );
  
  $Response = Http_Send($Settings['Params']['PrefixAPI'],$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspSoft_UnLock]: не удалось соедениться с сервером');
  
  $Response = Trim($Response['Body']);
  
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  $Doc = $XML['doc'];
  if(IsSet($Doc['error']))
    return new gException('IspSoft_UnLock','Не удалось разблокировать лицензию' . $ISPswScheme['elid']);
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Lock($Settings,$ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array','array');
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
  $Request = Array(
	#---------------------------------------------------------------------------
	'authinfo'	=> $authinfo,			# авторизационная информация
	'out'		=> 'xml',			# Формат вывода
	'func'		=> 'software.disable',		# Целевая функция
	'sok'		=> 'yes',			# Значение параметра должно быть равно "yes"
	'elid'		=> $ISPswScheme['elid'],	# идентификатор лицензии
  );
  
  $Response = Http_Send($Settings['Params']['PrefixAPI'],$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspSoft_Lock]: не удалось соедениться с сервером');
  
  $Response = Trim($Response['Body']);
  
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  $Doc = $XML['doc'];
  if(IsSet($Doc['error']))
    return new gException('IspSoft_Lock','Не удалось заблокировать лицензию' . $ISPswScheme['elid']);
  #-----------------------------------------------------------------------------
  return TRUE;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Get_List_Licenses($Settings){
	/****************************************************************************/
	$__args_types = Array('array');
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
	$Request = Array(
			'authinfo'	=> $authinfo,	# авторизационная информация
			'out'		=> 'xml',	# Формат вывода
			'func'		=> 'software',	# Целевая функция
			);
	$Response = Http_Send($Settings['Params']['PrefixAPI'],$Http,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[IspSoft_Get_List_Licenses]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	$Doc = $XML['doc'];
	if(IsSet($Doc['error']))
		return new gException('IspSoft_Get_List_Licenses','Не удалось получить список лицензий');
	#---------------------------------------------------------------------------
	return $Doc;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Delete($Settings,$ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  #-----------------------------------------------------------------------------
  # просто помечаем лицензию в таблице как свободную
  $IsUpdate = DB_Update('ISPswLicenses',Array('IsUsed'=>FALSE,'IsInternal'=>'yes','Flag'=>''),Array('Where'=>SPrintF('`elid` = %u',$ISPswScheme['elid'])));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspSoft_Scheme_Change($Settings,$ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  # проверяем внутренний это тариф или нет. на внутренних все лицензии вечные,
  # а значит надо просто вернуть TRUE
  if($ISPswScheme['IsInternal'])
	return TRUE;
  #-----------------------------------------------------------------------------
  # TODO часть для смены тарифа у невнутренних заказов не сделана. вообще!!!

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
  );
  #-----------------------------------------------------------------------------
  if(!$IsReselling)
    $Request['owner'] = $Settings['Login']; # Владелец
  else
    $Request['userlimit'] = $HostingScheme['QuotaUsers']; # Пользователи
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspSoft_Scheme_Change]: не удалось соедениться с сервером');
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
            return ERROR | @Trigger_Error('[IspSoft_Scheme_Change]: не удалось соедениться с сервером');
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Get_Balance($Settings){
	/****************************************************************************/
	$__args_types = Array('array');
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
	$Request = Array(
			'authinfo'	=> $authinfo,		# авторизационная информация
			'out'		=> 'xml',		# Формат вывода
			'func'		=> 'subaccount',	# Целевая функция
			);
	$Response = Http_Send($Settings['Params']['PrefixAPI'],$Http,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[IspSoft_Get_Balance]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	$Doc = $XML['doc'];
	if(IsSet($Doc['error']))
		return new gException('[IspSoft_Get_Balance]','Не удалось получить баланс аккаунтов');
	#---------------------------------------------------------------------------
	#---------------------------------------------------------------------------
	return $Doc;
	#---------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Check_ISPsystem_IP($Settings, $ISPswInfo){
	/****************************************************************************/
	$__args_types = Array('array', 'array');
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
	list($price, $period) = explode(":", $ISPswInfo['ISPtype']);
	#-----------------------------------------------------------------------------
	# authinfo=USER:PASSWD&out=xml&func=software.licinfo&sok=ok&price=7&period=8&ip=111.222.111.222
	# authinfo=USER:PASSWD&out=xml&func=soft.checkip&pricelist=7&period=1&ip=82.145.17.16
	$Request = Array(
			'authinfo'	=> $authinfo,		# авторизационная информация
			'out'		=> 'xml',		# Формат вывода
			'sok'		=> 'ok',		# yes/ok
			'func'		=> 'software.licinfo',	# Целевая функция
			'price'		=> $price,		# прайс
			'period'	=> $period,		# период на который заказываем
			'ip'		=> $ISPswInfo['IP'],	# проверяемый адрес
			);
	$Response = Http_Send($Settings['Params']['PrefixAPI'],$Http,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[IspSoft_Check_ISPsystem_IP]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray();
	$Doc = $XML['doc'];
	if(IsSet($Doc['error'])){
		#return new gException('[IspSoft_Check_ISPsystem_IP]', $Doc['error']);
		return FALSE;
	}
	#---------------------------------------------------------------------------
	return TRUE;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IspSoft_Check_Local_IP($Settings, $ISPswInfo){
	/****************************************************************************/
	$__args_types = Array('array', 'array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	# надо проверять IP по собственной базе.
	# если он есть у нас и на нём есть лицензия - то в испсистем не надо лазить
	# вообще, тут наверно будет достаточно сложная логика...
	# а пока - функция фейковая
	#---------------------------------------------------------------------------
	return TRUE;
}




?>
