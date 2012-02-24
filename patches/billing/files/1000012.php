<?php
#-------------------------------------------------------------------------------
$Contracts = DB_Select('Contracts','*',Array('Where'=>"`TypeID` != 'Public' AND `ProfileID` = 0"));
#-------------------------------------------------------------------------------
switch(ValueOf($Contracts)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    echo "Договоры не найдены\n";
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Contracts as $Contract){
      #-------------------------------------------------------------------------
      $Profile = DB_Select('Profiles','ID',Array('UNIQ','Where'=>SPrintF("`TemplateID` = '%s' AND `Name` LIKE '%%%s%%'",$Contract['TypeID'],MB_SubStr($Contract['CustomerName'],0,10))));
      if(!Is_Array($Profile)){
        #-----------------------------------------------------------------------
        echo SPrintF("%s - %s\n",$Contract['ID'],$Contract['CustomerName']);
        #-----------------------------------------------------------------------
        continue;
      }
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Contracts',Array('ProfileID'=>$Profile['ID']),Array('ID'=>$Contract['ID']));
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