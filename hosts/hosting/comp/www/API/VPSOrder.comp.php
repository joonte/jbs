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
$VPSSchemeID	= (integer) @$Args['VPSSchemeID'];
$DiskTemplate   =  (string) @$Args['DiskTemplate'];
$ServerID	= (integer) @$Args['ServerID'];
$Comment	=  (string) @$Args['Comment'];
$DependOrderID	= (integer) @$Args['DependOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!$VPSSchemeID)
	return new gException('VPS_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ContractID)
	return new gException('CONTRACT_NOT_DEFINED','Не выбран договор');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VPSScheme = DB_Select('VPSSchemes',Array('ID','Name','ServersGroupID','IsActive'),Array('UNIQ','ID'=>$VPSSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа виртуального сервера не найден');
  case 'array':
    #---------------------------------------------------------------------------
    if(!$VPSScheme['IsActive'])
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
        $IsPermission = Permission_Check('ContractsRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
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
	    $Where = ($ServerID)?SPrintF('`ID` = %u',$ServerID):SPrintF("`ServersGroupID` = %u AND `IsDefault` = 'yes'",$VPSScheme['ServersGroupID']);
	    #-------------------------------------------------------------------
            $Server = DB_Select('Servers',Array('ID','Params'),Array('Where'=>$Where));
            #-------------------------------------------------------------------
            switch(ValueOf($Server)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('SERVER_NOT_DEFINED','Сервер размещения не определён');
              case 'array':
                #---------------------------------------------------------------
                $Server = Current($Server);
                #---------------------------------------------------------------
		$Password = Comp_Load('Passwords/Generator');
		if(Is_Error($Password))
                  return ERROR | @Trigger_Error(500);
                #-------------------------TRANSACTION---------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('VPSOrder'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'VPSRules'",$Contract['ID']);
                #---------------------------------------------------------------
                $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if($Count < 1){
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'VPSRules'));
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
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServerID'=>$Server['ID'],'Params'=>Array('DiskTemplate'=>$DiskTemplate),'ServiceID'=>30000,'DependOrderID'=>$DependOrderID));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Login = SPrintF('%s%s',$Server['Params']['Prefix'],$OrderID);
                #---------------------------------------------------------------
                $IVPSOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'  => $OrderID,
                  'SchemeID' => $VPSScheme['ID'],
                  'Login'    => $Login,
                  'Password' => $Password,
		  'Domain'   => SPrintF('%s.%s',$Login,$Server['Params']['Domain']),
                );
                #---------------------------------------------------------------
                $VPSOrderID = DB_Insert('VPSOrders',$IVPSOrder);
                if(Is_Error($VPSOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Waiting','RowsIDs'=>$VPSOrderID,'Comment'=>($Comment)?$Comment:'Заказ создан и ожидает оплаты'));
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
					'Text'		=> SPrintF('Сформирована заявка на заказ VPS логин (%s), тариф (%s)',$Login,$VPSScheme['Name'])
		                  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    if(Is_Error(DB_Commit($TransactionID)))
                      return ERROR | @Trigger_Error(500);
                    #----------------------END TRANSACTION----------------------
                    return Array('Status'=>'Ok','VPSOrderID'=>$VPSOrderID,'ServiceOrderID'=>$VPSOrderID,'OrderID'=>$OrderID);
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
