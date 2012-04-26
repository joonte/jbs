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
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
$Password       =  (string) @$Args['Password'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/ExtraIPServer.class.php')))
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
$Columns = Array('ID','OrderID','UserID','ServerID','Login','Domain','StatusID','(SELECT `IsReselling` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) as `IsReselling`');
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',$Columns,Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($ExtraIPOrder['StatusID'] != 'Active')
      return new gException('ExtraIP_ORDER_NOT_ACTIVE','Заказ виртуального сервера не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ExtraIPManage',(integer)$__USER['ID'],(integer)$ExtraIPOrder['UserID']);
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
        $ExtraIPServer = new ExtraIPServer();
        #-----------------------------------------------------------------------
        $IsSelected = $ExtraIPServer->Select((integer)$ExtraIPOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
            #-------------------------------------------------------------------
            $PasswordChange = $ExtraIPServer->PasswordChange($ExtraIPOrder['Login'],$Password,$ExtraIPOrder['IsReselling']);
            #-------------------------------------------------------------------
            switch(ValueOf($PasswordChange)){
              case 'error':
                return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
              case 'exception':
                return new gException('PASSWORD_CHANGE_ERROR','Ошибка смены пароля',$PasswordChange);
              case 'true':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('ExtraIPOrders',Array('Password'=>$Password),Array('ID'=>$ExtraIPOrder['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $ExtraIPOrder['Password'] = $Password;
                #-------------------------------------------------------------------------------
                $Server = DB_Select('ExtraIPs',Array('Address','Url','Ns1Name','Ns2Name'),Array('UNIQ','ID'=>$ExtraIPOrder['ServerID']));
                if(!Is_Array($Server))
                  return ERROR | @Trigger_Error(500);
                #-------------------------------------------------------------------------------
                $ExtraIPOrder['Server'] = $Server;
                #---------------------------------------------------------------
                $IsSend = NotificationManager::sendMsg(new Message('ExtraIPPasswordChange',(integer)$ExtraIPOrder['UserID'],Array('Item'=>$ExtraIPOrder)));
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
