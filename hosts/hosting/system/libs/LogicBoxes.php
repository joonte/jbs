<?php
#-------------------------------------------------------------------------------
function LogicBoxes_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$IsPrivateWhoIs,$CustomerID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Wsdl = System_Element('config/Wsdl/DomContact.wsdl');
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $CustomerInfo = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  $Response = $CustomerInfo->listNames($Settings['Login'],$Settings['Password'],'reseller','ru',$Settings['ParentID'],$CustomerID);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  Debug(Print_R($Response,TRUE));
  #-----------------------------------------------------------------------------
  $Contact = Next($Response);
  $ContactId = $Contact['contact.contactid'];
  #-----------------------------------------------------------------------------
  $Wsdl = System_Element('config/Wsdl/DomOrder.wsdl');
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $ContactHash = Array(
    #---------------------------------------------------------------------------
    'registrantcontactid' => $ContactId,
    'admincontactid'      => $ContactId,
    'technicalcontactid'  => $ContactId,
    'billingcontactid'    => $ContactId
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $DomainHash = Array($Domain=>$Years);
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
    'userName'                => $Settings['Login'],
    'password'                => $Settings['Password'],
    'role'                    => 'reseller',
    'langpref'                => 'ru',
    'parentid'                => $Settings['ParentID'],
    'addParamList'            => Array(Array('domainhash'=>$DomainHash,'contacthash'=>$ContactHash)),
    'nameServerList'          => $NsServers,
    'customerId'              => $CustomerID,
    'invoiceOption'           => 'NoInvoice',
    'enablePrivacyProtection' => $IsPrivateWhoIs,
    'validate'                => TRUE,
    'extraInfo'               => Array()
  );
  #-----------------------------------------------------------------------------
  $RegDomain = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  Debug(Print_R($Params,TRUE));
  #-----------------------------------------------------------------------------
  $Response = $RegDomain->__SoapCall('RegisterDomain',$Params);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  Debug(Print_R($Response,TRUE));
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($DomainHash) as $DomainID){
    #---------------------------------------------------------------------------
    switch($Response[$DomainID]['status']){
      case 'error':
        #-----------------------------------------------------------------------
        Debug($Response[$DomainID]['error']);
        #-----------------------------------------------------------------------
        return new gException('REGISTRATION_ERROR','Ошибка регистрации домена');
      case 'Success':
        return Array('TicketID'=>$Domain);
      default:
        return ERROR | @Trigger_Error(101);
    }
  }
}
#-------------------------------------------------------------------------------
function LogicBoxes_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$CustomerID,$DomainID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $DomainHash = Array();
  #-----------------------------------------------------------------------------
  $Wsdl = System_Element('config/Wsdl/DomOrder.wsdl');
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $DomainInfo = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $Params = Array(
    #---------------------------------------------------------------------------
    'SERVICE_USERNAME'  => $Settings['Login'],
    'SERVICE_PASSWORD'  => $Settings['Password'],
    'SERVICE_ROLE'      => 'reseller',
    'SERVICE_LANGPREF'  => 'ru',
    'SERVICE_PARENTID'  => $Settings['ParentID'],
    'domainName'        => $Domain,
    'option'            => Array('All')
  );
  #-----------------------------------------------------------------------------
  Debug(Print_R($Params,TRUE));
  #-----------------------------------------------------------------------------
  $Response = $DomainInfo->__SoapCall('getDetailsByDomain',$Params);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #---------------------------------------------------------------------------
  $DomainHash[$Domain] = Array('entityid'=>$Response['entityid'],'noofyears'=>(string)$Years,'expirydate'=>$Response['endtime']);
  #-----------------------------------------------------------------------------
  $Wsdl = System_Element('config/Wsdl/DomOrder.wsdl');
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $RenewDomain = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  $Response = $RenewDomain->renewDomain($Settings['Login'],$Settings['Password'],'reseller','ru',$Settings['ParentID'],$DomainHash,'NoInvoice');
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  switch($Response[$Domain]['status']){
    case 'error':
      #-------------------------------------------------------------------------
      Debug($Response[$Domain]['error']);
      #-------------------------------------------------------------------------
      return new gException('REGISTRATION_ERROR','Ошибка регистрации домена');
    case 'Success':
      return Array('TicketID'=>$Domain);
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
function LogicBoxes_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Wsdl = System_Element('config/Wsdl/DomOrder.wsdl');
  #-----------------------------------------------------------------------------
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $DomainInfo = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $Params = Array(
    #---------------------------------------------------------------------------
    'SERVICE_USERNAME' => $Settings['Login'],
    'SERVICE_PASSWORD' => $Settings['Password'],
    'SERVICE_ROLE'     => 'reseller',
    'SERVICE_LANGPREF' => 'ru',
    'SERVICE_PARENTID' => $Settings['ParentID'],
    'domainName'       => $Domain,
    'option'           => Array('OrderDetails')
  );
  #-----------------------------------------------------------------------------
  Debug(Print_R($Params,TRUE));
  #------------------------------==---------------------------------------------
  $Result = $DomainInfo->__SoapCall('getDetailsByDomain',$Params);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Result)){
    #---------------------------------------------------------------------------
    Debug($Result->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  $OrderId = $Result['orderid'];
  #-----------------------------------------------------------------------------
  $NsHash = Array();
  #-----------------------------------------------------------------------------
  $NsHash['ns1'] = $Ns1Name;
  $NsHash['ns2'] = $Ns2Name;
  #-----------------------------------------------------------------------------
  if($Ns3Name)
    $NsHash['ns3'] = $Ns3Name;
  #-----------------------------------------------------------------------------
  if($Ns4Name)
    $NsHash['ns4'] = $Ns4Name;
  #-----------------------------------------------------------------------------
  $Wsdl = System_Element('config/Wsdl/DomOrder.wsdl');
  #-----------------------------------------------------------------------------
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $ModifyDomain = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  if(Is_Error($ModifyDomain))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Response = $ModifyDomain->modifyNameServer($Settings['Login'],$Settings['Password'],'reseller','ru',$Settings['ParentID'],$OrderId,$NsHash);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору при смене NS серверов');
  }
  #---------------------------------------------------------------------------
  switch($Response['status']){
    case 'error':
      #-------------------------------------------------------------------------
      Debug($Response['error']);
      #-------------------------------------------------------------------------
      return new gException('REGISTRATION_ERROR','Ошибка регистрации домена');
    case 'Success':
      return Array('TicketID'=>$Domain);
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
function LogicBoxes_Contract_Register($Settings,$PepsonID,$Person,$DomainZone){
  /****************************************************************************/
  $__args_types = Array('array','string','array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Wsdl = System_Element('config/Wsdl/Customer.wsdl');
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $AddCustomer = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  $Params = Array(
    #---------------------------------------------------------------------------
    'SERVICE_USERNAME' => $Settings['Login'],
    'SERVICE_PASSWORD' => $Settings['Password'],
    'SERVICE_ROLE'     => 'reseller',
    'SERVICE_LANGPREF' => 'ru',
    'SERVICE_PARENTID' => $Settings['ParentID'],
    'customerUserName' => $Person['Email'],
    'customerPassword' => UniqID(),
  );
  #-----------------------------------------------------------------------------
  switch($PepsonID){
    case 'Natural':
      #-------------------------------------------------------------------------
      $Params['name']     = SPrintF('%s %s %s',Translit($Person['Name']),Mb_SubStr(Translit($Person['Lastname']),0,1),Translit($Person['Sourname']));
      $Params['company']  = 'N/A';
      $Params['address1'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
      $Params['address2'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
      $Params['address3'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
      $Params['city']     = Translit($Person['pCity']);
      $Params['state']    = Translit($Person['pState']);
      $Params['country']  = IsSet($Person['PasportCountry'])?$Person['PasportCountry']:$Person['pCountry'];
      $Params['zip']      = $Person['pIndex'];
    break;
    case 'Juridical':
      #-------------------------------------------------------------------------
      $Params['name']     = SPrintF('%s %s %s',Translit($Person['dName']),Translit($Person['dLastname']),Translit($Person['dSourname']));
      $Params['company']  = SPrintF('%s %s',Translit($Person['CompanyName']),Translit($Person['CompanyFormFull']));
      $Params['address1'] = Translit(SPrintF('%s %s',$Person['jType'],$Person['jAddress']));
      $Params['address2'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
      $Params['address3'] = Translit(SPrintF('%s %s',$Person['pType'],$Person['pAddress']));
      $Params['city']     = Translit($Person['jCity']);
      $Params['state']    = Translit($Person['jState']);
      $Params['country']  = Translit($Person['jCountry']);
      $Params['zip']      = $Person['jIndex'];
    break;
    default:
      return ERROR | @Trigger_Error(400);
  }
  #-----------------------------------------------------------------------------
  $Phone = $Person['Phone'];
  #-----------------------------------------------------------------------------
  if($Phone){
    #---------------------------------------------------------------------------
    $Phone = Preg_Split('/\s+/',$Phone);
    #---------------------------------------------------------------------------
    $Params['telNoCc']    = Trim(Current($Phone),'+');
    $Params['telNo']      = SPrintF('%s%s',Next($Phone),Next($Phone));
    #---------------------------------------------------------------------------
    Reset($Phone);
    #---------------------------------------------------------------------------
    $Params['altTelNoCc'] = Trim(Current($Phone),'+');
    $Params['altTelNo']   = SPrintF('%s%s',Next($Phone),Next($Phone));
  }else{
    #---------------------------------------------------------------------------
    $Params['telNoCc']    = '';
    $Params['telNo']      = '';
    $Params['altTelNoCc'] = '';
    $Params['altTelNo']   = '';
  }
  #-----------------------------------------------------------------------------
  $Fax = $Person['Fax'];
  #-----------------------------------------------------------------------------
  if($Fax){
    #---------------------------------------------------------------------------
    $Fax = Preg_Split('/\s+/',$Fax);
    #---------------------------------------------------------------------------
    $Params['faxNoCc'] = Trim(Current($Fax),'+');
    $Params['faxNo']   = SPrintF('%s%s',Next($Fax),Next($Fax));
  }else{
    #---------------------------------------------------------------------------
    $Params['faxNoCc'] = '';
    $Params['faxNo']   = '';
  }
  #-----------------------------------------------------------------------------
  $Params['customerLangPref'] = 'ru';
  #-----------------------------------------------------------------------------
  Debug(Print_R($Params,TRUE));
  #-----------------------------------------------------------------------------
  $Response = $AddCustomer->__SoapCall('addCustomer',$Params);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  $CustomerID = $Response;
  #-----------------------------------------------------------------------------
  $Wsdl = System_Element('config/Wsdl/DomContact.wsdl');
  if(Is_Error($Wsdl))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $DefaultContact = new SoapClient($Wsdl,Array('exceptions'=>0));
  #-----------------------------------------------------------------------------
  $Response = $DefaultContact->addDefaultContact($Settings['Login'],$Settings['Password'],'reseller','ru',$Settings['ParentID'],$CustomerID);
  #-----------------------------------------------------------------------------
  if(Is_SOAP_Fault($Response)){
    #---------------------------------------------------------------------------
    Debug($Response->faultstring);
    #---------------------------------------------------------------------------
    return new gException('ANSWER_ERROR','Ошибка обращения к регистратору');
  }
  #-----------------------------------------------------------------------------
  return Array('TicketID'=>$CustomerID);
}
#-------------------------------------------------------------------------------
function LogicBoxes_Get_Contract($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('ContractID'=>$TicketID);
}
#-------------------------------------------------------------------------------
function LogicBoxes_Check_Task($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('DomainID'=>0);
}
#-------------------------------------------------------------------------------


# added by lissyara, for JBS-1132, 2015-11-27 in 20:32 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function LogicBoxes_Domain_GetPrice($Settings,$DomainName,$DomainZone){
	#-------------------------------------------------------------------------------
	return Array();
	#-------------------------------------------------------------------------------
}







?>
