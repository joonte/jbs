<?php
#-------------------------------------------------------------------------------
$Invoices = DB_Select('Invoices',Array('ID','Document'),Array('Where'=>"`PaymentSystemID` IN ('Juridical','Individual')"));
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
      $Document = Str_Replace('/Styles','/styles',$Invoice['Document']);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Invoices',Array('Document'=>$Document),Array('ID'=>$Invoice['ID']));
      if(Is_Error($IsUpdate))
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