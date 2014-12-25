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
$DNSmanagerOrderID = (integer) @$Args['DNSmanagerOrderID'];
$Password       =  (string) @$Args['Password'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DNSmanagerServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Password'],$Password))
	return new gException('WRONG_PASSWORD','Неверно указан новый пароль');
#-------------------------------------------------------------------------------
if(StrLen($Password) > 15)
	return new gException('BAD_PASSWORD_LENGTH','Слишком длинный пароль. Максимум - 15 символов.');
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','UserID','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `ServerID`','Login','StatusID','(SELECT `IsReselling` FROM `DNSmanagerSchemes` WHERE `DNSmanagerSchemes`.`ID` = `DNSmanagerOrdersOwners`.`SchemeID`) as `IsReselling`');
#-------------------------------------------------------------------------------
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',$Columns,Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($DNSmanagerOrder['StatusID'] != 'Active')
      return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ вторичного DNS не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DNSmanagerManage',(integer)$__USER['ID'],(integer)$DNSmanagerOrder['UserID']);
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
        $ClassDNSmanagerServer = new DNSmanagerServer();
        #-----------------------------------------------------------------------
        $IsSelected = $ClassDNSmanagerServer->Select((integer)$DNSmanagerOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
            #-------------------------------------------------------------------
            $PasswordChange = $ClassDNSmanagerServer->PasswordChange($DNSmanagerOrder['Login'],$Password,$DNSmanagerOrder['IsReselling']);
            #-------------------------------------------------------------------
            switch(ValueOf($PasswordChange)){
              case 'error':
                return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
              case 'exception':
                return new gException('PASSWORD_CHANGE_ERROR','Ошибка смены пароля',$PasswordChange);
              case 'true':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('DNSmanagerOrders',Array('Password'=>$Password),Array('ID'=>$DNSmanagerOrder['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $DNSmanagerOrder['Password'] = $Password;
                #---------------------------------------------------------------
                $msg = new DNSmanagerPasswordChangeMsg($DNSmanagerOrder, (integer)$DNSmanagerOrder['UserID']);
                $IsSend = NotificationManager::sendMsg($msg);
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
