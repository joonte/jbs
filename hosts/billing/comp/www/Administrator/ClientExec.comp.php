<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Header('Content-type: text/plain; charset=utf-8');
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$Server   =  (string) @$Args['Server'];
$Port     = (integer) @$Args['Port'];
$User     =  (string) @$Args['User'];
$Password =  (string) @$Args['Password'];
$DbName   =  (string) @$Args['DbName'];
$Users    =  (string) @$Args['Users'];
#-------------------------------------------------------------------------------
$Course = 30;
#-------------------------------------------------------------------------------
$Params = Array('Server'=>$Server,'Port'=>$Port,'User'=>$User,'Password'=>$Password,'DbName'=>$DbName);
#-------------------------------------------------------------------------------
$Link = new MySQL($Params);
#-------------------------------------------------------------------------------
if(Is_Error($Link->Open()))
  return 'Не удалось подключиться к серверу MySQL';
#-------------------------------------------------------------------------------
$Result = $Link->Query(SPrintF('SET NAMES `%s`',$Charset));
if(Is_Error($Result))
  return $Link->GetError();
#-------------------------------------------------------------------------------
if(Is_Error($Link->SelectDB()))
  return $Link->GetError();
#-------------------------------------------------------------------------------
$Query = 'SELECT * FROM `users`';
#-------------------------------------------------------------------------------
if($Users){
  #-----------------------------------------------------------------------------
  $Users = Preg_Split('/\s+/',$Users);
  #-----------------------------------------------------------------------------
  $Array = Array();
  #-----------------------------------------------------------------------------
  foreach($Users as $User)
    $Array[] = SPrintF("'%s'",$User);
  #-----------------------------------------------------------------------------
  $Users = $Array;
  #-----------------------------------------------------------------------------
  $Query .= SPrintF(' WHERE `email` IN(%s)',Implode(',',$Users));
}
#-------------------------------------------------------------------------------
$Query .= ' LIMIT 90,100';
#-------------------------------------------------------------------------------
$Result = $Link->Query($Query);
if(Is_Error($Result))
  return $Link->GetError();
