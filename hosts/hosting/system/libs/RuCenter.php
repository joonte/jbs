<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
function RuCenter_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$IsPrivateWhoIs,$ContractID = ''){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $ContractItem = Explode(':',$ContractID);
  #-----------------------------------------------------------------------------
  $ContractID = Current($ContractItem);
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #-----------------------------------------------------------------------------
  $Query[] = 'lang: ru';
  $Query[] = 'request: order';
  $Query[] = 'operation: create';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = SPrintF('subject-contract: %s',$ContractID);
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #-----------------------------------------------------------------------------
  $Query[] = '';
  #-----------------------------------------------------------------------------
  $Query[] = '[order-item]';
  $Query[] = 'check-ns: off';
  #-----------------------------------------------------------------------------
  switch($DomainZone){
    case 'ru':
      $Query[] = 'service: domain_ru';
      $Query[] = 'template: client_ru';
    break;
    case 'su':
      $Query[] = 'service: domain_su';
      $Query[] = 'template: client_ru';
    break;
    case 'рф':
      $Query[] = 'service: domain_rf';
      $Query[] = 'template: domain_rf';

      // Convert the domain
      $IDNAConverter = new IDNAConvert();
      $Domain = $IDNAConverter->encode($Domain);
    break;
    case 'msk.ru':
      $Query[] = 'service: domain_msk_ru';
      $Query[] = 'template: client_ru';
    break;
    case 'msk.su':
      $Query[] = 'service: domain_msk_su';
      $Query[] = 'template: client_ru';
    break;
    case 'spb.ru':
      $Query[] = 'service: domain_spb_ru';
      $Query[] = 'template: client_ru';
    break;
    case 'spb.su':
      $Query[] = 'service: domain_spb_su';
      $Query[] = 'template: client_ru';
    break;
    default:
      #-------------------------------------------------------------------------
      if(Count($ContractItem) < 2){
        #-----------------------------------------------------------------------
        $Query = Array();
        #-----------------------------------------------------------------------
        $RequestID = UniqID('ID');
        #-----------------------------------------------------------------------
        $Query[] = 'lang: ru';
        $Query[] = 'request: contract';
        $Query[] = 'operation: get';
        $Query[] = SPrintF('login: %s',$Settings['Login']);
        $Query[] = SPrintF('password: %s',$Settings['Password']);
        $Query[] = SPrintF('subject-contract: %s',$ContractID);
        $Query[] = SPrintF('request-id: %s',$RequestID);
        #-----------------------------------------------------------------------
        $Result = HTTP_Send('/dns/dealer',$HTTP,Array(),Array('SimpleRequest'=>Implode("\n",$Query)));
        if(Is_Error($Result))
          return ERROR | @Trigger_Error('[RuCenter_Domain_Register]: не удалось выполнить запрос к серверу');
        #-----------------------------------------------------------------------
        if(Preg_Match('/HTTP\/1.0\s502/i',$Result['Heads']))
          return false;
        #-----------------------------------------------------------------------
        $Result = Trim($Result['Body']);
        #-----------------------------------------------------------------------
        if(Preg_Match('/State:\s([0-9]+)\s/',$Result,$CodeID)){
          #---------------------------------------------------------------------
          $CodeID = Next($CodeID);
          #---------------------------------------------------------------------
          switch($CodeID){
            case '200':
              #-----------------------------------------------------------------
              if(Preg_Match_All('/([a-z\-])+\:(.+)/',$Result,$Matches)){
                #---------------------------------------------------------------
                Debug(Print_R($Matches,1));
                #---------------------------------------------------------------
                return new gException('NOT_SUPPORTED_YET','Пока не поддерживается');
              }
              #-----------------------------------------------------------------
              break;
            case '500':
              return FALSE;
            default:
              return new gException('WRONG_ERROR',SPrintF('Неизвестный статус ошибки (%s)',$Result));
          }
        }
      }else
        $NicHDL = Next($ContractItem);
      #-------------------------------------------------------------------------
      $Query[] = 'service: domain_rrp';
      $Query[] = 'template: client_rrp';
      $Query[] = SPrintF('period: %d',$Years);
      $Query[] = SPrintF('admin-c: %s',$NicHDL);
      $Query[] = SPrintF('bill-c: %s',$NicHDL);
      $Query[] = SPrintF('tech-c: %s',$NicHDL);
      $Query[] = 'lang-tag: RUS';
  }
  #-----------------------------------------------------------------------------
  $Query[] = 'action: new';
  $Query[] = SPrintF('domain: %s',$Domain);
  $Query[] = SPrintF('private-person: %s',($IsPrivateWhoIs?'ON':'OFF'));
  #-----------------------------------------------------------------------------
  if($Ns1Name)
    $Query[] = $Ns1IP?SPrintF('nserver: %s %s',$Ns1Name,$Ns1IP):SPrintF('nserver: %s',$Ns1Name);
  #-----------------------------------------------------------------------------
  if($Ns2Name)
    $Query[] = $Ns2IP?SPrintF('nserver: %s %s',$Ns2Name,$Ns2IP):SPrintF('nserver: %s',$Ns2Name);
  #-----------------------------------------------------------------------------
  if($Ns3Name)
    $Query[] = $Ns3IP?SPrintF('nserver: %s %s',$Ns3Name,$Ns3IP):SPrintF('nserver: %s',$Ns3Name);
  #-----------------------------------------------------------------------------
  if($Ns4Name)
    $Query[] = $Ns4IP?SPrintF('nserver: %s %s',$Ns4Name,$Ns4IP):SPrintF('nserver: %s',$Ns4Name);
  #-----------------------------------------------------------------------------
  $Query[] = 'type: CORPORATE';
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/dns/dealer',$HTTP,Array(),Array('SimpleRequest'=>Implode("\n",$Query)));
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RuCenter_Domain_Register]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/HTTP\/1.0\s502/i',$Result['Heads']))
    return false;
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/State:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        #-----------------------------------------------------------------------
        if(Preg_Match('/order_id:([0-9]+)/',$Result,$TicketID))
          return Array('TicketID'=>Next($TicketID));
      case '500':
        return FALSE;
      default:
        return new gException('WRONG_ERROR',SPrintF('Неизвестный статус ошибки (%s)',$Result));
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function RuCenter_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$ContractID,$DomainID){
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
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #-----------------------------------------------------------------------------
  $Query[] = 'lang: ru';
  $Query[] = 'request: order';
  $Query[] = 'operation: create';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = SPrintF('subject-contract: %s',$ContractID);
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #-----------------------------------------------------------------------------
  $Query[] = '';
  #-----------------------------------------------------------------------------
  $Query[] = '[order-item]';
  $Query[] = 'action: prolong';
  $Query[] = 'template: prolong';
  #-----------------------------------------------------------------------------
  switch($DomainZone){
    case 'ru':
      $Query[] = 'service: domain_ru';
    break;
    case 'su':
      $Query[] = 'service: domain_su';
    break;
    case 'msk.ru':
      $Query[] = 'service: domain_msk_ru';
    break;
    case 'msk.su':
      $Query[] = 'service: domain_msk_su';
    break;
    case 'spb.ru':
      $Query[] = 'service: domain_spb_ru';
    break;
    case 'spb.su':
      $Query[] = 'service: domain_spb_su';
    break;
    default:
      $Query[] = 'service: domain_rrp';
  }
  #-----------------------------------------------------------------------------
  $Query[] = SPrintF('domain: %s',$Domain);
  $Query[] = SPrintF('prolong: %u',$Years);
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/dns/dealer',$HTTP,Array(),Array('SimpleRequest'=>Implode("\n",$Query)));
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RuCenter_Domain_Prolong]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/HTTP\/1.0\s502/i',$Result['Heads']))
    return false;
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/order_id\:([0-9]+)/',$Result,$RequestID)){
    #---------------------------------------------------------------------------
    $RequestID = Next($RequestID);
    #---------------------------------------------------------------------------
    return Array('TicketID'=>$RequestID);
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function RuCenter_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
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
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #-----------------------------------------------------------------------------
  $Query[] = 'lang: ru';
  $Query[] = 'request: order';
  $Query[] = 'operation: create';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = SPrintF('subject-contract: %s',$ContractID);
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #-----------------------------------------------------------------------------
  $Query[] = '';
  #-----------------------------------------------------------------------------
  $Query[] = '[order-item]';
  $Query[] = 'check-ns: off';
  #-----------------------------------------------------------------------------
  switch($DomainZone){
    case 'ru':
      $Query[] = 'service: domain_ru';
      $Query[] = 'template: client_ru';
    break;
    case 'su':
      $Query[] = 'service: domain_su';
      $Query[] = 'template: client_ru';
    break;
    default:
      $Query[] = 'service: domain_rrp';
      $Query[] = 'template: client_rrp';
  }
  #-----------------------------------------------------------------------------
  $Query[] = 'action: update';
  $Query[] = SPrintF('domain: %s',$Domain);
  #-----------------------------------------------------------------------------
  if($Ns1Name)
    $Query[] = $Ns1IP?SPrintF('nserver: %s %s',$Ns1Name,$Ns1IP):SPrintF('nserver: %s',$Ns1Name);
  #-----------------------------------------------------------------------------
  if($Ns2Name)
    $Query[] = $Ns2IP?SPrintF('nserver: %s %s',$Ns2Name,$Ns2IP):SPrintF('nserver: %s',$Ns2Name);
  #-----------------------------------------------------------------------------
  if($Ns3Name)
    $Query[] = $Ns3IP?SPrintF('nserver: %s %s',$Ns3Name,$Ns3IP):SPrintF('nserver: %s',$Ns3Name);
  #-----------------------------------------------------------------------------
  if($Ns4Name)
    $Query[] = $Ns4IP?SPrintF('nserver: %s %s',$Ns4Name,$Ns4IP):SPrintF('nserver: %s',$Ns4Name);
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/dns/dealer',$HTTP,Array(),Array('SimpleRequest'=>Implode("\n",$Query)));
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RuCenter_Domain_Ns_Change]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/HTTP\/1.0\s502/i',$Result['Heads']))
    return false;
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/State:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        #-----------------------------------------------------------------------
        if(Preg_Match('/order_id:([0-9]+)/',$Result,$TicketID))
          return Array('TicketID'=>Next($TicketID));
      case '500':
        return FALSE;
      default:
        return new gException('WRONG_ERROR',SPrintF('Неизвестный статус ошибки (%s)',$Result));
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function RuCenter_Check_Task($Settings,$TicketID){
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
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array();
  #-----------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #-----------------------------------------------------------------------------
  $Query[] = 'lang: ru';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = 'request: order';
  $Query[] = 'operation: get';
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #-----------------------------------------------------------------------------
  $Query[] = '';
  #-----------------------------------------------------------------------------
  $Query[] = '[order]';
  $Query[] = SPrintF('order_id: %s',$TicketID);
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/dns/dealer',$HTTP,Array(),Array('SimpleRequest'=>Implode("\n",$Query)));
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RuCenter_Check_Task]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/HTTP\/1.0\s502/i',$Result['Heads']))
    return false;
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/State:\s([0-9]+)\s/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
      case '200':
        #-----------------------------------------------------------------------
        if(Preg_Match_All('/state:(waiting|failed|running|completed|deleted)/',$Result,$Matches)){
          #---------------------------------------------------------------------
          $Matches = Next($Matches);
          #---------------------------------------------------------------------
          switch(End($Matches)){
            case 'waiting':
              return FALSE;
            case 'running':
              return FALSE;
            case 'completed':
              return Array('DomainID'=>0);
            case 'failed':
              return new gException('OPERATION_FAILED','Произошла ошибка');
            case 'deleted':
              return new gException('ORDER_DELETED','Заказ отозван клиентом (партнером)');
            default:
              return new gException('WRONG_ERROR','Неизвестный статус заказа');
          }
        }
      break;
      case '500':
        return FALSE;
      default:
        return new gException('WRONG_ERROR',SPrintF('Неизвестный статус ошибки (%s)',$Result));
    }
  }
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function RuCenter_Contract_Register($Settings,$PepsonID,$Person,$DomainZone){
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
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array();
  #---------------------------------------------------------------------------
  $RequestID = UniqID('ID');
  #---------------------------------------------------------------------------
  $Query[] = 'lang: ru';
  $Query[] = 'request: contract';
  $Query[] = 'operation: create';
  $Query[] = SPrintF('login: %s',$Settings['Login']);
  $Query[] = SPrintF('password: %s',$Settings['Password']);
  $Query[] = SPrintF('request-id: %s',$RequestID);
  #---------------------------------------------------------------------------
  $Query[] = '';
  #---------------------------------------------------------------------------
  $Query[] = '[contract]';
  $Query[] = SPrintF('password: %s',UniqID());
  $Query[] = SPrintF('tech-password: %s',UniqID());
  $Query[] = 'currency-id: RUR';
  #---------------------------------------------------------------------------
  $Query[] = '';
  #---------------------------------------------------------------------------
  switch($PepsonID){
    case 'Natural':
      #-----------------------------------------------------------------------
      $Query[] = 'contract-type: PRS';
      $Query[] = SPrintF('person: %s %s %s',Translit($Person['Name']),Mb_SubStr(Translit($Person['Lastname']),0,1),Translit($Person['Sourname']));
      $Query[] = SPrintF('person-r: %s %s %s',$Person['Sourname'],$Person['Name'],$Person['Lastname']);
      $Query[] = SPrintF('country: %s',IsSet($Person['PasportCountry'])?$Person['PasportCountry']:$Person['pCountry']);
      $Query[] = SPrintF('phone: %s',$Person['Phone']);
      $Query[] = SPrintF('fax-no: %s',$Person['Fax']);
      $Query[] = SPrintF('birth-date: %s',$Person['BornDate']);
      $Query[] = SPrintF('passport: %s %s выдан %s, %s',$Person['PasportLine'],$Person['PasportNum'],$Person['PasportWhom'],$Person['PasportDate']);
      $Query[] = SPrintF('p-addr: %s, %s, %s, %s %s, %s',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress'],$Person['pRecipient']);
      $Query[] = SPrintF('e-mail: %s',$Person['Email']);
    break;
    case 'Juridical':
      #-----------------------------------------------------------------------
      $Query[] = 'client-type: ORG';
      $Query[] = SPrintF('org: %s %s',Translit($Person['CompanyName']),Translit($Person['CompanyFormFull']));
      $Query[] = SPrintF('org-r: %s',SPrintF('%s "%s"',$Person['CompanyFormFull'],$Person['CompanyName']));
      $Query[] = SPrintF('country: %s',$Person['jCountry']);
      $Query[] = SPrintF('e-mail: %s',$Person['Email']);
      $Query[] = SPrintF('phone: %s',$Person['Phone']);
      $Query[] = SPrintF('fax-no: %s',$Person['Fax']);
      $Query[] = SPrintF('code: %s',$Person['Inn']);
      $Query[] = SPrintF('kpp: %s',$Person['Kpp']);
      $Query[] = SPrintF('address-r: %s, %s, %s, %s %s',$Person['jIndex'],$Person['jState'],$Person['jCity'],$Person['jType'],$Person['jAddress']);
      $Query[] = SPrintF('p-addr: %s, %s, %s, %s %s, %s "%s"',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress'],$Person['CompanyFormFull'],$Person['CompanyName']);
      $Query[] = SPrintF('d-addr: %s, %s, %s, %s %s',$Person['jIndex'],$Person['jState'],$Person['jCity'],$Person['jType'],$Person['jAddress']);
    break;
    default:
      return new gException('WRONG_PROFILE_ID','Неверный идентификатор профиля');
  }
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/dns/dealer',$HTTP,Array(),Array('SimpleRequest'=>Implode("\n",$Query)));
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[RuCenter_Contract_Register]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/HTTP\/1.0\s502/i',$Result['Heads']))
    return false;
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/State:\s([0-9]+)/',$Result,$CodeID)){
    #---------------------------------------------------------------------------
    $CodeID = Next($CodeID);
    #---------------------------------------------------------------------------
    switch($CodeID){
     case '200':
        #-----------------------------------------------------------------------
        if(!Preg_Match('/nic-hdl:\s([0-9]+\/[A-Z\-]+)/',$Result,$ContractID))
          return new gException('LOGIN_NOT_FOUND','Неудалось получить номер договора');
        else
          $ContractID = Next($ContractID);
      break;
      case '500':
        return FALSE;
      default:
        return new gException('WRONG_ERROR',SPrintF('Неизвестный статус ошибки (%s)',$Result));
    }
  }else
    return new gException('WRONG_ANSWER',$Result);
  #-----------------------------------------------------------------------------
  # Делаем паузу перед запросом
  Sleep(10);
  #-----------------------------------------------------------------------------
  if(!In_Array($DomainZone,Array('ru','su'))){
    #---------------------------------------------------------------------------
    $Query = Array();
    #---------------------------------------------------------------------------
    $RequestID = UniqID('ID');
    #---------------------------------------------------------------------------
    $Query[] = SPrintF('login: %s',$Settings['Login']);
    $Query[] = SPrintF('password: %s',$Settings['Password']);
    $Query[] = SPrintF('subject-contract: %s',$ContractID);
    $Query[] = 'request: contact';
    $Query[] = 'operation: create';
    $Query[] = 'lang: ru';
    $Query[] = SPrintF('request-id: %s',$RequestID);
    #---------------------------------------------------------------------------
    $Query[] = '';
    #---------------------------------------------------------------------------
    $Query[] = '[contact]';
    $Query[] = SPrintF('status:registrant');
    $Query[] = SPrintF('org:');
    $Query[] = SPrintF('name: %s, %s',Translit($Person['Name']),Translit($Person['Lastname']));
    $Query[] = SPrintF('country: %s',IsSet($Person['PasportCountry'])?$Person['PasportCountry']:$Person['pCountry']);
    $Query[] = SPrintF('region: %s',Translit($Person['pCity']));
    $Query[] = SPrintF('city: %s',Translit($Person['pCity']));
    $Query[] = SPrintF('street: %s',Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress'])));
    $Query[] = SPrintF('zipcode: %s',$Person['pIndex']);
    $Query[] = SPrintF('phone: %s',$Person['Phone']);
    $Query[] = SPrintF('fax: %s',$Person['Fax']);
    $Query[] = SPrintF('email: %s',$Person['Email']);
    #---------------------------------------------------------------------------
    $Result = HTTP_Send('/dns/dealer',$HTTP,Array(),Array('SimpleRequest'=>Implode("\n",$Query)));
    if(Is_Error($Result))
      return ERROR | @Trigger_Error('[RuCenter_Contract_Register]: не удалось выполнить запрос к серверу');
    #---------------------------------------------------------------------------
    if(Preg_Match('/HTTP\/1.0\s502/i',$Result['Heads']))
      return FALSE;
    #---------------------------------------------------------------------------
    $Result = Trim($Result['Body']);
    #---------------------------------------------------------------------------
    if(Preg_Match('/State:\s([0-9]+)/',$Result,$CodeID)){
      #-------------------------------------------------------------------------
      $CodeID = Next($CodeID);
      #-------------------------------------------------------------------------
      switch($CodeID){
      case '200':
        #-----------------------------------------------------------------------
        if(!Preg_Match('/nic-hdl:([0-9A-Za-z\-]+)/',$Result,$NicHDL))
          return Array('TicketID'=>SPrintF('%s:%s',$ContractID,Next($NicHDL)));
        #-----------------------------------------------------------------------
        return new gException('LOGIN_NOT_FOUND','Неудалось получить номер договора');
      case '500':
        return FALSE;
      default:
        return new gException('WRONG_ERROR',SPrintF('Неизвестный статус ошибки (%s)',$Result));
      }
    }else
      return new gException('WRONG_ANSWER',$Result);
  }
  #-----------------------------------------------------------------------------
  return Array('TicketID'=>$ContractID);
}
#-------------------------------------------------------------------------------
function RuCenter_Get_Contract($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('ContractID'=>$TicketID);
}
#-------------------------------------------------------------------------------

# added by lissyara, for JBS-1132, 2015-11-27 in 20:32 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function RuCenter_Domain_GetPrice($Settings,$DomainName,$DomainZone){
	#-------------------------------------------------------------------------------
	return Array();
	#-------------------------------------------------------------------------------
}






?>
