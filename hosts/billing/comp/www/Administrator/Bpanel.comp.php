<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','libs/WhoIs.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$Server   =  (string) @$Args['Server'];
$Port     = (integer) @$Args['Port'];
$User     =  (string) @$Args['User'];
$Password =  (string) @$Args['Password'];
$DbName   =  (string) @$Args['DbName'];
$Users    =  (string) @$Args['Users'];
$Charset  =  (string) @$Args['Charset'];
#-------------------------------------------------------------------------------
$Course = 30;
#-------------------------------------------------------------------------------
function JulianDayToGregorian($Julian){
    #---------------------------------------------------------------------------
    $Julian = $Julian - 1721119;
    $Calc1 = 4*$Julian - 1;
    #---------------------------------------------------------------------------
    $Year = Floor($Calc1 / 146097);
    $Julian = Floor($Calc1 - 146097*$Year);
    $Day = Floor($Julian / 4);
    $Calc2 = 4*$Day + 3;
    #---------------------------------------------------------------------------
    $Julian = Floor($Calc2 / 1461);
    $Day = $Calc2 - 1461*$Julian;
    $Day = Floor(($Day + 4) / 4);
    $Calc3 = 5*$Day - 3;
    #---------------------------------------------------------------------------
    $Month = Floor($Calc3 / 153);
    $Day = $Calc3 - 153*$Month;
    $Day = Floor(($Day + 5) / 5);
    $Year = 100*$Year + $Julian;
    #---------------------------------------------------------------------------
    if($Month < 10)
      $Month = $Month + 3;
    else{
      #-------------------------------------------------------------------------
      $Month = $Month - 9;
      $Year  = $Year + 1;
    }
    #---------------------------------------------------------------------------
    return MkTime(0,0,0,$Month,$Day,$Year);
}
#-------------------------------------------------------------------------------
echo <<<EOD
<HTML>
 <HEAD>
  <TITLE>Формирование данных</TITLE>
  <LINK href="/styles/root/Css/Standard.css" rel="stylesheet" type="text/css" />
  <STYLE>body {margin:10px;}</STYLE>
 </HEAD>
 <BODY>
EOD;
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
$Query = 'SELECT * FROM `acc`';
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
# $Query .= ' LIMIT 10';
#-------------------------------------------------------------------------------
$Result = $Link->Query($Query);
if(Is_Error($Result))
  return $Link->GetError();
#-------------------------------------------------------------------------------
$dUsers = MySQL::Result($Result);
if(Is_Error($dUsers))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!Count($dUsers))
  return 'Пользователи не найдены';
