<?php
# Rootden 2012 for lowhosting.ru
  /****************************************************************************/
  if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
  
function InternetBS_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$ContractID = '',$IsPrivateWhoIs,$PepsonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  	$Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8'
    );

	$username = $Settings['Login'];
	$password = $Settings['Password'];

	$tld = $DomainZone;
	$sld = $DomainName;
	$regperiod = $Years;

	$RegistrantFirstName = Translit($Person['Name']);
	$RegistrantLastName  = Translit($Person['Lastname']);
	$RegistrantAddress1  = Translit($Person['pAddress']);
	$RegistrantCity      = Translit($Person['pCity']);
	$RegistrantCountry   = $Person['pCountry'];
	$RegistrantStateProvince = Translit($Person['pState']);
	$RegistrantPostalCode = $Person['pIndex'];
	$RegistrantEmailAddress = $Person['Email'];
	$RegistrantPhone = internetbs_reformatPhone ( $Person['Phone'], $Person['pCountry'] );
	
	$domainName = $sld . '.' . $tld;
	$apiServerUrl = 'https://'.$Settings['Address'].'/';
	$commandUrl = $apiServerUrl . 'Domain/Create';
	
	$nsar = array ('ns1'=>$Ns1Name, 'ns2'=>$Ns2Name, 'ns3'=>$Ns3Name, 'ns4'=>$Ns4Name);
	
	$nslist = array ();
	for($i = 1; $i <= 4; $i ++) {
		if (isset ( $nsar ["ns$i"] )) {
			array_push ( $nslist, $nsar ["ns$i"] );
		}
	}
	
  $data = array ('apikey' => $username, 'password' => $password, 'domain' => $domainName, 

  'registrant_firstname' => $RegistrantFirstName, 'registrant_lastname' => $RegistrantLastName, 'registrant_street' => $RegistrantAddress1, 'registrant_city' => $RegistrantCity, 'registrant_countrycode' => $RegistrantCountry, 'registrant_postalcode' => $RegistrantPostalCode, 'registrant_email' => $RegistrantEmailAddress, 'registrant_phonenumber' => $RegistrantPhone, 

  'technical_firstname' => $RegistrantFirstName, 'technical_lastname' => $RegistrantLastName, 'technical_street' => $RegistrantAddress1, 'technical_city' => $RegistrantCity, 'technical_countrycode' => $RegistrantCountry, 'technical_postalcode' => $RegistrantPostalCode, 'technical_email' => $RegistrantEmailAddress, 'technical_phonenumber' => $RegistrantPhone, 

  'admin_firstname'   =>   $RegistrantFirstName, 'admin_lastname' => $RegistrantLastName, 'admin_street' => $RegistrantAddress1, 'admin_city' => $RegistrantCity, 'admin_countrycode' => $RegistrantCountry, 'admin_postalcode' => $RegistrantPostalCode, 'admin_email' => $RegistrantEmailAddress, 'admin_phonenumber' => $RegistrantPhone, 

  'billing_firstname' => $RegistrantFirstName, 'billing_lastname' => $RegistrantLastName, 'billing_street' => $RegistrantAddress1, 'billing_city' => $RegistrantCity, 'billing_countrycode' => $RegistrantCountry, 'billing_postalcode' => $RegistrantPostalCode, 'billing_email' => $RegistrantEmailAddress, 'billing_phonenumber' => $RegistrantPhone );
	

	if(!empty($Person['CompanyName'])){
		$data["Registrant_Organization"] = trim(Translit($Person['CompanyName']));
		$data["technical_Organization"] = trim(Translit($Person['CompanyName']));
		$data["admin_Organization"] = trim(Translit($Person['CompanyName']));
		$data["billing_Organization"] = trim(Translit($Person['CompanyName']));
	}

	if (count ( $nslist )) {
		$data ['ns_list'] = trim ( implode ( ',', $nslist ), "," );
	}

	if (!$IsPrivateWhoIs){
	$data ["privateWhois"] = "FULL";
	}else {
	$data ["privateWhois"] = "DISABLE";
	}

	$extarr = explode ( '.', $tld );
	$ext = array_pop ( $extarr );
	
	if ($tld == 'eu' || $tld == 'be' || $ext == 'uk') {
		$data ['registrant_language'] = 'en';
	}
	
	if($tld=='eu') {
	    
	    $europianLanguages = array("cs","da","de","el","en","es","et","fi","fr","hu","it","lt","lv","mt","nl","pl","pt","sk","sl","sv","ro","bg","ga");
	    if(!in_array($data ['registrant_language'],$europianLanguages)) {
	        $data ['registrant_language']='en';
	    }
	    $europianCountries = array('AT', 'AX', 'BE', 'BG', 'CZ', 'CY', 'DE', 'DK', 'ES', 'EE', 'FI', 'FR', 'GR', 'GB', 'GF', 'GI', 'GP', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'MQ', 'NL', 'PL', 'PT', 'RE', 'RO', 'SE', 'SK', 'SI');
        if(!in_array($RegistrantCountry, $europianCountries)) {
            $RegistrantCountry = 'IT';
        }
        $data['registrant_countrycode'] = $RegistrantCountry;
	}
	
	if($tld=='be') {
	    if(!in_array($data ['registrant_language'],array('en','fr','nl'))) {
	        $data ['registrant_language']='en';
	    }
        if(!in_array($RegistrantCountry, array("AF","AX","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AU","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BA","BW","BV","BR","IO","VG","BN","BG","BF","BI","KH","CM","CA","CV","KY","CF","TD","CL","CN","CX","CC","CO","KM","CG","CK","CR","HR","CU","CY","CZ","CD","DK","DJ","DM","DO","TL","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FM","FJ","FI","FR","GF","PF","TF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP","GU","GT","GN","GW","GY","HT","HM","HN","HK","HU","IS","IN","ID","IR","IQ","IE","IM","IL","IT","CI","JM","JP","JO","KZ","KE","KI","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","MD","MC","MN","ME","MS","MA","MZ","MM","NA","NR","NP","NL","AN","NC","NZ","NI","NE","NG","NU","NF","KP","MP","NO","OM","PK","PW","PS","PA","PG","PY","PE","PH","PN","PL","PT","PR","QA","RE","RO","RU","RW","SH","KN","LC","PM","VC","WS","SM","ST","SA","SN","RS","SC","SL","SG","SK","SI","SB","SO","ZA","GS","KR","ES","LK","SD","SR","SJ","SZ","SE","CH","SY","TW","TJ","TZ","TH","TG","TK","TO","TT","TN","TR","TM","TC","TV","VI","UG","UA","AE","GB","US","UM","UY","UZ","VU","VA","VE","VN","WF","EH","YE","ZM","ZW"))) {
            $RegistrantCountry = 'IT';
        }
        $data['registrant_countrycode'] = $RegistrantCountry;
	}

	if($tld=='us')	{
		$data['registrant_uspurpose'] = 'P3';
		$data['registrant_usnexuscategory'] = 'C31';
		$data['registrant_usnexuscountry'] = $Person['pCountry'];
	}
	
	if ($ext == 'uk') {
		$data ['registrant_dotUKOrgType'] = "FOTHER";
		$data ['registrant_dotUKLocality'] = $RegistrantCountry;
	}
	
	if ($tld == 'asia') {
	    return new gException('WRONG_ZONE_NAME','Указанная зона не поддерживается в автоматическом режиме');
	}
	
	if (in_array($ext, array('fr','re','pm','tf','wf','yt'))) {
    return new gException('WRONG_ZONE_NAME','Указанная зона не поддерживается в автоматическом режиме');
	}
	
	if($tld=='tel') {
	    $data['telHostingAccount'] = md5($RegistrantLastName.$RegistrantFirstName.time().rand(0,99999));
	    $data['telHostingPassword'] = 'passwd'.rand(0,99999);
	}
	
	if($tld=='it') {
     return new gException('WRONG_ZONE_NAME','Указанная зона не поддерживается в автоматическом режиме');
	}
	
	if (isset ($Years) && $regperiod > 0) {
		$data ['period'] = $regperiod . "Y";
	}

	$Result = Http_Send($commandUrl,$Http,Array(),$data);
	if(Is_Error($Result))
    return ERROR | @Trigger_Error('[InternetBS_Domain_Register]:не удалось выполнить запрос к серверу');
	
    #-----------------------------------------------------------------------------
    $Result = Trim($Result['Body']);
    #-----------------------------------------------------------------------------
    $Result = internetbs_parseResult($Result);
    #-----------------------------------------------------------------------------

	#Debug(print_r($data, true));
	#Debug(print_r($Result, true));
	
	if(isset($Result['product_0_status'])){
	if($Result['product_0_status'] == 'SUCCESS'){
	 return Array('TicketID'=>$domainName);
    }
	}else{

	if($Result['status'] == 'FAILURE'){
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
    }
	}

   return new gException('WRONG_ANSWER',$Result);
}



