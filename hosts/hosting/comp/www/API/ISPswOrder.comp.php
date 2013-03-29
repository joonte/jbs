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
$ContractID	= (integer) @$Args['ContractID'];
$ISPswSchemeID	= (integer) @$Args['ISPswSchemeID'];
$IP		=  (string) @$Args['IP'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!$ISPswSchemeID)
  return new gException('ISPsw_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ISPswScheme = DB_Select('ISPswSchemes',Array('*'),Array('UNIQ','ID'=>$ISPswSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа ПО ISPsystem не найден');
  case 'array':
    #---------------------------------------------------------------------------
    if(!$ISPswScheme['IsActive'])
      return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа ПО ISPsystem не активен');
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    # проверяем IP адрес который нам воткнули, если это внешний заказ
    if(!$ISPswScheme['IsInternal']){
      #---------------------------------------------------------------------------
      $ISPswInfo = Array(
      			'IP'		=> $IP,
			'ISPtype'	=> $ISPswScheme['ISPtype'],
      		);
      #---------------------------------------------------------------------------
      if(Is_Error(System_Load('libs/IspSoft.php')))
        return ERROR | @Trigger_Error(500);
      #-----------------------------------------------------------------------------
      $Config = Config();
      $Settings = $Config['IspSoft']['Settings'];
      if(IspSoft_Check_ISPsystem_IP($Settings, $ISPswInfo)){
      	#OK
      }else{
        return new gException('ISPsw_IP_ADDRESS_IN_USE','Для указанного IP адреса [' . $IP . '] уже есть лицензия такого типа. За более подробной информацией, обратитесь в службу поддержки пользователей.');
      }
    }
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    # проверка - есть ли заказ от этого пользователя на этот IP
    if($ISPswScheme['IsInternal']){
	$Count = DB_Count('ISPswOrdersOwners',
	    		Array(
				#'Where'=>"`IP` = '" . $IP . "' AND `SchemeID` = " . $ISPswSchemeID . " AND `StatusID` != 'Deleted' AND `UserID` = " . $__USER['ID']
				'Where'=>"`IP` = '" . $IP . "' AND `StatusID` != 'Deleted' AND `UserID`=" . $__USER['ID'] . " AND (SELECT `SoftWareGroup` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) = " . $ISPswScheme['SoftWareGroup']
				)
			);
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if($Count)
    	return new gException('SOFTWARE_EXISTS','Для данного заказа VPS/DS уже существует заказ такого программного обеспечения ISPsystem. Для продления пробной лицензии - просто смените тарифный план, для активации заказа - оплатите его.');
    }
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    # проверяем, если тарифный плане не поддерживает продление, то заказывать его повторно для
    # этого же IP юзер не может, т.к. это не имеет смысла. тем самым срезаются попытки
    # повторного заказа триальных и вечных лицензий - т.к. и те и другие не продлеваются
    $Count = DB_Count('ISPswOrdersOwners',
    			Array(
				'Where'=>"`IP` = '" . $IP . "' AND `SchemeID` = " . $ISPswSchemeID . " AND (`StatusID` = 'Deleted' OR `StatusID` = 'Suspended') AND `UserID` = " . $__USER['ID'] . " AND (SELECT `IsProlong` FROM `ISPswSchemesOwners` WHERE `ISPswSchemesOwners`.`ID` = `ISPswOrdersOwners`.`SchemeID`) = 'no'"
			)
		);
    if(Is_Error($Count))
    	return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if($Count)
    	return new gException('SOFTWARE_EXISTS','Нельзя заказать вечную или триальную лицензию дважды для одного и того же IP адреса.');
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Contract = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contract)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('CONTRACT_NOT_FOUND','Договор не найден');
      case 'array':
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsPermission)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'false':
            return ERROR | @Trigger_Error(700);
          case 'true':
            #-------------------------------------------------------------------
                #---------------------------------------------------------------
                #---------------------------------------------------------------
                #-------------------------TRANSACTION---------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('ISPswOrder'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'ISPswRules'",$Contract['ID']);
                #---------------------------------------------------------------
                $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if($Count < 1){
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'ISPswRules'));
                  #-------------------------------------------------------------
                  switch(ValueOf($Comp)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'integer':
                      # No more...
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                }
                #---------------------------------------------------------------
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>51000));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                #---------------------------------------------------------------
                $IISPswOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'	=> $OrderID,
                  'SchemeID'	=> $ISPswScheme['ID'],
		  'IP'		=> $IP,
                );
                #---------------------------------------------------------------
                $ISPswOrderID = DB_Insert('ISPswOrders',$IISPswOrder);
                if(Is_Error($ISPswOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Waiting','RowsIDs'=>$ISPswOrderID,'Comment'=>'Заказ создан и ожидает оплаты'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    #return ERROR | @Trigger_Error(400);
		    return $Comp;
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $Contract['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Сформирована заявка на заказ ПО, тариф (%s)',$ISPswScheme['Name'])
		                  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    if(Is_Error(DB_Commit($TransactionID)))
                      return ERROR | @Trigger_Error(500);
                    #----------------------END TRANSACTION----------------------
                    return Array('Status'=>'Ok','ISPswOrderID'=>$ISPswOrderID);
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
