<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','classes/HostingServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Address'),Array('Where'=>"`SystemID` = 'IspManager4'"));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Servers as $Server){
      #-------------------------------------------------------------------------
      $ClassHostingServer = new Server();
      #-------------------------------------------------------------------------
      $IsSelected = $ClassHostingServer->Select((integer)$Server['ID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsSelected)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'true':
          #---------------------------------------------------------------------
          $Users = $ClassHostingServer->GetUsers();
          #---------------------------------------------------------------------
          switch(ValueOf($Users)){
            case 'error':
              # No more...
            break 2;
            case 'exception':
              # No more...
            break 2;
            case 'array':
              print_r($Users);
            break 2;
            default:
              return ERROR | @Trigger_Error(101);
          }
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return 'OK';
#-------------------------------------------------------------------------------

?>