#-------------------------------------------------------------------------------
function InternetBS_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
  /****************************************************************************/
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
    $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8'
    );
  
    $username = $Settings['Login'];
	$password = $Settings['Password'];
	$tld = $DomainZone;
	$sld = $DomainName;
	
	$nsar = array ('ns1'=>$Ns1Name, 'ns1_ip'=>$Ns1IP, 'ns2'=>$Ns2Name, 'ns2_ip'=>$Ns2IP, 'ns3'=>$Ns3Name, 'ns3_ip'=>$Ns3IP, 'ns4'=>$Ns4Name, 'ns4_ip'=>$Ns4IP);
	
	$nslist = array ();
	for($i = 1; $i <= 4; $i ++) {
		if (isset ( $nsar ["ns$i"] )) {
			if (isset ( $nsar ['ns' . $i . '_ip'] ) && strlen ( $nsar ['ns' . $i . '_ip'] )) {
				$nsar ["ns$i"] .= ' ' . $nsar ['ns' . $i . '_ip'];
			}
			array_push ( $nslist, $nsar ["ns$i"] );
		}
	}
	
	$domainName = $sld . '.' . $tld;
	
    $apiServerUrl = 'https://'.$Settings['Address'].'/';
	$commandUrl = $apiServerUrl . 'Domain/Update';
	
	$data = array ('apikey' => $username, 'password' => $password, 'domain' => $domainName, 'ns_list' => trim ( implode ( ',', $nslist ), "," ) );
    
	$Result = Http_Send($commandUrl,$Http,Array(),$data);
	if(Is_Error($Result))
    return ERROR | @Trigger_Error('[InternetBS_Domain_Register]:не удалось выполнить запрос к серверу');
	
    #-----------------------------------------------------------------------------
    $Result = Trim($Result['Body']);
    #-----------------------------------------------------------------------------
    $Result = internetbs_parseResult($Result);
    #-----------------------------------------------------------------------------
	
	if(isset($Result['status'])){
	if($Result['status'] == 'SUCCESS'){
	 return Array('TicketID'=>$domainName);
    }
	}else{
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
	}
	
   return new gException('WRONG_ANSWER',$Result);
}

