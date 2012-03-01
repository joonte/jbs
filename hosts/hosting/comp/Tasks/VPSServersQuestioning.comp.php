<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$VPSServers = DB_Select('VPSServers',Array('ID','Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSServers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($VPSServers as $iVPSServer){
      #-------------------------------------------------------------------------
      $VPSServer = new VPSServer();
      #-------------------------------------------------------------------------
      $IsSelected = $VPSServer->Select((integer)$iVPSServer['ID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsSelected)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'true':
          #---------------------------------------------------------------------
          $Users = $VPSServer->GetDomains();
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
                $Where = SPrintF('`ServerID` = %u AND `Login` IN (%s)',$iVPSServer['ID'],Implode(',',$Array));
                #---------------------------------------------------------------
                $VPSOrders = DB_Select('VPSOrders',Array('ID','Login'),Array('Where'=>$Where));
                #---------------------------------------------------------------
                switch(ValueOf($VPSOrders)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    # No more...
                  break;
                  case 'array':
                    #-----------------------------------------------------------
                    foreach($VPSOrders as $VPSOrder){
                      #---------------------------------------------------------
                      $Parked = $Users[$VPSOrder['Login']];
                      #---------------------------------------------------------
                      $IsUpdate = DB_Update('VPSOrders',Array('Domain'=>(Count($Parked)?Current($Parked):'not-found'),'Parked'=>Implode(',',$Parked)),Array('ID'=>$VPSOrder['ID']));
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
