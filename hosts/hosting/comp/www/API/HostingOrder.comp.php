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
$ContractID      = (integer) @$Args['ContractID'];
$Domain          =  (string) @$Args['Domain'];
$HostingSchemeID = (integer) @$Args['HostingSchemeID'];
$DomainTypeID    =  (string) @$Args['DomainTypeID'];
$DomainName      =  (string) @$Args['DomainName'];
$DomainSchemeID  = (integer) @$Args['DomainSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if($DomainTypeID != 'None'){
  #-----------------------------------------------------------------------------
  $Domain = StrToLower($Domain);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/^www\.(.+)$/',$Domain,$Mathces))
    $Domain = Next($Mathces);
  #-----------------------------------------------------------------------------
  if(!Preg_Match($Regulars['Domain'],$Domain))
    return new gException('WRONG_DOMAIN','Неверный домен');
  #-----------------------------------------------------------------------------
  $Count = DB_Count('HostingOrders',Array('Where'=>SPrintF("(`Domain` LIKE '%%%s%%' OR `Parked` LIKE '%%%s%%') AND `StatusID` != 'Waiting'",$Domain,$Domain)));
  if(Is_Error($Count))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if($Count)
    return new gException('DOMAIN_ALREADY_EXISTS','Доменное имя уже используется для одного из заказов хостинга');
}
#-------------------------------------------------------------------------------
if(!$HostingSchemeID)
  return new gException('HOSTING_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
$HostingScheme = DB_Select('HostingSchemes',Array('ID','Name','ServersGroupID','HardServerID','IsActive'),Array('UNIQ','ID'=>$HostingSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа хостинга не найден');
  case 'array':
    #---------------------------------------------------------------------------
    if(!$HostingScheme['IsActive'])
      return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа хостинга не активен');
    #---------------------------------------------------------------------------
    $Contract = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contract)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('CONTRACT_NOT_FOUND','Договор не найден');
      case 'array':
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsPermission)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'false':
            return ERROR | @Trigger_Error(700);
          case 'true':
            #-------------------------------------------------------------------
	    if($HostingScheme['HardServerID']){
              $Where = SPrintF("`ID` = %u",$HostingScheme['HardServerID']);
	    }else{
	      $Where = SPrintF("`ServersGroupID` = %u AND `IsDefault` = 'yes'",$HostingScheme['ServersGroupID']);
	    }
	    #-------------------------------------------------------------------
            $HostingServer = DB_Select('HostingServers',Array('ID','Domain','Prefix'),Array('Where'=>$Where));
            #-------------------------------------------------------------------
            switch(ValueOf($HostingServer)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('SERVER_NOT_DEFINED','Сервер размещения не определён');
              case 'array':
                #---------------------------------------------------------------
                $HostingServer = Current($HostingServer);
                #---------------------------------------------------------------
                $Password = SubStr(Md5(UniqID()),0,12);
                #-------------------------TRANSACTION---------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('HostingOrder'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'HostingRules'",$Contract['ID']);
                #---------------------------------------------------------------
                $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if($Count < 1){
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'HostingRules'));
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
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>10000));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Login = SPrintF('%s%s',$HostingServer['Prefix'],$OrderID);
                #---------------------------------------------------------------
                if($DomainTypeID == 'None')
                  $Domain = SPrintF('%s.%s',$Login,$HostingServer['Domain']);
                #---------------------------------------------------------------
                $IHostingOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'  => $OrderID,
                  'SchemeID' => $HostingScheme['ID'],
                  'ServerID' => $HostingServer['ID'],
                  'Domain'   => $Domain,
                  'Login'    => $Login,
                  'Password' => $Password,
                );
                #---------------------------------------------------------------
                $HostingOrderID = DB_Insert('HostingOrders',$IHostingOrder);
                if(Is_Error($HostingOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Waiting','RowsIDs'=>$HostingOrderID,'Comment'=>'Заказ создан и ожидает оплаты'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		                   'UserID'	=> $Contract['UserID'],
				   'PriorityID'	=> 'Billing',
				   'Text'	=> SPrintF('Сформирована заявка на заказ хостинга логин (%s), домен (%s), тариф (%s)',$Login,$Domain,$HostingScheme['Name'])
				  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    switch($DomainTypeID){
                      case 'Order':
                        #-------------------------------------------------------
                        if(!Preg_Match($Regulars['DomainName'],$DomainName))
                          return new gException('WRONG_DOMAIN_NAME','Неверное имя домена');
                        #-------------------------------------------------------
                        $DomainScheme = DB_Select('DomainsSchemes',Array('ID','IsActive','MinOrderYears'),Array('UNIQ','ID'=>$DomainSchemeID));
                        #-------------------------------------------------------
                        switch(ValueOf($DomainScheme)){
                          case 'error':
                            return ERROR | @Trigger_Error(500);
                          case 'exception':
                            return new gException('DOMAIN_SCHEME_NOT_FOUND','Выбранный тарифный домена план не найден');
                          case 'array':
                            #---------------------------------------------------
                            if(!$DomainScheme['IsActive'])
                              return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа домена не активен');
                            #---------------------------------------------------
                            $DomainOrder = Comp_Load('www/API/DomainOrder',Array('ContractID'=>$Contract['ID'],'DomainName'=>$DomainName,'DomainSchemeID'=>$DomainScheme['ID'],'HostingOrderID'=>$HostingOrderID));
                            #---------------------------------------------------
                            switch(ValueOf($DomainOrder)){
                              case 'error':
                                return ERROR | @Trigger_Error(500);
                              case 'exception':
                                # No more...
                              break 3;
                              case 'array':
                                #-----------------------------------------------
                                $DomainOrderPay = Comp_Load('www/API/DomainOrderPay',Array('DomainOrderID'=>$DomainOrder['DomainOrderID'],'YearsPay'=>$DomainScheme['MinOrderYears']));
                                #-----------------------------------------------
                                switch(ValueOf($DomainOrderPay)){
                                  case 'error':
                                    return ERROR | @Trigger_Error(500);
                                  case 'exception':
                                    # No more...
                                  break 4;
                                  case 'array':
                                    # No more...
                                  break 4;
                                  default:
                                    return ERROR | @Trigger_Error(101);
                                }
                              default:
                                return ERROR | @Trigger_Error(101);
                            }
                          default:
                            return ERROR | @Trigger_Error(101);
                        }
                      case 'Transfer':
                        #-------------------------------------------------------
                        $DomainScheme = DB_Select('DomainsSchemes','ID',Array('UNIQ','ID'=>$DomainSchemeID));
                        #-------------------------------------------------------
                        switch(ValueOf($DomainScheme)){
                          case 'error':
                            return ERROR | @Trigger_Error(500);
                          case 'exception':
                            return new gException('DOMAIN_SCHEME_NOT_FOUND','Выбранный тарифный домена план не найден');
                          case 'array':
                            #---------------------------------------------------
                            $DomainTransfer = Comp_Load('www/API/DomainTransfer',Array('ContractID'=>$Contract['ID'],'DomainName'=>$DomainName,'DomainSchemeID'=>$DomainScheme['ID']));
                            #---------------------------------------------------
                            switch(ValueOf($DomainTransfer)){
                              case 'error':
                                return ERROR | @Trigger_Error(500);
                              case 'exception':
                                # No more...
                              break 2;
                              case 'array':
                                # No more...
                              break 2;
                              default:
                                return ERROR | @Trigger_Error(101);
                            }
                          default:
                            return ERROR | @Trigger_Error(101);
                        }
                      break;
                      case 'Nothing':
                        # No more...
                      break;
                      default:
                        # No more...
                    }
                    #-----------------------------------------------------------
                    if(Is_Error(DB_Commit($TransactionID)))
                      return ERROR | @Trigger_Error(500);
                    #----------------------END TRANSACTION----------------------
                    return Array('Status'=>'Ok','HostingOrderID'=>$HostingOrderID);
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
