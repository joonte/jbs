<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$VPSOrderID	= (integer) @$Args['VPSOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/VPSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
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
	    Debug("[www/API/VPSReboot]: server is select");
	    # проверяем, не отключен ли сервер администратором
            $IsActive = $VPSServer->CheckIsActive($VPSOrder['Login']);
	    switch(ValueOf($IsActive)){
              case 'error':
                return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
              case 'true':
	        # OK, is enabled
	        break;
	      case 'false':
	        return new gException('SERVER_DISABLED_BY_ADMINISTRATOR','Сервер выключен администратором. За дополнительной информацией, обратитесь в систему тикетов.');
	      default:
	        return ERROR | @Trigger_Error(101);
            }
	    #-----------------------------------------------------------------------
	    # перезагружем сервер
            $IsReboot = $VPSServer->Reboot($VPSOrder['Login']);
            switch(ValueOf($IsActive)){
            case 'error':
              return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
            case 'true':
	      return Array('Status'=>'Ok');
            default:
              return ERROR | @Trigger_Error(101);
            }
	    #-----------------------------------------------------------------------
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
#-------------------------------------------------------------------------------

?>
