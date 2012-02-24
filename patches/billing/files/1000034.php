<?php
#-------------------------------------------------------------------------------
$ContractsEnclosures = DB_Select('ContractsEnclosures','ID',Array('Where'=>"UNIX_TIMESTAMP() - `CreateDate` < 2678400"));
#-------------------------------------------------------------------------------
switch(ValueOf($ContractsEnclosures)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ContractsEnclosures as $ContractEnclosure){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Contracts/Enclosures/Build',$ContractEnclosure['ID']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>