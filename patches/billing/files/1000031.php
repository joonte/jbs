<?php
#-------------------------------------------------------------------------------
$Invoices = DB_Select('Invoices','ID',Array('Where'=>"`PaymentSystemID` IN ('Juridical') AND `CreateDate` > UNIX_TIMESTAMP() - 172800"));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoices)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Invoices as $Invoice){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Invoices/Build',$Invoice['ID']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(101);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>