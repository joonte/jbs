<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','classes/Server.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingServers = DB_Select('HostingServers',Array('ID','Address'),Array('Where'=>"`SystemID` = 'IspManager'"));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($HostingServers as $HostingServer){
      #-------------------------------------------------------------------------
      $Server = new Server();
      #-------------------------------------------------------------------------
      $IsSelected = $Server->Select((integer)$HostingServer['ID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsSelected)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'true':
          #---------------------------------------------------------------------
          $Users = $Server->GetUsers();
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
