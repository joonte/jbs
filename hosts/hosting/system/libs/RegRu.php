<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
function RegRu_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$ContractID = '',$IsPrivateWhoIs,$PepsonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],

  );
  #-----------------------------------------------------------------------------
  $Domain = Mb_StrToLower(SPrintF('%s.%s',$DomainName,$DomainZone),'UTF-8');
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'domain_name'           => $Domain,
    'enduser_ip'            => '127.0.0.1',	// see JBS-225
    'pay_type'              => 'prepay',
    'private_person_flag'   => $IsPrivateWhoIs,
  );
  #-----------------------------------------------------------------------------
  $Query['period'] = $Years;
  #-----------------------------------------------------------------------------
  if(In_Array($DomainZone,Array('ru','su','рф'))){
    #---------------------------------------------------------------------------
    switch($PepsonID){
      case 'Natural':
        #-----------------------------------------------------------------------
        $Query['person']              = SPrintF('%s %s %s',Translit($Person['Name']),Mb_SubStr(Translit($Person['Lastname']),0,1),Translit($Person['Sourname']));
        $Query['private_person_flag'] = 1;
        $Query['person_r']            = SPrintF('%s %s %s',$Person['Sourname'],$Person['Name'],$Person['Lastname']);
        $Query['passport']            = SPrintF('%s %s выдан %s %s',$Person['PasportLine'],$Person['PasportNum'],$Person['PasportWhom'],$Person['PasportDate']);
        $Query['birth_date']          = $Person['BornDate'];
        $Query['country']             = IsSet($Person['PasportCountry'])?$Person['PasportCountry']:$Person['pCountry'];
        $Query['p_addr']              = SPrintF('%s, %s, %s, %s %s, %s',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress'],$Person['pRecipient']);
        $Query['phone']               = $Person['Phone'];
	$Query['sms_security_number'] = $Person['CellPhone'];
        $Query['fax']                 = $Person['Fax'];
        $Query['e_mail']              = $Person['Email'];
      break;
      case 'Juridical':
        #-----------------------------------------------------------------------
        $Query['org']                 = SPrintF('%s %s',Translit($Person['CompanyName']),Translit($Person['CompanyForm']));
        $Query['org_r']               = SPrintF('%s "%s"',$Person['CompanyForm'],$Person['CompanyName']);
        $Query['code']                = $Person['Inn'];
        $Query['kpp']                 = $Person['Kpp'];
        $Query['country']             = $Person['jCountry'];
        $Query['address_r']           = SPrintF('%s, %s, %s, %s %s',$Person['jIndex'],$Person['jState'],$Person['jCity'],$Person['jType'],$Person['jAddress']);
        $Query['p_addr']              = SPrintF('%s, %s, %s, %s %s, %s "%s"',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress'],$Person['CompanyForm'],$Person['CompanyName']);
        $Query['phone']               = $Person['Phone'];
	$Query['sms_security_number'] = $Person['CellPhone'];
        $Query['fax']                 = $Person['Fax'];
        $Query['e_mail']              = $Person['Email'];
      break;
      default:
        return new gException('WRONG_PROFILE_ID','Неверный идентификатор профиля');
    }
    #---------------------------------------------------------------------------
  }elseif(In_Array($DomainZone,Array('info','biz','org','com','net','be','cc','tv'))){
    #---------------------------------------------------------------------------
    switch($PepsonID){
      case 'Natural':
        #-----------------------------------------------------------------------
        $Query['o_company'] = 'Private person';
        $Query['a_company'] = 'Private person';
        $Query['t_company'] = 'Private person';
        $Query['b_company'] = 'Private person';
        #-----------------------------------------------------------------------
        $Query['o_country_code'] = $Person['pCountry'];
        $Query['a_country_code'] = $Person['pCountry'];
        $Query['t_country_code'] = $Person['pCountry'];
        $Query['b_country_code'] = $Person['pCountry'];
        #-----------------------------------------------------------------------
        $Query['o_postcode'] = $Person['pIndex'];
        $Query['a_postcode'] = $Person['pIndex'];
        $Query['t_postcode'] = $Person['pIndex'];
        $Query['b_postcode'] = $Person['pIndex'];
        #-----------------------------------------------------------------------
        $Query['o_first_name'] = Translit($Person['Name']);
        $Query['a_first_name'] = Translit($Person['Name']);
        $Query['t_first_name'] = Translit($Person['Name']);
        $Query['b_first_name'] = Translit($Person['Name']);
        #-----------------------------------------------------------------------
        $Query['o_last_name'] = Translit($Person['Sourname']);
        $Query['a_last_name'] = Translit($Person['Sourname']);
        $Query['t_last_name'] = Translit($Person['Sourname']);
        $Query['b_last_name'] = Translit($Person['Sourname']);
        #-----------------------------------------------------------------------
        $Query['o_email'] = $Person['Email'];
        $Query['a_email'] = $Person['Email'];
        $Query['t_email'] = $Person['Email'];
        $Query['b_email'] = $Person['Email'];
        #-----------------------------------------------------------------------
        $Query['o_addr'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
        $Query['a_addr'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
        $Query['t_addr'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
        $Query['b_addr'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
        #-----------------------------------------------------------------------
        $Query['o_city'] = Translit($Person['pCity']);
        $Query['a_city'] = Translit($Person['pCity']);
        $Query['t_city'] = Translit($Person['pCity']);
        $Query['b_city'] = Translit($Person['pCity']);
        #-----------------------------------------------------------------------
        $Query['o_state'] = Translit($Person['pState']);
        $Query['a_state'] = Translit($Person['pState']);
        $Query['t_state'] = Translit($Person['pState']);
        $Query['b_state'] = Translit($Person['pState']);
      break;
      case 'Juridical':
        #-----------------------------------------------------------------------
        $CompanyEn = SPrintF('%s %s',Translit($Person['CompanyName']),Translit($Person['CompanyForm']));
        #-----------------------------------------------------------------------
        $Query['o_company'] = $CompanyEn;
        $Query['a_company'] = $CompanyEn;
        $Query['t_company'] = $CompanyEn;
        $Query['b_company'] = $CompanyEn;
        #-----------------------------------------------------------------------
        $Query['o_country_code'] = $Person['jCountry'];
        $Query['a_country_code'] = $Person['jCountry'];
        $Query['t_country_code'] = $Person['jCountry'];
        $Query['b_country_code'] = $Person['jCountry'];
        #-----------------------------------------------------------------------
        $Query['o_postcode'] = $Person['jIndex'];
        $Query['a_postcode'] = $Person['jIndex'];
        $Query['t_postcode'] = $Person['jIndex'];
        $Query['b_postcode'] = $Person['jIndex'];
        #-----------------------------------------------------------------------
        $Query['o_first_name'] = Translit($Person['dName']);
        $Query['a_first_name'] = Translit($Person['dName']);
        $Query['t_first_name'] = Translit($Person['dName']);
        $Query['b_first_name'] = Translit($Person['dName']);
        #-----------------------------------------------------------------------
        $Query['o_last_name'] = Translit($Person['dSourname']);
        $Query['a_last_name'] = Translit($Person['dSourname']);
        $Query['t_last_name'] = Translit($Person['dSourname']);
        $Query['b_last_name'] = Translit($Person['dSourname']);
        #-----------------------------------------------------------------------
        $Query['o_email'] = $Person['Email'];
        $Query['a_email'] = $Person['Email'];
        $Query['t_email'] = $Person['Email'];
        $Query['b_email'] = $Person['Email'];
        #-----------------------------------------------------------------------
        $Query['o_addr'] = Translit(SPrintF('%s %s',$Person['jType'],$Person['jAddress']));
        $Query['a_addr'] = Translit(SPrintF('%s %s',$Person['jType'],$Person['jAddress']));
        $Query['t_addr'] = Translit(SPrintF('%s %s',$Person['jType'],$Person['jAddress']));
        $Query['b_addr'] = Translit(SPrintF('%s %s',$Person['jType'],$Person['jAddress']));
        #-----------------------------------------------------------------------
        $Query['o_city'] = Translit($Person['jCity']);
        $Query['a_city'] = Translit($Person['jCity']);
        $Query['t_city'] = Translit($Person['jCity']);
        $Query['b_city'] = Translit($Person['jCity']);
        #-----------------------------------------------------------------------
        $Query['o_state'] = Translit($Person['jState']);
        $Query['a_state'] = Translit($Person['jState']);
        $Query['t_state'] = Translit($Person['jState']);
        $Query['b_state'] = Translit($Person['jState']);
      break;
      default:
        return new gException('WRONG_PERSON_TYPE_ID','Неверный идентификатор типа персоны');
    }
    #---------------------------------------------------------------------------
    $Phone = $Person['Phone'];
    #---------------------------------------------------------------------------
    if($Phone){
      #-------------------------------------------------------------------------
      $Phone = Preg_Split('/\s+/',$Phone);
      #-------------------------------------------------------------------------
      $Phone = SPrintF('%s.%s%s',Current($Phone),Next($Phone),Next($Phone));
      #-------------------------------------------------------------------------
      $Query['o_phone'] = $Phone;
      $Query['a_phone'] = $Phone;
      $Query['t_phone'] = $Phone;
      $Query['b_phone'] = $Phone;
    }else{
      #-------------------------------------------------------------------------
      $Query['o_phone'] = '';
      $Query['a_phone'] = '';
      $Query['t_phone'] = '';
      $Query['b_phone'] = '';
    }
    #---------------------------------------------------------------------------
    $Fax = $Person['Fax'];
    #---------------------------------------------------------------------------
    if($Fax){
      #-------------------------------------------------------------------------
      $Fax = Preg_Split('/\s+/',$Fax);
      #-------------------------------------------------------------------------
      $Fax = SPrintF('%s.%s%s',Current($Fax),Next($Fax),Next($Fax));
      #-------------------------------------------------------------------------
      $Query['o_fax'] = $Fax;
      $Query['a_fax'] = $Fax;
      $Query['t_fax'] = $Fax;
      $Query['b_fax'] = $Fax;
    }else{
      #-------------------------------------------------------------------------
      $Query['o_fax'] = '';
      $Query['a_fax'] = '';
      $Query['t_fax'] = '';
      $Query['b_fax'] = '';
    }
  }else
    return new gException('WRONG_ZONE_NAME','Указанная зона не поддерживается в автоматическом режиме');
  #-----------------------------------------------------------------------------
  $Query['ns0'] = $Ns1Name;
  $Query['ns1'] = $Ns2Name;
  #-----------------------------------------------------------------------------
  if($Ns1IP && $Ns2IP){
    #---------------------------------------------------------------------------
    $Query['ns0ip'] = $Ns1IP;
    $Query['ns1ip'] = $Ns2IP;
  }
  #-----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","domain/create");
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Domain_Register]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'success'){
    if(IsSet($Result['error_code'])){
      return new gException('REGISTRATOR_ERROR_1',$Result['error_code']);
    }else{
      foreach(Array_Keys($Result['answer']) as $Key)
        Debug(SPrintF("[RegRu_Answer::Domain_Register]: %s => %s",$Key,$Result['answer'][$Key]));
      #---------------------------------------------------------------------------
      if($Result['answer']['dname'] == $Domain){
        return Array('TicketID'=>(integer)$Result['answer']['service_id']);
      #---------------------------------------------------------------------------
      }
    }
  }
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'error')
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function RegRu_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$ContractID,$DomainID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],
  );
  #-----------------------------------------------------------------------------
  $Domain = Mb_StrToLower(SPrintF('%s.%s',$DomainName,$DomainZone),'UTF-8');
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'period'                => $Years,
  );
  # Только для обеспечения совместимости со старым алгоритмом.
  # Удалить через 2-3 мес после релиза!!!
  if(IsSet($DomainID)&&(integer)$DomainID>0)
    $Query['service_id'] = $DomainID;
  else
    $Query['domain_name'] = $Domain;
  #-----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","service/renew");
  #---------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Domain_Prolong]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #-----------------------------------------------------------------------------
  #Debug($Result);
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'success'){
    #foreach(Array_Keys($Result['answer']) as $Key)
    #  Debug("[RegRu_Answer::Domain_Prolong]: " . $Key . " - " . $Result['answer'][$Key]);
    #---------------------------------------------------------------------------
    if($Result['answer']['status'] == 'renew_success')
      return Array('TicketID'=>(integer)$Result['answer']['service_id']);
    #---------------------------------------------------------------------------
  }
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'error'){
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function RegRu_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],
  );
  #-----------------------------------------------------------------------------
  $Domain = Mb_StrToLower(SPrintF('%s.%s',$DomainName,$DomainZone),'UTF-8');
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'domain_name'           => $Domain,
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
  if($Ns1IP)
    $Query['ns0ip'] = $Ns1IP;
  #-----------------------------------------------------------------------------
  if($Ns2IP)
    $Query['ns1ip'] = $Ns2IP;
  #-----------------------------------------------------------------------------
  if($Ns3IP)
    $Query['ns2ip'] = $Ns3IP;
  #-----------------------------------------------------------------------------
  if($Ns4IP)
    $Query['ns3ip'] = $Ns4IP;
  #-----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","domain/update_nss");
  #---------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Domain_Ns_Change]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #-----------------------------------------------------------------------------
  #Debug($Result);
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'success'){
    if(IsSet($Result['error_code'])){
      return new gException('REGISTRATOR_ERROR_1',$Result['error_code']);
    }else{
      foreach($Result['answer']['domains'] as $Domains){
        #foreach(Array_Keys($Domains) as $Key)
        #  Debug("[RegRu_Answer::Domain_Ns_Change]: " . $Key . " - " . $Domains[$Key]);
        #-------------------------------------------------------------------------
        if($Domains['dname'] == $Domain && IsSet($Domains['error_code']))
          return new gException('REGISTRATOR_ERROR',IsSet($Domains['error_text'])?$Domains['error_text']:'Регистратор вернул ошибку');
        #-------------------------------------------------------------------------
        if($Domains['dname'] == $Domain && $Domains['result'] == 'success')
          return Array('TicketID'=>(integer)$Domains['service_id']);
        #-------------------------------------------------------------------------
      }
    }
  }
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'error'){
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function RegRu_Check_Task($Settings,$TicketID){
  /****************************************************************************/
  /* Reg.Ru не предоставляет API для проверки статуса задания, а только       */
  /* для проверки существования домена у партнера.                            */
  /* Поэтому данная функция ничего полезного не делает :(                     */
  /* Нужна только для обеспечения универсальности класса Registrators         */
  /****************************************************************************/
  $__args_types = Array('array','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'service_id'            => $TicketID,
  );
  #-----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","domain/nop");
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Check_Task]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #-----------------------------------------------------------------------------
  #Debug($Result);
  #-----------------------------------------------------------------------------
  #foreach(Array_Keys($Result) as $Key)
  #  Debug("[RegRu_Answer::Check_Task]: " . $Key . " - " . $Result[$Key]);
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'success')
    return Array('DomainID'=>$TicketID);
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'error'){
    return ERROR | @Trigger_Error('[RegRu_Check_Task]: ошибка запроса');
  }
  #-----------------------------------------------------------------------------
  return ERROR | @Trigger_Error('[RegRu_Check_Task]: неизвестный ответ');
}
#-------------------------------------------------------------------------------
function RegRu_GetUploadID($Settings,$Domain){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'dname'                 => $Domain,
  );
  #-----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","domain/get_docs_upload_uri");
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_GetUploadID]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #-----------------------------------------------------------------------------
  #Debug($Result);
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'success')
    return Array('UploadID'=>$Result['answer']['docs_upload_sid']);
  #-----------------------------------------------------------------------------
  if($Result['result'] == 'error')
    return ERROR | @Trigger_Error('[RegRu_GetUploadID]: ошибка запроса');
  #-----------------------------------------------------------------------------
  return ERROR | @Trigger_Error('[RegRu_GetUploadID]: неизвестный ответ');
}
#-------------------------------------------------------------------------------
function RegRu_Get_Balance($Settings){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
   $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'currency'              => 'RUR',
  );
  #----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","user/get_balance");
  #----------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_GetBalance]: не удалось выполнить запрос к серверу');
  #----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #----------------------------------------------------------------------------
  #Debug($Result);
  #----------------------------------------------------------------------------
  if($Result['result'] == 'success')
    return Array('Prepay'=>$Result['answer']['prepay']);
  #----------------------------------------------------------------------------
  if($Result['result'] == 'error')
    return ERROR | @Trigger_Error('[RegRu_GetBalance]: ошибка запроса');
  #----------------------------------------------------------------------------
  return ERROR | @Trigger_Error('[RegRu_GetBalance]: неизвестный ответ');
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function RegRu_Is_Available_Domain($Settings,$Domain){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #---------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #--------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],
  );
  #----------------------------------------------------------------------------
  $Query = Array(
    #--------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'dname'                 => $Domain,
  );
  #----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","service/get_info");
  #----------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Is_Available_Domain]: не удалось выполнить запрос к серверу');
  #----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #----------------------------------------------------------------------------
  #Debug($Result);
  #----------------------------------------------------------------------------
  if($Result['result'] == 'success') {
    foreach($Result['answer']['services'] as $CurService){
      foreach(Array_Keys($CurService) as $Key){
        if($CurService['dname'] == $Domain){
          if($CurService['result'] == 'success'){
            switch($CurService['state']){
              case 'N':
                return Array('Status'=>'false','ErrorText'=>'Услуга неактивна (домен не зарегистрирован / не перенесён).');
              case 'A':
                return Array('Status'=>'true','ServiceID'=>IsSet($CurService['service_id'])?(integer)$CurService['service_id']:'0');
              case 'O':
                return Array('Status'=>'false','ErrorText'=>'Домен перенесён к другому регистратору.');
              default:
                return Array('Status'=>'false','ErrorText'=>'Услуга приостановлена или удалена.');
            }
          }
          else{
            switch($CurService['error_code']){
              case 'DOMAIN_NOT_FOUND':
                return Array('Status'=>'false','ErrorText'=>$CurService['result']);
                break;
              default:
                return ERROR | @Trigger_Error('[RegRu_Is_Available_Domain]: ошибка запроса');
            }
          }
        }
      }
    }
  }
  #----------------------------------------------------------------------------
  return ERROR | @Trigger_Error('[RegRu_Is_Available_Domain]: неизвестный ответ');
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function RegRu_Domain_Transfer($Settings,$DomainName,$DomainZone,$Param){
  /****************************************************************************/
  $__args_types = Array('array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],

  );
  #-----------------------------------------------------------------------------
  $Domain = Mb_StrToLower(SPrintF('%s.%s',$DomainName,$DomainZone),'UTF-8');
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'domain_name'           => $Domain,
    'enduser_ip'            => '77.73.25.114',
    'period'                => '0',
  );
  #-----------------------------------------------------------------------------
  if(In_Array($DomainZone,Array('ru','su','рф'))){
    #---------------------------------------------------------------------------
    $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","domain/transfer");
    #---------------------------------------------------------------------------
    $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
    if(Is_Error($Result))
      return ERROR | @Trigger_Error('[RegRu_Domain_Register]: не удалось выполнить запрос к серверу');
    #---------------------------------------------------------------------------
    $Result = Trim($Result['Body']);
    #---------------------------------------------------------------------------
    $Result = Json_Decode($Result,TRUE);
    #---------------------------------------------------------------------------
    if($Result['result'] == 'success'){
      #foreach(Array_Keys($Result['answer']) as $Key)
      #  Debug("[RegRu_Answer::Domain_Transfer]: " . $Key . " - " . $Result['answer'][$Key]);
      #-------------------------------------------------------------------------
      if($Result['answer']['dname'] == $Domain){
        return Array('DomainID'=>(integer)$Result['answer']['service_id']);
      #-------------------------------------------------------------------------
      }
    }
    #---------------------------------------------------------------------------
    if($Result['result'] == 'error')
      return new gException('REGISTRATOR_ERROR',IsSet($Result['error_text'])?$Result['error_text']:'Регистратор вернул ошибку');
    #---------------------------------------------------------------------------
    return new gException('WRONG_ANSWER',$Result);
  }
  else {
    return new gException('REGISTRATOR_ERROR',SPrintF("Трансфер доменов в зоне %s не реализован в текущей версии библиотеки.",$DomainZone));
  }
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function RegRu_Domain_Accept($Settings){
	/****************************************************************************/
	$__args_types = Array('array','array');
	#-----------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/****************************************************************************/
	$HTTP = Array(
			#---------------------------------------------------------------------------
			'Address'  => $Settings['Address'],
			'Port'     => $Settings['Port'],
			'Host'     => $Settings['Address'],
			'Protocol' => $Settings['Protocol'],
			'Charset'  => 'utf8',
			'Hidden'   => $Settings['Password'],
			#---------------------------------------------------------------------------
			);
	#-----------------------------------------------------------------------------
	$Query = Array(
			#---------------------------------------------------------------------------
			'username'              => $Settings['Login'],
			'password'              => $Settings['Password'],
			#---------------------------------------------------------------------------
			);
	#-----------------------------------------------------------------------------
	#---------------------------------------------------------------------------
	$Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","domain/look_at_entering_list");
	#---------------------------------------------------------------------------
	$Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
	if(Is_Error($Result))
		return ERROR | @Trigger_Error('[RegRu_Domain_Accept]: не удалось выполнить запрос к серверу');
	#---------------------------------------------------------------------------
	$Result = Trim($Result['Body']);
	#---------------------------------------------------------------------------
	$Result = Json_Decode($Result,TRUE);
	#---------------------------------------------------------------------------
	if($Result['result'] == 'success'){
		#foreach(Array_Keys($Result['answer']) as $Key)
			#Debug("[RegRu_Answer::Domain_Transfer]: " . $Key . " - " . $Result['answer'][$Key]);
		#-------------------------------------------------------------------------
		$Domains = Array();
		#-------------------------------------------------------------------------
		foreach($Result['answer']['messages'] as $Domain)
			$Domains[$Domain['id']] = $Domain['domain_name'];
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# если чё-то передано - принимаем
		foreach(Array_Keys($Domains) as $DomainID){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[system/libs/RegRu.php]: принимаем на аккаунт домен %s',$Domains[$DomainID]));
			#-------------------------------------------------------------------------------
			$Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","domain/accept_or_refuse_entering_list");
			#-------------------------------------------------------------------------------
			$Query['dname']		= $Domains[$DomainID];
			$Query['id']		= $DomainID;
			$Query['action_type']	= 'accept';
			$Query['action']	= 'accept';
			#-------------------------------------------------------------------------------
			$Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
			if(Is_Error($Result))
				return ERROR | @Trigger_Error('[RegRu_Domain_Accept]: не удалось выполнить запрос к серверу');
			#---------------------------------------------------------------------------
			$Result = Trim($Result['Body']);
			#---------------------------------------------------------------------------
			$Result = Json_Decode($Result,TRUE);
			#---------------------------------------------------------------------------
			if($Result['result'] == 'success')
				if(IsSet($Result['answer']['domains']['result']))
					if($Result['answer']['domains']['result'] == 'accepted')
						Debug(SPrintF('[system/libs/RegRu.php]: домен %s перенесён на аккаунт',$Domains[$DomainID]));
			#---------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function RegRu_Change_Contact_Detail($Settings,$Domain,$DomainZone,$Person){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],

  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'domain_name'           => $Domain,
    'enduser_ip'            => '77.73.25.114',
    'period'                => '0',
  );
  #---------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF('https://api.reg.ru/api/regru2/%s','domain/update_contacts');
  #---------------------------------------------------------------------------
  #---------------------------------------------------------------------------
  if(IsSet($Person['Phone']))
    $Query['phone'] = $Person['Phone'];
  #-------------------------------------------------------------------------------
  if(IsSet($Person['CellPhone']))
    $Query['sms_security_number'] = Str_Replace(' ','',$Person['CellPhone']);
  #-------------------------------------------------------------------------------
  if(IsSet($Person['Email']))
    $Query['e_mail'] = $Person['Email'];
  #-------------------------------------------------------------------------------
  if(IsSet($Person['PostalAddress']))
    $Query['p_addr'] = $Person['PostalAddress'];
  #---------------------------------------------------------------------------
  #---------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Domain_Register]: не удалось выполнить запрос к серверу');
  #---------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #---------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #---------------------------------------------------------------------------
  if($Result['result'] == 'success'){
    return Array('TicketID'=>'NO');
  }
  #---------------------------------------------------------------------------
  if($Result['result'] == 'error')
    return new gException('REGISTRATOR_ERROR',IsSet($Result['error_text'])?$Result['error_text']:'Регистратор вернул ошибку');
  #---------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);


}

# added 
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function RegRu_Get_Contact_Detail($Settings,$Domain){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],

  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'domain_name'           => $Domain,
    'enduser_ip'            => '77.73.25.114',
    'period'                => '0',
  );
  #---------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF('https://api.reg.ru/api/regru2/%s','service/get_details');
  #---------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Get_Contact_Detail]: не удалось выполнить запрос к серверу');
  #---------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #---------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #Debug("[RegRu_Answer::Get_Contact_Detail]: " . print_r($Result,TRUE));
  #---------------------------------------------------------------------------
  #---------------------------------------------------------------------------
  if($Result['result'] == 'success' && $Result['answer']['services'][0]['dname'] == $Domain){
    #-------------------------------------------------------------------------
    $ContactInfo = Array();
    #-------------------------------------------------------------------------
    if(IsSet($Result['answer']['services'][0]['details']['e_mail']))
      $ContactInfo['Email'] = $Result['answer']['services'][0]['details']['e_mail'];
    #-------------------------------------------------------------------------
    if(IsSet($Result['answer']['services'][0]['details']['phone']))
      $ContactInfo['Phone'] = $Result['answer']['services'][0]['details']['phone'];
    #-------------------------------------------------------------------------
    if(IsSet($Result['answer']['services'][0]['details']['sms_security_number']))
      $ContactInfo['CellPhone'] = $Result['answer']['services'][0]['details']['sms_security_number'];
    #-------------------------------------------------------------------------
    if(IsSet($Result['answer']['services'][0]['details']['p_addr']))
      $ContactInfo['PostalAddress'] = $Result['answer']['services'][0]['details']['p_addr'];
    #-------------------------------------------------------------------------
    if(IsSet($Result['answer']['services'][0]['details'])){
      $FullInfo = $Result['answer']['services'][0]['details'];
    }else{
      $FullInfo = Array('FullInfo'=>'Домен отсутствует у регистратора');
    }
    return Array('ContactInfo'=>$ContactInfo,'FullInfo'=>$FullInfo);
  }
  #---------------------------------------------------------------------------
  #---------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result['error_text']);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function RegRu_Get_List_Domains($Settings){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #---------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #--------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'utf8',
    'Hidden'   => $Settings['Password'],
  );
  #----------------------------------------------------------------------------
  $Query = Array(
    #--------------------------------------------------------------------------
    'username'              => $Settings['Login'],
    'password'              => $Settings['Password'],
    'servtype'              => 'domain',
  );
  #----------------------------------------------------------------------------
  $Settings['PrefixAPI'] = SprintF("https://api.reg.ru/api/regru2/%s","service/get_list");
  #----------------------------------------------------------------------------
  $Result = HTTP_Send($Settings['PrefixAPI'],$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RegRu_Get_List_Domains]: не удалось выполнить запрос к серверу');
  #----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #----------------------------------------------------------------------------
  $Result = Json_Decode($Result,TRUE);
  #----------------------------------------------------------------------------
  if($Result['result'] == 'success') {
    # перебираем массив, составляем массив на выхлоп функции
    $Out = Array();
    foreach($Result['answer']['services'] as $CurService){
      #----------------------------------------------------------------------------
      #Debug("Array_Keys answer services " . print_r($CurService,true));
      if($CurService['state'] == 'A' || $CurService['state'] == 'S')	// активные или заблокированные
        $Out[] = $CurService['dname'];
    }
  }
  #----------------------------------------------------------------------------
  if(IsSet($Out) && SizeOf($Out) > 0){
    return Array('Status'=>'true','Domains'=>$Out);
  }else{
    return Array('Status'=>'false','ErrorText'=>'No domains on account');
  }
  #----------------------------------------------------------------------------
  return ERROR | @Trigger_Error('[RegRu_Get_List_Domains]: неизвестный ответ');
}
#-------------------------------------------------------------------------------


?>
