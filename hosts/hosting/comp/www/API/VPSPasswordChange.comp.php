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
$VPSOrderID = (integer) @$Args['VPSOrderID'];
$Password       =  (string) @$Args['Password'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/VPSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(StrLen($Password) > 15)
	return new gException('BAD_PASSWORD_LENGTH','Слишком длинный пароль. Максимум - 15 символов.');
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Password'],$Password))
  return new gException('WRONG_PASSWORD','Неверно указан новый пароль');
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','UserID','ServerID','Login','Domain','StatusID','(SELECT `IsReselling` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrdersOwners`.`SchemeID`) as `IsReselling`');
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($VPSOrder['StatusID'] != 'Active')
      return new gException('VPS_ORDER_NOT_ACTIVE','Заказ виртуального сервера не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('VPSManage',(integer)$__USER['ID'],(integer)$VPSOrder['UserID']);
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
        $VPSServer = new VPSServer();
        #-----------------------------------------------------------------------
        $IsSelected = $VPSServer->Select((integer)$VPSOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
            #-------------------------------------------------------------------
            $PasswordChange = $VPSServer->PasswordChange($VPSOrder['Login'],$Password,$VPSOrder['IsReselling']);
            #-------------------------------------------------------------------
            switch(ValueOf($PasswordChange)){
              case 'error':
                return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
              case 'exception':
                return new gException('PASSWORD_CHANGE_ERROR','Ошибка смены пароля',$PasswordChange);
              case 'true':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('VPSOrders',Array('Password'=>$Password),Array('ID'=>$VPSOrder['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $VPSOrder['Password'] = $Password;
                #-------------------------------------------------------------------------------
                $Server = DB_Select('VPSServers',Array('Address','Url','Ns1Name','Ns2Name'),Array('UNIQ','ID'=>$VPSOrder['ServerID']));
                if(!Is_Array($Server))
                  return ERROR | @Trigger_Error(500);
                #-------------------------------------------------------------------------------
                $VPSOrder['Server'] = $Server;
                #---------------------------------------------------------------
                $IsSend = NotificationManager::sendMsg(new VPSPasswordChangeMsg($VPSOrder, (integer)$VPSOrder['UserID']));
                #---------------------------------------------------------------
                switch(ValueOf($IsSend)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    # No more...
                  case 'true':
                    return Array('Status'=>'Ok');
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
  default:
    return ERROR | @Trigger_Error(101);

}
#-------------------------------------------------------------------------------

?>
