<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------
function IspManager_Logon($Settings,$Login,$Password){
  /****************************************************************************/
  $__args_types = Array('array','string','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('Url'=>SPrintF('https://%s/manager/ispmgr',$Settings['Address']),'Args'=>Array('lang'=>$Settings['Language'],'theme'=>$Settings['Theme'],'checkcookie'=>'no','username'=>$Login,'password'=>$Password,'func'=>'auth'));
}
#-------------------------------------------------------------------------------
function IspManager_Get_Domains($Settings){
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
  $Response = Http_Send('/manager/ispmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'wwwdomain'));
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
  $Users = Array();
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
  }
  #-----------------------------------------------------------------------------
  return $Users;
}
#-------------------------------------------------------------------------------
function IspManager_Get_Users($Settings){
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
  $Response = Http_Send('/manager/ispmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'user.edit','elid'=>'avacon'));
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
  $Users = $XML['doc'];return $Users;
  #-----------------------------------------------------------------------------
  if(!Is_Array($Users))
    return new gException('DOMAINS_NOT_FOUND','Доменов не обнаружено');
  #-----------------------------------------------------------------------------
  if(IsSet($Users['error']))
    return new gException('GET_DOMAINS_ERROR',$Users['error']);
  #-----------------------------------------------------------------------------
  $Result = Array();
  #-----------------------------------------------------------------------------
  foreach($Users as $User){
    #---------------------------------------------------------------------------
    if(!IsSet($User['name']))
      continue;
    #---------------------------------------------------------------------------
    $Result[$User['name']] = $User;
  }
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
function IspManager_Create($Settings,$Login,$Password,$Domain,$IP,$HostingScheme,$PersonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','string','string','array','string','array');
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
  $IDNA = new Net_IDNA_php5();
  $Domain = $IDNA->encode($Domain);
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo'        => $authinfo,
    'out'             => 'xml', # Формат вывода
    'func'            => ($IsReselling?'reseller.edit':'user.edit'), # Целевая функция
    'sok'             => 'yes', # Значение параметра должно быть равно "yes"
    'name'            => $Login, # Имя пользователя (реселлера)
    'passwd'          => $Password, # Пароль
    'confirm'         => $Password, # Подтверждение
    'domain'          => $Domain, # Домен
    'ip'              => $IP, # IP-адрес
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
    'bandwidthlimit'  => $HostingScheme['QuotaTraffic'], # Трафик
    #---------------------------------------------------------------------------
    'shell'           => ($HostingScheme['IsShellAccess']?'on':'off'), # Доступ к shell
    'ssl'             => ($HostingScheme['IsSSLAccess']?'on':'off'), # SSL
    'cgi'             => ($HostingScheme['IsCGIAccess']?'on':'off'), # CGI
    'ssi'             => ($HostingScheme['IsSSIAccess']?'on':'off'), # SSI
    'phpmod'          => ($HostingScheme['IsPHPModAccess']?'on':'off'), # PHP как модуль Apache
    'phpcgi'          => ($HostingScheme['IsPHPCGIAccess']?'on':'off'), # PHP как CGI
    'phpfcgi'         => ($HostingScheme['IsPHPFastCGIAccess']?'on':'off'), # PHP как FastCGI
    'safemode'        => ($HostingScheme['IsPHPSafeMode']?'on':'off'), # Безопасный режим
    'cpulimit'        => Ceil($HostingScheme['QuotaCPU']), # Ограничение на CPU
    'memlimit'        => Ceil($HostingScheme['QuotaMEM']), # Ограничение на память
    'proclimit'       => $HostingScheme['QuotaPROC'], # Кол-во процессов
  );

  // Set e-mail
  if (IsSet($Person['Email'])) {
    $Request['email'] = $Person['Email'];
  } else {
    // Set default e-mail
    $Request['email'] = SPrintF('%s@%s','webmaster',$Domain);
  }
  #-----------------------------------------------------------------------------
  if(!$IsReselling)
    $Request['owner'] = $Settings['Login']; # Владелец
  else
    $Request['userlimit'] = $HostingScheme['QuotaUsers']; # Пользователи
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Create]: не удалось соедениться с сервером');
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
    return new gException('ACCOUNT_CREATE_ERROR','Не удалось создать заказ хостинга');
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'out'      => 'xml',
    'func'     => 'restart'
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Create]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager_Active($Settings,$Login,$IsReseller = FALSE){
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
  $Response = Http_Send('/manager/ispmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.enable':'user.enable','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Activate]: не удалось соедениться с сервером');
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
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'out'      => 'xml',
    'func'     => 'restart'
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Activate]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager_Suspend($Settings,$Login,$IsReseller = FALSE){
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
  $Response = Http_Send('/manager/ispmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.disable':'user.disable','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Suspend]: не удалось соедениться с сервером');
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
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'out'      => 'xml',
    'func'     => 'restart'
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Suspend]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager_Delete($Settings,$Login,$IsReseller = FALSE){
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
  $Response = Http_Send('/manager/ispmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'reseller.delete':'user.delete','elid'=>$Login));
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Delete]: не удалось соедениться с сервером');
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
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'out'      => 'xml',
    'func'     => 'restart'
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Delete]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager_Scheme_Change($Settings,$Login,$HostingScheme){
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
    'ip'              => $Settings['IP'], # IP-адрес
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
    'bandwidthlimit'  => $HostingScheme['QuotaTraffic'], # Трафик
    #---------------------------------------------------------------------------
    'shell'           => ($HostingScheme['IsShellAccess']?'on':'off'), # Доступ к shell
    'ssl'             => ($HostingScheme['IsSSLAccess']?'on':'off'), # SSL
    'cgi'             => ($HostingScheme['IsCGIAccess']?'on':'off'), # CGI
    'ssi'             => ($HostingScheme['IsSSIAccess']?'on':'off'), # SSI
    'phpmod'          => ($HostingScheme['IsPHPModAccess']?'on':'off'), # PHP как модуль Apache
    'phpcgi'          => ($HostingScheme['IsPHPCGIAccess']?'on':'off'), # PHP как CGI
    'phpfcgi'         => ($HostingScheme['IsPHPFastCGIAccess']?'on':'off'), # PHP как FastCGI
    'safemode'        => ($HostingScheme['IsPHPSafeMode']?'on':'off'), # Безопасный режим
    'cpulimit'        => Ceil($HostingScheme['QuotaCPU']), # Ограничение на CPU
    'memlimit'        => Ceil($HostingScheme['QuotaMEM']), # Ограничение на память
    'proclimit'       => $HostingScheme['QuotaPROC'], # Кол-во процессов
  );
  #-----------------------------------------------------------------------------
  if(!$IsReselling)
    $Request['owner'] = $Settings['Login']; # Владелец
  else
    $Request['userlimit'] = $HostingScheme['QuotaUsers']; # Пользователи
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Scheme_Change]: не удалось соедениться с сервером');
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
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'out'      => 'xml',
    'func'     => 'restart'
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Scheme_Change]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IspManager_Password_Change($Settings,$Login,$Password,$IsReseller = FALSE){
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
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => $authinfo,
    'out'      => 'xml',
    'func'     => ($IsReseller?'reseller.edit':'user.edit'),
    'elid'     => $Login
  );
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Password_Change]: не удалось соедениться с сервером');
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
    return new gException('USER_DATA_TAKE_ERROR','Ошибка получения данных пользователя из системы управления');
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'out'      => 'xml',
    'func'     => ($IsReseller?'reseller.edit':'user.edit'),
    'elid'     => $Login,
    'passwd'   => $Password,
    'confirm'  => $Password,
    'sok'      => 'yes'
  );
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($Doc) as $ParamID)
    $Request[$ParamID] = $Doc[$ParamID];
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/ispmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[IspManager_Password_Change]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = $Response['Body'];
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return ERROR | @Trigger_Error('[IspManager_Password_Change]: неверный ответ от сервера');
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
function IspManager_Get_Email_Boxes($Settings){
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
  $Response = Http_Send('/manager/ispmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'emaildomain'));
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
    return ERROR | @Trigger_Error('[IspManager_Get_Email_Boxes]: не удалось получить список почтовых ящиков');
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
  $Response = Http_Send('/manager/ispmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'email'));
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
    return ERROR | @Trigger_Error('[IspManager_Get_Email_Boxes]: не удалось получить список почтовых ящиков');
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
?>
