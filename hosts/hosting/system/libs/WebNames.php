<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
function WebNames_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$ContractID = '',$IsPrivateWhoIs,$PersonID = 'Default',$Person = Array()){
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
			'Charset'  => 'CP1251'
			#-------------------------------------------------------------------------------
			);
	#-----------------------------------------------------------------------------
	#-----------------------------------------------------------------------------
	$Query = Array(
			#---------------------------------------------------------------------------
			'thisPage'           => 'pispRegistration',
			'username'           => $Settings['Login'],
			'password'           => $Settings['Password'],
			'domain_name'        => SPrintF('%s.%s',$DomainName,$DomainZone),
			'interface_revision' => 1,
			'interface_lang'     => 'en'
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Query['period'] = $Years;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Params = Array(
			#-------------------------------------------------------------------------------
			'DomainName'	=> $DomainName,
			'DomainZone'	=> $DomainZone,
			'Ns1Name'	=> $Ns1Name,
			'Ns2Name'	=> $Ns2Name,
			'Ns3Name'	=> $Ns3Name,
			'Ns4Name'	=> $Ns4Name,
			'Ns1IP'		=> $Ns1IP,
			'Ns2IP'		=> $Ns2IP,
			'Ns3IP'		=> $Ns3IP,
			'Ns4IP'		=> $Ns4IP,
			'ContractID'	=> $ContractID,
			'IsPrivateWhoIs'=> $IsPrivateWhoIs,
			'PersonID'	=> $PersonID,
			'Person'	=> $Person
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Query = Build_Query($Query,$Params);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
	if(Is_Error($Result))
		return ERROR | @Trigger_Error('[WebNames_Domain_Register]: не удалось выполнить запрос к серверу');
	#-------------------------------------------------------------------------------
	$Result = Trim($Result['Body']);
	#-------------------------------------------------------------------------------
	if(Preg_Match('/Success:/',$Result))
		return Array('TicketID'=>SPrintF('%s.%s',$DomainName,$DomainZone));
	#-------------------------------------------------------------------------------
	if(Preg_Match('/Error:/',$Result))
		return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
	#-------------------------------------------------------------------------------
	return new gException('WRONG_ANSWER',$Result);
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function WebNames_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$ContractID,$DomainID){
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
    'Charset'  => 'CP1251'
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'thisPage'           => 'pispRenewDomain',
    'username'           => $Settings['Login'],
    'password'           => $Settings['Password'],
    'domain_name'        => SPrintF('%s.%s',$DomainName,$DomainZone),
    'interface_revision' => 1,
    'interface_lang'     => 'en',
    'period'             => $Years
  );
  #-----------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames_Domain_Prolong]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Success:/',$Result))
    return Array('TicketID'=>'NO');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function WebNames_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'CP1251'
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'thisPage'           => 'pispRedelegation',
    'username'           => $Settings['Login'],
    'password'           => $Settings['Password'],
    'domain_name'        => SPrintF('%s.%s',$DomainName,$DomainZone),
    'interface_revision' => 1,
    'interface_lang'     => 'en'
  );
  #-----------------------------------------------------------------------------
  $Query['ns0'] = $Ns1Name;
  $Query['ns1'] = $Ns2Name;
  #-----------------------------------------------------------------------------
  if($Ns3Name)
    $Query['ns3'] = $Ns3Name;
  #-----------------------------------------------------------------------------
  if($Ns4Name)
    $Query['ns4'] = $Ns4Name;
  #-----------------------------------------------------------------------------
  if($Ns1IP && $Ns2IP){
    #---------------------------------------------------------------------------
    $Query['ns0ip'] = $Ns1IP;
    $Query['ns1ip'] = $Ns2IP;
  }
  #-----------------------------------------------------------------------------
  if($Ns3IP)
    $Query['ns2ip'] = $Ns3IP;
  #-----------------------------------------------------------------------------
  if($Ns4IP)
    $Query['ns3ip'] = $Ns4IP;
  #-----------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames_Domain_Ns_Change]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Success:/',$Result))
    return Array('TicketID'=>'NO');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function WebNames_Check_Task($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  if($TicketID == 'NO')
    return Array('DomainID'=>0);
  #-----------------------------------------------------------------------------
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'CP1251'
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'thisPage'           => 'pispGetApprovalStatus',
    'username'           => $Settings['Login'],
    'password'           => $Settings['Password'],
    'domain_name'        => $TicketID,
    'interface_revision' => 1,
    'interface_lang'     => 'en'
  );
  #-----------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames_Check_Task]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Success:\sDomain\sstatus\sis\s\'([A-Za-z\/]+)\'/',$Result,$Status)){
    #---------------------------------------------------------------------------
    $Status = Next($Status);
    #---------------------------------------------------------------------------
    switch($Status){
      case 'pending':
        return FALSE;
      case 'approved':
        return Array('DomainID'=>0);
      case 'errsent':
        return new gException('WRONG_CLIENT_DATA','В результате ручной проверки данных клиента регистратором были обнаружены ошибки');
      case 'N/A':
        return Array('DomainID'=>0);
      break;
      default:
        return new gException('WRONG_STATUS','Статус домена ошибочный');
    }
  }
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function WebNames_Get_Balance($Settings){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'CP1251'
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'thisPage'           => 'pispBalance',
    'username'           => $Settings['Login'],
    'password'           => $Settings['Password'],
    'interface_revision' => 1,
    'interface_lang'     => 'en'
  );
  #-----------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames_Check_Task]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Success:\sbalance\s-\s/',$Result)){
        Preg_Match('/([0-9]|\.)+/',$Result,$Prepay);
    return Array('Prepay'=>$Prepay[0]);
  }
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function WebNames_Is_Available_Domain($Settings,$Domain){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  # ввиду того, что вебнеймс интерфейса нормального не предоставляет, а за частые
  # запросы банит, кэшируем полученный результат и юазем кэш
  $CacheID = Md5($Settings['Login'] . $Settings['Password'] . 'pispAllDomainsInfo');
  $Result = CacheManager::get($CacheID);
  # если результата нет - лезем в вебнеймс
  if(!$Result || SizeOf($Result) < 2){
    $Http = Array(
      #---------------------------------------------------------------------------
      'Address'  => $Settings['Address'],
      'Port'     => $Settings['Port'],
      'Host'     => $Settings['Address'],
      'Protocol' => $Settings['Protocol'],
      'Charset'  => 'CP1251'
      );
    #-----------------------------------------------------------------------------
    $Query = Array(
      #---------------------------------------------------------------------------
      'thisPage'           => 'pispAllDomainsInfo',	# see JBS-252
      'username'           => $Settings['Login'],
      'password'           => $Settings['Password'],
      'interface_revision' => 1,
      'interface_lang'     => 'en'
      );
    #-----------------------------------------------------------------------------
    $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
    if(Is_Error($Result))
      return ERROR | @Trigger_Error('[WebNames_Is_Available_Domain]: не удалось выполнить запрос к серверу');
    #-----------------------------------------------------------------------------
    $Result = Trim($Result['Body']);
    #-----------------------------------------------------------------------------
    if(Preg_Match('/Error:/',$Result))
      return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
    #-----------------------------------------------------------------------------
    if(!Preg_Match('/Success:/',$Result))
      return ERROR | @Trigger_Error('[WebNames_Is_Available_Domain]: неизвестный ответ');
    #-----------------------------------------------------------------------------
    # кэшируем полученный результат
	CacheManager::add($CacheID, $Result, 3600);
  }
  #Debug("[system/libs/WebNames][WebNames_Is_Available_Domain]: " . print_r($Result,true));
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # разбираем строчки на массив
  $iDomains = Explode("\n", $Result);
  #-----------------------------------------------------------------------------
  # перебираем массив, ищщем нужный домен
  foreach($iDomains as $iDomain){
    # Domain f-box59.ru; Status N; CreationDate 2010-02-23; ExpirationDate 2012-02-23; FutureExpDate ;
    #Debug("[system/libs/WebNames][WebNames_Is_Available_Domain]: " . $iDomain);
    $DomainInfo = Explode(" ",$iDomain);
    #Debug("[system/libs/WebNames][WebNames_Is_Available_Domain]: " . print_r($DomainInfo,true));
    if(StrToLower(Trim($DomainInfo[1])) == StrToLower($Domain) . ';'){
      # домен есть на аккаунте
      return Array('Status'=>'true','ServiceID'=>'0');
    }
  }
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  return Array('Status'=>'false','ErrorText'=>'Domain not found');
  #-----------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------

# added by lissyara, for JBS-353, 2012-03-19 in 14:00 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function WebNames_Change_Contact_Detail($Settings,$Domain,$Person){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  // phone, e_mail, cell_phone
  #-------------------------------------------------------------------------------
  $Http = Array(
                #---------------------------------------------------------------------------
	        'Address'  => $Settings['Address'],
                'Port'     => $Settings['Port'],
                'Host'     => $Settings['Address'],
                'Protocol' => $Settings['Protocol'],
                'Charset'  => 'CP1251'
               );
  #-------------------------------------------------------------------------------
  $Query = Array(
                 'thisPage'           => 'pispContactDetails',
                 'username'           => $Settings['Login'],
                 'password'           => $Settings['Password'],
                 'interface_revision' => 1,
                 'interface_lang'     => 'en',
                 'domain_name'        => $Domain,
                );
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  if(IsSet($Person['Phone']))
    $Query['phone'] = $Person['Phone'];
  #-------------------------------------------------------------------------------
  if(IsSet($Person['CellPhone']))
    $Query['cell_phone'] = Str_Replace(' ','',$Person['CellPhone']);
  #-------------------------------------------------------------------------------
  if(IsSet($Person['Email']))
    $Query['e_mail'] = $Person['Email'];
  #-------------------------------------------------------------------------------
  if(IsSet($Person['PostalAddress']))
    $Query['p_addr'] = $Person['PostalAddress'];
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames_Change_Contact_Detail]: не удалось выполнить запрос к серверу');
  #-------------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  if(!Preg_Match('/Success:/',$Result))
    return ERROR | @Trigger_Error('[WebNames_Change_Contact_Detail]: неизвестный ответ');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Success:/',$Result))
      return Array('TicketID'=>'NO');
  #-------------------------------------------------------------------------------
}

