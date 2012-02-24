<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Server.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingServers = DB_Select('HostingServers',Array('ID','Address'));
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
          $Users = $Server->GetDomains();
          #---------------------------------------------------------------------
          switch(ValueOf($Users)){
            case 'error':
              # No more...
            break 2;
            case 'exception':
              # No more...
            break 2;
            case 'array':
              #-----------------------------------------------------------------
              if(Count($Users)){
                #---------------------------------------------------------------
                $Array = Array();
                #---------------------------------------------------------------
                foreach(Array_Keys($Users) as $UserID)
                  $Array[] = SPrintF("'%s'",$UserID);
                #---------------------------------------------------------------
                $Where = SPrintF('`ServerID` = %u AND `Login` IN (%s)',$HostingServer['ID'],Implode(',',$Array));
                #---------------------------------------------------------------
                $HostingOrders = DB_Select('HostingOrders',Array('ID','Login'),Array('Where'=>$Where));
                #---------------------------------------------------------------
                switch(ValueOf($HostingOrders)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    # No more...
                  break;
                  case 'array':
                    #-----------------------------------------------------------
                    foreach($HostingOrders as $HostingOrder){
                      #---------------------------------------------------------
                      $Parked = $Users[$HostingOrder['Login']];
                      #---------------------------------------------------------
                      $IsUpdate = DB_Update('HostingOrders',Array('Domain'=>(Count($Parked)?Current($Parked):'not-found'),'Parked'=>Implode(',',$Parked)),Array('ID'=>$HostingOrder['ID']));
                      if(Is_Error($IsUpdate))
                        return ERROR | @Trigger_Error(500);
                    }
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              }
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
return 1800;
#-------------------------------------------------------------------------------

?>
