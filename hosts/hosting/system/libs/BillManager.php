<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Logon($Settings,$Params){
	/******************************************************************************/
	$__args_types = Array('array','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	return Array('Url'=>$Params['Url'],'Args'=>Array('checkcookie'=>'no','username'=>$Params['Login'],'password'=>$Params['Password'],'func'=>'auth'));
	#-------------------------------------------------------------------------------
}


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Find_Free_License($ISPswScheme){
	/******************************************************************************/
	$__args_types = Array('array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Find_Free_License'));
	#-------------------------------------------------------------------------------
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
	$Where = Array(SPrintF("`pricelist_id` = '%s'",$ISPswScheme['pricelist_id']),SPrintF("`IP` = '%s'",$ISPswScheme['IP']));
	#-------------------------------------------------------------------------------
	$ISPswLicenses = DB_Select('ISPswLicenses','*',Array('UNIQ','Where'=>$Where,'Limits'=>Array(0,1)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ISPswLicenses)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[system/libs/BillManager.php]: exact IP, no free licenses, pricelist_id = %s; IP = %s',$ISPswScheme['pricelist_id'],$ISPswScheme['IP']));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[system/libs/BillManager.php]: exact IP, found free license, pricelist_id = %s; elid = %s',$ISPswScheme['pricelist_id'],$ISPswLicenses['elid']));
		#-------------------------------------------------------------------------------
		return Array('elid'=>$ISPswLicenses['elid'],'LicenseID'=>$ISPswLicenses['ID']);
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# делаем тот же запрос снова, только без части про IP и с датой
	$Where = Array(SPrintF("`pricelist_id` = '%s'",$ISPswScheme['pricelist_id']),'UNIX_TIMESTAMP() - `ip_change_date` > 24 * 3600 * 31',"`IsInternal` = 'yes'","`IsUsed` = 'no'","`Flag` != 'Locked'");
	#-------------------------------------------------------------------------------
	$ISPswLicenses = DB_Select('ISPswLicenses','*',Array('UNIQ','Where'=>$Where,'Limits'=>Array(0,1),'SortOn'=>'update_expiredate','IsDesc'=>TRUE));
	switch(ValueOf($ISPswLicenses)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[system/libs/BillManager.php]: no free licenses, pricelist_id = %s; IP not set',$ISPswScheme['pricelist_id']));
		#-------------------------------------------------------------------------------
		return FALSE;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[system/libs/BillManager.php]: found free license, pricelist_id = %s; elid = %s',$ISPswScheme['pricelist_id'],$ISPswLicenses['elid']));
		#-------------------------------------------------------------------------------
		return Array('elid'=>$ISPswLicenses['elid'],'LicenseID'=>$ISPswLicenses['ID']);
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return FALSE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Create($Settings,$ISPswScheme){
	/******************************************************************************/
	$__args_types = Array('array','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Create'));
	#-------------------------------------------------------------------------------
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-------------------------------------------------------------------------------
	$HTTP = BillManager_Build_HTTP($Settings);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# billmgr?authinfo=USER:PASSWD&out=xml&func=soft.order.param&clicked_button=finish&ip=82.156.37.16&licname=name&period=1&pricelist=4601&addon_4602=1&sok=ok&skipbasket=on
	$Request = Array(
			#-------------------------------------------------------------------------------
			'authinfo'	=> $authinfo,			# авторизационная информация
			'out'		=> 'xml',			# Формат вывода
			'func'		=> 'soft.order.param',		# Целевая функция
			'sok'		=> 'ok',			# Значение параметра должно быть равно "yes"
			'clicked_button'=> 'finish',
			'skipbasket'	=> 'on',
			'licname'	=> $ISPswScheme['LicComment'],	# имя лицензии
			'ip'		=> $ISPswScheme['IP'],		# IP на который цеплять лицензию
			'remoteip'	=> IsSet($ISPswScheme['remoteip'])?$ISPswScheme['remoteip']:'',
			'pricelist'	=> $ISPswScheme['pricelist_id'],# прайс
			'period'	=> $ISPswScheme['period'],	# период на который заказываем
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	# проверяем необходимость параметра addon_XXXX
	$Comp = Comp_Load('Formats/ISPswOrder/SoftWareList',TRUE,$ISPswScheme['pricelist_id'],TRUE,TRUE);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Comp)
		$Request[$Comp] = 1;
  	#-------------------------------------------------------------------------------
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_Create]: не удалось соедениться с сервером');
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
		return new gException('SOFT_CREATE_ERROR','Не удалось создать заказ ПО');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# JBS-909, надо сразу класть ключ лицензии в базу
	$Request = Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'soft.edit','elid'=>$Doc['item.id']);
	#-------------------------------------------------------------------------------
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_Create]: не удалось соедениться с сервером');
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
	if(IsSet($Doc['error']))
		return new gException('BillManager_Create','Не удалось получить полную информацию лицензии');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$License = Array(
			#-------------------------------------------------------------------------------
			'pricelist_id'		=> $ISPswScheme['pricelist_id'],
			'period'		=> $ISPswScheme['period'],
			'addon'			=> 1,
			'IP'			=> $ISPswScheme['IP'],
			'remoteip'		=> (IsSet($Doc['remoteip'])?$Doc['remoteip']:''),
			'elid'			=> $Doc['elid'],
			'LicKey'		=> $Doc['lickey'],
			'IsInternal'		=> $ISPswScheme['IsInternal']?'yes':'no',
			'IsUsed'		=> 'yes',
			'StatusID'		=> 'Active',
			'CreateDate'		=> time(),	// дата создания лицензии
			'ip_change_date'	=> time(),	// когда можно менять IP адрес
			'lickey_change_date'	=> time(),	// когда можно менять ключ лицензии
			'StatusDate'		=> time()	// поле по которому узнаётся дата смены IP
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('ISPswLicenses',$License);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Sleep(10);
	#-------------------------------------------------------------------------------
	return Array('elid'=>$Doc['elid'],'LicenseID'=>$IsInsert);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}



