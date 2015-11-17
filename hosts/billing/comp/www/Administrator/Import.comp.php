<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$IsDelete = (boolean) @$Args['IsDelete'];
#-------------------------------------------------------------------------------
Header('Content-type: text/plain; charset=utf-8');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Upload = Upload_Get('DataImport');
#-------------------------------------------------------------------------------
switch(ValueOf($Upload)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return 'Файл базы данных не был загружен';
  case 'array':
    #---------------------------------------------------------------------------
    $Data = $Upload['Data'];
    #---------------------------------------------------------------------------
    if($gZip = @GzInflate(SubStr($Upload['Data'],10))){
      #-------------------------------------------------------------------------
      echo "Файл базы данных является сжатым файлом gzip\n";
      #-------------------------------------------------------------------------
      $Data = $gZip;
    }else
      echo "Файл базы данных не является сжатым файлом gzip\n";
    #---------------------------------------------------------------------------
    $File = rTrim($Upload['Name'],'.gz');
    #---------------------------------------------------------------------------
    $File = PathInfo($File);
    #---------------------------------------------------------------------------
    switch(StrToLower($File['extension'])){
      case 'xml':
        #-----------------------------------------------------------------------
        $Data = String_XML_Parse($Data);
        if(Is_Exception($Data))
          return SPrintF('Ошибка чтения базы данных: (%s)',$Data->String);
        #-----------------------------------------------------------------------
      break;
      case 'serialize':
        #-----------------------------------------------------------------------
        $Data = UnSerialize($Data);
        if(!$Data)
          return 'Ошибка чтения базы данных';
        #-----------------------------------------------------------------------
      break;
      default:
        return 'Не верный формат файла';
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Data = $Data->ToArray('User','Contract','Invoice','HostingOrder','DomainOrder','Ticket','Message');
#-------------------------------------------------------------------------------
$Users = $Data['Users'];
#-------------------------------------------------------------------------------
$Users = (Is_Array($Users)?$Users:Array());
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
echo "Сканирование базы данных\n";
#-------------------------------------------------------------------------------
foreach($Users as $User){
  #-----------------------------------------------------------------------------
  $Email = $User['Email'];
  #-----------------------------------------------------------------------------
  if($IsDelete){
    #---------------------------------------------------------------------------
    echo SPrintF("Удаление пользователя (%s)\n",$Email);
    #---------------------------------------------------------------------------
    $IsOk = DB_Delete('Users',Array('Where'=>SPrintF("`Email` = '%s' AND `ID` != 100",$Email)));
    if(Is_Error($IsOk))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    continue;
  }else
    echo SPrintF("Импорт пользователя (%s)\n",$Email);
  #-----------------------------------------------------------------------------
  $dUser = DB_Select('Users','ID',Array('Where'=>SPrintF("`Email` = '%s'",$Email)));
  #-----------------------------------------------------------------------------
  switch(ValueOf($dUser)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      #-------------------------------------------------------------------------
      $Name = $User['Name'];
      #-------------------------------------------------------------------------
      $Settings = $Config['Interface']['User']['Register'];
      #-------------------------------------------------------------------------
      $IUser = Array(
        #-----------------------------------------------------------------------
        'RegisterDate'    => GetTime($User['RegisterDate']),
        'OwnerID'         => 100,
        'IsManaged'       => FALSE,
        'Name'            => $Name,
        'Sign'            => SPrintF('%s %s.',$Settings['Sign'],$Name),
        'Watchword'       => $User['Password'],
        'EnterDate'       => GetTime(@$User['EnterDate']),
        'EnterIP'         => (string)@$User['EnterIP'],
        'Email'           => $Email,
        'Mobile'          => (string)@$User['Mobile'],
        'LayPayMaxDays'   => $Settings['LayPayMaxDays'],
        'LayPayMaxSumm'   => $Settings['LayPayMaxSumm'],
        'LayPayThreshold' => $Settings['LayPayThreshold']
      );
      #-------------------------------------------------------------------------
      $Group = DB_Select('Groups','ID',Array('UNIQ','Where'=>"`IsDefault` = 'yes'"));
      #-------------------------------------------------------------------------
      switch(ValueOf($Group)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'array':
          $IUser['GroupID'] = $Group['ID'];
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      if(Is_Error(DB_Transaction($TransactionID = UniqID('Import'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $UserID = DB_Insert('Users',$IUser);
      if(Is_Error($UserID))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Contracts = $User['Contracts'];
      #-------------------------------------------------------------------------
      $Contracts = (Is_Array($Contracts)?$Contracts:Array());
      #-------------------------------------------------------------------------
      foreach($Contracts as $Contract){
        #-----------------------------------------------------------------------
        $TypeID = $Contract['TypeID'];
        #-----------------------------------------------------------------------
        $IContract = Array(
          #---------------------------------------------------------------------
          'CreateDate' => GetTime($User['RegisterDate']),
          'UserID'     => $UserID,
          'TypeID'     => $TypeID,
          'Customer'   => $Contract['Customer'],
          'Balance'    => $Contract['Balance'],
          'StatusID'   => 'OnForming',
          'StatusDate' => GetTime($User['RegisterDate'])
        );
        #-----------------------------------------------------------------------
        if(IsSet($Contract['Profile'])){
          #---------------------------------------------------------------------
          $pAttribs = (array)$Contract['Profile'];
          #---------------------------------------------------------------------
          $Template = System_XML(SPrintF('profiles/%s.xml',$TypeID));
          if(Is_Error($Template))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $tAttribs = $Template['Attribs'];
          #---------------------------------------------------------------------
          foreach(Array_Keys($tAttribs) as $AttribID){
            #-------------------------------------------------------------------
            if(!IsSet($pAttribs[$AttribID]))
              $pAttribs[$AttribID] = $tAttribs[$AttribID]['Value'];
          }
          #---------------------------------------------------------------------
          $IProfile = Array(
            #-------------------------------------------------------------------
            'CreateDate' => GetTime($User['RegisterDate']),
            'UserID'     => $UserID,
            'Name'       => $Contract['Customer'],
            'TemplateID' => $TypeID,
            'IsDefault'  => TRUE,
            'Attribs'    => $pAttribs,
            'StatusID'   => 'OnFilling',
            'StatusDate' => GetTime($User['RegisterDate'])
          );
          #---------------------------------------------------------------------
          $ProfileID = DB_Insert('Profiles',$IProfile);
          if(Is_Error($ProfileID))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $IContract['ProfileID'] = $ProfileID;
        }
        #-----------------------------------------------------------------------
        $ContractID = DB_Insert('Contracts',$IContract);
        if(Is_Error($ContractID))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Contracts/Build',$ContractID);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Invoices = $Contract['Invoices'];
        #-----------------------------------------------------------------------
        $Invoices = (Is_Array($Invoices)?$Invoices:Array());
        #-----------------------------------------------------------------------
        foreach($Invoices as $Invoice){
          #---------------------------------------------------------------------
          $IInvoice = Array(
            #-------------------------------------------------------------------
            'ID'              => @$Invoice['ID'],
            'CreateDate'      => GetTime($Invoice['CreateDate']),
            'ContractID'      => $ContractID,
            'PaymentSystemID' => $Invoice['PaymentSystemID'],
            'Summ'            => $Invoice['Summ'],
            'IsPosted'        => TRUE,
            'StatusID'        => 'Payed',
            'StatusDate'      => GetTime($Invoice['PayDate'])
          );
          #---------------------------------------------------------------------
          $InvoiceID = DB_Insert('Invoices',$IInvoice);
          if(Is_Error($InvoiceID))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Items = $Invoice['Items'];
          #---------------------------------------------------------------------
          $Items = (Is_Array($Items)?$Items:Array());
          #---------------------------------------------------------------------
          if(Count($Items)){
            #-------------------------------------------------------------------
            foreach($Items as $Item){
              #-----------------------------------------------------------------
              $IItem = Array(
                #---------------------------------------------------------------
                'InvoiceID' => $InvoiceID,
                'ServiceID' => $Item['ServiceID'],
                'Amount'    => $Item['Amount'],
                'Summ'      => $Item['Summ']
              );
              #-----------------------------------------------------------------
              $IsInsert = DB_Insert('InvoicesItems',$IItem);
              if(Is_Error($IsInsert))
                return ERROR | @Trigger_Error(500);
            }
          }else{
            #-------------------------------------------------------------------
            $IItem = Array(
              #-----------------------------------------------------------------
              'InvoiceID' => $InvoiceID,
              'ServiceID' => 1000,
              'Amount'    => 1,
              'Summ'      => $Invoice['Summ']
            );
            #-------------------------------------------------------------------
            $IsInsert = DB_Insert('InvoicesItems',$IItem);
            if(Is_Error($IsInsert))
              return ERROR | @Trigger_Error(500);
          }
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Invoices/Build',$InvoiceID);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
        }
        #-----------------------------------------------------------------------
        if(IsSet($Contract['HostingOrders'])){
          #---------------------------------------------------------------------
          $HostingOrders = $Contract['HostingOrders'];
          #---------------------------------------------------------------------
          $HostingOrders = (Is_Array($HostingOrders)?$HostingOrders:Array());
          #---------------------------------------------------------------------
          foreach($HostingOrders as $HostingOrder){
            #-------------------------------------------------------------------
            $DaysRemainded = Max(0,(integer)$HostingOrder['DaysRemainded']);
            #-------------------------------------------------------------------
            $IHostingOrder = Array(
              #-----------------------------------------------------------------
              'Login'      => $HostingOrder['Login'],
              'Password'   => $HostingOrder['Password'],
              'Domain'     => $HostingOrder['Domain'],
              'StatusID'   => ($DaysRemainded?'Active':'Suspended'),
              'StatusDate' => GetTime($HostingOrder['OrderDate'])
            );
            #-------------------------------------------------------------------
            $HostingScheme = $HostingOrder['HostingScheme'];
            #-------------------------------------------------------------------
            $HostingScheme = (Is_Array($HostingScheme)?$HostingScheme:Array());
            #-------------------------------------------------------------------
            $dHostingScheme = DB_Select('HostingSchemes',Array('ID','ServersGroupID','CostDay'),Array('Where'=>SPrintF("`Name` = '%s'",$HostingScheme['Name'])));
            #-------------------------------------------------------------------
            switch(ValueOf($dHostingScheme)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $CostDay = $HostingScheme['CostDay'];
                #---------------------------------------------------------------
                $IHostingScheme = Array(
                  #-------------------------------------------------------------
                  'GroupID'            => 2000000,
                  'UserID'             => 100,
                  'Name'               => $HostingScheme['Name'],
                  'PackageID'          => $HostingScheme['PackageID'],
                  'IsReselling'        => $HostingScheme['IsReselling'],
                  'IsActive'           => $HostingScheme['IsActive'],
                  'QuotaDisk'          => $HostingScheme['QuotaDisk'],
                  'CostDay'            => $CostDay,
                  'QuotaDisk'          => (integer)@$HostingScheme['QuotaDisk'],
                  'QuotaEmail'         => (integer)@$HostingScheme['QuotaEmail'],
                  'QuotaDomains'       => (integer)@$HostingScheme['QuotaDomains'],
                  'QuotaFTP'           => (integer)@$HostingScheme['QuotaFTP'],
                  'QuotaParkDomains'   => (integer)@$HostingScheme['QuotaParkDomains'],
                  'QuotaSubDomains'    => (integer)@$HostingScheme['QuotaSubDomains'],
                  'QuotaDBs'           => (integer)@$HostingScheme['QuotaDBs'],
                  'QuotaTraffic'       => (integer)@$HostingScheme['QuotaTraffic'],
                  'QuotaEmailAutoResp' => (integer)@$HostingScheme['QuotaEmailAutoResp'],
                  'QuotaEmailLists'    => (integer)@$HostingScheme['QuotaEmailLists'],
                  'QuotaUsers'         => (integer)@$HostingScheme['QuotaUsers'],
                  'IsShellAccess'      => (boolean)@$HostingScheme['IsShellAccess'],
                  'IsSSLAccess'        => (boolean)@$HostingScheme['IsSSLAccess'],
                  'IsCGIAccess'        => (boolean)@$HostingScheme['IsCGIAccess'],
                  'IsDnsControll'      => (boolean)@$HostingScheme['IsDnsControll'],

                  'QuotaWWWDomains'    => (integer)@$HostingScheme['QuotaWWWDomains'],
                  'QuotaEmailDomains'  => (integer)@$HostingScheme['QuotaEmailDomains'],
                  'QuotaUsersDBs'      => (integer)@$HostingScheme['QuotaUsersDBs'],
                  'QuotaCPU'           => (integer)@$HostingScheme['QuotaCPU'],
                  'QuotaMEM'           => (integer)@$HostingScheme['QuotaMEM'],
                  'QuotaPROC'          => (integer)@$HostingScheme['QuotaPROC'],
                  'IsSSIAccess'        => (boolean)@$HostingScheme['IsSSIAccess'],
                  'IsPHPModAccess'     => (boolean)@$HostingScheme['IsPHPModAccess'],
                  'IsPHPCGIAccess'     => (boolean)@$HostingScheme['IsPHPCGIAccess'],
                  'IsPHPFastCGIAccess' => (boolean)@$HostingScheme['IsPHPFastCGIAccess'],
                  'IsPHPSafeMode'      => (boolean)@$HostingScheme['IsPHPSafeMode'],

                  'QuotaAddonDomains'  => (integer)@$HostingScheme['QuotaAddonDomains'],

                  'QuotaWebUsers'      => (integer)@$HostingScheme['QuotaWebUsers'],
                  'QuotaEmailBox'      => (integer)@$HostingScheme['QuotaEmailBox'],
                  'QuotaEmailGroups'   => (integer)@$HostingScheme['QuotaEmailGroups'],
                  'QuotaWebApp'        => (integer)@$HostingScheme['QuotaWebApp'],
                  'QuotaEmailForwards' => (integer)@$HostingScheme['QuotaEmailForwards'],
                  'IsAnonimousFTP'     => (boolean)@$HostingScheme['IsAnonimousFTP'],
                  'IsPHPAccess'        => (boolean)@$HostingScheme['IsPHPAccess'],
                  'IsSpamAssasing'     => (boolean)@$HostingScheme['IsSpamAssasing'],
                  'IsCatchAll'         => (boolean)@$HostingScheme['IsCatchAll'],
                  'IsSystemInfo'       => (boolean)@$HostingScheme['IsSystemInfo']
                );
                #---------------------------------------------------------------
                $ServersGroup = $HostingScheme['ServersGroup'];
                #---------------------------------------------------------------
                $ServersGroup = (Is_Array($ServersGroup)?$ServersGroup:Array());
                #---------------------------------------------------------------
                $dServersGroup = DB_Select('HostingServersGroups','ID',Array('Where'=>SPrintF("`Name` = '%s'",$ServersGroup['Name'])));
                #---------------------------------------------------------------
                switch(ValueOf($dServersGroup)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    #-----------------------------------------------------------
                    $IServersGroup = Array('Name'=>$ServersGroup['Name'],'Comment'=>'Россия, M9');
                    #-----------------------------------------------------------
                    $ServersGroupID = DB_Insert('HostingServersGroups',$IServersGroup);
                    if(Is_Error($ServersGroupID))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    $IHostingScheme['ServersGroupID'] = $ServersGroupID;
                  break;
                  case 'array':
                    #-----------------------------------------------------------
                    $dServersGroup = Current($dServersGroup);
                    #-----------------------------------------------------------
                    $ServersGroupID  = $IHostingScheme['ServersGroupID'] = $dServersGroup['ID'];
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
                #---------------------------------------------------------------
                $HostingSchemeID = DB_Insert('HostingSchemes',$IHostingScheme);
                if(Is_Error($HostingSchemeID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IHostingOrder['SchemeID'] = $HostingSchemeID;
              break;
              case 'array':
                #---------------------------------------------------------------
                $dHostingScheme = Current($dHostingScheme);
                #---------------------------------------------------------------
                $CostDay = $dHostingScheme['CostDay'];
                #---------------------------------------------------------------
                $IHostingOrder['SchemeID'] = $dHostingScheme['ID'];
                #---------------------------------------------------------------
                $ServersGroupID = $dHostingScheme['ServersGroupID'];
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $HostingServer = $HostingOrder['HostingServer'];
            #-------------------------------------------------------------------
            $HostingServer = (Is_Array($HostingServer)?$HostingServer:Array());
            #-------------------------------------------------------------------
            $dHostingServer = DB_Select('HostingServers','ID',Array('UNIQ','Where'=>SPrintF("`Address` = '%s'",$HostingServer['Address'])));
            #-------------------------------------------------------------------
            switch(ValueOf($dHostingServer)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $IHostingServer = Array(
                  #-------------------------------------------------------------
                  'ServersGroupID' => $ServersGroupID,
                  #-------------------------------------------------------------
                  'Address'   => $HostingServer['Address'],
                  'IsDefault' => TRUE,
                  'Login'     => $HostingServer['Login'],
                  'Password'  => 'Default',
                  'SystemID'  => $HostingServer['SystemID'],
                  'Url'       => $HostingServer['Url'],
                  'Protocol'  => $HostingServer['Protocol'],
                  'Port'      => $HostingServer['Port'],
                  'IP'        => $HostingServer['IP'],
                  'Ns1Name'   => (string)@$HostingServer['Ns1Name'],
                  'Ns2Name'   => (string)@$HostingServer['Ns2Name'],
                  'Ns3Name'   => (string)@$HostingServer['Ns3Name'],
                  'Ns4Name'   => (string)@$HostingServer['Ns4Name']
                );
                #---------------------------------------------------------------
                $HostingServerID = DB_Insert('HostingServers',$IHostingServer);
                if(Is_Error($HostingServerID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IHostingOrder['ServerID'] = $HostingServerID;
              break;
              case 'array':
                $IHostingOrder['ServerID'] = $dHostingServer['ID'];
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $OrderID = DB_Insert('Orders',Array('OrderDate'=>GetTime($HostingOrder['OrderDate']),'ContractID'=>$ContractID,'ServiceID'=>10000));
            if(Is_Error($OrderID))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $IHostingOrder['OrderID'] = $OrderID;
            #-------------------------------------------------------------------
            $HostingOrderID = DB_Insert('HostingOrders',$IHostingOrder);
            if(Is_Error($HostingOrderID))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            if($DaysRemainded){
              #-----------------------------------------------------------------
              $IsInsert = DB_Insert('HostingConsider',Array('HostingOrderID'=>$HostingOrderID,'DaysReserved'=>$DaysRemainded,'Cost'=>$CostDay,'Discont'=>1));
              if(Is_Error($IsInsert))
                return ERROR | @Trigger_Error(500);
            }
          }
        }
        #-----------------------------------------------------------------------
        if(IsSet($Contract['DomainsOrders'])){
          #---------------------------------------------------------------------
          $DomainsOrders = $Contract['DomainsOrders'];
          #---------------------------------------------------------------------
          $DomainsOrders = (Is_Array($DomainsOrders)?$DomainsOrders:Array());
          #---------------------------------------------------------------------
          foreach($DomainsOrders as $DomainOrder){
            #-------------------------------------------------------------------
            $IDomainOrder = Array(
              #-----------------------------------------------------------------
              'DomainName'     => $DomainOrder['DomainName'],
              'ExpirationDate' => GetTime($DomainOrder['ExpirationDate']),
              'StatusID'       => 'Active',
              'StatusDate'     => GetTime($DomainOrder['OrderDate'])
            );
            #-------------------------------------------------------------------
            $DomainScheme = $DomainOrder['DomainScheme'];
            #-------------------------------------------------------------------
            $DomainScheme = (Is_Array($DomainScheme)?$DomainScheme:Array());
            #-------------------------------------------------------------------
            $dDomainScheme = DB_Select('DomainsSchemes','ID',Array('Where'=>SPrintF("`Name` = '%s'",$DomainScheme['DomainZone'])));
            #-------------------------------------------------------------------
            switch(ValueOf($dDomainScheme)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $IDomainScheme = Array(
                  #-------------------------------------------------------------
                  'GroupID'        => 2000000,
                  'UserID'         => 100,
                  'Name'           => $DomainScheme['DomainZone'],
                  'CostOrder'      => $DomainScheme['CostOrder'],
                  'CostProlong'    => $DomainScheme['CostProlong'],
                  'MinOrderYears'  => 1,
                  'MaxActionYears' => 1,
                  'DaysToProlong'  => 31
                );
                #---------------------------------------------------------------
                $Registrator = $DomainScheme['Registrator'];
                #---------------------------------------------------------------
                $Registrator = (Is_Array($Registrator)?$Registrator:Array());
                #---------------------------------------------------------------
                $dRegistrator = DB_Select('Registrators','ID',Array('UNIQ','Where'=>SPrintF("`Name` = '%s'",$Registrator['Name'])));
                #---------------------------------------------------------------
                switch(ValueOf($dRegistrator)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    #-----------------------------------------------------------
                    $IRegistrator = Array(
                      #---------------------------------------------------------
                      'Name'     => $Registrator['Name'],
                      'TypeID'   => $Registrator['TypeID'],
                      'Address'  => $Registrator['Address'],
                      'Port'     => $Registrator['Port'],
                      'Protocol' => $Registrator['Protocol'],
                      'Login'    => $Registrator['Login'],
                      'Password' => 'Default'
                    );
                    #-----------------------------------------------------------
                    $RegistratorID = DB_Insert('Registrators',$IRegistrator);
                    if(Is_Error($RegistratorID))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    $IDomainScheme['RegistratorID'] = $RegistratorID;
                  break;
                  case 'array':
                    #-----------------------------------------------------------
                    $IDomainScheme['RegistratorID'] = $dRegistrator['ID'];
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
                #---------------------------------------------------------------
                $DomainSchemeID = DB_Insert('DomainsSchemes',$IDomainScheme);
                if(Is_Error($DomainSchemeID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IDomainOrder['SchemeID'] = $DomainSchemeID;
              break;
              case 'array':
                #---------------------------------------------------------------
                $dDomainScheme = Current($dDomainScheme);
                #---------------------------------------------------------------
                $IDomainOrder['SchemeID'] = $dDomainScheme['ID'];
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $OrderID = DB_Insert('Orders',Array('OrderDate'=>GetTime($DomainOrder['OrderDate']),'ContractID'=>$ContractID,'ServiceID'=>20000));
            if(Is_Error($OrderID))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $IDomainOrder['OrderID'] = $OrderID;
            #-------------------------------------------------------------------
            $DomainOrderID = DB_Insert('DomainsOrders',$IDomainOrder);
            if(Is_Error($DomainOrderID))
              return ERROR | @Trigger_Error(500);
          }
        }
      }
      #-------------------------------------------------------------------------
      if(IsSet($User['Tickets'])){
        #-----------------------------------------------------------------------
        $Tickets = $User['Tickets'];
        #-----------------------------------------------------------------------
        $Tickets = (Is_Array($Tickets)?$Tickets:Array());
        #-----------------------------------------------------------------------
        foreach($Tickets as $Ticket){
          #---------------------------------------------------------------------
          $ITicket = Array(
            #-------------------------------------------------------------------
            'CreateDate'    => GetTime($Ticket['CreateDate']),
            'UserID'        => $UserID,
            'TargetGroupID' => 3100000,
            'TargetUserID'  => 100,
            'PriorityID'    => $Ticket['PriorityID'],
            'Theme'         => $Ticket['Theme'],
            'UpdateDate'    => GetTime($Ticket['CreateDate']),
            'StatusID'      => $Ticket['StatusID'],
            'StatusDate'    => GetTime($Ticket['CreateDate'])
          );
          #---------------------------------------------------------------------
          $TicketID = DB_Insert('Edesks',$ITicket);
          if(Is_Error($TicketID))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Messages = $Ticket['Messages'];
          #---------------------------------------------------------------------
          foreach($Messages as $Message){
            #-------------------------------------------------------------------
            $IMessage = Array(
              #-----------------------------------------------------------------
              'CreateDate' => GetTime($Message['CreateDate']),
              'UserID'     => ($Message['IsSupport']?100:$UserID),
              'EdeskID'    => $TicketID,
              'Content'    => Base64_Decode($Message['Content'])
            );
            #-------------------------------------------------------------------
            $MessageID = DB_Insert('EdesksMessages',$IMessage);
            if(Is_Error($MessageID))
              return ERROR | @Trigger_Error(500);
          }
        }
      }
      #-------------------------------------------------------------------------
      if(IsSet($User['Profiles'])){
        #-----------------------------------------------------------------------
        $Profiles = $User['Profiles'];
        #-----------------------------------------------------------------------
        $Profiles = (Is_Array($Profiles)?$Profiles:Array());
        #-----------------------------------------------------------------------
        foreach($Profiles as $Profile){
          #---------------------------------------------------------------------
          $pAttribs = (array)$Profile['Attribs'];
          #---------------------------------------------------------------------
          $Template = System_XML(SPrintF('profiles/%s.xml',$Profile['TemplateID']));
          if(Is_Error($Template))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $tAttribs = $Template['Attribs'];
          #---------------------------------------------------------------------
          foreach(Array_Keys($tAttribs) as $AttribID){
            #-------------------------------------------------------------------
            if(!IsSet($pAttribs[$AttribID]))
              $pAttribs[$AttribID] = $tAttribs[$AttribID]['Value'];
          }
          #---------------------------------------------------------------------
          $Replace = Array_ToLine($pAttribs,'%');
          #---------------------------------------------------------------------
          $ProfileName = $Template['ProfileName'];
          #---------------------------------------------------------------------
          foreach(Array_Keys($Replace) as $Key)
            $ProfileName = Str_Replace($Key,$Replace[$Key],$ProfileName);
          #---------------------------------------------------------------------
          $IProfile = Array(
            #-------------------------------------------------------------------
            'CreateDate'    => GetTime($Profile['CreateDate']),
            'UserID'        => $UserID,
            'TemplateID'    => $Profile['TemplateID'],
            'Name'          => $ProfileName,
            'Attribs'       => $pAttribs,
            'StatusID'      => 'Checked',
            'StatusDate'    => GetTime($Profile['CreateDate'])
          );
          #---------------------------------------------------------------------
          $ProfileID = DB_Insert('Profiles',$IProfile);
          if(Is_Error($ProfileID))
            return ERROR | @Trigger_Error(500);
        }
      }
      #-------------------------------------------------------------------------
      if(Is_Error(DB_Commit($TransactionID)))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      echo "Успешно импортирован\n";
    break;
    case 'array':
      echo "Уже существует\n";
    continue 2;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
echo "Импорт завершен";
#-------------------------------------------------------------------------------

?>
