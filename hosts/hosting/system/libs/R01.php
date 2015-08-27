<?php
#-------------------------------------------------------------------------------
function R01_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$IsPrivateWhoIs,$CustomerID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $AddDomain = new SoapClient(null, Array(
    'location' => SPrintF('https://%s:%d/%s', $Settings['Address'], $Settings['Port'], $Settings['Params']['PrefixAPI']),
    'uri' => 'urn:RegbaseSoapInterface',
    'exceptions' => 1,
    'user_agent' => 'RegbaseSoapInterfaceClient',
    'trace' => 1
  ));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($AddDomain)){
    #---------------------------------------------------------------------------
    Debug($AddDomain->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  $loginResult = $AddDomain->logIn($Settings['Login'], $Settings['Password']);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($loginResult)){
    #---------------------------------------------------------------------------
    Debug($loginResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка авторизации.');
  }
  #-----------------------------------------------------------------------------
  $AddDomain -> __setCookie('SOAPClient', $loginResult->status->message);
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName, $DomainZone);
  #-----------------------------------------------------------------------------
  $NsServers = Array($Ns1Name,$Ns2Name);
  #-----------------------------------------------------------------------------
  if($Ns3Name)
    $NsServers[] = $Ns3Name;
  #-----------------------------------------------------------------------------
  if($Ns4Name)
    $NsServers[] = $Ns4Name;
  #-----------------------------------------------------------------------------
  $Params = Array(
    #---------------------------------------------------------------------------
    'domainname'              => $Domain,
    'nservers'                => Join("\n", $NsServers),
    'nichdl'                  => $CustomerID,
    'description'             => '',
    'check_whois'             => '',
    'hide_name_nichdl'        => $IsPrivateWhoIs,
    'hide_email'              => $IsPrivateWhoIs,
    'spam_process'            => 1, // mark  letter
    'hide_phone'              => $IsPrivateWhoIs,
    'hide_phone_email'        => $IsPrivateWhoIs,
    'years'                   => $Years,
    'registrar'               => 'R01',
    'dont_test_ns'            => 0
  );
  #-----------------------------------------------------------------------------
  Debug(Print_R($Params,TRUE));
  #-----------------------------------------------------------------------------
  $Response = $AddDomain->addDomain(
    $Params['domainname'],
    $Params['nservers'],
    $Params['nichdl'],
    $Params['description'],
    $Params['check_whois'],
    $Params['hide_name_nichdl'],
    $Params['hide_email'],
    $Params['spam_process'],
    $Params['hide_phone'],
    $Params['hide_phone_email'],
    $Params['years'],
    $Params['registrar'],
    $Params['dont_test_ns']
  );
  #-----------------------------------------------------------------------------
  Debug(Print_R($Response,TRUE));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    //Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору.');
  }
  #-----------------------------------------------------------------------------
  //Debug(SPringF('Status: %s', $Response->status->name));
  #-----------------------------------------------------------------------------
  if ($Response->status->code != 1) {
    Debug($Response->status->name);
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору.');
  }
  #-----------------------------------------------------------------------------
  $TicketID = $Response->taskid;
  #-----------------------------------------------------------------------------
  Debug($TicketID);
  #-----------------------------------------------------------------------------
  $logoutResult = $AddDomain->logOut();
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($logoutResult)){
    #---------------------------------------------------------------------------
    Debug($logoutResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка закрытия сессии.');
  }
  #-----------------------------------------------------------------------------
  return Array('TicketID'=>$TicketID);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function R01_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$CustomerID,$DomainID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  /****************************************************************************/
  $DomainProlong = new SoapClient(null, Array(
    'location'   => SPrintF('https://%s:%d/%s', $Settings['Address'], $Settings['Port'], $Settings['Params']['PrefixAPI']),
    'uri'        => 'urn:RegbaseSoapInterface',
    'exceptions' => 1,
    'user_agent' => 'RegbaseSoapInterfaceClient',
    'trace'      => 1
  ));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($DomainProlong)){
    #---------------------------------------------------------------------------
    #Debug($DomainProlong->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  $loginResult = $DomainProlong->logIn($Settings['Login'], $Settings['Password']);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($loginResult)){
    #---------------------------------------------------------------------------
    #Debug($loginResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка авторизации.');
  }
  #-----------------------------------------------------------------------------
  $DomainProlong->__setCookie('SOAPClient', $loginResult->status->message);
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName, $DomainZone);
  #---------------------------------------------------------------------------
  $Params = Array(
    #---------------------------------------------------------------------------
    'domainname'              => $Domain,
    'years'                   => $Years
  );
  #-----------------------------------------------------------------------------
  #Debug(Print_R($Params,TRUE));
  #-----------------------------------------------------------------------------
  $Response = $DomainProlong->prolongDomain(
    $Params['domainname'],
    $Params['years']
  );
  #-----------------------------------------------------------------------------
  #Debug(Print_R($Response,TRUE));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    //Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору.');
  }
  #-----------------------------------------------------------------------------
  //Debug(SPringF('Status: %s', $Response->status->name));
  #-----------------------------------------------------------------------------
  if ($Response->status->code != 1) {
    Debug($Response->status->name);
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору.');
  }
  #-----------------------------------------------------------------------------
  $TicketID = $Response->taskid;
  #-----------------------------------------------------------------------------
  #Debug($TicketID);
  #-----------------------------------------------------------------------------
  $logoutResult = $DomainProlong->logOut();
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($logoutResult)){
    #---------------------------------------------------------------------------
    Debug($logoutResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка закрытия сессии.');
  }
  #-----------------------------------------------------------------------------
  return Array('TicketID'=>$TicketID);
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function R01_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return new gException('UNSUPPORTED_EXCEPTION','');
}
#-------------------------------------------------------------------------------
function R01_Contract_Register($Settings,$PepsonID,$Person,$DomainZone){
  /****************************************************************************/
  $__args_types = Array('array','string','array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $AddCustomer = new SoapClient(null, Array(
    'location' => SPrintF('https://%s:%d/%s', $Settings['Address'], $Settings['Port'], $Settings['Params']['PrefixAPI']),
    'uri' => 'urn:RegbaseSoapInterface',
    'exceptions' => 1,
    'user_agent' => 'RegbaseSoapInterfaceClient',
    'trace' => 1
  ));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($AddCustomer)){
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка обращения к регистратору: %s', $AddCustomer->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  $loginResult = $AddCustomer->logIn($Settings['Login'], $Settings['Password']);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($loginResult)){
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка при авторизации: %s', $loginResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  if ($loginResult->status->code == '0') {
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка авторизации [name=%s, message=%s]',
        $loginResult->status->name,
        $loginResult->status->message
    );
    #---------------------------------------------------------------------------
    return new gException('AUTHORIZATION_FAILED', $Msg);
  }
  #-----------------------------------------------------------------------------
  $AddCustomer -> __setCookie('SOAPClient', $loginResult->status->message);
  #-----------------------------------------------------------------------------
  switch($PepsonID){
    case 'Natural':
      #-------------------------------------------------------------------------
      $Params = Array();
      #-------------------------------------------------------------------------
      $Params['NICHDL'] = SprintF('%s%s_%s-GPT', Mb_SubStr(Translit($Person['Name']),0,1), Translit($Person['Sourname']), Mb_SubStr(UniqID(), 0, 5));
      #-------------------------------------------------------------------------
      Debug(Print_R($Params,TRUE));
      #-------------------------------------------------------------------------
      $Response = $AddCustomer->addDadminPerson(
        #-----------------------------------------------------------------------
        $Params['NICHDL'],
        SPrintF('%s %s %s',$Person['Sourname'], $Person['Name'], $Person['Lastname']),
        SPrintF('%s %s %s',Translit($Person['Name']),Mb_SubStr(Translit($Person['Lastname']),0,1),Translit($Person['Sourname'])),
        SPrintF('%s %s, %s, %s', $Person['PasportLine'], $Person['PasportNum'], $Person['PasportWhom'], $Person['PasportDate']),
        $Person['BornDate'],
        SPrintF('%d, %s, %s, %s, %s %s', $Person['pIndex'], $Person['pCountry'], $Person['pState'], $Person['pCity'], $Person['pType'], $Person['pAddress']),
        $Person['Phone'],
        $Person['Fax'],
        $Person['Email'],
        1,
        1
      );
    break;
    case 'Juridical':
      #-------------------------------------------------------------------------
      $Params['NICHDL'] = SprintF('%s_%s-ORG-GPT', Translit($Person['CompanyName']), Mb_SubStr(UniqID(), 0, 5));
      #-------------------------------------------------------------------------
      $Response = $AddCustomer->addDadminOrg(
        #-----------------------------------------------------------------------
        $Params['NICHDL'],
        SPrintF('%s %s',$Person['CompanyForm'], $Person['CompanyName']),
        SPrintF('%s %s',Translit($Person['CompanyForm']),Translit($Person['CompanyName'])),
        $Person['Inn'],
        $Person['Ogrn'],
        SPrintF('%d, %s, %s, %s, %s %s', $Person['jIndex'], $Person['jCountry'], $Person['jState'], $Person['jCity'], $Person['jType'], $Person['jAddress']),
        SPrintF('%d, %s, %s, %s, %s %s', $Person['jIndex'], $Person['jCountry'], $Person['jState'], $Person['jCity'], $Person['pType'], $Person['pAddress']),
        $Person['Phone'],
        $Person['Fax'],
        $Person['Email'],
        SPrintF('%s %s %s', $Person['dSourname'], $Person['dName'], $Person['dLastname']),
        $Person['BankName'],
        $Person['BankAccount'],
        $Person['Kor'],
        $Person['Bik'],
        1
      );
    break;
    default:
      return ERROR | @Trigger_Error(400);
  }
  #-----------------------------------------------------------------------------
  Debug(Print_R($Response,TRUE));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка при выполнении запроса: %s', $Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  if ($Response->status->code == '0') {
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка регистрации домена [name=%s, message=%s]',
        $Response->status->name,
        $Response->status->message
    );
    #---------------------------------------------------------------------------
    return new gException('ADD_DOMAIN_FAILED', $Msg);
  }
  #-----------------------------------------------------------------------------
  $CustomerID = $Response->nic_hdl;
  #-----------------------------------------------------------------------------
  Debug($CustomerID);
  #-----------------------------------------------------------------------------
  $logoutResult = $AddCustomer->logOut();
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($logoutResult)){
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка закрытия сессии: %s', $logoutResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  return Array('TicketID'=>$CustomerID);
}
#-------------------------------------------------------------------------------
function R01_Get_Contract($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('ContractID'=>$TicketID);
}
#-------------------------------------------------------------------------------
function R01_Check_Task($Settings, $TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $CheckTask = new SoapClient(null, Array(
    'location' => SPrintF('https://%s:%d/%s', $Settings['Address'], $Settings['Port'], $Settings['Params']['PrefixAPI']),
    'uri' => 'urn:RegbaseSoapInterface',
    'exceptions' => 1,
    'user_agent' => 'RegbaseSoapInterfaceClient',
    'trace' => 1
  ));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($CheckTask)) {
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка обращения к регистратору: %s', $CheckTask->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  $loginResult = $CheckTask->logIn($Settings['Login'], $Settings['Password']);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($loginResult)){
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка при авторизации: %s', $loginResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  if ($loginResult->status->code == '0') {
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка авторизации [name=%s, message=%s]',
        $loginResult->status->name,
        $loginResult->status->message
    );
    #---------------------------------------------------------------------------
    return new gException('AUTHORIZATION_FAILED', $Msg);
  }
  #-----------------------------------------------------------------------------
  $CheckTask->__setCookie('SOAPClient', $loginResult->status->message);
  #-----------------------------------------------------------------------------
  $Response = $CheckTask->checkTask($TicketID);
  #-----------------------------------------------------------------------------
  //Debug(Print_R($Response, TRUE));
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка при выполнении запроса: %s', $Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  if ($Response->status->code == '0') {
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка проверки статуса задачи [name=%s, message=%s]',
        $Response->status->name,
        $Response->status->message
    );
    #---------------------------------------------------------------------------
    return new gException('TASK_FAILED', $Msg);
  }
  #-----------------------------------------------------------------------------
  switch($Response->status->name) {
    case 'TASK_SUCCESS':
      return Array('DomainID' => 0);

    case 'TASK_FAILURE':
      #-------------------------------------------------------------------------
      $Msg = SPrintF('Ошибка при выполнения задачи [name=%s, message=%s]',
          $Response->status->name,
          $Response->status->message
      );
      #-------------------------------------------------------------------------
      return new gException('TASK_FAILURE', $Msg);

    case 'TASK_QUEUED':
        return FALSE;

    default:
      return new gException('WRONG_STATUS','Неизвестный статус задачи.');
  }
  #-----------------------------------------------------------------------------
  $logoutResult = $CheckTask->logOut();
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($logoutResult)){
    #---------------------------------------------------------------------------
    $Msg = SPrintF('Ошибка закрытия сессии: %s', $logoutResult->faultstring);
    #---------------------------------------------------------------------------
    return new gException('REQUEST_ERROR', $Msg);
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_CASE','');
}
#-------------------------------------------------------------------------------
?>
