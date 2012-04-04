<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DomainOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Registrator.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','DomainName','UserID','IsPrivateWhoIs','PersonID','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `DomainZone`','ProfileID','(SELECT `RegistratorID` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `RegistratorID`','StatusID','(SELECT SUM(`YearsRemainded`) FROM `DomainsConsider` WHERE `DomainsConsider`.`DomainOrderID` = `DomainsOrdersOwners`.`ID`) as `YearsRemainded`','Ns1Name','Ns1IP','Ns2Name','Ns2IP','Ns3Name','Ns3IP','Ns4Name','Ns4IP');
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Registrator = new Registrator();
    #---------------------------------------------------------------------------
    $RegistratorID = $DomainOrder['RegistratorID'];
    #---------------------------------------------------------------------------
    $IsSelected = $Registrator->Select((integer)$DomainOrder['RegistratorID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
      case 'true':
        #-----------------------------------------------------------------------
        $GLOBALS['TaskReturnInfo'] = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone']);
        #-----------------------------------------------------------------------
        switch($DomainOrder['StatusID']){
          case 'ForRegister':
            #-------------------------------------------------------------------
            $PersonID = $DomainOrder['PersonID'];
            #-------------------------------------------------------------------
            if($PersonID)
              $DomainRegister = $Registrator->DomainRegister(mb_StrToLower($DomainOrder['DomainName'],'UTF-8'),$DomainOrder['DomainZone'],(integer)$DomainOrder['YearsRemainded'],$DomainOrder['Ns1Name'],$DomainOrder['Ns1IP'],$DomainOrder['Ns2Name'],$DomainOrder['Ns2IP'],$DomainOrder['Ns3Name'],$DomainOrder['Ns3IP'],$DomainOrder['Ns4Name'],$DomainOrder['Ns4IP'],$DomainOrder['IsPrivateWhoIs'],$PersonID);
            else{
              #-----------------------------------------------------------------
              $ProfileID = $DomainOrder['ProfileID'];
              #-----------------------------------------------------------------
              $Profile = DB_Select('Profiles',Array('TemplateID','Attribs'),Array('UNIQ','ID'=>$ProfileID));
              #-----------------------------------------------------------------
              switch(ValueOf($Profile)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  return ERROR | @Trigger_Error(400);
                case 'array':
                  # готовим поля профиля
                  $ProfileCompile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
                  #-------------------------------------------------------------
                  switch(ValueOf($ProfileCompile)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'array':
                      # страна профиля должна быть кодом
                      $ProfileCompile['Attribs']['pCountry'] = $Profile['Attribs']['pCountry'];
		      $ProfileCompile['Attribs']['jCountry'] = $Profile['Attribs']['jCountry'];
                      break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                  #-------------------------------------------------------------
                  $DomainRegister = $Registrator->DomainRegister(mb_StrToLower($DomainOrder['DomainName'],'UTF-8'),$DomainOrder['DomainZone'],(integer)$DomainOrder['YearsRemainded'],$DomainOrder['Ns1Name'],$DomainOrder['Ns1IP'],$DomainOrder['Ns2Name'],$DomainOrder['Ns2IP'],$DomainOrder['Ns3Name'],$DomainOrder['Ns3IP'],$DomainOrder['Ns4Name'],$DomainOrder['Ns4IP'],$DomainOrder['IsPrivateWhoIs'],'',$Profile['TemplateID'],$ProfileCompile['Attribs']);
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
            }
            #-------------------------------------------------------------------
            switch(ValueOf($DomainRegister)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
              case 'false':
                return 300;
              case 'array':
                #---------------------------------------------------------------
                if(IsSet($DomainRegister['ContractID'])){
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('DomainsOrders',Array('PersonID'=>$DomainRegister['ContractID']),Array('ID'=>$DomainOrder['ID']));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                }
                #---------------------------------------------------------------
                $Task['Params']['TicketID'] = $DomainRegister['TicketID'];
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('Tasks',Array('Params'=>$Task['Params']),Array('ID'=>$Task['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'OnRegister','RowsIDs'=>$DomainOrderID,'Comment'=>'Регистратор успешно принял заявку на регистрацию'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    return 300;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
          case 'OnRegister':
            #-------------------------------------------------------------------
            $TicketID = $Task['Params']['TicketID'];
            #-------------------------------------------------------------------
            $IsDomainRegister = $Registrator->CheckTask($TicketID);
            #-------------------------------------------------------------------
            switch(ValueOf($IsDomainRegister)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
              case 'false':
                return 300;
              case 'array':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('DomainsOrders',Array('ProfileID'=>NULL,'DomainID'=>$IsDomainRegister['DomainID']),Array('ID'=>$DomainOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'Active','RowsIDs'=>$DomainOrderID,'Comment'=>'Доменное имя успешно зарегистрированно'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    return TRUE;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
          default:
            return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
