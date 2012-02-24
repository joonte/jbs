<?php
#-------------------------------------------------------------------------------
$Users = DB_Select('Users','ID');
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Users as $User){
      #-------------------------------------------------------------------------
      $Count = DB_Count('Contracts',Array('Where'=>SPrintF("`UserID` = %u AND `TypeID` != 'Default'",$User['ID'])));
      if(Is_Error($Count))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if($Count){
        #-----------------------------------------------------------------------
        $IsDelete = DB_Delete('Contracts',Array('Where'=>SPrintF("`UserID` = %u AND `TypeID` = 'Default'",$User['ID'])));
        if(Is_Error($IsDelete))
          return ERROR | @Trigger_Error(500);
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