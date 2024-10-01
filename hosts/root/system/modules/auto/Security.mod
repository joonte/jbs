<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
/*------------------------------------------------------------------------------
      Задача:
Проверить все присланные параметры на соответствие шаблону безопасности.
------------------------------------------------------------------------------*/
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Other']['Modules']['Security'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsNoAction = FALSE;
#-------------------------------------------------------------------------------
$Array = Explode(',',$Settings['ExcludeIPs']);
#-------------------------------------------------------------------------------
foreach($Array as $IP)
	if(Trim($IP) == @$_SERVER['REMOTE_ADDR'])
		$IsNoAction = TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Шаблон безопасности (регулярное выражение)
#-------------------------------------------------------------------------------
$Template = "/^(SELECT.*FROM.*|INSERT INTO|UPDATE .* SET).*/i";
#-------------------------------------------------------------------------------
$TemplateAPI = "#^/API/(ISPswSettingURL|Telegram|Viber|VK|Confirm|UnSubScribe|Logon|Logout|YandexMetrika|Events|OrdersPay|.*OrderPay|v2).*#";
#-------------------------------------------------------------------------------
$PayAPI = "#^/API/(OrdersPay|.*OrderPay).*#";
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Hidden = Explode(',',$Settings['Hidden']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# запрещённые значения переменых
$DenyValues = Array('../','..\\','`','\'','<script>');
# исключения при проверке значений переменных
$ExcludeVariables = Array('Text','Message','pAddress','Theme');
# файл куда пишем события
$SuspiciousValues = SPrintF('%s/suspicious.values.log',SYSTEM_PATH);




#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Args = &Args();
#-------------------------------------------------------------------------------
if(Count($_COOKIE) > 0){
	#-------------------------------------------------------------------------------
	Debug('[Security module]: [проверка Cookie]');
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($_COOKIE) as $CookieID){
		#-------------------------------------------------------------------------------
		$Cookie = $_COOKIE[$CookieID];
		#-------------------------------------------------------------------------------
		if(In_Array($CookieID,$Hidden)){
			#-------------------------------------------------------------------------------
			$Value = ' ***HIDDEN*** ';
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Value = Is_Array($Cookie)?Print_R($Cookie,true):$Cookie;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[Security module]: (%s) = (%s)',$CookieID,$Value));
		#-------------------------------------------------------------------------------
		# TODO: перберать бы содержимое массива, по хорошему-то...
		if(Is_Array($Cookie))
			continue;
		#-------------------------------------------------------------------------------
		if(Preg_Match($Template,$Cookie))
			if(!$IsNoAction)
				return ERROR | @Trigger_Error(600);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# реализация JBS-1210, проверка значений параметров
		if(!Is_Array($Cookie))
			foreach($DenyValues as $DenyValue)
				if(Substr_Count($Cookie,$DenyValue))
					Debug(SPrintF('[Security module]: DENY VALUE (%s) = (%s)',$CookieID,$Value));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Count($Args) > 0){
	#-------------------------------------------------------------------------------
	// если это оплата, то недопустимо делать частые запросы
	if(Preg_Match($PayAPI,$_SERVER["REQUEST_URI"])){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[Security module]: оплата: %s',$_SERVER["REQUEST_URI"]));
		#-------------------------------------------------------------------------------
		$CacheID = 'PayAPI';
		#-------------------------------------------------------------------------------
		$Result = CacheManager::get($CacheID);
		#-------------------------------------------------------------------------------
		$Mt = MicroTime(TRUE);
		#-------------------------------------------------------------------------------
		if($Result && $Result['Time'] > $Mt - IntVal($Settings['PayTimeout'])){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[Security module]: сработала защита от уязвимости "Race Condition", %s < %s; IP: %s;URI: %s;',$Mt - $Result['Time'],$Settings['PayTimeout'],$Result['IP'],$Result['URI']));
			#-------------------------------------------------------------------------------
			if(!$IsNoAction)
				return ERROR | @Trigger_Error(600);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		CacheManager::add($CacheID,Array('Time'=>$Mt,'URI'=>$_SERVER["REQUEST_URI"],'IP'=>$_SERVER['REMOTE_ADDR']),$Settings['PayTimeout'] + 10);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Debug('[Security module]: [проверка параметров]');
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Args) as $ArgID){
		#-------------------------------------------------------------------------------
		$Arg = &$Args[$ArgID];
		#-------------------------------------------------------------------------------
		if(In_Array($ArgID,$Hidden)){
			#-------------------------------------------------------------------------------
			$Value = ' ***HIDDEN*** ';
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Value = $Arg?(Is_Array($Arg)?'Array':$Arg):'EMPTY';
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[Security module]: (%s) = (%s)',$ArgID,$Value));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# реализация JBS-1210, проверка значений параметров
		if(!Is_Array($Arg))
			foreach($DenyValues as $DenyValue)
				if(!In_Array($ArgID,$ExcludeVariables))
					if(Substr_Count(Mb_StrToLower($Arg),$DenyValue)){
						#-------------------------------------------------------------------------------
						Debug(SPrintF('[Security module]: DENY VALUE (%s) = (%s)',$ArgID,$Arg));
						#-------------------------------------------------------------------------------
						List($micro, $seconds) = Explode(' ',MicroTime());
						#-------------------------------------------------------------------------------
						$Message = SPrintF("[%s.%02u][%s] %s %s\n",Date('Y-m-d H:i:s'), $micro * 100, IsSet($_SERVER["REMOTE_PORT"])?$_SERVER["REMOTE_PORT"]:"console",@$_SERVER['REMOTE_ADDR'],SPrintF('(%s) = (%s)',$ArgID,$Value));
						#-------------------------------------------------------------------------------
						@File_Put_Contents($SuspiciousValues,$Message,FILE_APPEND);
						#-------------------------------------------------------------------------------
					}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if($ArgID == 'CSRF')
			$CSRF = $Arg;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(Is_Array($Arg)){
			#-------------------------------------------------------------------------------
			foreach(Array_Keys($Arg) as $Key){
				#-------------------------------------------------------------------------------
				$Element = &$Arg[$Key];
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[Security module]: проверка параметра (%s [%s]) = (%s)',$ArgID,$Key,Is_Array($Element)?'Array':$Element));
				#-------------------------------------------------------------------------------
				if(Is_Scalar($Element) && Preg_Match($Template,$Element))
					if(!$IsNoAction)
						return ERROR | @Trigger_Error(600);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			continue;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!Preg_Match($TemplateAPI,$_SERVER["REQUEST_URI"])){
		#-------------------------------------------------------------------------------
		if(!IsSet($CSRF)){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[Security module]: параметр CSRF не задан, API = %s',IsSet($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:'не задано'));
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			List($Time,$Hash) = Explode('/',$CSRF);
			#-------------------------------------------------------------------------------
			$IsOK = (Md5($Time . @$Config['CSRFKey'] . (IsSet($_COOKIE['SessionID'])?$_COOKIE['SessionID']:'no_session')) != $Hash)?FALSE:TRUE;
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[Security module]: проверка значения CSRF: %s',$IsOK?'OK':'Ошибка, хэш не сопадает'));
			#-------------------------------------------------------------------------------
			if($Settings['CSRFCheck'] && !$IsOK)
				if(!$IsNoAction)
					return ERROR | @Trigger_Error(603);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Settings['IsCheckReferer'] && IsSet($_SERVER["REQUEST_URI"]) && Preg_Match('#/API/#',$_SERVER["REQUEST_URI"])){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[Security module]: проверка параметров реферера для REQUEST_URI = %s',$_SERVER["REQUEST_URI"]));
	#-------------------------------------------------------------------------------
	if(Preg_Match($TemplateAPI,$_SERVER["REQUEST_URI"]))
		return TRUE;
	#-------------------------------------------------------------------------------
	if(IsSet($_SERVER["HTTP_REFERER"])){
		#-------------------------------------------------------------------------------
		if(IsSet($_SERVER["HTTP_HOST"])){
			#-------------------------------------------------------------------------------
			if(!Preg_Match(SPrintF('#^(http|https)://%s.*#',$_SERVER["HTTP_HOST"]),$_SERVER["HTTP_REFERER"])){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[Security module]: реферер не соответствует хосту, IP = %s; API = %s',$_SERVER['REMOTE_ADDR'],$_SERVER["REQUEST_URI"]));
				Debug(SPrintF('[Security module]: %s != %s',$_SERVER['HTTP_HOST'],$_SERVER["HTTP_REFERER"]));
				#-------------------------------------------------------------------------------
				if($Settings['IsCheckReferer'])
					if(!$IsNoAction)
						return ERROR | @Trigger_Error(601);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[Security module]: не задана переменная $_SERVER["HTTP_HOST"], невозможно проверить HTTP_REFERER'));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[Security module]: отсутствует реферер, IP = %s; API = %s',$_SERVER['REMOTE_ADDR'],$_SERVER["REQUEST_URI"]));
		#-------------------------------------------------------------------------------
		if(!$Settings['IsNoReferer'])
			if(!$IsNoAction)
				return ERROR | @Trigger_Error(602);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


?>
