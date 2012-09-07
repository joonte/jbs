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
$NewSchemeID    = (integer) @$Args['NewSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','StatusID','StatusDate');
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('HOSTING_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ISPswOrdersSchemeChange',(integer)$__USER['ID'],(integer)$ISPswOrder['UserID']);
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
        if($ISPswOrder['StatusID'] != 'Active')
          return new gException('ORDER_NO_ACTIVE','Заказ ПО не активен');
        #-----------------------------------------------------------------------
	if(!$__USER['IsAdmin']){
            #-------------------------------------------------------------------
            $LastChange = Time() - $ISPswOrder['StatusDate'];
            #-------------------------------------------------------------------
            if($LastChange < 86400){
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Date/Remainder',$LastChange);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              return new gException('TIME_NOT_EXPIRED',SPrintF('Тарифный план можно менять только 1 раз в сутки, сменить тарифный план можно только через %s, однако, в случае необходимости Вы можете обратиться в службу поддержки',$Comp));
            }
        }
        #-----------------------------------------------------------------------
        $OldScheme = DB_Select('ISPswSchemes',Array('IsSchemeChange','Name'),Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($OldScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if(!$OldScheme['IsSchemeChange'])
              return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа ПО не позволяет смену тарифа');
            #-------------------------------------------------------------------
            $NewScheme = DB_Select('ISPswSchemes',Array('ID','IsSchemeChangeable','Name'),Array('UNIQ','ID'=>$NewSchemeID));
            #-------------------------------------------------------------------
            switch(ValueOf($NewScheme)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('NEW_SCHEME_NOT_FOUND','Новый тарифный план не найден');
              case 'array':
                #---------------------------------------------------------------
                if($ISPswOrder['SchemeID'] == $NewScheme['ID'])
                  return new gException('SCHEMES_MATCHED','Старый и новый тарифные планы совпадают');
                #---------------------------------------------------------------
                if(!$NewScheme['IsSchemeChangeable'])
                  return new gException('SCHEME_NOT_CHANGEABLE','Выбранный тариф не позволяет переход');
                #---------------------------------------------------------------
#                if($OldScheme['QuotaDisk'] > $NewScheme['QuotaDisk']){
#                  #-------------------------------------------------------------
#                  if(!$__USER['IsAdmin'])
#                    return new gException('QUOTA_DISK_ERROR','Для смены тарифа обратитесь в Центр Поддержки');
#                }
                #---------------------------------------------------------------
                $ISPswOrderID = (integer)$ISPswOrder['ID'];
                #--------------------------TRANSACTION--------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('ISPswOrderSchemeChange'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$ISPswOrder['UserID'],'TypeID'=>'ISPswSchemeChange','Params'=>Array($ISPswOrderID,$ISPswOrder['SchemeID'])));
                #---------------------------------------------------------------
                switch(ValueOf($IsAdd)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    $IsUpdate = DB_Update('ISPswOrders',Array('SchemeID'=>$NewSchemeID),Array('ID'=>$ISPswOrderID));
                    if(Is_Error($IsUpdate))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'SchemeChange','RowsIDs'=>$ISPswOrderID,'Comment'=>"Поступила заявка на изменение тарифного плана [".$OldScheme['Name']."->".$NewScheme['Name']."]"));
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
