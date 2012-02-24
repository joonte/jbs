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
$DSSchemeID = (integer) @$Args['DSSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!$DSSchemeID)
  return new gException('DS_SCHEME_NOT_DEFINED','Сервер не выбран');
#-------------------------------------------------------------------------------
$DSScheme = DB_Select('DSSchemes',Array('ID','Name','ServersGroupID','IsActive'),Array('UNIQ','ID'=>$DSSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Выбранный сервер не найден');
  case 'array':
    #---------------------------------------------------------------------------
    if(!$DSScheme['IsActive'])
      return new gException('SCHEME_NOT_ACTIVE','Выбранный сервер нельзя заказать - не активен');
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
                #---------------------------------------------------------------
                #-------------------------TRANSACTION---------------------------
                if(Is_Error(DB_Transaction($TransactionID = UniqID('DSOrder'))))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Where = SPrintF("`ContractID` = %u AND `TypeID` = 'DSRules'",$Contract['ID']);
                #---------------------------------------------------------------
                $Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if($Count < 1){
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'DSRules'));
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
                $OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>40000));
                if(Is_Error($OrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                #---------------------------------------------------------------
                $IDSOrder = Array(
                  #-------------------------------------------------------------
                  'OrderID'  => $OrderID,
                  'SchemeID' => $DSScheme['ID']
                );
                #---------------------------------------------------------------
                $DSOrderID = DB_Insert('DSOrders',$IDSOrder);
                if(Is_Error($DSOrderID))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'Waiting','RowsIDs'=>$DSOrderID,'Comment'=>'Заказ создан и ожидает оплаты'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $Contract['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Сформирована заявка на аренду выделенного сервера (%s)',$DSScheme['Name'])
		                  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    if(Is_Error(DB_Commit($TransactionID)))
                      return ERROR | @Trigger_Error(500);
                    #----------------------END TRANSACTION----------------------
                    return Array('Status'=>'Ok','DSOrderID'=>$DSOrderID);
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
