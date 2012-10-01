<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$DomainName     =  (string) @$Args['DomainName'];
$DomainSchemeID = (integer) @$Args['DomainSchemeID'];
$ContractID     = (integer) @$Args['ContractID'];
$HostingOrderID = (integer) @$Args['HostingOrderID'];
$IsPrivateWhoIs = (boolean) @$Args['IsPrivateWhoIs'];
$Ns1Name        =  (string) @$Args['Ns1Name'];
$Ns1IP          =  (string) @$Args['Ns1IP'];
$Ns2Name        =  (string) @$Args['Ns2Name'];
$Ns2IP          =  (string) @$Args['Ns2IP'];
$Ns3Name        =  (string) @$Args['Ns3Name'];
$Ns3IP          =  (string) @$Args['Ns3IP'];
$Ns4Name        =  (string) @$Args['Ns4Name'];
$Ns4IP          =  (string) @$Args['Ns4IP'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/WhoIs.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainName = Mb_StrToLower($DomainName,'UTF-8');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Ns1Name){
	if(Mb_StrToLower($Ns1Name,'UTF-8') == Mb_StrToLower($Ns2Name,'UTF-8')){
		return new gException('DNS_SERVERS_CANNOT_BE_EQUAL','Имена DNS серверов должны быть разными');
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Ns1IP){
	if($Ns1IP == $Ns2IP){
		return new gException('IP_DNS_SERVERS_CANNOT_BE_EQUAL','IP адреса DNS серверов должны быть разными');
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Mb_StrToLower($Ns1Name,'UTF-8') == $DomainName || Mb_StrToLower($Ns2Name,'UTF-8') == $DomainName || Mb_StrToLower($Ns3Name,'UTF-8') == $DomainName || Mb_StrToLower($Ns4Name,'UTF-8') == $DomainName)
	return new gException('NS_HOSTNAME_CANT_BE_EQUAL_TO_DOMAIN','Имя DNS сервера не может совпадать с именем домена');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['DomainName'],$DomainName))
  return new gException('WRONG_DOMAIN_NAME','Неверное имя домена');