# added by lissyara, for JBS-353, 2012-03-19 in 20:21 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function WebNames_Get_Contact_Detail($Settings,$Domain){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $CacheID = Md5(SPrintF('Get_Contact_Detail_%s',$Domain));
  #-------------------------------------------------------------------------------
  $Result = CacheManager::get($CacheID);
  if($Result)
    return $Result;
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  // phone, e_mail, cell_phone
  #-------------------------------------------------------------------------------
  $Http = Array(
                #---------------------------------------------------------------------------
	        'Address'  => $Settings['Address'],
                'Port'     => $Settings['Port'],
                'Host'     => $Settings['Address'],
                'Protocol' => $Settings['Protocol'],
                'Charset'  => 'CP1251'
               );
  #-------------------------------------------------------------------------------
  $Query = Array(
                 'thisPage'           => 'pispGetContactDetails',
                 'username'           => $Settings['Login'],
                 'password'           => $Settings['Password'],
                 'interface_revision' => 1,
                 'interface_lang'     => 'en',
                 'domain_name'        => $Domain,
                );
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames_Get_Contact_Detail]: не удалось выполнить запрос к серверу');
  #-------------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  if(!Preg_Match('/Success:/',$Result))
    return ERROR | @Trigger_Error('[WebNames_Get_Contact_Detail]: неизвестный ответ');
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  $ContactInfo = Array();
  $FullInfo    = Array();
  #-------------------------------------------------------------------------------
  $iContactData = Explode("\n", $Result);
  #-------------------------------------------------------------------------------
  foreach($iContactData as $Line){
   $ContactData = Explode(": ",$Line);
   #-------------------------------------------------------------------------------
   if(Trim($ContactData[0]) == 'e_mail')
     $ContactInfo['Email'] = SubStr($ContactData[1],0,-1);
   #-------------------------------------------------------------------------------
   if(Trim($ContactData[0]) == 'phone')
     $ContactInfo['Phone'] = SubStr($ContactData[1],0,-1);
   #-------------------------------------------------------------------------------
   if(Trim($ContactData[0]) == 'cell_phone')
     $ContactInfo['CellPhone'] = SubStr($ContactData[1],0,-1);
   #-------------------------------------------------------------------------------
   if(Trim($ContactData[0]) == 'p_addr')
     $ContactInfo['PostalAddress'] = SubStr($ContactData[1],0,-1);
   #-------------------------------------------------------------------------------
   # буржуйские домены
   if(Trim($ContactData[0]) == 'o_phone')
     $ContactInfo['Phone'] = SubStr($ContactData[1],0,-1);
   #-------------------------------------------------------------------------------
   if(Trim($ContactData[0]) == 'o_email')
     $ContactInfo['Email'] = SubStr($ContactData[1],0,-1);
   #-------------------------------------------------------------------------------
   # полная информация
   if(IsSet($ContactData[1]))
     $FullInfo[Trim($ContactData[0])] = SubStr($ContactData[1],0,-1);
  }
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  $Out = Array('ContactInfo'=>$ContactInfo,'FullInfo'=>$FullInfo);
  #-------------------------------------------------------------------------------
  CacheManager::add($CacheID,$Out,300);
  #-------------------------------------------------------------------------------
  return $Out;
}