#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Change_IP($Settings,$ISPswScheme){
	/******************************************************************************/
	$__args_types = Array('array','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Change_IP'));
	#-------------------------------------------------------------------------------
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-------------------------------------------------------------------------------
	$HTTP = BillManager_Build_HTTP($Settings);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# https://api.ispsystem.com/manager/billmgr?authinfo=USER:PASSWD&out=xml&func=soft.edit&elid=334673&licname=NEWLICNAME&ip=111.222.111.223&sok=ok
	$Request = Array(
			#-------------------------------------------------------------------------------
			'authinfo'	=> $authinfo,			# авторизационная информация
			'out'		=> 'xml',			# Формат вывода
			'func'		=> 'soft.edit',			# Целевая функция
			'sok'		=> 'ok',			# Значение параметра должно быть равно "yes"
			'licname'	=> $ISPswScheme['LicComment'],	# имя лицензии
			'ip'		=> $ISPswScheme['IP'],		# IP на который цеплять лицензию
			'elid'		=> $ISPswScheme['elid'],	# идентификатор лицензии
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	if(IsSet($ISPswScheme['remoteip']))
		$Request['remoteip'] = $ISPswScheme['remoteip'];
	#-------------------------------------------------------------------------------
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_Change_IP]: не удалось соедениться с сервером');
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
		return ERROR | @Trigger_Error(SPrintF('[BillManager_Change_IP]: Не удалось изменить IP для лицензии %u',$ISPswScheme['elid']));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	#Debug(print_r($Doc,true));
	#Debug(print_r($XML,true));
	if(!IsSet($Doc['pricelist']))
		$Doc = $Doc['doc'];
	#-------------------------------------------------------------------------------
	$License = Array(
			#-------------------------------------------------------------------------------
			'pricelist_id'		=> $Doc['pricelist'],
			'period'		=> $Doc['period'],
			'addon'			=> 1,
			'IP'			=> $Doc['ip'],
			'remoteip'		=> (IsSet($Doc['remoteip'])?$Doc['remoteip']:''),
			'LicKey'		=> $Doc['lickey'],
			'IsInternal'		=> $ISPswScheme['IsInternal']?'yes':'no',
			'IsUsed'		=> 'yes',
			// JBS-1033 пусть планировщик по ночам статусы исправляет
			//'StatusID'		=> 'Active',
			'CreateDate'		=> time(),	// дата создания лицензии
			'ip_change_date'	=> time(),	// когда можно менять IP адрес
			'lickey_change_date'	=> time(),	// когда можно менять ключ лицензии
			'StatusDate'		=> time()	// поле по которому узнаётся дата смены IP
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('ISPswLicenses',$License,Array('Where'=>SPrintF('`elid` = %u',$ISPswScheme['elid'])));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Sleep(10);
	#-------------------------------------------------------------------------------
	return Array('elid'=>$Doc['elid']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}



#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_UnLock($Settings,$ISPswScheme){
	/******************************************************************************/
	$__args_types = Array('array','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_UnLock'));
	#-------------------------------------------------------------------------------
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-------------------------------------------------------------------------------
	$HTTP = BillManager_Build_HTTP($Settings);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# https://api.ispsystem.com/manager/billmgr?authinfo=USER:PASSWD&out=xml&func=soft.resume&elid=код_лицензии
	$Request = Array(
			#-------------------------------------------------------------------------------
			'authinfo'	=> $authinfo,			# авторизационная информация
			'out'		=> 'xml',			# Формат вывода
			'func'		=> 'soft.resume',		# Целевая функция
			'sok'		=> 'yes',			# Значение параметра должно быть равно "yes"
			'elid'		=> $ISPswScheme['elid'],	# идентификатор лицензии
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_UnLock]: не удалось соедениться с сервером');
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
	if(IsSet($Doc['error']))
		return new gException('BillManager_UnLock','Не удалось разблокировать лицензию' . $ISPswScheme['elid']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Sleep(10);
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}




