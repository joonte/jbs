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
$Password       =  (string) @$Args['Password'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/Server.class.php')))
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
$Columns = Array('ID','OrderID','UserID','ServerID','Login','Domain','StatusID','(SELECT `IsReselling` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `IsReselling`');
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
        $Server = new Server();
        #-----------------------------------------------------------------------
        $IsSelected = $Server->Select((integer)$HostingOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
            #-------------------------------------------------------------------
            $PasswordChange = $Server->PasswordChange($HostingOrder['Login'],$Password,$HostingOrder['IsReselling']);
            #-------------------------------------------------------------------
            switch(ValueOf($PasswordChange)){
              case 'error':
                return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
              case 'exception':
                return new gException('PASSWORD_CHANGE_ERROR','Ошибка смены пароля',$PasswordChange);
              case 'true':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('HostingOrders',Array('Password'=>$Password),Array('ID'=>$HostingOrder['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $HostingOrder['Password'] = $Password;
                #---------------------------------------------------------------
		$IsSend = NotificationManager::sendMsg(new Message('HostingPasswordChange',(integer)$HostingOrder['UserID'],Array('HostingOrder'=>$HostingOrder)));
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
