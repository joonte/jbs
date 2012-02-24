<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Contracts.lib')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainsOrdersOwners');
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DomainOrders as $DomainOrder){
      #-------------------------------------------------------------------------
      $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'DomainsRules'",$DomainOrder['ContractID']);
      #-------------------------------------------------------------------------
      $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
      if(Is_Error($Count))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if($Count < 1){
        #-----------------------------------------------------------------------
        $EnclosureID = Contract_Add_Enclosure((integer)$DomainOrder['ContractID'],'DomainsRules');
        #-----------------------------------------------------------------------
        switch(ValueOf($EnclosureID)){
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
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>