# added by lissyara, for JBS-394, 2012-09-26 in 14:28 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function WebNames_Get_List_Domains($Settings){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  # ввиду того, что вебнеймс интерфейса нормального не предоставляет, а за частые
  # запросы банит, кэшируем полученный результат и юазем кэш
  $CacheID = Md5($Settings['Login'] . $Settings['Password'] . 'pispAllDomainsInfo');
  $Result = CacheManager::get($CacheID);
  # если результата нет - лезем в вебнеймс
  if(!$Result || SizeOf($Result) < 2){
    $Http = Array(
      #---------------------------------------------------------------------------
      'Address'  => $Settings['Address'],
      'Port'     => $Settings['Port'],
      'Host'     => $Settings['Address'],
      'Protocol' => $Settings['Protocol'],
      'Charset'  => 'UTF-8'
      );
    #-----------------------------------------------------------------------------
    $Query = Array(
      #---------------------------------------------------------------------------
      'thisPage'           => 'pispAllDomainsInfo',	# see JBS-252
      'username'           => $Settings['Login'],
      'password'           => $Settings['Password'],
      'interface_revision' => 1,
      'interface_lang'     => 'en'
      );
    #-----------------------------------------------------------------------------
    $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
    if(Is_Error($Result))
      return ERROR | @Trigger_Error('[WebNames_Get_List_Domains]: не удалось выполнить запрос к серверу');
    #-----------------------------------------------------------------------------
    $Result = Trim($Result['Body']);
    #-----------------------------------------------------------------------------
    if(Preg_Match('/Error:/',$Result))
      return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
    #-----------------------------------------------------------------------------
    if(!Preg_Match('/Success:/',$Result))
      return ERROR | @Trigger_Error('[WebNames_Get_List_Domains]: неизвестный ответ');
    #-----------------------------------------------------------------------------
    # кэшируем полученный результат
    CacheManager::add($CacheID, $Result, 3600);
  }
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # разбираем строчки на массив
  $Domains = Explode("\n", $Result);
  Debug('[WebNames_Get_List_Domains]: ' . print_r($Domains,true));
  #-----------------------------------------------------------------------------
  # перебираем массив, составляем массив на выхлоп функции
  $Out = Array();
  foreach($Domains as $Domain){
    # Domain f-box59.ru; Status N; CreationDate 2010-02-23; ExpirationDate 2012-02-23; FutureExpDate ;
    $DomainInfo = Explode(" ",$Domain);
    # добавляем домен в выхлоп, если он есть вообще
    if(StrLen(Trim($DomainInfo[1])) > 3){
      $Out[] = Str_Replace(';','',StrToLower(Trim($DomainInfo[1])));
    }
  }
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  if(SizeOf($Out) > 0){
  	return Array('Status'=>'true','Domains'=>$Out);
  }else{
	return Array('Status'=>'false','ErrorText'=>'No domains on account');
  }
  #-----------------------------------------------------------------------------
}

