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
$HostingOrderID = (integer) @$Args['HostingOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/HostingServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','ServerID','Login','Password','StatusID','(SELECT `Url` FROM `HostingServers` WHERE `HostingServers`.`ID` = `HostingOrdersOwners`.`ServerID`) as `Url`');
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($HostingOrder['StatusID'] != 'Active')
      return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ хостинга не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('HostingManage',(integer)$__USER['ID'],(integer)$HostingOrder['UserID']);
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
        $IsSelected = $ClassHostingServer->Select((integer)$HostingOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
            #-------------------------------------------------------------------
            $IsLogon = $ClassHostingServer->Logon(Array('Login'=>$HostingOrder['Login'],'Password'=>$HostingOrder['Password'],'Url'=>$HostingOrder['Url']));
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
