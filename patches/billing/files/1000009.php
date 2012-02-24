<?php
#-------------------------------------------------------------------------------
$Contracts = DB_Select('Contracts','*');
#-------------------------------------------------------------------------------
switch(ValueOf($Contracts)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Contracts as $Contract){
      #-------------------------------------------------------------------------
      $Customer = $Contract['Customer'];
      #-------------------------------------------------------------------------
      $CustomerName = $Contract['CustomerName'];
      #-------------------------------------------------------------------------
      switch($Contract['TypeID']){
        case 'Natural':
          $CustomerName = SPrintF('%s %s',$Customer['Sourname']['Value'],$Customer['Name']['Value']);
        break;
        case 'Juridical':
          $CustomerName = $Customer['CompanyName']['Value'];
        break;
        case 'Individual':
          $CustomerName = $Customer['CompanyName']['Value'];
        break;
        default:
          # No more...
      }
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Contracts',Array('CustomerName'=>$CustomerName),Array('ID'=>$Contract['ID']));
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