#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Lock($Settings,$ISPswScheme){
	/******************************************************************************/
	$__args_types = Array('array','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Lock'));
	#-------------------------------------------------------------------------------
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-------------------------------------------------------------------------------
	$HTTP = BillManager_Build_HTTP($Settings);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# /manager/billmgr?authinfo=USER:PASSWD&out=xml&func=soft.suspend&elid=код_лицензии
	$Request = Array(
			#-------------------------------------------------------------------------------
			'authinfo'	=> $authinfo,			# авторизационная информация
			'out'		=> 'xml',			# Формат вывода
			'func'		=> 'soft.suspend',		# Целевая функция
			'sok'		=> 'yes',			# Значение параметра должно быть равно "yes"
			'elid'		=> $ISPswScheme['elid'],	# идентификатор лицензии
			#-------------------------------------------------------------------------------
			);
  	#-------------------------------------------------------------------------------
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_Lock]: не удалось соедениться с сервером');
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
	if(IsSet($Doc['error']))
		return new gException('BillManager_Lock','Не удалось заблокировать лицензию' . $ISPswScheme['elid']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# затычка
function BillManager_Get_Users($Settings){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Get_Users'));
	#-------------------------------------------------------------------------------
	return BillManager_Get_List_Licenses($Settings);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Get_List_Licenses($Settings){
	/****************************************************************************/
	$__args_types = Array('array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Get_List_Licenses'));
	#-------------------------------------------------------------------------------
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-----------------------------------------------------------------------------
	$HTTP = BillManager_Build_HTTP($Settings);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Request = Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'soft');
	#-------------------------------------------------------------------------------
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_Get_List_Licenses]: не удалось соедениться с сервером');
	#-------------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	#-------------------------------------------------------------------------------
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-------------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-------------------------------------------------------------------------------
	$Doc = $XML['doc'];
	if(IsSet($Doc['error']))
		return new gException('BillManager_Get_List_Licenses','Не удалось получить список лицензий');
	#-------------------------------------------------------------------------------
	# в полученном массиве недостаточно данных. перебираем лицензии по одной, достаём полную информацию.
	$Out = Array();
	#-------------------------------------------------------------------------------
	foreach($Doc as $License){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF("[system/libs/BillManager.php]: License = %s",print_r($License, true)));
		#-------------------------------------------------------------------------------
		if(!IsSet($License['expiredate']))
			continue;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$CacheID = Md5(SPrintF('BillManager_Get_List_Licenses-%s',$License['id']));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$LicenseDetail = CacheManager::get($CacheID);
		#-------------------------------------------------------------------------------
		if(!$LicenseDetail){
			#-------------------------------------------------------------------------------
			Debug(SPrintF("[system/libs/BillManager.php]: LicenseDetail elid = %s НЕ найдено в кэше",$License['id']));
			#-------------------------------------------------------------------------------
			Sleep(1);
			#-------------------------------------------------------------------------------
			$Request = Array('authinfo'=>$authinfo,'out'=>'xml','func'=>'soft.edit','elid'=>$License['id']);
			#-------------------------------------------------------------------------------
			$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
			if(Is_Error($Response))
				return ERROR | @Trigger_Error('[BillManager_Get_List_Licenses]: не удалось соедениться с сервером');
			#-------------------------------------------------------------------------------
			$Response = Trim($Response['Body']);
			#-------------------------------------------------------------------------------
			$XML = String_XML_Parse($Response);
			if(Is_Exception($XML))
				return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
			#-------------------------------------------------------------------------------
			$XML = $XML->ToArray();
			#-------------------------------------------------------------------------------
			$LicenseDetail = $XML['doc'];
			if(IsSet($LicenseDetail['error']))
				return new gException('BillManager_Get_List_Licenses','Не удалось получить детальную информацию о лицензии');
			#-------------------------------------------------------------------------------
			#Debug(SPrintF("[system/libs/BillManager.php]: LicenseDetail = %s",print_r($LicenseDetail, true)));
			#-------------------------------------------------------------------------------
			CacheManager::add($CacheID,$LicenseDetail,3600);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			Debug(SPrintF("[system/libs/BillManager.php]: LicenseDetail elid = %s найдено в кэше",$License['id']));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------	
		$Out[] = Array_Merge($License,$LicenseDetail);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Delete($Settings,$ISPswScheme){
	/******************************************************************************/
	$__args_types = Array('array','array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Delete'));
	#-------------------------------------------------------------------------------
	# просто помечаем лицензию в таблице как свободную
	$IsUpdate = DB_Update('ISPswLicenses',Array('IsUsed'=>FALSE,'IsInternal'=>'yes','Flag'=>''),Array('Where'=>SPrintF('`elid` = %u',$ISPswScheme['elid'])));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}



