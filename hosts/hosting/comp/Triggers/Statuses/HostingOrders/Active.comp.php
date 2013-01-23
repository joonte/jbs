<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('HostingOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Order = DB_Select('Orders',Array('ID','ContractID'),Array('UNIQ','ID'=>$HostingOrder['OrderID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Order)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Contract = DB_Select('Contracts',Array('ID','IsUponConsider'),Array('UNIQ','ID'=>$Order['ContractID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contract)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        if(!$Contract['IsUponConsider']){
          #---------------------------------------------------------------------
          $CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
          #---------------------------------------------------------------------
          $Number = Comp_Load('Formats/Order/Number',$Order['ID']);
          if(Is_Error($Number))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Where = SPrintF('`OrderID` = %u AND `DaysRemainded` > 0',$Order['ID']);
          #---------------------------------------------------------------------
          $OrdersConsider = DB_Select('OrdersConsider','*',Array('Where'=>$Where));
          #---------------------------------------------------------------------
          switch(ValueOf($OrdersConsider)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              # No more...
            break;
            case 'array':
              #-----------------------------------------------------------------
              if(Is_Error(DB_Transaction($TransactionID = UniqID('OrdersConsider'))))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              foreach($OrdersConsider as $ConsiderItem){
                #---------------------------------------------------------------
                $IWorkComplite = Array(
                  #-------------------------------------------------------------
                  'ContractID' => $Contract['ID'],
                  'Month'      => $CurrentMonth,
                  'ServiceID'  => 10000,
                  'Comment'    => SPrintF('№%s',$Number),
                  'Amount'     => $ConsiderItem['DaysConsidered'],
                  'Cost'       => $ConsiderItem['Cost'],
                  'Discont'    => $ConsiderItem['Discont']
                );
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('WorksComplite',$IWorkComplite);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$ConsiderItem['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              if(Is_Error(DB_Commit($TransactionID)))
                return ERROR | @Trigger_Error(500);
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        }
      break 2;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
switch($HostingOrder['StatusID']){
  case 'SchemeChange':
    #---------------------------------------------------------------------------
    $HostingScheme = DB_Select('HostingSchemes','CostDay',Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($HostingScheme)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Cost = $HostingScheme['CostDay'];
        #-----------------------------------------------------------------------
        $IsQuery = DB_Query(SPrintF('UPDATE `OrdersConsider` SET `DaysRemainded` = `DaysRemainded`*(`Cost`/%f), `DaysConsidered` = `DaysConsidered`*(`Cost`/%f), `Cost` = %f WHERE `DaysRemainded` > 0 AND `OrderID` = %u AND `Cost` != %f',$Cost,$Cost,$Cost,$Order['ID'],$Cost));
        if(Is_Error($IsQuery))
          return ERROR | @Trigger_Error(500);
      break 2;
      default:
         return ERROR | @Trigger_Error(101);
    }
  case 'Suspended':
  #-----------------------------------------------------------------------
  # added by lissyara 2012-08-29 in 11:39 MSK, for JBS-474
  /*
  $Where = Array("`TypeID` = 'HostingDelete' OR `TypeID` = 'HostingSuspend' OR `TypeID` = 'HostingActive' OR `TypeID` = 'HostingCreate'","`IsExecuted` = 'no'");
  #-------------------------------------------------------------------------------
  $TaskExecuteTime = DB_Select('Tasks','ExecuteDate',Array('UNIQ','Where'=>$Where,'SortOn'=>'ExecuteDate','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
  #-------------------------------------------------------------------------------
  switch(ValueOf($TaskExecuteTime)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      $ExecuteDate = Time();
      break;
    case 'array':
      if($TaskExecuteTime['ExecuteDate'] > Time() + 2*3600){
        $ExecuteDate = Time();
      }else{
        $ExecuteDate = $TaskExecuteTime['ExecuteDate'] + 2*60;
      }
      break;
    default:
      return ERROR | @Trigger_Error(101);
    }
    */
    #-------------------------------------------------------------------------------
    # added by lissyara, for JBS-536
    $Where = Array(SPrintF('`Params` = \'{"ID":"%u"}\'',$HostingOrder['ID']));
    #-------------------------------------------------------------------------------
    $TaskExecuteTime = DB_Select('Tasks','ExecuteDate',Array('UNIQ','Where'=>$Where,'SortOn'=>'ExecuteDate','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
    #-------------------------------------------------------------------------------
    switch(ValueOf($TaskExecuteTime)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      $ExecuteDate = Time();
      break;
    case 'array':
        $ExecuteDate = $TaskExecuteTime['ExecuteDate'] + 2*60;
      break;
    default:
      return ERROR | @Trigger_Error(101);
    }

    #$ExecuteDate = Time();
    #---------------------------------------------------------------------------
    $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingActive','ExecuteDate'=>$ExecuteDate,'Params'=>Array($HostingOrder['ID'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($IsAdd)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        # No more...
      break 2;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    # No more...
}
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('HostingOrders',Array('ConsiderDay'=>0),Array('ID'=>$HostingOrder['ID']));
if(Is_Error($IsUpdate))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