#-------------------------------------------------------------------------------
$Users = new Tag('Users');
#-------------------------------------------------------------------------------
foreach($dUsers as $dUser){
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($dUser) as $ColumnID){
    #---------------------------------------------------------------------------
    $Column = &$dUser[$ColumnID];
    #---------------------------------------------------------------------------
    $Column = Mb_Convert_Encoding($Column,'UTF-8',$Charset);
  }
  #-----------------------------------------------------------------------------
  echo SPrintF('<DIV>Экспорт пользователя <B>%s</B></DIV>',$Email = StrToLower($dUser['email']));
  #-----------------------------------------------------------------------------
  $Name = SPrintF('%s %s',$dUser['firstname'],$dUser['secname']);
  #-----------------------------------------------------------------------------
  $User = new Tag('User');
  #-----------------------------------------------------------------------------
  $User->AddChild(new Tag('RegisterDate',$dUser['datereg']));
  $User->AddChild(new Tag('Password',Md5($dUser['userpass'])));
  $User->AddChild(new Tag('Name',$Name));
  $User->AddChild(new Tag('EnterDate',$dUser['last']));
  $User->AddChild(new Tag('EnterIP',$dUser['ip']));
  $User->AddChild(new Tag('Email',$Email));
  $User->AddChild(new Tag('ICQ',$dUser['icq']));
  $User->AddChild(new Tag('Mobile',$dUser['phone']));
  #-----------------------------------------------------------------------------
  $Contracts = new Tag('Contracts');
  #-----------------------------------------------------------------------------
  $Contract = new Tag('Contract');
  #-----------------------------------------------------------------------------
  $Contract->AddChild(new Tag('CreateDate',$dUser['datereg']));
  $Contract->AddChild(new Tag('TypeID','Natural'));
  $Contract->AddChild(new Tag('Customer',$Name));
  #-----------------------------------------------------------------------------
  $Profile = new Tag('Profile');
  #-----------------------------------------------------------------------------
  $Profile->AddChild(new Tag('Sourname',$dUser['secname']));
  $Profile->AddChild(new Tag('Name',$dUser['firstname']));
  $Profile->AddChild(new Tag('Lastname',$dUser['thirdname']));
  $Profile->AddChild(new Tag('pCountry',$dUser['country']));
  $Profile->AddChild(new Tag('pIndex',$dUser['zip']));
  $Profile->AddChild(new Tag('pCity',$dUser['city']));
  $Profile->AddChild(new Tag('pAddress',$dUser['address']));
  $Profile->AddChild(new Tag('Phone',SPrintF('%s%s',$dUser['thecode'],$dUser['phone'])));
  $Profile->AddChild(new Tag('Email',$dUser['email']));
  #-----------------------------------------------------------------------------
  $Contract->AddChild($Profile);
  #-----------------------------------------------------------------------------
  $Result = $Link->Query(SPrintF("SELECT * FROM `payments` WHERE `userid` = %u AND `thestatus` = 'PAID'",$dUser['ID']));
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
    $Merchant = $dInvoice['merchant'];
    #---------------------------------------------------------------------------
    switch($Merchant){
      case 'WM_Merchant':
       $PaymentSystemID = 'WebMoneyZ';
      break;
      case 'YA_Merchant':
       $PaymentSystemID = 'Yandex';
      break;
      case 'FIZ_Bank':
       $PaymentSystemID = 'Natural';
      break;
      default:
       $PaymentSystemID = 'InOffice';
      break;
    }
    #---------------------------------------------------------------------------
    $Invoice = new Tag('Invoice');
    #---------------------------------------------------------------------------
    $Invoice->AddChild(new Tag('CreateDate',$dInvoice['invoice_date']));
    $Invoice->AddChild(new Tag('PaymentSystemID',$PaymentSystemID));
    $Invoice->AddChild(new Tag('Summ',Round($dInvoice['money']*$dInvoice['course'],2)));
    $Invoice->AddChild(new Tag('PayDate',$dInvoice['invoice_date']));
    $Invoice->AddChild(new Tag('Items'));
    #---------------------------------------------------------------------------
    $Invoices->AddChild($Invoice);
  }
  #-----------------------------------------------------------------------------
  $Contract->AddChild($Invoices);
  #-----------------------------------------------------------------------------
  $Result = $Link->Query(SPrintF("SELECT * FROM `host` WHERE `domen` = '%s'",$dUser['domen']));
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
    $Term = (integer)$dUser['term'];
    #---------------------------------------------------------------------------
    if(!$Term)
      continue;
    #---------------------------------------------------------------------------
    $DaysRemainded = (integer)((JulianDayToGregorian($Term) - Time())/86400);
    #---------------------------------------------------------------------------
    $HostingOrder = new Tag('HostingOrder');
    #---------------------------------------------------------------------------
    $HostingOrder->AddChild(new Tag('OrderDate',$dHostingOrder['thedate']));
    $HostingOrder->AddChild(new Tag('Login',$dHostingOrder['login']));
    $HostingOrder->AddChild(new Tag('Password',$dHostingOrder['pass']));
    $HostingOrder->AddChild(new Tag('Domain',$dHostingOrder['domen']));
    $HostingOrder->AddChild(new Tag('DaysRemainded',$DaysRemainded));
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF("SELECT * FROM `tarifhost` WHERE `ID` = %u AND `thetype` != 'DEDICATED'",$dUser['plan']));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dHostingScheme = MySQL::Result($Result);
    if(Is_Error($dHostingScheme))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!Count($dHostingScheme)){
      #-------------------------------------------------------------------------
      echo SPrintF('<DIV style="color:red;">Заказ хостинга (%s) ссылается на несуществующий тарифный план</DIV>',$dHostingOrder['login']);
      #-------------------------------------------------------------------------
      continue;
    }
    #---------------------------------------------------------------------------
    $dHostingScheme = Current($dHostingScheme);
    #---------------------------------------------------------------------------
    foreach(Array_Keys($dHostingScheme) as $ColumnID){
      #-------------------------------------------------------------------------
      $Column = &$dHostingScheme[$ColumnID];
      #-------------------------------------------------------------------------
      $Column = ($Column != 'unlimited'?Mb_Convert_Encoding($Column,'UTF-8',$Charset):99999);
    }
    #---------------------------------------------------------------------------
    $HostingScheme = new Tag('HostingScheme');
    #---------------------------------------------------------------------------
    $HostingScheme->AddChild(new Tag('ServersGroup',new Tag('Name','По умолчанию')));
    $HostingScheme->AddChild(new Tag('Name',$dHostingScheme['name']));
    $HostingScheme->AddChild(new Tag('PackageID',$dHostingScheme['whmname']));
    $HostingScheme->AddChild(new Tag('CostDay',Round($dHostingScheme['cost']/$Course,2)));
    $HostingScheme->AddChild(new Tag('CostMonth',$dHostingScheme['cost']));
    $HostingScheme->AddChild(new Tag('IsReselling',$dHostingScheme['thetype'] != 'HOSTING'?1:0));
    $HostingScheme->AddChild(new Tag('IsActive',$dHostingScheme['thestatus'] != 'On'?0:1));
    $HostingScheme->AddChild(new Tag('QuotaDisk',$dHostingScheme['diskspace']));
    $HostingScheme->AddChild(new Tag('QuotaDBs',$dHostingScheme['mysql']));
    $HostingScheme->AddChild(new Tag('QuotaFTP',$dHostingScheme['ftp']));
    $HostingScheme->AddChild(new Tag('QuotaEmail',$dHostingScheme['email']));
    $HostingScheme->AddChild(new Tag('QuotaEmailLists',$dHostingScheme['dispatch']));
    $HostingScheme->AddChild(new Tag('QuotaSubDomains',$dHostingScheme['subdomens']));
    $HostingScheme->AddChild(new Tag('QuotaTraffic',$dHostingScheme['trafic']));
    $HostingScheme->AddChild(new Tag('QuotaParkDomains',$dHostingScheme['parking']));
    $HostingScheme->AddChild(new Tag('QuotaAddonDomains',$dHostingScheme['addon']));
    #---------------------------------------------------------------------------
    $HostingOrder->AddChild($HostingScheme);
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF('SELECT * FROM `server` WHERE `ID` = %u',$dHostingScheme['server']));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dHostingServer = MySQL::Result($Result);
    if(Is_Error($dHostingServer))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!Count($dHostingServer)){
      #-------------------------------------------------------------------------
      echo SPrintF('<DIV style="color:red;">Заказ хостинга (%s) ссылается на несуществующий сервер</DIV>',$dHostingOrder['login']);
      #-------------------------------------------------------------------------
      continue;
    }
    #---------------------------------------------------------------------------
    $dHostingServer = Current($dHostingServer);
    #---------------------------------------------------------------------------
    foreach(Array_Keys($dHostingServer) as $ColumnID){
      #-------------------------------------------------------------------------
      $Column = &$dHostingServer[$ColumnID];
      #-------------------------------------------------------------------------
      $Column = Mb_Convert_Encoding($Column,'UTF-8',$Charset);
    }
    #---------------------------------------------------------------------------
    $HostingServer = new Tag('HostingServer');
    #---------------------------------------------------------------------------
    $HostingServer->AddChild(new Tag('Address',$dHostingServer['name']));
    $HostingServer->AddChild(new Tag('Login',$dHostingServer['reslogin']));
    $HostingServer->AddChild(new Tag('Password',''));
    $HostingServer->AddChild(new Tag('IP',$dHostingServer['ip']));
    $HostingServer->AddChild(new Tag('Ns1Name',$dHostingServer['ns1']));
    $HostingServer->AddChild(new Tag('Ns2Name',$dHostingServer['ns2']));
    $HostingServer->AddChild(new Tag('Ns3Name',$dHostingServer['ns3']));
    #---------------------------------------------------------------------------
    Debug("[comp/www/Administrator/Bpanel]: dHostingServer panel = " . $dHostingServer['panel']);
    switch($dHostingServer['panel']){
      case 'cPanel':
       #------------------------------------------------------------------------
       $HostingServer->AddChild(new Tag('SystemID','Cpanel'));
       $HostingServer->AddChild(new Tag('Url',SPrintF('http://%s:2082',$dHostingServer['name'])));
       $HostingServer->AddChild(new Tag('Protocol','ssl'));
       $HostingServer->AddChild(new Tag('Port',2087));
      break;
      case 'ISPmanager':
        $HostingServer->AddChild(new Tag('SystemID','IspManager'));
	$HostingServer->AddChild(new Tag('Url',SPrintF('https://%s/manager/',$dHostingServer['name'])));
	$HostingServer->AddChild(new Tag('Protocol','ssl'));
	$HostingServer->AddChild(new Tag('Port',443));
      break;
      case 'Plesk':
        $HostingServer->AddChild(new Tag('SystemID','Plesk'));
        $HostingServer->AddChild(new Tag('Url',SPrintF('https://%s:8443',$dHostingServer['name'])));
        $HostingServer->AddChild(new Tag('Protocol','ssl'));
        $HostingServer->AddChild(new Tag('Port',8443));
      break;
      case 'DirectAdmin':
        $HostingServer->AddChild(new Tag('SystemID','DirectAdmin'));
        $HostingServer->AddChild(new Tag('Url',SPrintF('https://%s:2222',$dHostingServer['name'])));
        $HostingServer->AddChild(new Tag('Protocol','ssl'));
        $HostingServer->AddChild(new Tag('Port',2222));
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
  $Contract->AddChild($HostingOrders);
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
    foreach(Array_Keys($dDomainsOrder) as $ColumnID){
      #-------------------------------------------------------------------------
      $Column = &$dDomainsOrder[$ColumnID];
      #-------------------------------------------------------------------------
      $Column = Mb_Convert_Encoding($Column,'UTF-8',$Charset);
    }
    #---------------------------------------------------------------------------
    $Domain = $dDomainsOrder['domen'];
    #---------------------------------------------------------------------------
    $DomainOrder = new Tag('DomainOrder');
    #---------------------------------------------------------------------------
    $DomainOrder->AddChild(new Tag('OrderDate',$dDomainsOrder['dateorder']));
    $DomainOrder->AddChild(new Tag('ExpirationDate',(string)JulianDayToGregorian($dDomainsOrder['reg'])));
    #---------------------------------------------------------------------------
    $Parse = WhoIs_Parse($Domain);
    #---------------------------------------------------------------------------
    switch(ValueOf($Parse)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'false':
        #-----------------------------------------------------------------------
        $DomainName = SubStr($Domain,0,$Index = StrRpos($Domain,'.'));
        $DomainZone = SubStr($Domain,$Index+1);
      break;
      case 'array':
        #-----------------------------------------------------------------------
        $DomainName = $Parse['DomainName'];
        $DomainZone = $Parse['DomainZone'];
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $DomainOrder->AddChild(new Tag('DomainName',$DomainName));
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF("SELECT * FROM `tarifdom` WHERE `name` = '.%s'",$DomainZone));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dDomainScheme = MySQL::Result($Result);
    if(Is_Error($dDomainScheme))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!Count($dDomainScheme)){
      #-------------------------------------------------------------------------
      echo SPrintF('<DIV style="color:red;">Заказ домен (%s) ссылается на несуществующий тарифный план</DIV>',$dDomainsOrder['domen']);
      #-------------------------------------------------------------------------
      continue;
    }
    #---------------------------------------------------------------------------
    $dDomainScheme = Current($dDomainScheme);
    #---------------------------------------------------------------------------
    foreach(Array_Keys($dDomainScheme) as $ColumnID){
      #-------------------------------------------------------------------------
      $Column = &$dDomainScheme[$ColumnID];
      #-------------------------------------------------------------------------
      $Column = Mb_Convert_Encoding($Column,'UTF-8',$Charset);
    }
    #---------------------------------------------------------------------------
    $DomainScheme = new Tag('DomainScheme');
    #---------------------------------------------------------------------------
    $DomainScheme->AddChild(new Tag('DomainZone',$DomainZone));
    $DomainScheme->AddChild(new Tag('CostOrder',Round($dDomainScheme['cost']*$Course,2)));
    $DomainScheme->AddChild(new Tag('CostProlong',Round($dDomainScheme['prolongcost']*$Course)));
    #---------------------------------------------------------------------------
    $Registrator = new Tag('Registrator');
    #---------------------------------------------------------------------------
    switch($dDomainScheme['registrar']){
      case 'directi':
        #-----------------------------------------------------------------------
        $Registrator->AddChild(new Tag('Name','Directi'));
        $Registrator->AddChild(new Tag('TypeID','LogicBoxes'));
        $Registrator->AddChild(new Tag('Address','logicboxes.com'));
        $Registrator->AddChild(new Tag('Port',1000));
        $Registrator->AddChild(new Tag('Protocol','ssl'));
        $Registrator->AddChild(new Tag('Login','user'));
      break;
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
  $Result = $Link->Query(SPrintF("SELECT * FROM `h_tickets` WHERE `userid` = %u AND `thetype` = 'CUSTOMER'",$dUser['ID']));
  if(Is_Error($Result))
    return $Link->GetError();
  #-----------------------------------------------------------------------------
  $dTickets = MySQL::Result($Result);
  if(Is_Error($dTickets))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Tickets = new Tag('Tickets');
  #-----------------------------------------------------------------------------
  foreach($dTickets as $dTicket){
    #---------------------------------------------------------------------------
    foreach(Array_Keys($dTicket) as $ColumnID){
      #-------------------------------------------------------------------------
      $Column = &$dTicket[$ColumnID];
      #-------------------------------------------------------------------------
      $Column = Mb_Convert_Encoding($Column,'UTF-8',$Charset);
    }
    #---------------------------------------------------------------------------
    $Dept = (integer)$dTicket['dept'];
    #---------------------------------------------------------------------------
    $Ticket = new Tag('Ticket');
    #---------------------------------------------------------------------------
    $Ticket->AddChild(new Tag('CreateDate',$dTicket['thedate']));
    $Ticket->AddChild(new Tag('PriorityID',($Dept > 1?($Dept > 2?'Hight':'Middle'):'Low')));
    $Ticket->AddChild(new Tag('Theme',$dTicket['subj']));
    $Ticket->AddChild(new Tag('StatusID',$dTicket['thestatus'] != 'OPENED'?'Closed':'Opened'));
    #---------------------------------------------------------------------------
    $Result = $Link->Query(SPrintF('SELECT * FROM `h_answers` WHERE `ticket` = %u',$dTicket['ID']));
    if(Is_Error($Result))
      return $Link->GetError();
    #---------------------------------------------------------------------------
    $dMessages = MySQL::Result($Result);
    if(Is_Error($dMessages))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!Count($dMessages))
      continue;
    #---------------------------------------------------------------------------
    $Messages = new Tag('Messages');
    #---------------------------------------------------------------------------
    foreach($dMessages as $dMessage){
      #-------------------------------------------------------------------------
      foreach(Array_Keys($dMessage) as $ColumnID){
        #-----------------------------------------------------------------------
        $Column = &$dMessage[$ColumnID];
        #-----------------------------------------------------------------------
        $Column = Mb_Convert_Encoding($Column,'UTF-8',$Charset);
      }
      #-------------------------------------------------------------------------
      $Message = new Tag('Message');
      #-------------------------------------------------------------------------
      $Message->AddChild(new Tag('CreateDate',$dMessage['thedate']));
      $Message->AddChild(new Tag('IsSupport',$dMessage['thetype'] != 'CUSTOMER'?1:0));
      $Message->AddChild(new Tag('Content',Base64_Encode($dMessage['comments'])));
      #-------------------------------------------------------------------------
      $Messages->AddChild($Message);
    }
    #---------------------------------------------------------------------------
    $Ticket->AddChild($Messages);
    #---------------------------------------------------------------------------
    $Tickets->AddChild($Ticket);
  }
  #-----------------------------------------------------------------------------
  $User->AddChild($Tickets);
  #-----------------------------------------------------------------------------
  $Users->AddChild($User);
}
#-------------------------------------------------------------------------------
$Dump = $Users->ToXMLString();
#-------------------------------------------------------------------------------
$Tmp = System_Element('tmp');
if(Is_Error($Tmp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$File = SPrintF('Bpanel[%s%s].xml.gz',Md5($_SERVER['REMOTE_ADDR']),Date('d.m.Y'));
#-------------------------------------------------------------------------------
$IsWrite = IO_Write(SPrintF('%s/files/%s',$Tmp,$File),GzEncode($Dump),TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
echo SPrintF('<A href="/GetTemp?File=%s&Name=Bpanel.xml.gz&Mime=application/gzip">[Сохранить файл базы данных]</A>',$File);
#-------------------------------------------------------------------------------
echo <<<EOD
 </BODY>
</HTML>
EOD;
#-------------------------------------------------------------------------------

?>