# added by lissyara, for JBS-122, 2013-02-06 in 17:22 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function WebNames_Domain_Transfer($Settings,$DomainName,$DomainZone,$Params){
	#-------------------------------------------------------------------------------
	if(In_Array($DomainZone,Array('ru','su','рф'))){
		# ну до того там мутно всё...
		# пеернос этих доменов по параметрам аналогичен регистрации.
		# только 'thisPage' другой
		return new gException('REGISTRATOR_ERROR',SPrintF("В текущей версии библиотеки перенос доменов в зоне '%s' не реализован.",$DomainZone));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Http = Array(
			#-------------------------------------------------------------------------------
			'Address'  => $Settings['Address'],
			'Port'     => $Settings['Port'],
			'Host'     => $Settings['Address'],
			'Protocol' => $Settings['Protocol'],
			'Charset'  => 'CP1251'
			#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Query = Array(
			'thisPage'           => 'pispInitiateTransfer',
			'username'           => $Settings['Login'],
			'password'           => $Settings['Password'],
			'interface_revision' => 1,
			'interface_lang'     => 'en',
			'domain_name'        => SPrintF('%s.%s',$DomainName,$DomainZone),
			'notpaid'            => 0,
			'period'             => 1,
			'authinfo'           => $Params['AuthInfo']
		);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Query = Build_Query($Query,$Params);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
	if(Is_Error($Result))
		return ERROR | @Trigger_Error('[WebNames_Domain_Transfer]: не удалось выполнить запрос к серверу');
	#-------------------------------------------------------------------------------
	$Result = Trim($Result['Body']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Preg_Match('/Error:/',$Result))
		return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
	#-----------------------------------------------------------------------------
	if(!Preg_Match('/Success:/',$Result))
		return ERROR | @Trigger_Error('[WebNames_Domain_Transfer]: неизвестный ответ');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return Array('DomainID'=>0);
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# общие внутрение функции
function Build_Query($Query = Array(),$Params){
	#-------------------------------------------------------------------------------
	if(In_Array($Params['DomainZone'],Array('ru','su','рф'))){
		#-------------------------------------------------------------------------------
		switch($Params['PersonID']){
		case 'Natural':
			#-------------------------------------------------------------------------------
			$Query['person']	= SPrintF('%s %s %s',Translit($Params['Person']['Name']),Mb_SubStr(Translit($Params['Person']['Lastname']),0,1),Translit($Params['Person']['Sourname']));
			$Query['private_person']= ($Params['IsPrivateWhoIs']?'1':'0');
			$Query['person_r']	= SPrintF('%s %s %s',$Params['Person']['Sourname'],$Params['Person']['Name'],$Params['Person']['Lastname']);
			$Query['passport']	= SPrintF('%s %s выдан %s %s',$Params['Person']['PasportLine'],$Params['Person']['PasportNum'],$Params['Person']['PasportWhom'],$Params['Person']['PasportDate']);
			$Query['residence']	= SPrintF('%s, %s, %s, %s %s',$Params['Person']['pIndex'],$Params['Person']['pState'],$Params['Person']['pCity'],$Params['Person']['pType'],$Params['Person']['pAddress']);
			$Query['birth_date']	= $Params['Person']['BornDate'];
			$Query['country']	= IsSet($Params['Person']['PasportCountry'])?$Params['Person']['PasportCountry']:$Params['Person']['pCountry'];
			$Query['p_addr']	= SPrintF('%s, %s, %s, %s %s, %s',$Params['Person']['pIndex'],$Params['Person']['pState'],$Params['Person']['pCity'],$Params['Person']['pType'],$Params['Person']['pAddress'],$Params['Person']['pRecipient']);
			$Query['phone']		= $Params['Person']['Phone'];
			$Query['cell_phone']	= Preg_Replace('/\s+/', '', $Params['Person']['CellPhone']);
			$Query['fax']		= $Params['Person']['Fax'];
			$Query['e_mail']	= $Params['Person']['Email'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'Juridical':
			#-------------------------------------------------------------------------------
			$Query['org']		= SPrintF('%s %s',Translit($Params['Person']['CompanyName']),Translit($Params['Person']['CompanyForm']));
			$Query['org_r']		= SPrintF('%s "%s"',$Params['Person']['CompanyForm'],$Params['Person']['CompanyName']);
			$Query['code']		= $Params['Person']['Inn'];
			$Query['kpp']		= $Params['Person']['Kpp'];
			$Query['country']	= $Params['Person']['jCountry'];
			$Query['address_r']	= SPrintF('%s, %s, %s, %s %s',$Params['Person']['jIndex'],$Params['Person']['pState'],$Params['Person']['jCity'],$Params['Person']['jType'],$Params['Person']['jAddress']);
			$Query['p_addr']	= SPrintF('%s, %s, %s, %s, %s %s, %s "%s"',$Params['Person']['pIndex'],$Params['Person']['pState'],$Params['Person']['pCountry'],$Params['Person']['pCity'],$Params['Person']['pType'],$Params['Person']['pAddress'],$Params['Person']['CompanyForm'],$Params['Person']['CompanyName']);
			$Query['phone']		= $Params['Person']['Phone'];
			$Query['cell_phone']	= Preg_Replace('/\s+/', '', $Params['Person']['CellPhone']);
			$Query['fax']		= $Params['Person']['Fax'];
			$Query['e_mail']	= $Params['Person']['Email'];
			#-------------------------------------------------------------------------------
			if(In_Array($Params['DomainZone'],Array('ru','su','рф')))
				$Query['ogrn_org']	= $Params['Person']['Ogrn'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return new gException('WRONG_PROFILE_ID','Неверный идентификатор профиля');
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		switch($Params['PersonID']){
		case 'Natural':
			#-------------------------------------------------------------------------------
			$Query['o_company']	= 'Private person';
			$Query['a_company']	= 'Private person';
			$Query['t_company']	= 'Private person';
			$Query['b_company']	= 'Private person';
			#-------------------------------------------------------------------------------
			$Query['o_country_code']= $Params['Person']['pCountry'];
			$Query['a_country_code']= $Params['Person']['pCountry'];
			$Query['t_country_code']= $Params['Person']['pCountry'];
			$Query['b_country_code']= $Params['Person']['pCountry'];
			#-------------------------------------------------------------------------------
			$Query['o_postcode']	= $Params['Person']['pIndex'];
			$Query['a_postcode']	= $Params['Person']['pIndex'];
			$Query['t_postcode']	= $Params['Person']['pIndex'];
			$Query['b_postcode']	= $Params['Person']['pIndex'];
			#-------------------------------------------------------------------------------
			$Query['o_first_name']	= Translit($Params['Person']['Name']);
			$Query['a_first_name']	= Translit($Params['Person']['Name']);
			$Query['t_first_name']	= Translit($Params['Person']['Name']);
			$Query['b_first_name']	= Translit($Params['Person']['Name']);
			#-------------------------------------------------------------------------------
			$Query['o_last_name']	= Translit($Params['Person']['Sourname']);
			$Query['a_last_name']	= Translit($Params['Person']['Sourname']);
			$Query['t_last_name']	= Translit($Params['Person']['Sourname']);
			$Query['b_last_name']	= Translit($Params['Person']['Sourname']);
			#-------------------------------------------------------------------------------
			$Query['o_email']	= $Params['Person']['Email'];
			$Query['a_email']	= $Params['Person']['Email'];
			$Query['t_email']	= $Params['Person']['Email'];
			$Query['b_email']	= $Params['Person']['Email'];
			#-------------------------------------------------------------------------------
			$Query['o_addr']	= Translit(SPrintF('%s %s',$Params['Person']['pType'],$Params['Person']['pAddress']));
			$Query['a_addr']	= Translit(SPrintF('%s %s',$Params['Person']['pType'],$Params['Person']['pAddress']));
			$Query['t_addr']	= Translit(SPrintF('%s %s',$Params['Person']['pType'],$Params['Person']['pAddress']));
			$Query['b_addr']	= Translit(SPrintF('%s %s',$Params['Person']['pType'],$Params['Person']['pAddress']));
			#-------------------------------------------------------------------------------
			$Query['o_city']	= Translit($Params['Person']['pCity']);
			$Query['a_city']	= Translit($Params['Person']['pCity']);
			$Query['t_city']	= Translit($Params['Person']['pCity']);
			$Query['b_city']	= Translit($Params['Person']['pCity']);
			#-------------------------------------------------------------------------------
			$Query['o_state']	= Translit($Params['Person']['pState']);
			$Query['a_state']	= Translit($Params['Person']['pState']);
			$Query['t_state']	= Translit($Params['Person']['pState']);
			$Query['b_state']	= Translit($Params['Person']['pState']);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'Juridical':
			#-------------------------------------------------------------------------------
			$Query['o_company']	= SPrintF('%s %s',Translit($Params['Person']['CompanyName']),Translit($Params['Person']['CompanyForm']));
			$Query['a_company']	= SPrintF('%s %s',Translit($Params['Person']['CompanyName']),Translit($Params['Person']['CompanyForm']));
			$Query['t_company']	= SPrintF('%s %s',Translit($Params['Person']['CompanyName']),Translit($Params['Person']['CompanyForm']));
			$Query['b_company']	= SPrintF('%s %s',Translit($Params['Person']['CompanyName']),Translit($Params['Person']['CompanyForm']));
			#-------------------------------------------------------------------------------
			$Query['o_country_code']= $Params['Person']['jCountry'];
			$Query['a_country_code']= $Params['Person']['jCountry'];
			$Query['t_country_code']= $Params['Person']['jCountry'];
			$Query['b_country_code']= $Params['Person']['jCountry'];
			#-------------------------------------------------------------------------------
			$Query['o_postcode']	= $Params['Person']['jIndex'];
			$Query['a_postcode']	= $Params['Person']['jIndex'];
			$Query['t_postcode']	= $Params['Person']['jIndex'];
			$Query['b_postcode']	= $Params['Person']['jIndex'];
			#-------------------------------------------------------------------------------
			$Query['o_first_name']	= Translit($Params['Person']['dName']);
			$Query['a_first_name']	= Translit($Params['Person']['dName']);
			$Query['t_first_name']	= Translit($Params['Person']['dName']);
			$Query['b_first_name']	= Translit($Params['Person']['dName']);
			#-------------------------------------------------------------------------------
			$Query['o_last_name']	= Translit($Params['Person']['dSourname']);
			$Query['a_last_name']	= Translit($Params['Person']['dSourname']);
			$Query['t_last_name']	= Translit($Params['Person']['dSourname']);
			$Query['b_last_name']	= Translit($Params['Person']['dSourname']);
			#-------------------------------------------------------------------------------
			$Query['o_email']	= $Params['Person']['Email'];
			$Query['a_email']	= $Params['Person']['Email'];
			$Query['t_email']	= $Params['Person']['Email'];
			$Query['b_email']	= $Params['Person']['Email'];
			#-------------------------------------------------------------------------------
			$Query['o_addr']	= Translit(SPrintF('%s %s',$Params['Person']['jType'],$Params['Person']['jAddress']));
			$Query['a_addr']	= Translit(SPrintF('%s %s',$Params['Person']['jType'],$Params['Person']['jAddress']));
			$Query['t_addr']	= Translit(SPrintF('%s %s',$Params['Person']['jType'],$Params['Person']['jAddress']));
			$Query['b_addr']	= Translit(SPrintF('%s %s',$Params['Person']['jType'],$Params['Person']['jAddress']));
			#-------------------------------------------------------------------------------
			$Query['o_city']	= Translit($Params['Person']['jCity']);
			$Query['a_city']	= Translit($Params['Person']['jCity']);
			$Query['t_city']	= Translit($Params['Person']['jCity']);
			$Query['b_city']	= Translit($Params['Person']['jCity']);
			#-------------------------------------------------------------------------------
			$Query['o_state']	= Translit($Params['Person']['jState']);
			$Query['a_state']	= Translit($Params['Person']['jState']);
			$Query['t_state']	= Translit($Params['Person']['jState']);
			$Query['b_state']	= Translit($Params['Person']['jState']);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return new gException('WRONG_PERSON_TYPE_ID','Неверный идентификатор типа персоны');
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if($Params['DomainZone'] == 'kz'){
			#-------------------------------------------------------------------------------
			$Query['street']	= 'Chizhevskogo, 17';
			$Query['city']		= 'Karaganda';
			$Query['sp']		= 'KAR';
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Phone = $Params['Person']['Phone'];
		#-------------------------------------------------------------------------------
		if($Phone){
			#-------------------------------------------------------------------------------
			$Phone = Preg_Split('/\s+/',$Phone);
			#-------------------------------------------------------------------------------
			$Phone = SPrintF('%s.%s%s',Current($Phone),Next($Phone),Next($Phone));
			#-------------------------------------------------------------------------------
			$Query['o_phone']	= $Phone;
			$Query['a_phone']	= $Phone;
			$Query['t_phone']	= $Phone;
			$Query['b_phone']	= $Phone;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Query['o_phone']	= '';
			$Query['a_phone']	= '';
			$Query['t_phone']	= '';
			$Query['b_phone']	= '';
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Fax = $Params['Person']['Fax'];
		#-------------------------------------------------------------------------------
		if($Fax){
			#-------------------------------------------------------------------------------
			$Fax = Preg_Split('/\s+/',$Fax);
			#-------------------------------------------------------------------------------
			$Fax = SPrintF('%s.%s%s',Current($Fax),Next($Fax),Next($Fax));
			#-------------------------------------------------------------------------------
			$Query['o_fax']		= $Fax;
			$Query['a_fax']		= $Fax;
			$Query['t_fax']		= $Fax;
			$Query['b_fax']		= $Fax;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Query['o_fax']		= '';
			$Query['a_fax']		= '';
			$Query['t_fax']		= '';
			$Query['b_fax']		= '';
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Query['ns0'] = $Params['Ns1Name'];
	$Query['ns1'] = $Params['Ns2Name'];
	#-------------------------------------------------------------------------------
	if($Params['Ns3Name'])
		$Query['ns3'] = $Params['Ns3Name'];
	#-------------------------------------------------------------------------------
	if($Params['Ns4Name'])
		$Query['ns4'] = $Params['Ns4Name'];
	#-------------------------------------------------------------------------------
	if($Params['Ns1IP'] && $Params['Ns2IP']){
		#-------------------------------------------------------------------------------
		$Query['ns0ip'] = $Params['Ns1IP'];
		$Query['ns1ip'] = $Params['Ns2IP'];
	}
	#-------------------------------------------------------------------------------
	if($Params['Ns3IP'])
		$Query['ns3ip'] = $Params['Ns3IP'];
	#-------------------------------------------------------------------------------
	if($Params['Ns4IP'])
		$Query['ns4ip'] = $Params['Ns4IP'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Query;
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