#-------------------------------------------------------------------------------
function InternetBS_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$CustomerID,$DomainID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
    $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8'
    );
  
    $username = $Settings['Login'];
	$password = $Settings['Password'];
	$tld = $DomainZone;
	$sld = $DomainName;
	$regperiod = $Years;
	
	$domainName = $sld . '.' . $tld;
    
	$apiServerUrl = 'https://'.$Settings['Address'].'/';
	$commandUrl = $apiServerUrl . 'Domain/Renew';
	
	$data = array ('apikey' => $username, 'password' => $password, 'domain' => $domainName );
	
	
    if ($regperiod > 0) {
		$data ['period'] = $regperiod . "Y";
	}
	
	$Result = Http_Send($commandUrl,$Http,Array(),$data);
	if(Is_Error($Result))
    return ERROR | @Trigger_Error('[InternetBS_Domain_Register]:не удалось выполнить запрос к серверу');
	
    #-----------------------------------------------------------------------------
    $Result = Trim($Result['Body']);
    #-----------------------------------------------------------------------------
    $Result = internetbs_parseResult($Result);
    #-----------------------------------------------------------------------------

    if(isset($Result['product_0_status'])){
	if($Result['product_0_status'] == 'SUCCESS'){
	 return Array('TicketID'=>$domainName);
    }
	}else{
	if($Result['status'] == 'FAILURE'){
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
    }
	}

   return new gException('WRONG_ANSWER',$Result);
}


function InternetBS_Check_Task($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('DomainID'=>0);
}




