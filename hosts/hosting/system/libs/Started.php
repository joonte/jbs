<?php
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
function Started_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$IsPrivateWhoIs,$CustomerID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array('Protocol'=>$Settings['Protocol'],'Port'=>$Settings['Port'],'Address'=>$Settings['Address'],'Host'=>$Settings['Address']);
  #-----------------------------------------------------------------------------
  $Reseller = new Tag('reseller');
  $Reseller->AddChild(new Tag('login',$Settings['Login']));
  $Reseller->AddChild(new Tag('password',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $Request = new Tag('RequestBody',$Reseller);
  #-----------------------------------------------------------------------------
  $objRequest = new Tag('objRequest');
  $objRequest->AddChild(new Tag('method','create'));
  $objRequest->AddChild(new Tag('user_id',$CustomerID));
  $objRequest->AddChild(new Tag('type','domain'));
  $objRequest->AddChild(new Tag('domain',SPrintF('%s.%s',$DomainName,$DomainZone)));
  $objRequest->AddChild(new Tag('ns',SPrintF('%s %s,%s %s',$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP)));
  $objRequest->AddChild(new Tag('who_pay','reseller'));
  $objRequest->AddChild(new Tag('operation','register'));
  #-----------------------------------------------------------------------------
  $Request->AddChild($objRequest);
  #-----------------------------------------------------------------------------
  $Post = SprintF("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n%s",$Request->ToXMLString());
  #-----------------------------------------------------------------------------
  $Responce = Http_Send('/',$Http,Array(),$Post,Array('Content-type: text/xml'));
  if(Is_Error($Responce))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Answer = String_XML_Parse($Responce['Body']);
  if(Is_Exception($Answer))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Answer = $Answer->ToArray();
  #-----------------------------------------------------------------------------
  $Answer = $Answer['AnswerBody'];
  #-----------------------------------------------------------------------------
  if(IsSet($Answer['statusCode']))
    return new gException('REGISTRATOR_ERROR',SPrintF('[%s]=(%s)',$Answer['statusCode'],$Answer['statusMessage']));
  #-----------------------------------------------------------------------------
  return Array('TicketID'=>$Answer['request_id']);
}
#-------------------------------------------------------------------------------
function Started_Check_Task($Settings,$RequestID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array('Protocol'=>$Settings['Protocol'],'Port'=>$Settings['Port'],'Address'=>$Settings['Address'],'Host'=>$Settings['Address']);
  #-----------------------------------------------------------------------------
  $Request = new Tag('RequestBody');
  #-----------------------------------------------------------------------------
  $Reseller = new Tag('reseller');
  $Reseller->AddChild(new Tag('login',$Settings['Login']));
  $Reseller->AddChild(new Tag('password',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $Request->AddChild($Reseller);
  #-----------------------------------------------------------------------------
  $objRequest = new Tag('objRequest');
  $objRequest->AddChild(new Tag('method','getState'));
  $objRequest->AddChild(new Tag('request_id',$RequestID));
  #-----------------------------------------------------------------------------
  $Request->AddChild($objRequest);
  #-----------------------------------------------------------------------------
  $Post = SprintF("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n%s",$Request->ToXMLString());
  #-----------------------------------------------------------------------------
  $Responce = Http_Send('/',$Http,Array(),$Post,Array('Content-type: text/xml'));
  if(Is_Error($Responce))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Response = Trim($Responce['Body']);
  #-----------------------------------------------------------------------------
  $Answer = String_XML_Parse($Response);
  if(Is_Exception($Answer))
    return new gException('WRONG_ANSWER',$Response,$Answer);
  #-----------------------------------------------------------------------------
  $Answer = $Answer->ToArray();
  #-----------------------------------------------------------------------------
  $Answer = $Answer['AnswerBody'];
  #-----------------------------------------------------------------------------
  if(IsSet($Answer['statusCode']))
    return new gException('REGISTRATOR_ERROR',SPrintF('[%s]=(%s)',$Answer['statusCode'],$Answer['statusMessage']));
  #-----------------------------------------------------------------------------
  switch($Answer['state']){
    case 'new':
      return FALSE;
    case 'waiting':
      return FALSE;
    case 'processing':
      return FALSE;
    case 'payment awaiting':
      return FALSE;
    case 'cart':
      return FALSE;
    case 'ready':
      return FALSE;
    case 'done':
      return Array('DomainID'=>$Answer['domain_id']);
    case 'error':
      return new gException('DOMAIN_REGISTER_ERROR','Заявка завершилась неудачей');
    case 'failed':
      return new gException('DOMAIN_REGISTER_FAILED','Заявка завершилась неудачей');
    case 'canceled':
      return new gException('DOMAIN_REGISTER_CANCELED','Заявка отменена');
    default:
      return new gException('WRONG_STATE','Ошибочный ответ о состоянии заявки');
  }
}
#-------------------------------------------------------------------------------
function Started_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$ContractID,$DomainID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array('Protocol'=>$Settings['Protocol'],'Port'=>$Settings['Port'],'Address'=>$Settings['Address'],'Host'=>$Settings['Address']);
  #-----------------------------------------------------------------------------
  $Request = new Tag('RequestBody');
  $Reseller = new Tag('reseller');
  $Reseller->AddChild(new Tag('login',$Settings['Login']));
  $Reseller->AddChild(new Tag('password',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $Request->AddChild($Reseller);
  #-----------------------------------------------------------------------------
  $objRequest = new Tag('objRequest');
  #-----------------------------------------------------------------------------
  $objRequest->AddChild(new Tag('method','create'));
  #-----------------------------------------------------------------------------
  $objRequest->AddChild(new Tag('type','domain'));
  $objRequest->AddChild(new Tag('domain_id',$DomainID));
  $objRequest->AddChild(new Tag('years',$Years));
  $objRequest->AddChild(new Tag('operation','prolong'));
  $objRequest->AddChild(new Tag('who_pay','reseller'));
  #-----------------------------------------------------------------------------
  $Request->AddChild($objRequest);
  #-----------------------------------------------------------------------------
  $Post = SprintF("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n%s",$Request->ToXMLString());
  #-----------------------------------------------------------------------------
  $Responce = Http_Send('/',$Http,Array(),$Post,Array('Content-type: text/xml'));
  if(Is_Error($Responce))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Response = Trim($Responce['Body']);
  #-----------------------------------------------------------------------------
  $Answer = String_XML_Parse($Response);
  if(Is_Exception($Answer))
    return new gException('WRONG_ANSWER',$Response,$Answer);
  #-----------------------------------------------------------------------------
  $Answer = $Answer->ToArray();
  #-----------------------------------------------------------------------------
  if(IsSet($Answer['statusCode']))
    return new gException('REGISTRATOR_ERROR',SPrintF('[%s]=(%s)',$Answer['statusCode'],$Answer['statusMessage']));
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function Started_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array('Protocol'=>$Settings['Protocol'],'Port'=>$Settings['Port'],'Address'=>$Settings['Address'],'Host'=>$Settings['Address']);
  #-----------------------------------------------------------------------------
  $Request = new Tag('RequestBody');
  #-----------------------------------------------------------------------------
  $Reseller = new Tag('reseller');
  $Reseller->AddChild(new Tag('login',$Settings['Login']));
  $Reseller->AddChild(new Tag('password',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $Request->AddChild($Reseller);
  #-----------------------------------------------------------------------------
  $objRequest = new Tag('objRequest');
  #-----------------------------------------------------------------------------
  $objRequest->AddChild(new Tag('method','create'));
  #-----------------------------------------------------------------------------
  $objRequest->AddChild(new Tag('type','domain'));
  $objRequest->AddChild(new Tag('domain_id',$DomainID));
  $objRequest->AddChild(new Tag('ns',SPrintF('%s %s,%s,%s',$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP)));
  $objRequest->AddChild(new Tag('operation','change ns'));
  #-----------------------------------------------------------------------------
  $Request->AddChild($objRequest);
  #-----------------------------------------------------------------------------
  $Post = SprintF("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n%s",$Request->ToXMLString());
  #-----------------------------------------------------------------------------
  $Responce = Http_Send('/',$Http,Array(),$Post,Array('Content-type: text/xml'));
  if(Is_Error($Responce))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Response = Trim($Responce['Body']);
  #-----------------------------------------------------------------------------
  $Answer = String_XML_Parse($Response);
  if(Is_Exception($Answer))
    return new gException('WRONG_ANSWER',$Response,$Answer);
  #-----------------------------------------------------------------------------
  if(IsSet($Answer['statusCode']))
    return new gException('REGISTRATOR_ERROR',SPrintF('[%s]=(%s)',$Answer['statusCode'],$Answer['statusMessage']));
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function Started_Contract_Register($Settings,$PepsonID,$Person,$DomainZone){
  /****************************************************************************/
  $__args_types = Array('array','string','array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array('Protocol'=>$Settings['Protocol'],'Port'=>$Settings['Port'],'Address'=>$Settings['Address'],'Host'=>$Settings['Address']);
  #-----------------------------------------------------------------------------
  $Reseller = new Tag('reseller');
  $Reseller->AddChild(new Tag('login',$Settings['Login']));
  $Reseller->AddChild(new Tag('password',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $Tmp = System_Element('tmp');
  if(Is_Error($Tmp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $pCountry = IsSet($Person['PasportCountry'])?$Person['PasportCountry']:$Person['pCountry'];
  #-----------------------------------------------------------------------------
  $Path = SPrintF('%s/started/states[%s].json',$Tmp,$pCountry);
  #-----------------------------------------------------------------------------
  if(File_Exists($Path)){
    #---------------------------------------------------------------------------
    $States = IO_Read($Path);
    if(Is_Error($States))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $States = JSON_Decode($States,TRUE);
    if(!$States)
      return ERROR | @Trigger_Error(500);
  }else{
    #---------------------------------------------------------------------------
    $Request = new Tag('RequestBody',$Reseller);
    #---------------------------------------------------------------------------
    $objCountry = new Tag('objCountry');
    $objCountry->AddChild(new Tag('method','getRegions'));
    $objCountry->AddChild(new Tag('country',$pCountry));
    #---------------------------------------------------------------------------
    $Request->AddChild($objCountry);
    #---------------------------------------------------------------------------
    $Post = SprintF("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n%s",$Request->ToXMLString());
    #---------------------------------------------------------------------------
    $Responce = Http_Send('/',$Http,Array(),$Post,Array('Content-type: text/xml'));
    if(Is_Error($Responce))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Response = Trim($Responce['Body']);
    #---------------------------------------------------------------------------
    $Answer = String_XML_Parse($Response);
    if(Is_Exception($Answer))
      return new gException('WRONG_ANSWER',$Response,$Answer);
    #---------------------------------------------------------------------------
    $States = Current($States->ToArray('item'));
    #---------------------------------------------------------------------------
    $IsWrite = IO_Write($Path,JSON_Encode($States),TRUE);
    if(Is_Error($IsWrite))
      return ERROR | @Trigger_Error(500);
  }
  #-----------------------------------------------------------------------------
  $objUser = new Tag('objUser');
  $objUser->AddChild(new Tag('method','create'));
  #-----------------------------------------------------------------------------
  $objUser->AddChild(new Tag('email',$Person['Email']));
  $objUser->AddChild(new Tag('password',SubStr(Md5(UniqID()),-8)));
  #-----------------------------------------------------------------------------
  $IsRussian = In_Array(IsSet($Person['PasportCountry'])?$Person['PasportCountry']:$Person['pCountry'],Array('RU','BY','UA'));
  #-----------------------------------------------------------------------------
  switch($PepsonID){
    case 'Natural':
      #-------------------------------------------------------------------------
      $pStateID = 0;
      #-------------------------------------------------------------------------
      $pState = $Person['pState'];
      #-------------------------------------------------------------------------
      foreach($States as $State){
        #-----------------------------------------------------------------------
        if(Preg_Match(SPrintF('/%s/',StrToLower($State['title'])),StrToLower($pState))){
          #---------------------------------------------------------------------
          $pStateID = $State['id'];
          #---------------------------------------------------------------------
          Debug(SPrintF('Найдена область: %s',$pStateID));
          #---------------------------------------------------------------------
          break;
        }
      }
      #-------------------------------------------------------------------------
      if(!$pStateID){
        #-----------------------------------------------------------------------
        Debug(SPrintF('Область не найдена, проверьте название области (%s)',$pState));
        #-----------------------------------------------------------------------
        return new gException('POST_REGION_NOT_FOUND','Область почтового адреса не найдена');
      }
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('type','person'));
      #-------------------------------------------------------------------------
      if($IsRussian){
        #-----------------------------------------------------------------------
        $objUser->AddChild(new Tag('fname',$Person['Name']));
        $objUser->AddChild(new Tag('lname',$Person['Lastname']));
        $objUser->AddChild(new Tag('mname',$Person['Sourname']));
        $objUser->AddChild(new Tag('doc_issued',$Person['PasportWhom']));
      }else{
        $objUser->AddChild(new Tag('fname',Translit($Person['Name'])));
        $objUser->AddChild(new Tag('lname',Translit($Person['Lastname'])));
        $objUser->AddChild(new Tag('mname',Translit($Person['Sourname'])));
        $objUser->AddChild(new Tag('doc_issued',Translit($Person['PasportWhom'])));
      }
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('p_inn',''));
      $objUser->AddChild(new Tag('doc_serial',$Person['PasportLine']));
      $objUser->AddChild(new Tag('doc_number',$Person['PasportNum']));
      $objUser->AddChild(new Tag('doc_date',$Person['PasportDate']));
      $objUser->AddChild(new Tag('birth_date',$Person['BornDate']));
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('country',StrToLower(IsSet($Person['PasportCountry'])?$Person['PasportCountry']:$Person['pCountry'])));
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('zip',$Person['pIndex']));
      $objUser->AddChild(new Tag('state',$pStateID));
      $objUser->AddChild(new Tag('city_type','city'));
      $objUser->AddChild(new Tag('addr_type',StrToLower($Person['pType'])));
      #-------------------------------------------------------------------------
      if($IsRussian){
        #-----------------------------------------------------------------------
        $objUser->AddChild(new Tag('city',$Person['pCity']));
        $objUser->AddChild(new Tag('addr',SPrintF('%s %s',$Person['pType'],$Person['pAddress'])));
      }else{
        $objUser->AddChild(new Tag('city',Translit($Person['pCity'])));
        $objUser->AddChild(new Tag('addr',Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']))));
      }
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('pzip',$Person['pIndex']));
      $objUser->AddChild(new Tag('pstate',$pStateID));
      $objUser->AddChild(new Tag('pcity_type','city'));
      $objUser->AddChild(new Tag('paddr_type',StrToLower($Person['pType'])));
      #-------------------------------------------------------------------------
      if($IsRussian){
        #-----------------------------------------------------------------------
        $objUser->AddChild(new Tag('pcity',$Person['pCity']));
        $objUser->AddChild(new Tag('paddr',SPrintF('%s %s',$Person['pType'],$Person['pAddress'])));
        $objUser->AddChild(new Tag('pto',$Person['pRecipient']));
      }else{
        #-----------------------------------------------------------------------
        $objUser->AddChild(new Tag('pcity',Translit($Person['pCity'])));
        $objUser->AddChild(new Tag('paddr',Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']))));
        $objUser->AddChild(new Tag('pto',Translit($Person['pRecipient'])));
      }
    break;
    case 'Juridical':
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('type','organization'));
      #-------------------------------------------------------------------------
      if($IsRussian){
        #-----------------------------------------------------------------------
        $objUser->AddChild(new Tag('fname',$Person['dName']));
        $objUser->AddChild(new Tag('lname',$Person['dLastname']));
        $objUser->AddChild(new Tag('mname',$Person['dSourname']));
        $objUser->AddChild(new Tag('org',SPrintF('%s "%s"',$Person['CompanyForm'],$Person['CompanyName'])));
      }else{
        $objUser->AddChild(new Tag('fname',Translit($Person['dName'])));
        $objUser->AddChild(new Tag('lname',Translit($Person['dLastname'])));
        $objUser->AddChild(new Tag('mname',Translit($Person['dSourname'])));
        $objUser->AddChild(new Tag('org',SPrintF('%s %s',Translit($Person['CompanyName']),Translit($Person['CompanyForm']))));
      }
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('o_inn',$Person['Inn']));
      $objUser->AddChild(new Tag('kpp',$Person['Kpp']));
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('country',StrToLower($Person['jCountry'])));
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('zip',$Person['jIndex']));
      #-------------------------------------------------------------------------
      $jStateID = 0;
      #-------------------------------------------------------------------------
      $jState = $Person['jState'];
      #-------------------------------------------------------------------------
      foreach($States as $State){
        #-----------------------------------------------------------------------
        if(Preg_Match(SPrintF('/%s/',StrToLower($State['title'])),StrToLower($jState))){
          #---------------------------------------------------------------------
          $jStateID = $State['id'];
          #---------------------------------------------------------------------
          Debug(SPrintF('Найдена область: %s',$jStateID));
          #---------------------------------------------------------------------
          break;
        }
      }
      #-------------------------------------------------------------------------
      if(!$jStateID){
        #-----------------------------------------------------------------------
        Debug(SPrintF('Область не найдена, проверьте название области (%s)',$jState));
        #-----------------------------------------------------------------------
        return new gException('JURIDICAL_REGION_NOT_FOUND','Область юридического адреса не найдена');
      }
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('state',$jStateID));
      $objUser->AddChild(new Tag('city_type','city'));
      $objUser->AddChild(new Tag('addr_type',StrToLower($Person['jType'])));
      #-------------------------------------------------------------------------
      if($IsRussian){
        #-----------------------------------------------------------------------
        $objUser->AddChild(new Tag('city',$Person['jCity']));
        $objUser->AddChild(new Tag('addr',SPrintF('%s %s',$Person['jType'],$Person['jAddress'])));
      }else{
        $objUser->AddChild(new Tag('city',Translit($Person['jCity'])));
        $objUser->AddChild(new Tag('addr',Translit(SPrintF('%s %s',$Person['jType'],$Person['jAddress']))));
      }
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('pzip',$Person['pIndex']));
      #-------------------------------------------------------------------------
      $pStateID = 0;
      #-------------------------------------------------------------------------
      $pState = $Person['pState'];
      #-------------------------------------------------------------------------
      foreach($States as $State){
        #-----------------------------------------------------------------------
        if(Preg_Match(SPrintF('/%s/',StrToLower($State['title'])),StrToLower($pState))){
          #---------------------------------------------------------------------
          $pStateID = $State['id'];
          #---------------------------------------------------------------------
          Debug(SPrintF('Найдена область: %s',$pStateID));
          #---------------------------------------------------------------------
          break;
        }
      }
      #-------------------------------------------------------------------------
      if(!$pStateID){
        #-----------------------------------------------------------------------
        Debug(SPrintF('Область не найдена, проверьте название области (%s)',$pState));
        #-----------------------------------------------------------------------
        return new gException('POST_REGION_NOT_FOUND','Область почтового адреса не найдена');
      }
      #-------------------------------------------------------------------------
      $objUser->AddChild(new Tag('pstate',$pStateID));
      $objUser->AddChild(new Tag('pcity_type','city'));
      $objUser->AddChild(new Tag('paddr_type',StrToLower($Person['pType'])));
      #-------------------------------------------------------------------------
      if($IsRussian){
        #-----------------------------------------------------------------------
        $objUser->AddChild(new Tag('pcity',$Person['pCity']));
        $objUser->AddChild(new Tag('paddr',SPrintF('%s %s',$Person['pType'],$Person['pAddress'])));
        $objUser->AddChild(new Tag('pto',SPrintF('%s "%s"',$Person['CompanyForm'],$Person['CompanyName'])));
      }else{
        $objUser->AddChild(new Tag('pcity',Translit($Person['pCity'])));
        $objUser->AddChild(new Tag('paddr',Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']))));
        $objUser->AddChild(new Tag('pto',SPrintF('%s %s',Translit($Person['CompanyName']),Translit($Person['CompanyForm']))));
      }
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
  #-----------------------------------------------------------------------------
  $Phone = $Person['Phone'];
  #-----------------------------------------------------------------------------
  if($Phone){
    #---------------------------------------------------------------------------
    $Phone = StrPBrk($Phone,' ');
    #---------------------------------------------------------------------------
    $objUser->AddChild(new Tag('tel',Trim($Phone)));
  }else
    $objUser->AddChild(new Tag('tel'));
  #-----------------------------------------------------------------------------
  $Fax = $Person['Fax'];
  #-----------------------------------------------------------------------------
  if($Fax){
    #---------------------------------------------------------------------------
    $Fax = StrPBrk($Fax,' ');
    #---------------------------------------------------------------------------
    $objUser->AddChild(new Tag('fax',Trim($Fax)));
  }else
    $objUser->AddChild(new Tag('fax'));
  #-----------------------------------------------------------------------------
  $Request = new Tag('RequestBody',$Reseller);
  #-----------------------------------------------------------------------------
  $Request->AddChild($objUser);
  #-----------------------------------------------------------------------------
  $Post = SprintF("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n%s",$Request->ToXMLString());
  #-----------------------------------------------------------------------------
  $Responce = Http_Send('/',$Http,Array(),$Post,Array('Content-type: text/xml; charset=utf-8'));
  if(Is_Error($Responce))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Response = Trim($Responce['Body']);
  #-----------------------------------------------------------------------------
  $Answer = String_XML_Parse($Response);
  if(Is_Exception($Answer))
    return new gException('WRONG_ANSWER',$Response,$Answer);
  #-----------------------------------------------------------------------------
  $Answer = $Answer->ToArray();
  #-----------------------------------------------------------------------------
  $Answer = $Answer['AnswerBody'];
  #-----------------------------------------------------------------------------
  if(IsSet($Answer['statusCode']))
    return new gException('REGISTRATOR_ERROR',SPrintF('[%s]=(%s)',$Answer['statusCode'],$Answer['statusMessage']));
  #-----------------------------------------------------------------------------
  return Array('TicketID'=>$Answer['user_id']);
}
#-------------------------------------------------------------------------------
function Started_Get_Contract($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('ContractID'=>$TicketID);
}
#-------------------------------------------------------------------------------
?>
