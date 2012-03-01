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
$Columns = Array('ID','UserID','DomainName','ProfileID','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `DomainZone`','(SELECT `RegistratorID` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `RegistratorID`','StatusID');
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
    $IsSelected = $Registrator->Select((integer)$DomainOrder['RegistratorID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
      case 'true':
        #-----------------------------------------------------------------------
        switch($DomainOrder['StatusID']){
          case 'ForContractRegister':
            #-------------------------------------------------------------------
            $Profile = DB_Select('Profiles',Array('TemplateID','Attribs'),Array('UNIQ','ID'=>$DomainOrder['ProfileID']));
            #-------------------------------------------------------------------
            switch(ValueOf($Profile)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'false':
                return 300;
              case 'array':
                #---------------------------------------------------------------
                $ContractRegister = $Registrator->ContractRegister($Profile['TemplateID'],$Profile['Attribs'],$DomainOrder['DomainZone']);
                #---------------------------------------------------------------
                switch(ValueOf($ContractRegister)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
                  case 'array':
                    #-----------------------------------------------------------
                    $Task['Params']['TicketID'] = $ContractRegister['TicketID'];
                    #-----------------------------------------------------------
                    $IsUpdate = DB_Update('Tasks',Array('Params'=>$Task['Params']),Array('ID'=>$Task['ID']));
                    if(Is_Error($IsUpdate))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'OnContractRegister','RowsIDs'=>$DomainOrderID,'Comment'=>'Регистратор принял заявку на регистрацию договора клиента'));
                    #-----------------------------------------------------------
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
              default:
                return ERROR | @Trigger_Error(101);
            }
          case 'OnContractRegister':
            #-------------------------------------------------------------------
            $TicketID = $Task['Params']['TicketID'];
            #-------------------------------------------------------------------
            $GetContract = $Registrator->GetContract((string)$TicketID);
            #-------------------------------------------------------------------
            switch(ValueOf($GetContract)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
              case 'false':
                return 300;
              case 'array':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('DomainsOrders',Array('ProfileID'=>NULL,'PersonID'=>$GetContract['ContractID']),Array('ID'=>$DomainOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'ForRegister','RowsIDs'=>$DomainOrderID,'Comment'=>'Регистратор зарегистрировал договор клиента, идет передача доменного имени на регистрацию'));
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