#-------------------------------------------------------------------------------
function BillManager_Scheme_Change($Settings,$ISPswScheme){
  /****************************************************************************/
  $__args_types = Array('array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Scheme_Change'));
  #-------------------------------------------------------------------------------
  # проверяем внутренний это тариф или нет. на внутренних все лицензии вечные,
  # а значит надо просто вернуть TRUE
  if($ISPswScheme['IsInternal'])
	return TRUE;
  #-----------------------------------------------------------------------------
  # TODO часть для смены тарифа у невнутренних заказов не сделана. вообще!!!

}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Get_Balance($Settings){
	/****************************************************************************/
	$__args_types = Array('array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Get_Balance'));
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-----------------------------------------------------------------------------
	$HTTP = BillManager_Build_HTTP($Settings);
	#-----------------------------------------------------------------------------
	$Request = Array(
			'authinfo'	=> $authinfo,		# авторизационная информация
			'out'		=> 'xml',		# Формат вывода
			'func'		=> 'subaccount',	# Целевая функция
			);
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_Get_Balance]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-----------------------------------------------------------------------------
	$XML = $XML->ToArray('elem');
	#-------------------------------------------------------------------------------
	$Doc = $XML['doc'];
	#-------------------------------------------------------------------------------
	if(IsSet($Doc['error']))
		return new gException('[BillManager_Get_Balance]','Не удалось получить баланс аккаунтов');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Doc;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Check_ISPsystem_IP($Settings, $ISPswInfo){
	/******************************************************************************/
	$__args_types = Array('array', 'array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Check_ISPsystem_IP'));
	#-------------------------------------------------------------------------------
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-------------------------------------------------------------------------------
	$HTTP = BillManager_Build_HTTP($Settings);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# authinfo=USER:PASSWD&out=xml&func=soft.checkip&pricelist=7&period=1&ip=82.145.17.16
	$Request = Array(
			'authinfo'	=> $authinfo,			# авторизационная информация
			'out'		=> 'xml',			# Формат вывода
			'sok'		=> 'yes',			# yes/ok
			'func'		=> 'soft.checkip',		# Целевая функция
			'pricelist'	=> $ISPswInfo['pricelist_id'],	# прайс
			'period'	=> $ISPswInfo['period'],	# период на который заказываем
			'ip'		=> $ISPswInfo['IP'],		# проверяемый адрес
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	$Response = HTTP_Send($Settings['Params']['PrefixAPI'],$HTTP,Array(),$Request);
	if(Is_Error($Response))
		return ERROR | @Trigger_Error('[BillManager_Check_ISPsystem_IP]: не удалось соедениться с сервером');
	#-----------------------------------------------------------------------------
	$Response = Trim($Response['Body']);
	$XML = String_XML_Parse($Response);
	if(Is_Exception($XML))
		return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
	#-------------------------------------------------------------------------------
	$XML = $XML->ToArray();
	#-------------------------------------------------------------------------------
	$Doc = $XML['doc'];
	if(IsSet($Doc['error']))
		return FALSE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Sleep(1);
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}




#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function BillManager_Check_Local_IP($Settings, $ISPswInfo){
	/******************************************************************************/
	$__args_types = Array('array', 'array');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	Debug(SPrintF('[system/libs/BillManager.php]: run BillManager_Check_Local_IP'));
	#-------------------------------------------------------------------------------
	# надо проверять IP по собственной базе.
	# если он есть у нас и на нём есть лицензия - то в испсистем не надо лазить
	# вообще, тут наверно будет достаточно сложная логика...
	# а пока - функция фейковая
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
}





# внутренние функции
#-----------------------------------------------------------------------------
#-----------------------------------------------------------------------------
function BillManager_Build_HTTP($Settings){
	/******************************************************************************/
	$__args_types = Array('array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	$authinfo = SPrintF('%s:%s',$Settings['Login'],$Settings['Password']);
	#-------------------------------------------------------------------------------
	$HTTP = Array(
			'Address'       => $Settings['Address'],
			'Port'          => $Settings['Port'],
			'Host'          => $Settings['Address'],
			'Protocol'      => $Settings['Protocol'],
			'Hidden'        => $authinfo,
			'IsLogging'	=> $Settings['Params']['IsLogging']
			);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $HTTP;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------






?>
