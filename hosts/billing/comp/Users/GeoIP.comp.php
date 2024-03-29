<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IP','IsString','UA');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#Debug(SPrintF("[comp/Users/GeoIP]: IP = %s, UA = %s",$IP,$UA));
#-------------------------------------------------------------------------------
// MaxMindDB
use MaxMind\Db\Reader;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выхлопное сообщение для неудачной работы компонента
$Message = 'неизвестная ошибка определения страны/города';
// IP может быть не задан (тикетница, например)
if(!$IP || $IP == '-'){
	#-------------------------------------------------------------------------------
	if($UA){
		#-------------------------------------------------------------------------------
		return HtmlSpecialChars($UA,ENT_QUOTES);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		return '';
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Extension_Loaded('geoip') && (geoip_db_avail(GEOIP_COUNTRY_EDITION))){
	#-------------------------------------------------------------------------------
	$Country = geoip_country_name_by_name($IP);
	#-------------------------------------------------------------------------------
	if($GeoIP = @geoip_record_by_name($IP)){
		#-------------------------------------------------------------------------------
		if($GeoIP['city'])
			$City = $GeoIP['city'];
		#-------------------------------------------------------------------------------
		if($GeoIP['country_code3'])
			$CountryCode = $GeoIP['country_code3'];
		#-------------------------------------------------------------------------------
		if(!$Country && $GeoIP['country_name'])
			$Country = $GeoIP['country_name'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#Debug(SPrintF("[comp/Users/GeoIP]: @geoip_record_by_name = %s",print_r(@geoip_record_by_name($IP),true)));
	#-------------------------------------------------------------------------------
	if(!IsSet($CountryCode))
		$CountryCode = @geoip_country_code3_by_name($IP);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Message = 'В php отсуствует модуль GeoIP или отсутствует база IP адресов';
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Extension_Loaded('maxminddb')){
	#-------------------------------------------------------------------------------
	// ищем базу
	$Preffixes = Array('/usr/local/share/GeoIP','/usr/local/GeoIP');
	$DbFileZ = Array('GeoLite2-Country.mmdb','GeoLite2-City.mmdb');
	#-------------------------------------------------------------------------------
	foreach($Preffixes as $Preffix)
		foreach($DbFileZ as $DbFile)
			if(@File_Exists(SPrintF('%s/%s',$Preffix,$DbFile)))
				$MaxMindDB = SPrintF('%s/%s',$Preffix,$DbFile);
	#-------------------------------------------------------------------------------
	if(IsSet($MaxMindDB)){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF("[comp/Users/GeoIP]: MaxMindDB = %s",$MaxMindDB));
		#-------------------------------------------------------------------------------
		$Reader = new Reader($MaxMindDB);
		#-------------------------------------------------------------------------------
		$GeoIP = $Reader->get($IP);
		#-------------------------------------------------------------------------------
		#Debug(SPrintF("[comp/Users/GeoIP]: GeoIP = %s",print_r($GeoIP,true)));
		// город
		if(IsSet($GeoIP['city']['names']['ru'])){
			#-------------------------------------------------------------------------------
			$City = $GeoIP['city']['names']['ru'];
			#-------------------------------------------------------------------------------
		}elseif(IsSet($GeoIP['city']['names']['en'])){
			#-------------------------------------------------------------------------------
			$City = $GeoIP['city']['names']['en'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// код страны
		if(IsSet($GeoIP['country']['iso_code']))
			$CountryCode = $GeoIP['country']['iso_code'];
		#-------------------------------------------------------------------------------
		// страна
		if(IsSet($GeoIP['country']['names']['ru'])){
			#-------------------------------------------------------------------------------
			$Country = $GeoIP['country']['names']['ru'];
			#-------------------------------------------------------------------------------
		}elseif(IsSet($GeoIP['country']['names']['en'])){
			#-------------------------------------------------------------------------------
			$Country = $GeoIP['country']['names']['en'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#Debug(SPrintF("[comp/Users/GeoIP]: CountryCode = %s; Country = %s; City = %s",$CountryCode,$Country,@$City));
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Message = 'Модуль php MaxMindDB вклчюен, но не удалось найти базу IP адресов (или включен open_basedir)';
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Message = 'В php отсуствует модуль MaxMindDB';
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#Debug(SPrintF("[comp/Users/GeoIP]: @City = %s",print_r(@$City,true)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# города бывают не всегда латиницей. пример - Орёл
if(IsSet($City))
	$City = @Iconv('UTF-8','UTF-8//IGNORE',$City);
//$City = @Iconv('','ISO-8859-1//IGNORE',$City);
#-------------------------------------------------------------------------------
#Debug(SPrintF("[comp/Users/GeoIP]: City = %s",@$City));
#-------------------------------------------------------------------------------
$IPInfo = SPrintF('IP: %s %s %s',$IP,(IsSet($Country))?SPrintF(' / %s',$Country):$Message,(IsSet($City))?SPrintF(' / %s',$City):'');
//Debug(SPrintF("[comp/Users/GeoIP]: IPInfo = %s",$IPInfo));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($UA){
	#-------------------------------------------------------------------------------
	$InfoImage = SprintF('%s<BR />%s',HtmlSpecialChars($IPInfo,ENT_QUOTES),HtmlSpecialChars($UA,ENT_QUOTES));
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$InfoImage = SprintF('%s',HtmlSpecialChars($IPInfo,ENT_QUOTES));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
// нужно в HTML, не-объект
if($IsString)
	$InfoImage = HtmlSpecialChars($InfoImage,ENT_QUOTES);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($CountryCode) && $CountryCode){
	#-------------------------------------------------------------------------------
	$Flag = Comp_Load('Formats/CountryImage',$CountryCode,16,$InfoImage,$IsString);
	if(Is_Error($Flag))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($IsString){
		#-------------------------------------------------------------------------------
		return $Flag;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		return new Tag('NOBODY',$Flag,new Tag('SPAN',SPrintF(' | %s',$IP)));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}elseif($IsString){
	#-------------------------------------------------------------------------------
	return SPrintF('<IMG height="16" width="16" src="SRC:{Images/Icons/Info.gif}" align="top" onmouseover="PromptShow(event,\'%s\',this);"/>',$InfoImage);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $IP;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