#-------------------------------------------------------------------------------
$dUsers = MySQL::Result($Result);
if(Is_Error($dUsers))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Users = new Tag('Users');
#-------------------------------------------------------------------------------
foreach($dUsers as $dUser){
  #-----------------------------------------------------------------------------
  echo SPrintF("Экспорт пользователя (%s)\n",$Email = StrToLower($dUser['email']));
  #-----------------------------------------------------------------------------
  $Name = SPrintF('%s %s',$dUser['firstname'],$dUser['lastname']);
  #-----------------------------------------------------------------------------
  $User = new Tag('User');
  #-----------------------------------------------------------------------------
  $User->AddChild(new Tag('RegisterDate',$dUser['dateActivated']));
  $User->AddChild(new Tag('Password',Trim($dUser['password'],'*')));
  $User->AddChild(new Tag('Name',$Name));
  $User->AddChild(new Tag('EnterDate',$dUser['lastseen']));
  $User->AddChild(new Tag('Email',$Email));
  #-----------------------------------------------------------------------------
  $Contracts = new Tag('Contracts');
  #-----------------------------------------------------------------------------
  $Contract = new Tag('Contract');
  #-----------------------------------------------------------------------------
  $Contract->AddChild(new Tag('CreateDate',$dUser['dateActivated']));
  $Contract->AddChild(new Tag('TypeID',$dUser['isOrganization']?'Juridical':'Natural'));
  #-----------------------------------------------------------------------------
  $Result = $Link->Query(SPrintF('SELECT *,(SELECT `name` FROM `customuserfields` WHERE `customuserfields`.`id` = `user_customuserfields`.`customid`) as `Name` FROM `user_customuserfields` WHERE `userid` = %u',$dUser['id']));
  if(Is_Error($Result))
    return $Link->GetError();
  #-----------------------------------------------------------------------------
  $dInfo = MySQL::Result($Result);
  if(Is_Error($dInfo))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  foreach($dInfo as $dRow)
    $dInfo[$dRow['Name']] = $dRow['value'];
  #-----------------------------------------------------------------------------
  $Profile = new Tag('Profile',new Tag('jCountry','RU'),new Tag('Email',$dInfo['Email']));
  #-----------------------------------------------------------------------------
  if(IsSet($dInfo['Почтовый индекс']))
    $Profile->AddChild(new Tag('jIndex',$dInfo['Почтовый индекс']));
  #-----------------------------------------------------------------------------
  if(IsSet($dInfo['Область']))
    $Profile->AddChild(new Tag('jState',$dInfo['Область']));
  #-----------------------------------------------------------------------------
  if(IsSet($dInfo['Город']))
    $Profile->AddChild(new Tag('jCity',$dInfo['Город']));
  #-----------------------------------------------------------------------------
  if($dUser['isOrganization']){
    #---------------------------------------------------------------------------
    if(IsSet($dInfo['Организация']))
      $Profile->AddChild(new Tag('CompanyName',$dInfo['Организация']));
    #---------------------------------------------------------------------------
    if(IsSet($dInfo['Адрес'])){
      #-------------------------------------------------------------------------
      $Profile->AddChild(new Tag('jAddress',$dInfo['Адрес']));
      $Profile->AddChild(new Tag('pAddress',$dInfo['Адрес']));
    }
  }else{
    #---------------------------------------------------------------------------
    if(IsSet($dInfo['Имя']))
      $Profile->AddChild(new Tag('Name',$dInfo['Имя']));
    #---------------------------------------------------------------------------
    if(IsSet($dInfo['Фамилия']))
      $Profile->AddChild(new Tag('Sourname',$dInfo['Фамилия']));
    #---------------------------------------------------------------------------
    if(IsSet($dInfo['Адрес']))
      $Profile->AddChild(new Tag('Address',$dInfo['Адрес']));
  }
  #-----------------------------------------------------------------------------
  $Contract->AddChild($Profile);
  #-----------------------------------------------------------------------------
  $Result = $Link->Query(SPrintF("SELECT * FROM `invoice` WHERE `customerid` = %u AND `paid` = 1",$dUser['id']));
  if(Is_Error($Result))
    return $Link->GetError();
  #-----------------------------------------------------------------------------
  $dInvoices = MySQL::Result($Result);
  if(Is_Error($dInvoices))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Invoices = new Tag('Invoices');
  #-----------------------------------------------------------------------------
  foreach($dInvoices as $dInvoice){
    #---------------------------------------------------------------------------
    switch($dUser['paymenttype']){
      case 'wmz':
        $PaymentSystemID = 'WebMoneyZ';
      break;
      case 'wmr':
        $PaymentSystemID = 'WebMoneyR';
      break;
      case 'egold':
        $PaymentSystemID = 'Egold';
      break;
      case 'yandex':
        $PaymentSystemID = 'Yandex';
      break;
      default:
        $PaymentSystemID = 'InOffice';
    }
    #---------------------------------------------------------------------------
    $Invoice = new Tag('Invoice');
    #---------------------------------------------------------------------------
    $Invoice->AddChild(new Tag('CreateDate',$dInvoice['billdate']));
    $Invoice->AddChild(new Tag('PaymentSystemID',$PaymentSystemID));
    $Invoice->AddChild(new Tag('Summ',$dInvoice['amount']));
    $Invoice->AddChild(new Tag('PayDate',$dInvoice['datepaid']));
    $Invoice->AddChild(new Tag('Items'));
    #---------------------------------------------------------------------------
    $Invoices->AddChild($Invoice);
  }
  #-----------------------------------------------------------------------------
  $Contract->AddChild($Invoices);
  #-----------------------------------------------------------------------------
  $Result = $Link->Query(SPrintF("SELECT *,UNIX_TIMESTAMP(`nextbilldate`) as `nextbilldate` FROM `domains` WHERE `CustomerID` = %u",$dUser['id']));
  if(Is_Error($Result))
    return $Link->GetError();
  #-----------------------------------------------------------------------------
  $dHostingOrders = MySQL::Result($Result);
  if(Is_Error($dHostingOrders))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $HostingOrders = new Tag('HostingOrders');
  #-----------------------------------------------------------------------------
  foreach($dHostingOrders as $dHostingOrder){
    #---------------------------------------------------------------------------
    $DaysRemainded = (integer)(($dHostingOrder['nextbilldate'] - Time())/86400);
    #---------------------------------------------------------------------------
    $HostingOrder = new Tag('HostingOrder');
    #---------------------------------------------------------------------------
    $HostingOrder->AddChild(new Tag('OrderDate',$dHostingOrder['dateActivated']));
    $HostingOrder->AddChild(new Tag('Login',$dHostingOrder['UserName']));
    $HostingOrder->AddChild(new Tag('Password',Md5($dHostingOrder['password'])));
    $HostingOrder->AddChild(new Tag('Domain',$dHostingOrder['DomainName']));
    $HostingOrder->AddChild(new Tag('DaysRemainded',$DaysRemainded));
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF("SELECT * FROM `package` WHERE `ID` = %u",$dHostingOrder['Plan']));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dHostingScheme = MySQL::Result($Result);
    if(Is_Error($dHostingScheme))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!Count($dHostingScheme))
      continue;
    #---------------------------------------------------------------------------
    $dHostingScheme = Current($dHostingScheme);
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF("SELECT * FROM `package_variable` WHERE `packageid` = %u",$dHostingScheme['id']));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dQuotas = MySQL::Result($Result);
    if(Is_Error($dQuotas))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    foreach($dQuotas as $dQuota)
      $dHostingScheme[SubStr($dQuota['varname'],StrrPos($dQuota['varname'],'_')+1)] = $dQuota['value'];
    #---------------------------------------------------------------------------
    $HostingScheme = new Tag('HostingScheme');
    #---------------------------------------------------------------------------
    $HostingScheme->AddChild(new Tag('ServersGroup',new Tag('Name','По умолчанию')));
    $HostingScheme->AddChild(new Tag('Name',$dHostingScheme['planname']));
    $HostingScheme->AddChild(new Tag('PackageID',$dHostingScheme['planname']));
    $HostingScheme->AddChild(new Tag('CostDay',Round($dHostingScheme['price']/$Course,2)));
    $HostingScheme->AddChild(new Tag('CostMonth',$dHostingScheme['price']));
    $HostingScheme->AddChild(new Tag('IsReselling',1));
    $HostingScheme->AddChild(new Tag('QuotaDisk',$dHostingScheme['disklimit']));
    $HostingScheme->AddChild(new Tag('QuotaSubDomains',(integer)@$dHostingScheme['subdom']));
    $HostingScheme->AddChild(new Tag('QuotaDBs',(integer)@$dHostingScheme['baseuserlimit']));
    $HostingScheme->AddChild(new Tag('QuotaFTP',(integer)@$dHostingScheme['ftplimit']));
    $HostingScheme->AddChild(new Tag('QuotaEmail',(integer)@$dHostingScheme['maillimit']));
    $HostingScheme->AddChild(new Tag('QuotaEmailLists',(integer)@$dHostingScheme['maildomainlimit']));
    $HostingScheme->AddChild(new Tag('QuotaTraffic',(integer)@$dHostingScheme['bandwidthlimit']));
    $HostingScheme->AddChild(new Tag('QuotaParkDomains',(integer)@$dHostingScheme['domainlimit']));
    $HostingScheme->AddChild(new Tag('QuotaAddonDomains',(integer)@$dHostingScheme['domainlimit']));
    $HostingScheme->AddChild(new Tag('QuotaWWWDomains',(integer)@$dHostingScheme['webdomainlimit']));
    $HostingScheme->AddChild(new Tag('IsCGIAccess',(integer)@$dHostingScheme['cgi']));
    $HostingScheme->AddChild(new Tag('QuotaUsersDBs',(integer)@$dHostingScheme['baseuserlimit']));
    $HostingScheme->AddChild(new Tag('IsPHPCGIAccess',(integer)@$dHostingScheme['phpcgi']));
    $HostingScheme->AddChild(new Tag('IsPHPFastCGIAccess',(integer)@$dHostingScheme['phpfcgi']));
    $HostingScheme->AddChild(new Tag('IsPHPModAccess',(integer)@$dHostingScheme['phpmod']));
    $HostingScheme->AddChild(new Tag('IsShellAccess',(integer)@$dHostingScheme['shell']));
    $HostingScheme->AddChild(new Tag('IsSSLAccess',(integer)@$dHostingScheme['ssl']));
    $HostingScheme->AddChild(new Tag('IsSSIAccess',(integer)@$dHostingScheme['ssi']));
    $HostingScheme->AddChild(new Tag('QuotaEmailLists',(integer)@$dHostingScheme['maillists']));
    $HostingScheme->AddChild(new Tag('QuotaEmailBox',(integer)@$dHostingScheme['box']));
    $HostingScheme->AddChild(new Tag('QuotaEmailForwards',(integer)@$dHostingScheme['redir']));
    $HostingScheme->AddChild(new Tag('QuotaEmailAutoResp',(integer)@$dHostingScheme['resp']));
    $HostingScheme->AddChild(new Tag('QuotaWebApp',(integer)@$dHostingScheme['webapps']));
    #---------------------------------------------------------------------------
    $HostingOrder->AddChild($HostingScheme);
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF('SELECT * FROM `server` WHERE `id` = %u',$dHostingOrder['serverid']));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dHostingServer = MySQL::Result($Result);
    if(Is_Error($dHostingServer))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $dHostingServer = Current($dHostingServer);
    #---------------------------------------------------------------------------
    $HostingServer = new Tag('HostingServer');
    #---------------------------------------------------------------------------
    $HostingServer->AddChild(new Tag('Address',$dHostingServer['hostname']));
    $HostingServer->AddChild(new Tag('Login','root'));
    $HostingServer->AddChild(new Tag('Password','password'));
    $HostingServer->AddChild(new Tag('IP',$dHostingServer['sharedip']));
    $HostingServer->AddChild(new Tag('Ns1Name',$dHostingServer['hostname']));
    $HostingServer->AddChild(new Tag('Ns2Name',$dHostingServer['hostname']));
    $HostingServer->AddChild(new Tag('Ns3Name',$dHostingServer['hostname']));
    #---------------------------------------------------------------------------
    switch($dHostingServer['plugin']){
      case 'cpanel':
       #------------------------------------------------------------------------
       $HostingServer->AddChild(new Tag('SystemID','Cpanel'));
       $HostingServer->AddChild(new Tag('Url',SPrintF('http://%s:2082',$dHostingServer['hostname'])));
       $HostingServer->AddChild(new Tag('Protocol','ssl'));
       $HostingServer->AddChild(new Tag('Port',2087));
      break;
      case 'ispmanager':
       #------------------------------------------------------------------------
       $HostingServer->AddChild(new Tag('SystemID','IspManager'));
       $HostingServer->AddChild(new Tag('Url',SPrintF('https://%s/manager',$dHostingServer['hostname'])));
       $HostingServer->AddChild(new Tag('Protocol','ssl'));
       $HostingServer->AddChild(new Tag('Port',443));
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $HostingOrder->AddChild($HostingServer);
    #---------------------------------------------------------------------------
    $HostingOrders->AddChild($HostingOrder);
  }
  #-----------------------------------------------------------------------------
  $Contract->AddChild($HostingOrders); echo $Contract->ToXMLString();die();
  #-----------------------------------------------------------------------------
  $Result = $Link->Query(SPrintF('SELECT * FROM `domen` WHERE `userid` = %u',$dUser['ID']));
  if(Is_Error($Result))
    return $Link->GetError();
  #-----------------------------------------------------------------------------
  $dDomainsOrders = MySQL::Result($Result);
  if(Is_Error($dDomainsOrders))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $DomainsOrders = new Tag('DomainsOrders');
  #-----------------------------------------------------------------------------
  foreach($dDomainsOrders as $dDomainsOrder){
    #---------------------------------------------------------------------------
    $Domain = $dDomainsOrder['domen'];
    #---------------------------------------------------------------------------
    $DomainOrder = new Tag('DomainOrder',new Tag('CreateDate',$dDomainsOrder['dateorder']),new Tag('ExpirationDate',(string)JulianDayToGregorian($dDomainsOrder['reg'])),new Tag('DomainName',SubStr($Domain,0,$Index = StrRpos($Domain,'.'))));
    #---------------------------------------------------------------------------
    $Zone = SubStr($Domain,$Index);
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF("SELECT * FROM `tarifdom` WHERE `name` = '%s'",$Zone));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dDomainScheme = MySQL::Result($Result);
    if(Is_Error($dDomainScheme))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $dDomainScheme = Current($dDomainScheme);
    #---------------------------------------------------------------------------
    $DomainScheme = new Tag('DomainScheme',new Tag('Name',Trim($dDomainScheme['name'],'.')),new Tag('CostOrder',Round($dDomainScheme['cost']*$Course)),new Tag('CostProlong',Round($dDomainScheme['prolongcost']*$Course)));
    #---------------------------------------------------------------------------
    $Registrator = new Tag('Registrator');
    #---------------------------------------------------------------------------
    switch($dDomainScheme['registrar']){
      default:
        #-----------------------------------------------------------------------
        $Registrator->AddChild(new Tag('Name','WebNames'));
        $Registrator->AddChild(new Tag('TypeID','WebNames'));
        $Registrator->AddChild(new Tag('Address','webnames.ru'));
        $Registrator->AddChild(new Tag('Port',81));
        $Registrator->AddChild(new Tag('Protocol','ssl'));
        $Registrator->AddChild(new Tag('Login','user'));
    }
    #---------------------------------------------------------------------------
    $DomainScheme->AddChild($Registrator);
    #---------------------------------------------------------------------------
    $DomainOrder->AddChild($DomainScheme);
    #---------------------------------------------------------------------------
    $DomainsOrders->AddChild($DomainOrder);
  }
  #-----------------------------------------------------------------------------
  $Contract->AddChild($DomainsOrders);
  #-----------------------------------------------------------------------------
  $Result = $Link->Query(SPrintF("SELECT SUM(`bonus`) as `Balance` FROM `partner` WHERE `userid` = %u",$dUser['ID']));
  if(Is_Error($Result))
    return $Link->GetError();
  #-----------------------------------------------------------------------------
  $dBonuses = MySQL::Result($Result);
  if(Is_Error($dBonuses))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $dBonuse = Current($dBonuses);
  #-----------------------------------------------------------------------------
  $Contract->AddChild(new Tag('Balance',Round($Result['Balance']*$Course)));
  #-----------------------------------------------------------------------------
  $Contracts->AddChild($Contract);
  #-----------------------------------------------------------------------------
  $User->AddChild($Contracts);
  #-----------------------------------------------------------------------------
  $Users->AddChild($User);
}
#-------------------------------------------------------------------------------
$Dump = $Users->ToXMLString();
#-------------------------------------------------------------------------------
$IsWrite = IO_Write('/work/ClientExec.xml.gz',GzEncode($Dump),TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return 'Ok';
#-------------------------------------------------------------------------------

?>
