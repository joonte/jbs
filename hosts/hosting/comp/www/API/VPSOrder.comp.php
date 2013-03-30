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
$ContractID      = (integer) @$Args['ContractID'];
$VPSSchemeID = (integer) @$Args['VPSSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!$VPSSchemeID)
  return new gException('VPS_SCHEME_NOT_DEFINED','Тарифный план не выбран');
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
            $VPSServer = DB_Select('VPSServers',Array('ID','Domain','Prefix'),Array('Where'=>SPrintF("`ServersGroupID` = %u AND `IsDefault` = 'yes'",$VPSScheme['ServersGroupID'])));
            #-------------------------------------------------------------------
            switch(ValueOf($VPSServer)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('SERVER_NOT_DEFINED','Сервер размещения не определён');
              case 'array':
                #---------------------------------------------------------------
                $VPSServer = Current($VPSServer);
                #---------------------------------------------------------------
                $Password = SubStr(Md5(UniqID()),0,12);
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
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>30000));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Login = SPrintF('%s%s',$VPSServer['Prefix'],$OrderID);
                #---------------------------------------------------------------
                $IVPSOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'  => $OrderID,
                  'SchemeID' => $VPSScheme['ID'],
                  'ServerID' => $VPSServer['ID'],
                  'Login'    => $Login,
                  'Password' => $Password,
		  'Domain'   => $Login . '.' . $VPSServer['Domain'],
                );
                #---------------------------------------------------------------
                $VPSOrderID = DB_Insert('VPSOrders',$IVPSOrder);
                if(Is_Error($VPSOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Waiting','RowsIDs'=>$VPSOrderID,'Comment'=>'Заказ создан и ожидает оплаты'));
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
                    return Array('Status'=>'Ok','VPSOrderID'=>$VPSOrderID);
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
