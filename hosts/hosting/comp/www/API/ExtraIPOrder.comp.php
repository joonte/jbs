<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ContractID	= (integer) @$Args['ContractID'];
$ExtraIPSchemeID= (integer) @$Args['ExtraIPSchemeID'];
$OrderType	=  (string) @$Args['OrderType'];	# тип заказа к которому цепляем IP
$DependOrderID	= (integer) @$Args['DependOrderID'];	# номер заказа к которому цепляем IP
$Comment	=  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!$ExtraIPSchemeID)
  return new gException('ExtraIP_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
$ExtraIPScheme = DB_Select('ExtraIPSchemes',Array('ID','Name','IsActive'),Array('UNIQ','ID'=>$ExtraIPSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа виртуального сервера не найден');
  case 'array':
    #---------------------------------------------------------------------------
    if(!$ExtraIPScheme['IsActive'])
      return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа виртуального сервера не активен');
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
        $__USER = $GLOBALS['__USER'];
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
                #-------------------------TRANSACTION---------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('ExtraIPOrder'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'ExtraIPRules'",$Contract['ID']);
                #---------------------------------------------------------------
                $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if($Count < 1){
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'ExtraIPRules'));
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
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>50000));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                #---------------------------------------------------------------
                $IExtraIPOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'		=> $OrderID,
                  'SchemeID'		=> $ExtraIPScheme['ID'],
		  'DependOrderID'	=> $DependOrderID,
		  'OrderType'		=> $OrderType,
                );
                #---------------------------------------------------------------
                $ExtraIPOrderID = DB_Insert('ExtraIPOrders',$IExtraIPOrder);
                if(Is_Error($ExtraIPOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Waiting','RowsIDs'=>$ExtraIPOrderID,'Comment'=>($Comment)?$Comment:'Заказ создан и ожидает оплаты'));
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
					'Text'		=> SPrintF('Сформирована заявка на заказ выделенного IP адреса, по тарифу (%s)',$ExtraIPScheme['Name'])
		                  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    if(Is_Error(DB_Commit($TransactionID)))
                      return ERROR | @Trigger_Error(500);
                    #----------------------END TRANSACTION----------------------
                    return Array('Status'=>'Ok','ExtraIPOrderID'=>$ExtraIPOrderID,'ServiceOrderID'=>$ExtraIPOrderID,'OrderID'=>$OrderID);
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