#-----------------------------------------------------------------------------------------------------------
function internetbs_parseResult($data) {
	$result = array ();
	$arr = explode ( "\n", $data );
	foreach ( $arr as $str ) {
		list ( $varName, $value ) = explode ( "=", $str, 2 );
		$varName = trim ( $varName );
		$value = trim ( $value );
		$result [$varName] = $value;
	}
	return $result;
}

#-----------------------------------------------------------------------------------------------------------


#-----------------------------------------------------------------------------------------------------------

function internetbs_mapCountry($countryCode) {
	$mapc = array ('US' => 1, 'CA' => 1, 'AI' => 1, 'AG' => 1, 'BB' => 1, 'BS' => 1, 'VG' => 1, 'VI' => 1, 'KY' => 1, 'BM' => 1, 'GD' => 1, 'TC' => 1, 'MS' => 1, 'MP' => 1, 'GU' => 1, 'LC' => 1, 'DM' => 1, 'VC' => 1, 'PR' => 1, 'DO' => 1, 'TT' => 1, 'KN' => 1, 'JM' => 1, 'EG' => 20, 'MA' => 212, 'DZ' => 213, 'TN' => 216, 'LY' => 218, 'GM' => 220, 'SN' => 221, 'MR' => 222, 'ML' => 223, 'GN' => 224, 'CI' => 225, 'BF' => 226, 'NE' => 227, 'TG' => 228, 'BJ' => 229, 'MU' => 230, 'LR' => 231, 'SL' => 232, 'GH' => 233, 'NG' => 234, 'TD' => 235, 'CF' => 236, 'CM' => 237, 'CV' => 238, 'ST' => 239, 'GQ' => 240, 'GA' => 241, 'CG' => 242, 'CD' => 243, 'AO' => 244, 'GW' => 245, 'IO' => 246, 'AC' => 247, 'SC' => 248, 'SD' => 249, 'RW' => 250, 'ET' => 251, 'SO' => 252, 'DJ' => 253, 'KE' => 254, 'TZ' => 255, 'UG' => 256, 'BI' => 257, 'MZ' => 258, 'ZM' => 260, 'MG' => 261, 'RE' => 262, 'ZW' => 263, 'NA' => 264, 'MW' => 265, 'LS' => 266, 'BW' => 267, 'SZ' => 268, 'KM' => 269, 'YT' => 269, 'ZA' => 27, 'SH' => 290, 'ER' => 291, 'AW' => 297, 'FO' => 298, 'GL' => 299, 'GR' => 30, 'NL' => 31, 'BE' => 32, 'FR' => 33, 'ES' => 34, 'GI' => 350, 'PT' => 351, 'LU' => 352, 'IE' => 353, 'IS' => 354, 'AL' => 355, 'MT' => 356, 'CY' => 357, 'FI' => 358, 'BG' => 359, 'HU' => 36, 'LT' => 370, 'LV' => 371, 'EE' => 372, 'MD' => 373, 'AM' => 374, 'BY' => 375, 'AD' => 376, 'MC' => 377, 'SM' => 378, 'VA' => 379, 'UA' => 380, 'CS' => 381, 'YU' => 381, 'HR' => 385, 'SI' => 386, 'BA' => 387, 'EU' => 388, 'MK' => 389, 'IT' => 39, 'RO' => 40, 'CH' => 41, 'CZ' => 420, 'SK' => 421, 'LI' => 423, 'AT' => 43, 'GB' => 44, 'DK' => 45, 'SE' => 46, 'NO' => 47, 'PL' => 48, 'DE' => 49, 'FK' => 500, 'BZ' => 501, 'GT' => 502, 'SV' => 503, 'HN' => 504, 'NI' => 505, 'CR' => 506, 'PA' => 507, 'PM' => 508, 'HT' => 509, 'PE' => 51, 'MX' => 52, 'CU' => 53, 'AR' => 54, 'BR' => 55, 'CL' => 56, 'CO' => 57, 'VE' => 58, 'GP' => 590, 'BO' => 591, 'GY' => 592, 'EC' => 593, 'GF' => 594, 'PY' => 595, 'MQ' => 596, 'SR' => 597, 'UY' => 598, 'AN' => 599, 'MY' => 60, 'AU' => 61, 'CC' => 61, 'CX' => 61, 'ID' => 62, 'PH' => 63, 'NZ' => 64, 'SG' => 65, 'TH' => 66, 'TL' => 670, 'AQ' => 672, 'NF' => 672, 'BN' => 673, 'NR' => 674, 'PG' => 675, 'TO' => 676, 'SB' => 677, 'VU' => 678, 'FJ' => 679, 'PW' => 680, 'WF' => 681, 'CK' => 682, 'NU' => 683, 'AS' => 684, 'WS' => 685, 'KI' => 686, 'NC' => 687, 'TV' => 688, 'PF' => 689, 'TK' => 690, 'FM' => 691, 'MH' => 692, 'RU' => 7, 'KZ' => 7, 'XF' => 800, 'XC' => 808, 'JP' => 81, 'KR' => 82, 'VN' => 84, 'KP' => 850, 'HK' => 852, 'MO' => 853, 'KH' => 855, 'LA' => 856, 'CN' => 86, 'XS' => 870, 'XE' => 871, 'XP' => 872, 'XI' => 873, 'XW' => 874, 'XU' => 878, 'BD' => 880, 'XG' => 881, 'XN' => 882, 'TW' => 886, 'TR' => 90, 'IN' => 91, 'PK' => 92, 'AF' => 93, 'LK' => 94, 'MM' => 95, 'MV' => 960, 'LB' => 961, 'JO' => 962, 'SY' => 963, 'IQ' => 964, 'KW' => 965, 'SA' => 966, 'YE' => 967, 'OM' => 968, 'PS' => 970, 'AE' => 971, 'IL' => 972, 'BH' => 973, 'QA' => 974, 'BT' => 975, 'MN' => 976, 'NP' => 977, 'XR' => 979, 'IR' => 98, 'XT' => 991, 'TJ' => 992, 'TM' => 993, 'AZ' => 994, 'GE' => 995, 'KG' => 996, 'UZ' => 998 );
	if (isset ( $mapc [$countryCode] )) {
		return ($mapc [$countryCode]);
	} else {
		return (1);
	}
}
#-----------------------------------------------------------------------------------------------------------


