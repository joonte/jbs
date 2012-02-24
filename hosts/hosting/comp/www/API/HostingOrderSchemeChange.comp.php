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
$NewSchemeID    = (integer) @$Args['NewSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `ServersGroupID` FROM `HostingServers` WHERE `HostingServers`.`ID` = `HostingOrdersOwners`.`ServerID`) as `ServersGroupID`','StatusID','StatusDate');
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('HOSTING_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('HostingOrdersSchemeChange',(integer)$__USER['ID'],(integer)$HostingOrder['UserID']);
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
        if($HostingOrder['StatusID'] != 'Active')
          return new gException('ORDER_NO_ACTIVE','Заказ хостинга не активен');
        #-----------------------------------------------------------------------
        $IsPermission = Permission_Check('/Administrator/',(integer)$__USER['ID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsPermission)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'false':
            #-------------------------------------------------------------------
            $LastChange = Time() - $HostingOrder['StatusDate'];
            #-------------------------------------------------------------------
            if($LastChange < 86400){
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Date/Remainder',$LastChange);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
#              return new gException('TIME_NOT_EXPIRED',SPrintF('Тарифный план можно менять только 1 раз в сутки, сменить тарифный план можно только через %s, однако, в случае необходимости Вы можете обратиться в службу поддержки',$Comp));
            }
          case 'true':
            # No more...
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $OldScheme = DB_Select('HostingSchemes',Array('IsSchemeChange','QuotaDisk','Name','IsProlong','ID'),Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($OldScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if(!$OldScheme['IsSchemeChange'])
              return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа хостинга не позволяет смену тарифа');
            #-------------------------------------------------------------------
            $NewScheme = DB_Select('HostingSchemes',Array('ID','ServersGroupID','IsSchemeChangeable','QuotaDisk','Name'),Array('UNIQ','ID'=>$NewSchemeID));
            #-------------------------------------------------------------------
            switch(ValueOf($NewScheme)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('NEW_SCHEME_NOT_FOUND','Новый тарифный план не найден');
              case 'array':
                #---------------------------------------------------------------
                if($HostingOrder['SchemeID'] == $NewScheme['ID'])
                  return new gException('SCHEMES_MATCHED','Старый и новый тарифные планы совпадают');
                #---------------------------------------------------------------
                if(!$NewScheme['IsSchemeChangeable'])
                  return new gException('SCHEME_NOT_CHANGEABLE','Выбранный тариф не позволяет переход');
                #---------------------------------------------------------------
                if($OldScheme['QuotaDisk'] > $NewScheme['QuotaDisk']){
                  #-------------------------------------------------------------
		  if($OldScheme['IsProlong'])
                    if(!$IsPermission)
                      return new gException('QUOTA_DISK_ERROR','Дисковое пространство на новом тарифном плане, меньше чем на текущем. Для смены тарифа обратитесь в Центр Поддержки.');
                }
                #---------------------------------------------------------------
                if($HostingOrder['ServersGroupID'] != $NewScheme['ServersGroupID'])
                  return new gException('NEW_SCHEME_ANOTHER_SERVERS_GROUP','Выбранный тарифный план относиться к другой группе серверов');
                #---------------------------------------------------------------
                $HostingOrderID = (integer)$HostingOrder['ID'];
                #--------------------------TRANSACTION--------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('HostingOrderSchemeChange'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingSchemeChange','Params'=>Array($HostingOrderID,$HostingOrder['SchemeID'])));
                #---------------------------------------------------------------
                switch(ValueOf($IsAdd)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    $IsUpdate = DB_Update('HostingOrders',Array('SchemeID'=>$NewSchemeID,'OldSchemeID'=>$OldScheme['ID']),Array('ID'=>$HostingOrderID));
                    if(Is_Error($IsUpdate))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'SchemeChange','RowsIDs'=>$HostingOrderID,'Comment'=>SPrintF('Поступила заявка на изменение тарифного плана [%s=>%s]',$OldScheme['Name'],$NewScheme['Name'])));
                    #-----------------------------------------------------------
                    switch(ValueOf($Comp)){
                      case 'error':
                        return ERROR | @Trigger_Error(500);
                      case 'exception':
                        return ERROR | @Trigger_Error(400);
                      case 'array':
                        #-------------------------------------------------------
                        if(Is_Error(DB_Commit($TransactionID)))
                          return ERROR | @Trigger_Error(500);
                        #----------------------END TRANSACTION------------------
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
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
