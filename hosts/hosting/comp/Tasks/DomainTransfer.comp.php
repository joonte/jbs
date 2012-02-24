<?php


#-------------------------------------------------------------------------------
/** @author Serge Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DomainOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/WhoIs.php','classes/Registrator.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `DomainZone`','(SELECT `RegistratorID` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `RegistratorID`','StatusID');
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
$WhoIs = WhoIs_Check($DomainOrder['DomainName'],$DomainOrder['DomainZone']);
IsSet($WhoIs['Registrar'])?$Registrar = $WhoIs['Registrar']:$Registrar = 'NOT_FOUND';
#-------------------------------------------------------------------------------
$Registrator = DB_Select('Registrators','PrefixNic',Array('UNIQ','ID'=>$DomainOrder['RegistratorID']));
#-------------------------------------------------------------------------------
Debug("[Task/DomainTransfer]: Registrar - ". $Registrar);
Debug("[Task/DomainTransfer]: PrefixNic - ". $Registrator['PrefixNic']);
#-------------------------------------------------------------------------------
$IsInternal = FALSE;
if(Preg_Match(SPrintF('/%s/',$Registrator['PrefixNic']), $Registrar))
  $IsInternal = TRUE;
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
        switch($DomainOrder['StatusID']){
          case 'OnTransfer':
            #-------------------------------------------------------------------
            if($IsInternal){
              Debug("[Task/DomainTransfer]: IsInternal - TRUE");
              return TRUE;
            }
            #-------------------------------------------------------------------
            $IsDomainTransfer = $Registrator->DomainTransfer($DomainOrder['DomainName'],$DomainOrder['DomainZone']);
            #-------------------------------------------------------------------
            switch(ValueOf($IsDomainTransfer)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
              case 'array':
                $IsUpdate = DB_Update('DomainsOrders',Array('ProfileID'=>NULL,'DomainID'=>$IsDomainTransfer['DomainID']),Array('ID'=>$DomainOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                return TRUE;
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
