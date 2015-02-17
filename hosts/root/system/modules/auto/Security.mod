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
		Debug(SPrintF('[Security module]: (%s) = (%s)',$CookieID,$Cookie));
		#-------------------------------------------------------------------------------
		if(Preg_Match($Template,$Cookie))
			if(!$IsNoAction)
				return ERROR | @Trigger_Error(600);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Count($Args) > 0){
	#-------------------------------------------------------------------------------
	Debug('[Security module]: [проверка параметров]');
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Args) as $ArgID){
		#-------------------------------------------------------------------------------
		$Arg = &$Args[$ArgID];
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[Security module]: (%s) = (%s)',$ArgID,$Arg?(Is_Array($Arg)?'Array':$Arg):'EMPTY'));
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
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Settings['IsCheckReferer'] && IsSet($_SERVER["REQUEST_URI"]) && Preg_Match('#/API/#',$_SERVER["REQUEST_URI"])){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[Security module]: проверка параметров реферера для REQUEST_URI = %s',$_SERVER["REQUEST_URI"]));
	#-------------------------------------------------------------------------------
	$Template = "#^(/API/EmailConfirm|/API/Logon|/API/Events).*#";
	#-------------------------------------------------------------------------------
	if(Preg_Match($Template,$_SERVER["REQUEST_URI"]))
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
