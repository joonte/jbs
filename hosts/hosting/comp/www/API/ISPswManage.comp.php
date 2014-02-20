<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ISPswOrderID = (integer) @$Args['ISPswOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/HostingServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','ServerID','Login','Password','StatusID');
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($ISPswOrder['StatusID'] != 'Active')
      return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ ПО ISPsystem не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ISPswManage',(integer)$__USER['ID'],(integer)$ISPswOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $ClassHostingServer = new HostingServer();
        #-----------------------------------------------------------------------
        $IsSelected = $ClassHostingServer->Select((integer)$ISPswOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
            #-------------------------------------------------------------------
            $IsLogon = $ClassHostingServer->Logon($ISPswOrder['Login'],$ISPswOrder['Password']);
            #-------------------------------------------------------------------
            switch(ValueOf($IsLogon)){
              case 'error':
                return new gException('ERROR_SERVER_ACCESS','Ошибка доступа к серверу');
              case 'exception':
                return $IsLogon;
              case 'array':
                #---------------------------------------------------------------
                $IsLogon['Status'] = 'Ok';
                #---------------------------------------------------------------
                return $IsLogon;
              default:
                return ERROR | @Trigger_Error(101);
            }
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
