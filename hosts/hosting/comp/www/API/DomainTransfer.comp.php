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
$ContractID     = (integer) @$Args['ContractID'];
$DomainName     =  (string) @$Args['DomainName'];
$DomainSchemeID = (integer) @$Args['DomainSchemeID'];
$PersonID       =  (string) @$Args['PersonID'];
$AuthInfo	=  (string) @$Args['AuthInfo'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/WhoIs.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
$DomainName = Mb_StrToLower($DomainName,'UTF-8');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['DomainName'],$DomainName))
  return new gException('WRONG_DOMAIN_NAME','Неверное имя домена');
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACT_NOT_FOUND','Договор не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $DomainScheme = DB_Select('DomainsSchemes',Array('ID','Name'),Array('UNIQ','ID'=>$DomainSchemeID));
        #-----------------------------------------------------------------------
        switch(ValueOf($DomainScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return new gException('DOMAIN_SCHEME_NOT_FOUND','Выбранный тарифный план домена не найден');
          case 'array':
	    #-------------------------------------------------------------------
            if(!In_Array($DomainScheme['Name'],Array('ru','su','рф'))){
              if(StrLen($AuthInfo) < 3 || StrLen($AuthInfo) > 40)
	        return new gException('INCORRECT_AUTHINFO','Указан неверный код переноса домена');
	    }
            #-------------------------------------------------------------------
            $Count = DB_Count('DomainsOrders',Array('Where'=>SPrintF("`DomainName` = '%s' AND (SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrders`.`SchemeID`) = '%s'",$DomainName,$DomainScheme['Name'])));
            if(Is_Error($Count))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            if($Count)
              return new gException('DOMAIN_ORDER_EXISTS','Домен уже находиться в списке заказов');
            #-------------------------------------------------------------------
            $WhoIs = WhoIs_Check($DomainName,$DomainScheme['Name']);
            #-------------------------------------------------------------------
            switch(ValueOf($WhoIs)){
              case 'exception':
                return new Tag('WHOIS_ERROR','Ошибка получения данных WhoIs',$WhoIs);
              case 'true':
                return new gException('DOMAIN_IS_FREE','Выбранный Вами домен свободен');
              case 'error':
                # No more...
              case 'false':
                # No more...
              case 'array':
                #-------------------------TRANSACTION---------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainTransfer'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>20000,'IsPayed'=>TRUE));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IDomainOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'    => $OrderID,
                  'DomainName' => Mb_StrToLower($DomainName,'UTF-8'),
                  'SchemeID'   => $DomainScheme['ID'],
                  'PersonID'   => $PersonID,
                  'WhoIs'      => $WhoIs['Info'],
		  'AuthInfo'   => ($AuthInfo)?$AuthInfo:'NotUsed',
                  'UpdateDate' => Time()
                );
                #---------------------------------------------------------------
                $IDomainOrder['ExpirationDate'] = Max($WhoIs['ExpirationDate'],Time());
                #---------------------------------------------------------------
                $Domain = SPrintF('%s.%s',$DomainName,$DomainScheme['Name']);
                #---------------------------------------------------------------
                for($i=1;$i<5;$i++){
                  #-------------------------------------------------------------
                  $NsName = (IsSet($WhoIs[SPrintF('Ns%uName',$i)])?$WhoIs[SPrintF('Ns%uName',$i)]:'');
                  #-------------------------------------------------------------
                  $IDomainOrder[SPrintF('Ns%uName',$i)] = $NsName;
                  #-------------------------------------------------------------
                  $NsIP = (IsSet($WhoIs[SPrintF('Ns%uIP',$i)])?$WhoIs[SPrintF('Ns%uIP',$i)]:'');
                  #-------------------------------------------------------------
                  $IDomainOrder[SPrintF('Ns%uIP',$i)] = $NsIP;
                }
                #---------------------------------------------------------------
                $DomainOrderID = DB_Insert('DomainsOrders',$IDomainOrder);
                if(Is_Error($DomainOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'OnTransfer','RowsIDs'=>$DomainOrderID,'Comment'=>'Поступила заявка на перенос доменного имени'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'DomainRules'",$Contract['ID']);
                    #-----------------------------------------------------------
                    $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
                    if(Is_Error($Count))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    if($Count < 1){
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'DomainRules'));
                      #---------------------------------------------------------
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
                    #-----------------------------------------------------------
                    if(Is_Error(DB_Commit($TransactionID)))
                      return ERROR | @Trigger_Error(500);
                    #----------------------END TRANSACTION----------------------
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