#-----------------------------------------------------------------------------------------------------------


function internetbs_chekPhone($phoneNumber) {
	$phoneNumber = str_replace ( " ", "", $phoneNumber );
	$phoneNumber = str_replace ( "\t", "", $phoneNumber );
	
	if (eregi ( '^\+[0-9]{1,4}\.[0-9 ]+$', $phoneNumber )) {
		return (true);
	}
	
	return (false);
}

#-----------------------------------------------------------------------------------------------------------


#-----------------------------------------------------------------------------------------------------------

function internetbs_reformatPhone($phoneNumber, $countryCode) {
	$countryPhoneCode = internetbs_mapCountry ( $countryCode );
	$plus = 0;
	$country = "";
	
	$scontrol = trim ( $phoneNumber );
	$l = strlen ( $scontrol );
	if ($scontrol {0} == '+')
		$plus = true;
	$scontrol = preg_replace ( '#\D*#si', "", $scontrol );
	if ($plus)
		$scontrol = "+" . $scontrol;
	if (! $l) {
		return ("+$countryPhoneCode.1111111");
	}
	if (strncmp ( $scontrol, "00", 2 ) == 0) {
		$scontrol = "+" . substr ( $scontrol, 2 );
		if (strlen ( $scontrol ) == 1) {
			$scontrol = '1111111';
		}
	}
	$rphone = "+1.1111111";
	if ($scontrol {0} == '+') {
		for($i = 2; $i < strlen ( $scontrol ); $i ++) {
			$first = substr ( $scontrol, 1, $i - 1 );
			if ($first == $countryPhoneCode) {
				$scontrol = "+" . $first . "." . substr ( $scontrol, $i );
				return $scontrol;
			}
		}
		$scontrol = trim ( $scontrol, "+" );
		$rphone = "+" . $countryPhoneCode . "." . $scontrol;
	} else {
		$rphone = "+" . $countryPhoneCode . "." . $scontrol;
	}
	
	if (internetbs_chekPhone ( $rphone )) {
		return $rphone;
	}
	
	return "+1.1111111";
}
#-----------------------------------------------------------------------------------------------------------

?>
