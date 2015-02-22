<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
function MasterName_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$IsPrivateWhoIs,$ContractID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Password = $Settings['Password'];
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'CP1251',
    'Hidden'   => $Password,
    'IsLogging'=> $Settings['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  if(In_Array($DomainZone,Array('ru','su'))){
    #---------------------------------------------------------------------------
    $Query = Array();
    #---------------------------------------------------------------------------
    $Query[] = '[request]';
    $Query[] = SPrintF('login: %s',$Settings['Login']);
    $Query[] = SPrintF('password: %s',$Password);
    $Query[] = 'action: register_domain';
    #---------------------------------------------------------------------------
    $RequestID = UniqID('ID');
    #---------------------------------------------------------------------------
    $Query[] = SPrintF('request-id: %s',$RequestID);
    #---------------------------------------------------------------------------
    $Query[] = '';
    $Query[] = '[domain]';
    $Query[] = SPrintF('client: %s',$ContractID);
    $Query[] = SPrintF('domain: %s',$Domain);
  }else
    return new gException('WRONG_ZONE_NAME','Указанная зона не поддерживается в автоматическом режиме');
  #-----------------------------------------------------------------------------
  if($Ns1IP && $Ns2IP){
    #---------------------------------------------------------------------------
    $Query[] = SPrintF('nserver: %s %s',$Ns1Name,$Ns1IP);
    $Query[] = SPrintF('nserver: %s %s',$Ns2Name,$Ns2IP);
  }else{
    #---------------------------------------------------------------------------
    $Query[] = SPrintF('nserver: %s',$Ns1Name);
    $Query[] = SPrintF('nserver: %s',$Ns2Name);
  }
  #-----------------------------------------------------------------------------
  if($Ns3Name){
    #---------------------------------------------------------------------------
    if($Ns3IP){
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s %s',$Ns3Name,$Ns3IP);
    }else{
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s',$Ns3Name);
    }
  }
  #-----------------------------------------------------------------------------
  if($Ns4Name){
    #---------------------------------------------------------------------------
    if($Ns4IP){
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s %s',$Ns4Name,$Ns4IP);
    }else{
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s',$Ns4Name);
    }
  }
  #-----------------------------------------------------------------------------
  $Query = Array('request'=>Implode("\n",$Query));
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/partner_gateway',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[MasterName_Domain_Register]:не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/status:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        return Array('TicketID'=>$RequestID);
      case '203':
        return new gException('REQUEST_FAILED','Обработка запроса завершилась с ошибкой');
      case '400':
        return new gException('BAD_REQUEST','Неверный формат запроса');
      case '401':
        return new gException('AUTH_ERROR','Ошибка авторизации');
      case '402':
        return new gException('DATA_ERROR','Ошибка в данных запроса');
      case '403':
        return new gException('FORBIDDEN','Доступ к запрашиваемому объекту запрещен');
      case '404':
        return new gException('NOT_FOUND','Запрашиваемый объект не найден');
      case '405':
        return new gException('INVALID_REQUEST','Невозможно выполнить запрос');
      case '500':
        return new gException('SERVER_ERROR','Внутренняя ошибка сервера');
      default:
        return new gException('WRONG_ERROR','Неизвестный статус ответа');
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function MasterName_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$ContractID,$DomainID){
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
    'Charset'  => 'CP1251',
    'IsLogging'=> $Settings['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $Query[] = '[request]';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = 'action: prolong_domain';
  #-----------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #-----------------------------------------------------------------------------
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #-----------------------------------------------------------------------------
  $Query[] = '';
  $Query[] = '[domain]';
  $Query[] = SPrintF('client: %s',$ContractID);
  $Query[] = SPrintF('domain: %s',$Domain);
  #-----------------------------------------------------------------------------
  $Query = Array('request'=>Implode("\n",$Query));
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/partner_gateway',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[MasterName_Domain_Prolong]:не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/status:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        return Array('TicketID'=>$RequestID);
      case '203':
        return new gException('REQUEST_FAILED','Обработка запроса завершилась с ошибкой');
      case '400':
        return new gException('BAD_REQUEST','Неверный формат запроса');
      case '401':
        return new gException('AUTH_ERROR','Ошибка авторизации');
      case '402':
        return new gException('DATA_ERROR','Ошибка в данных запроса');
      case '403':
        return new gException('FORBIDDEN','Доступ к запрашиваемому объекту запрещен');
      case '404':
        return new gException('NOT_FOUND','Запрашиваемый объект не найден');
      case '405':
        return new gException('INVALID_REQUEST','Невозможно выполнить запрос');
      case '500':
        return new gException('SERVER_ERROR','Внутренняя ошибка сервера');
      default:
        return new gException('WRONG_ERROR','Неизвестный статус ошибки');
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function MasterName_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
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
    'Charset'  => 'CP1251',
    'IsLogging'=> $Settings['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $Query[] = '[request]';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = 'action: update_domain';
  #-----------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #-----------------------------------------------------------------------------
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #-----------------------------------------------------------------------------
  $Query[] = '';
  $Query[] = '[domain]';
  $Query[] = SPrintF('client: %s',$ContractID);
  $Query[] = SPrintF('domain: %s',$Domain);
  #-----------------------------------------------------------------------------
  if($Ns1IP && $Ns2IP){
    #---------------------------------------------------------------------------
    $Query[] = SPrintF('nserver: %s %s',$Ns1Name,$Ns1IP);
    $Query[] = SPrintF('nserver: %s %s',$Ns2Name,$Ns2IP);
  }else{
    #---------------------------------------------------------------------------
    $Query[] = SPrintF('nserver: %s',$Ns1Name);
    $Query[] = SPrintF('nserver: %s',$Ns2Name);
  }
  #-----------------------------------------------------------------------------
  if($Ns3Name){
    #---------------------------------------------------------------------------
    if($Ns3IP){
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s %s',$Ns3Name,$Ns3IP);
    }else{
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s',$Ns3Name);
    }
  }
  #-----------------------------------------------------------------------------
  if($Ns4Name){
    #---------------------------------------------------------------------------
    if($Ns4IP){
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s %s',$Ns4Name,$Ns4IP);
    }else{
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('nserver: %s',$Ns4Name);
    }
  }
  #-----------------------------------------------------------------------------
  $Query = Array('request'=>Implode("\n",$Query));
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/partner_gateway',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[MasterName_Domain_Ns_Change]:не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/status:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        return Array('TicketID'=>$RequestID);
      case '203':
        return new gException('REQUEST_FAILED','Обработка запроса завершилась с ошибкой');
      case '400':
        return new gException('BAD_REQUEST','Неверный формат запроса');
      case '401':
        return new gException('AUTH_ERROR','Ошибка авторизации');
      case '402':
        return new gException('DATA_ERROR','Ошибка в данных запроса');
      case '403':
        return new gException('FORBIDDEN','Доступ к запрашиваемому объекту запрещен');
      case '404':
        return new gException('NOT_FOUND','Запрашиваемый объект не найден');
      case '405':
        return new gException('INVALID_REQUEST','Невозможно выполнить запрос');
      case '500':
        return new gException('SERVER_ERROR','Внутренняя ошибка сервера');
      default:
        return new gException('WRONG_ERROR','Неизвестный статус ошибки');
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function MasterName_Check_Task($Settings,$TicketID){
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
    'Charset'  => 'CP1251',
    'IsLogging'=> $Settings['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $Query[] = '[request]';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = 'action: request_status';
  #-----------------------------------------------------------------------------
  $Query[] = SPrintF('request-id: %s',$TicketID);
  #-----------------------------------------------------------------------------
  $Query = Array('request'=>Implode("\n",$Query));
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/partner_gateway',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[MasterName_Check_Task]:не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/status:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        return FALSE;
      case '201':
        return FALSE;
      case '202':
        return Array('DomainID'=>0);
      case '203':
        return new gException('REQUEST_FAILED','Обработка запроса завершилась с ошибкой');
      case '400':
        return new gException('BAD_REQUEST','Неверный формат запроса');
      case '401':
        return new gException('AUTH_ERROR','Ошибка авторизации');
      case '402':
        return new gException('DATA_ERROR','Ошибка в данных запроса');
      case '403':
        return new gException('FORBIDDEN','Доступ к запрашиваемому объекту запрещен');
      case '404':
        return new gException('NOT_FOUND','Запрашиваемый объект не найден');
      case '405':
        return new gException('INVALID_REQUEST','Невозможно выполнить запрос');
      case '500':
        return new gException('SERVER_ERROR','Внутренняя ошибка сервера');
      default:
        return new gException('WRONG_ERROR','Неизвестный статус ошибки');
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function MasterName_Contract_Register($Settings,$PepsonID,$Person,$DomainZone){
  /****************************************************************************/
  $__args_types = Array('array','string','array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'CP1251',
    'IsLogging'=> $Settings['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $Query[] = '[request]';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = 'action: create_client';
  #-----------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #-----------------------------------------------------------------------------
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #-----------------------------------------------------------------------------
  $Query[] = '';
  #-----------------------------------------------------------------------------
  switch($PepsonID){
    case 'Natural':
      #-------------------------------------------------------------------------
      $Query[] = '[client]';
      #-------------------------------------------------------------------------
      $Query[] = 'client-type: ФИЗЛИЦО';
      $Query[] = ($Person['PasportCountry'] != 'RU'?'non-resident:':'resident:');
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('person-r: %s %s %s',$Person['Sourname'],$Person['Name'],$Person['Lastname']);
      $Query[] = SPrintF('person: %s %s %s',Translit($Person['Name']),Mb_SubStr(Translit($Person['Lastname']),0,1),Translit($Person['Sourname']));
      $Query[] = SPrintF('email: %s',$Person['Email']);
      #-------------------------------------------------------------------------
      $Phone = $Person['Phone'];
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('phone: %s',$Phone);
      #-------------------------------------------------------------------------
      $Fax = $Person['Fax'];
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('fax: %s',$Fax?$Fax:$Phone);
      #-------------------------------------------------------------------------
      $BornDate = Explode('.',$Person['BornDate']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('birth-date: %s-%s-%s',End($BornDate),Prev($BornDate),Prev($BornDate));
      #-------------------------------------------------------------------------
      $PasportLine = Preg_Split('/^([0-9]{2})/',$Person['PasportLine'],-1,PREG_SPLIT_DELIM_CAPTURE);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('passport-series: %s %s',Next($PasportLine),Next($PasportLine));
      $Query[] = SPrintF('passport-number: %s',$Person['PasportNum']);
      #-------------------------------------------------------------------------
      $PasportDate = Explode('.',$Person['PasportDate']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('passport-date: %s-%s-%s',End($PasportDate),Prev($PasportDate),Prev($PasportDate));
      $Query[] = SPrintF('passport-org: %s',$Person['PasportWhom']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('post-country: %s',$Person['pCountry']);
      $Query[] = SPrintF('post-region: %s',$Person['pState']);
      $Query[] = SPrintF('post-zip-code: %s',$Person['pIndex']);
      $Query[] = SPrintF('post-city: %s',$Person['pCity']);
      $Query[] = SPrintF('post-street: %s %s, %s',$Person['pType'],$Person['pAddress'],$Person['pRecipient']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('reg-country: %s',$Person['pCountry']);
      $Query[] = SPrintF('reg-region: %s',$Person['pState']);
      $Query[] = SPrintF('reg-city: %s',$Person['pCity']);
      $Query[] = SPrintF('reg-street: %s %s',$Person['pType'],$Person['pAddress']);
    break;
    case 'Juridical':
      #-------------------------------------------------------------------------
      $Query[] = '[client]';
      #-------------------------------------------------------------------------
      $Query[] = 'client-type: ЮРЛИЦО';
      $Query[] = 'resident:';
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('ownership: %s',$Person['CompanyForm']);
      $Query[] = SPrintF('org-r: %s',$Person['CompanyName']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('org: %s %s',Translit($Person['CompanyName']),Translit($Person['CompanyForm']));
      $Query[] = SPrintF('email: %s',$Person['Email']);
      #-------------------------------------------------------------------------
      $Phone = $Person['Phone'];
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('phone: %s',$Phone);
      #-------------------------------------------------------------------------
      $Fax = $Person['Fax'];
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('fax: %s',$Fax?$Fax:$Phone);
      $Query[] = SPrintF('contact: %s %s %s',$Person['dSourname'],$Person['dName'],$Person['dLastname']);
      $Query[] = SPrintF('inn: %s',$Person['Inn']);
      $Query[] = SPrintF('kpp: %s',$Person['Kpp']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('post-country: %s',$Person['pCountry']);
      $Query[] = SPrintF('post-zip-code: %s',$Person['pIndex']);
      $Query[] = SPrintF('post-region: %s',$Person['pState']);
      $Query[] = SPrintF('post-city: %s',$Person['pCity']);
      $Query[] = SPrintF('post-street: %s %s, %s "%s"',$Person['pType'],$Person['pAddress'],$Person['CompanyForm'],$Person['CompanyName']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('jur-country: %s',$Person['jCountry']);
      $Query[] = SPrintF('jur-zip-code: %s',$Person['jIndex']);
      $Query[] = SPrintF('jur-region: %s',$Person['jState']);
      $Query[] = SPrintF('jur-city: %s',$Person['jCity']);
      $Query[] = SPrintF('jur-street: %s %s',$Person['jType'],$Person['jAddress']);
      #-------------------------------------------------------------------------
      $Query[] = SPrintF('real-country: %s',$Person['jCountry']);
      $Query[] = SPrintF('real-zip-code: %s',$Person['jIndex']);
      $Query[] = SPrintF('real-region: %s',$Person['jState']);
      $Query[] = SPrintF('real-city: %s',$Person['jCity']);
      $Query[] = SPrintF('real-street: %s %s',$Person['pType'],$Person['jAddress']);
    break;
    default:
      return new gException('WRONG_PROFILE_ID','Неверный идентификатор профиля');
  }
  #-----------------------------------------------------------------------------
  $Query = Array('request'=>Implode("\n",$Query));
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/partner_gateway',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[MasterName_Contract_Register]:не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/status:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        # No more...
      case '201':
        # No more...
      case '202':
        return Array('TicketID'=>$RequestID);
      case '203':
        return new gException('REQUEST_FAILED','Обработка запроса завершилась с ошибкой');
      case '400':
        return new gException('BAD_REQUEST','Неверный формат запроса');
      case '401':
        return new gException('AUTH_ERROR','Ошибка авторизации');
      case '402':
        return new gException('DATA_ERROR','Ошибка в данных запроса');
      case '403':
        return new gException('FORBIDDEN','Доступ к запрашиваемому объекту запрещен');
      case '404':
        return new gException('NOT_FOUND','Запрашиваемый объект не найден');
      case '405':
        return new gException('INVALID_REQUEST','Невозможно выполнить запрос');
      case '500':
        return new gException('SERVER_ERROR','Внутренняя ошибка сервера');
      default:
        return new gException('WRONG_ERROR','Неизвестный статус ошибки');
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function MasterName_Get_Contract($Settings,$TicketID){
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
    'Charset'  => 'CP1251',
    'IsLogging'=> $Settings['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $Query[] = '[request]';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = 'action: request_status';
  $Query[] = SPrintF('request-id: %s',$TicketID);
  #-----------------------------------------------------------------------------
  $Query = Array('request'=>Implode("\n",$Query));
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/partner_gateway',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[MasterName_Check_Task]:не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/status:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        return FALSE;
      case '201':
        return FALSE;
      case '202':
        #-----------------------------------------------------------------------
        if(Preg_Match('/client:\s([0-9]+\/[A-Z\-]+)\n/',$Result,$Contract))
          return Array('ContractID'=>Next($Contract));
        #-----------------------------------------------------------------------
        return new gException('REQUEST_FAILED','Не удалось определить клиентский номер договора');
      case '203':
        return new gException('REQUEST_FAILED','Обработка запроса завершилась с ошибкой');
      case '400':
        return new gException('BAD_REQUEST','Неверный формат запроса');
      case '401':
        return new gException('AUTH_ERROR','Ошибка авторизации');
      case '402':
        return new gException('DATA_ERROR','Ошибка в данных запроса');
      case '403':
        return new gException('FORBIDDEN','Доступ к запрашиваемому объекту запрещен');
      case '404':
        return new gException('NOT_FOUND','Запрашиваемый объект не найден');
      case '405':
        return new gException('INVALID_REQUEST','Невозможно выполнить запрос');
      case '500':
        return new gException('SERVER_ERROR','Внутренняя ошибка сервера');
      default:
        return new gException('WRONG_ERROR','Неизвестный статус ошибки');
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
?>