#-------------------------------------------------------------------------------
$DomainScheme = DB_Select('DomainsSchemes',Array('ID','Name','IsActive'),Array('UNIQ','ID'=>$DomainSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DOMAIN_SCHEME_NOT_FOUND','Выбранный тарифный план не найден');
  case 'array':
    #---------------------------------------------------------------------------
    if(!$DomainScheme['IsActive'])
      return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа домена не активен');
    #---------------------------------------------------------------------------
    $Count = DB_Count('DomainsOrders',Array('Where'=>SPrintF("`DomainName` = '%s' AND (SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrders`.`SchemeID`) = '%s'",$DomainName,$DomainScheme['Name'])));
    if(Is_Error($Count))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if($Count)
      return new gException('DOMAIN_ORDER_EXISTS','Домен уже находиться в списке заказов');
    #---------------------------------------------------------------------------
    $IsCheck = WhoIs_Check($DomainName,$DomainScheme['Name']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsCheck)){
      case 'exception':
        return $IsCheck;
      break;
      case 'array':
        return new gException('DOMAIN_IS_BORROWED','Выбранный Вами домен уже занят. Выберите другое имя.');
      case 'error':
        # No more...
      case 'false':
        # No more...
      case 'true':
        #-----------------------------------------------------------------------
        $Domain = SPrintF('%s.%s',$DomainName,$DomainScheme['Name']);
        #-----------------------------------------------------------------------
        if($HostingOrderID){
          #---------------------------------------------------------------------
          $Columns = Array('ID','(SELECT `Ns1Name` FROM `HostingServers` WHERE `HostingServers`.`ID` = `ServerID`) as `Ns1Name`','(SELECT `Ns2Name` FROM `HostingServers` WHERE `HostingServers`.`ID` = `ServerID`) as `Ns2Name`');
          #---------------------------------------------------------------------
          $HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
          #---------------------------------------------------------------------
          switch(ValueOf($HostingOrder)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return new gException('HOSTING_ORDER_NOT_FOUND','Заказ хостинга не найден');
            case 'array':
              #-----------------------------------------------------------------
              $Ns1Name = $HostingOrder['Ns1Name'];
              $Ns2Name = $HostingOrder['Ns2Name'];
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        }else{
          #---------------------------------------------------------------------
          $Ns1Name = Trim(Mb_StrToLower($Ns1Name,'UTF-8'),'.');
          #NS1------------------------------------------------------------------
          if(!Preg_Match($Regulars['Domain'],$Ns1Name))
            return new gException('WRONG_NAME_NS1','Неверное имя первого сервера имен');
          #---------------------------------------------------------------------
          if(Mb_SubStr($Ns1Name,-Mb_StrLen($Domain)) == $Domain){
            #-------------------------------------------------------------------
            if(!Preg_Match($Regulars['IP'],$Ns1IP))
              return new gException('WRONG_IP_NS1','Неверный IP адрес первого сервера имен');
          }else{
            #-------------------------------------------------------------------
            if($Ns1IP){
              #-----------------------------------------------------------------
              return new gException('IP_NS1_CAN_NOT_FILL','IP адрес первого сервера имен не может быть указан');
            }
          }
          #---------------------------------------------------------------------
          $Ns2Name = Trim(Mb_StrToLower($Ns2Name,'UTF-8'),'.');
          #NS2------------------------------------------------------------------
          if(!Preg_Match($Regulars['Domain'],$Ns1Name))
            return new gException('WRONG_NAME_NS2','Неверное имя второго сервера имен');
          #---------------------------------------------------------------------
          if(Mb_SubStr($Ns2Name,-Mb_StrLen($Domain)) == $Domain){
            #-------------------------------------------------------------------
            if(!Preg_Match($Regulars['IP'],$Ns2IP))
              return new gException('WRONG_IP_NS2','Неверный IP адрес второго сервера имен');
          }else{
            #-------------------------------------------------------------------
            if($Ns2IP)
              return new gException('IP_NS2_CAN_NOT_FILL','IP адрес второго сервера имен не может быть указан');
          }
          #---------------------------------------------------------------------
          $Ns3Name = Trim(Mb_StrToLower($Ns3Name,'UTF-8'),'.');
          #NS3------------------------------------------------------------------
          if($Ns3Name){
            #-------------------------------------------------------------------
            if(!Preg_Match($Regulars['Domain'],$Ns3Name))
              return new gException('WRONG_NAME_NS3','Неверное имя дополнительного сервера имен');
            #-------------------------------------------------------------------
            if(Mb_SubStr($Ns3Name,-Mb_StrLen($Domain)) == $Domain){
              #-----------------------------------------------------------------
              if(!Preg_Match($Regulars['IP'],$Ns3IP))
                return new gException('WRONG_IP_NS3','Неверный IP адрес дополнительного сервера имен');
            }else{
              #-----------------------------------------------------------------
              if($Ns3IP)
                return new gException('IP_NS3_CAN_NOT_FILL','IP адрес дополнительного сервера имен не может быть указан');
            }
          }else{
            #-------------------------------------------------------------------
            if($Ns3IP)
              return new gException('NAME_NS3_NOT_FILL','Укажите имя дополнительного сервера имен');
          }
          #---------------------------------------------------------------------
          $Ns4Name = Trim(Mb_StrToLower($Ns4Name,'UTF-8'),'.');
          #NS4------------------------------------------------------------------
          if($Ns4Name){
            #-------------------------------------------------------------------
            if(!Preg_Match($Regulars['Domain'],$Ns4Name))
              return new gException('WRONG_NAME_NS4','Неверное имя расширенного сервера имен');
            #-------------------------------------------------------------------
            if(Mb_SubStr($Ns4Name,-Mb_StrLen($Domain)) == $Domain){
              #-----------------------------------------------------------------
              if(!Preg_Match($Regulars['IP'],$Ns4IP))
                return new gException('WRONG_IP_NS4','Неверный IP адрес расширенного сервера имен');
            }else{
              #-----------------------------------------------------------------
              if($Ns4IP)
                return new gException('IP_NS4_CAN_NOT_FILL','IP адрес расширенного сервера имен не может быть указан');
            }
          }else{
            #-------------------------------------------------------------------
            if($Ns4IP)
              return new gException('NAME_NS4_NOT_FILL','Укажите имя расширенного сервера имен');
          }
        }
        #-----------------------------------------------------------------------
        $Contract = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
        #-----------------------------------------------------------------------
        switch(ValueOf($Contract)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return new gException('CONTRACT_NOT_FOUND','Договор не найден');
          case 'array':
            #-------------------------------------------------------------------
            $__USER = $GLOBALS['__USER'];
            #-------------------------------------------------------------------
            $IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
            #-------------------------------------------------------------------
            switch(ValueOf($IsPermission)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'false':
                return ERROR | @Trigger_Error(700);
              case 'true':
                #-------------------------TRANSACTION---------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainOrder'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'DomainRules'",$Contract['ID']);
                #---------------------------------------------------------------
                $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if($Count < 1){
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'DomainRules'));
                  #-------------------------------------------------------------
                  switch(ValueOf($Comp)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'integer':
                      # No more...
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                }
                #---------------------------------------------------------------
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>20000));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IDomainOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'        => $OrderID,
                  'DomainName'     => $DomainName,
                  'SchemeID'       => $DomainScheme['ID'],
                  'IsPrivateWhoIs' => $IsPrivateWhoIs,
                  'Ns1Name'        => $Ns1Name,
                  'Ns1IP'          => $Ns1IP,
                  'Ns2Name'        => $Ns2Name,
                  'Ns2IP'          => $Ns2IP,
                  'Ns3Name'        => $Ns3Name,
                  'Ns3IP'          => $Ns3IP,
                  'Ns4Name'        => $Ns4Name,
                  'Ns4IP'          => $Ns4IP
                );
                #---------------------------------------------------------------
                $DomainOrderID = DB_Insert('DomainsOrders',$IDomainOrder);
                if(Is_Error($DomainOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'Waiting','RowsIDs'=>$DomainOrderID,'Comment'=>'Заказ успешно создан и ожидает оплаты'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    if(Is_Error(DB_Commit($TransactionID)))
                      return ERROR | @Trigger_Error(500);
                    #---------------------END TRANSACTION-----------------------
                    return Array('Status'=>'Ok','DomainOrderID'=>$DomainOrderID);
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
