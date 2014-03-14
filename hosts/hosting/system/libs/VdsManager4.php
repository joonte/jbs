<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/* VDS functions written by lissyara, for www.host-food.ru */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));

#-------------------------------------------------------------------------------
function VdsManager4_Logon($Settings,$Login,$Password){
	/****************************************************************************/
	$__args_types = Array('array','string','string');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	return Array('Url'=>SPrintF('https://%s/manager/vdsmgr',$Settings['Address']),'Args'=>Array('lang'=>$Settings['Params']['Language'],'theme'=>$Settings['Params']['Theme'],'checkcookie'=>'no','username'=>$Login,'password'=>$Password,'func'=>'auth'));
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Create($Settings,$Login,$Password,$Domain,$IP,$VPSScheme,$Email,$PersonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','string','string','array','string','string','array');
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
  $IsReselling = $VPSScheme['IsReselling'];
  #-----------------------------------------------------------------------------
  $IDNA = new Net_IDNA_php5();
  $Domain = $IDNA->encode($Domain);
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo'        => $authinfo,
    'out'             => 'xml',				# Формат вывода
    'func'            => ($IsReselling?'user.edit':'vds.edit'), # Целевая функция
    'sok'             => 'yes',				# Значение параметра должно быть равно "yes"
    'id'              => 'auto',			# Идентификатор. Параметр зависим от возможности vdsid
    'name'            => ($IsReselling?$Login:$Domain), # Имя пользователя (реселлера)
    'passwd'          => $Password,			# Пароль
    'confirm'         => $Password,			# Подтверждение
    'ip'              => 'auto',			# IP-адрес
    'vdspreset'       => $VPSScheme['PackageID'],	# Шаблон
    #---------------------------------------------------------------------------
    'disk'            => $VPSScheme['disklimit'],	# Диск
    'ncpu'            => $VPSScheme['ncpu'],		# число процессоров
    'cpu'             => ceil($VPSScheme['cpu']),	# частота процессора
    'mem'             => ceil($VPSScheme['mem']),	# RAM
    'bmem'            => ceil($VPSScheme['bmem']),	# Burstable RAM
    ($IsReselling?'maxswap':'swap') => ceil($VPSScheme['maxswap']), # использование swap
    'traf'            => $VPSScheme['traf'],		# Трафик
    'chrate'          => SPrintF('%u',$VPSScheme['chrate'] * 1024),   # канал, полоса, мегабит
    'desc'            => $VPSScheme['maxdesc'],		# открытых файлов
    'proc'            => $VPSScheme['proc'],		# процессов
    'ipcount'         => $VPSScheme['ipalias'],		# дополнительных IP
    'disktempl'       => $VPSScheme['disktempl'],	# шаблон диска
    'extns'           => $VPSScheme['extns'],		# DNS
    'limitpvtdns'     => $VPSScheme['limitpvtdns'],	# ограничение на число доменов собственных DNS
    'limitpubdns'     => $VPSScheme['limitpubdns'],	# ограничение на число доменов DNS провайдера
    'backup'          => $VPSScheme['backup'],		# резервное копирование
  );
  
  if(!$IsReselling) {
    $Request['owner'] = $Settings['Login']; # Владелец
  }
  else {
    $Request['userlimit'] = $VPSScheme['QuotaUsers']; # Пользователи
  }
  
  $Response = Http_Send('/manager/vdsmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[VdsManager4_Create]: не удалось соедениться с сервером');
  
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
    return new gException('ACCOUNT_CREATE_ERROR','Не удалось создать заказ виртуального сервера');
  #-----------------------------------------------------------------------------
  Debug("[system/libs/VdsManager4]: VPS order created with IP = " . $Doc['ip']);
  #-----------------------------------------------------------------------------
  $IsQuery = DB_Query("UPDATE `VPSOrders` SET `Login`='" . $Doc['ip'] . "' WHERE `Login`='" . $Login . "'");
  if(Is_Error($IsQuery))
        return ERROR | @Trigger_Error('[VdsManager4_Create]: не удалось прописать IP адрес для виртуального сервера');
  #-----------------------------------------------------------------------------
  return TRUE;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Active($Settings,$Login,$IsReseller = FALSE){
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
	$Response = Http_Send('/manager/vdsmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'user.enable':'vds.enable','elid'=>$Login));
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_Activate]: не удалось соедениться с сервером');
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
	return TRUE;
}


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Suspend($Settings,$Login,$IsReseller = FALSE){
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
	$Response = Http_Send('/manager/vdsmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'user.disable':'vds.disable','elid'=>$Login));
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_Suspend]: не удалось соедениться с сервером');
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
	return TRUE;
}


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Delete($Settings,$Login,$IsReseller = FALSE){
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
	$Response = Http_Send('/manager/vdsmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>$IsReseller?'user.delete':'vds.delete','elid'=>$Login));
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_Delete]: не удалось соедениться с сервером');
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
	return TRUE;
}



#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Scheme_Change($Settings,$Login,$VPSScheme){
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
  $IsReselling = $VPSScheme['IsReselling'];
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'authinfo'        => $authinfo,
    'out'             => 'xml',				# Формат вывода
    'func'            => ($IsReselling?'user.edit':'vds.edit'), # Целевая функция
    'sok'             => 'yes',				# Значение параметра должно быть равно "yes"
    'name'            => $VPSScheme['Domain'],		# Имя пользователя (реселлера)
    'vdspreset'       => $VPSScheme['PackageID'],	# Шаблон
    'elid'            => $Login,			# кому меняем
    #---------------------------------------------------------------------------
    'disk'            => $VPSScheme['disklimit'],	# Диск
    'ncpu'            => $VPSScheme['ncpu'],		# число процессоров
    'cpu'             => ceil($VPSScheme['cpu']),	# частота процессора
    'mem'             => ceil($VPSScheme['mem']),	# RAM
    'bmem'            => ceil($VPSScheme['bmem']),	# Burstable RAM
    ($IsReselling?'maxswap':'swap') => ceil($VPSScheme['maxswap']), # использование swap
    'traf'            => $VPSScheme['traf'],		# Трафик
    'chrate'          => SPrintF('%u',$VPSScheme['chrate'] * 1024),   # канал, полоса, мегабит
    'desc'            => $VPSScheme['maxdesc'],		# открытых файлов
    'proc'            => $VPSScheme['proc'],		# процессов
    'ipcount'         => $VPSScheme['ipalias'],		# дополнительных IP
    'extns'           => $VPSScheme['extns'],		# DNS
    'limitpvtdns'     => $VPSScheme['limitpvtdns'],     # ограничение на число доменов собственных DNS
    'limitpubdns'     => $VPSScheme['limitpubdns'],     # ограничение на число доменов DNS провайдера
    'backup'          => $VPSScheme['backup'],		# резервное копирование
  );
  #-----------------------------------------------------------------------------
  if(!$IsReselling)
    $Request['owner'] = $Settings['Login']; # Владелец
  else
    $Request['userlimit'] = $VPSScheme['QuotaUsers']; # Пользователи
  #-----------------------------------------------------------------------------
  $Response = Http_Send('/manager/vdsmgr',$Http,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[VdsManager4_Scheme_Change]: не удалось соедениться с сервером');
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
  return TRUE;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Password_Change($Settings,$Login,$Password,$IsReseller = FALSE){
	/****************************************************************************/
	$__args_types = Array('array','string','string','boolean');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-----------------------------------------------------------------------------
	$Request = Array(
		#---------------------------------------------------------------------------
		'authinfo' => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
		'out'      => 'xml',
		'func'     => 'usrparam',
                'name'     => $Login,
                'su'       => $Login,
		'passwd'   => $Password,
		'confirm'  => $Password,
		'sok'      => 'yes',
                'su'       => $Login,
		'atype'    => 'atany',         # разрешаем доступ к панели с любого IP
	);
        #---------------------------------------------------------------------------
        $Http = Array(
        #---------------------------------------------------------------------------
                'Address'  => $Settings['Params']['IP'],
                'Port'     => $Settings['Port'],
                'Host'     => $Settings['Address'],
                'Protocol' => $Settings['Protocol'],
                'Hidden'   => $authinfo
        );
	#-----------------------------------------------------------------------------
	$Response = Http_Send('/manager/vdsmgr',$Http,$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_Password_Change]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = $Response['Body'];
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return ERROR | @Trigger_Error('[VdsManager4_Password_Change]: неверный ответ от сервера');
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray();
	#-----------------------------------------------------------------------------
	$Doc = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(IsSet($Doc['error']))
		return new gException('PASSWORD_CHANGE_ERROR','Не удалось изменить пароль для заказа виртуального сервера');
	#-----------------------------------------------------------------------------
	return TRUE;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara 2011-08-09 in 09:55 MSK
function VdsManager4_AddIP($Settings,$Login,$ID,$Domain,$IP,$AddressType){
	/****************************************************************************/
        $__args_types = Array('array','string','string','string','string','string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
        #Debug("ExtraIP order ID = " . $ID);
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
        if($AddressType == "IPv4"){
                $AddrType = "auto";
        }else{
                $AddrType = "auto6";
        }
        #-----------------------------------------------------------------------------
        $Request = Array(
                'authinfo'      => $authinfo,
                'func'          => 'vds.ip.edit',
                'out'           => 'xml',
                'ip'            => $AddrType,
                'otherip'       => '',
                'ipcount'       => '',
                'name'          => $Domain,
                'elid'          => '',
                'plid'          => $Login,
                'sok'           => 'ok'
        );
        #Debug(var_export($Settings, true));
	#-----------------------------------------------------------------------------
	$Response = Http_Send('/manager/vdsmgr',$Http,$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_AddIP]: не удалось соедениться с сервером');
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
                return new gException('AddIP_ERROR','Не удалось добавить IP для виртуального сервера');
        #-----------------------------------------------------------------------------
        Debug("[system/libs/VdsManager4]: to VPS added IP = " . $Doc['ip']);
        #-----------------------------------------------------------------------------
        $IsQuery = DB_Query("UPDATE `ExtraIPOrders` SET `Login`='" . $Doc['ip'] . "' WHERE `ID`='" . $ID . "'");
        if(Is_Error($IsQuery))
                return ERROR | @Trigger_Error('[VdsManager4_Create]: не удалось прописать IP адрес для виртуального сервера');
        #-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	return TRUE;
}


# added by lissyara 2011-08-09 in 13:03 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_DeleteIP($Settings,$ExtraIP){
	/****************************************************************************/
        $__args_types = Array('array','string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
        #Debug("ExtraIP order ID = " . $ID);
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
        # func=vds.ip.delete&elid=91.227.18.39&plid=91.227.18.7
        $Request = Array(
                'authinfo'      => $authinfo,
                'func'          => 'vds.ip.delete',
                'out'           => 'xml',
                'elid'          => $ExtraIP,
                'plid'          => $Settings['UserLogin'],
                'sok'           => 'ok'
        );
        #Debug(var_export($Settings, true));
	#-----------------------------------------------------------------------------
	$Response = Http_Send('/manager/vdsmgr',$Http,$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_DeleteIP]: не удалось соедениться с сервером');
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
                return new gException('AddIP_ERROR','Не удалось удалить IP у виртуального сервера');
        #-----------------------------------------------------------------------------
        #-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	return TRUE;
}

# added by lissyara 2011-10-07 in 10:28 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_MainUsage($Settings){
	/****************************************************************************/
        $__args_types = Array('array');
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
        # 
        $Request = Array(
                'authinfo'      => $authinfo,
                'func'          => 'mainusage',
                'out'           => 'xml',
                'sok'           => 'ok'
        );
        #Debug(var_export($Settings, true));
	#-----------------------------------------------------------------------------
	$Response = Http_Send('/manager/vdsmgr',$Http,$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_MainUsage]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
        $Response = Trim($Response['Body']);
        $XML = String_XML_Parse($Response);
        if(Is_Exception($XML))
                return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
        #-----------------------------------------------------------------------------
        $XML = $XML->ToArray('elem');
        $Doc = $XML['doc'];
        if(IsSet($Doc['error']))
                return new gException('VdsManager4_MainUsage','Не удалось получить нагрузку сервера');
        #---------------------------------------------------------------------------
        # перебираем, складываем
        $Out = Array(
                        'cpuu'  => 0,
                        'memu'  => 0,
                        'swapu' => 0,
                        'disk0' => 0
                );
        $NumStrings = SizeOf($Doc);
        foreach($Doc as $Usage){
                $Out['cpuu'] = $Out['cpuu'] + $Usage['cpuu'];
                $Out['memu'] = $Out['memu'] + $Usage['memu'];
                $Out['swapu'] = $Out['swapu'] + $Usage['swapu'];
                $Out['disk0'] = $Out['disk0'] + $Usage['disk0'];
        }
        # считаем средние значнеия
        $Out['cpuu'] = $Out['cpuu'] / SizeOf($Doc);
        $Out['memu'] = $Out['memu'] / SizeOf($Doc);
        $Out['swapu'] = $Out['swapu'] / SizeOf($Doc);
        $Out['disk0'] = $Out['disk0'] / SizeOf($Doc);
        
        Debug("[system/libs/VdsManager4.php]: usage for " . $Settings['Address'] . " is " . $Out['cpuu'] ."/". $Out['memu'] ."/". $Out['swapu'] ."/". $Out['disk0']);
        return ($Out['cpuu'] + $Out['memu'] + $Out['swapu'] + $Out['disk0']);
	#-----------------------------------------------------------------------------
}

# added by lissyara, 2012-02-02 in 21:53 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_CheckIsActive($Settings,$Login){
	/****************************************************************************/
	$__args_types = Array('array','string');
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
	$Response = Http_Send('/manager/vdsmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'vds'));
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_CheckIsActive]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-----------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-----------------------------------------------------------------------------
	$Doc = $XML['doc'];
	#-----------------------------------------------------------------------------
	if(IsSet($Doc['error']))
		return new gException('CHECK_ACCOUNT_ACTIVE_ERROR','Не удалось проверить состояние виртуального сервера');
	#-----------------------------------------------------------------------------
	$VPSs = $XML['doc'];
	#-----------------------------------------------------------------------------
	foreach($VPSs as $VPS){
		if($VPS['ip'] == $Login){
			if(IsSet($VPS['disabled'])){
				if(IsSet($VPS['admdown'])){
					Debug(SPrintF("[system/libs/VdsManager4]: %s is disabled by administrator",$Login));
					return FALSE;
				}
			}
		}
	}
	#-----------------------------------------------------------------------------
	# not found, or enabled
	Debug(SPrintF("[system/libs/VdsManager4]: %s is enabled, disabled not by administrator, or not found",$Login));
	return TRUE;
}

# added by lissyara, 2012-02-03 in 09:59 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Reboot($Settings,$Login){
	/****************************************************************************/
	$__args_types = Array('array','string');
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
	$Response = Http_Send('/manager/vdsmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'vds.reboot','elid'=>$Login));
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_Reboot]: не удалось соедениться с сервером');
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
		return new gException('ACCOUNT_REBOOT_ERROR','Не удалось перезагрузить виртуальный сервер');
	#-----------------------------------------------------------------------------
	return TRUE;
	#-----------------------------------------------------------------------------
}
#-----------------------------------------------------------------------------

# added by lissyara, 2013-05-17 in 09:53 MSK, for JBS-280
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function VdsManager4_Get_Users($Settings){
	/****************************************************************************/
	$__args_types = Array('array','string');
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
	$Response = Http_Send('/manager/vdsmgr',$Http,Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'vds'));
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[VdsManager4_Get_Users]: не удалось соедениться с сервером');
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
		if(!IsSet($User['ip']))
		continue;
		#---------------------------------------------------------------------------
		if(!IsSet($User['owner']))
		continue;
		#---------------------------------------------------------------------------
		if($User['owner'] == $Settings['Login'])
		$Result[] = $User['ip'];
		#-----------------------------------------------------------------------------
	}
	#-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	return $Result;
	#-----------------------------------------------------------------------------
}
#-----------------------------------------------------------------------------


?>
