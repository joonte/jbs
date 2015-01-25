<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ISPswOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Order = DB_Select('Orders',Array('ID','ContractID'),Array('UNIQ','ID'=>$ISPswOrder['OrderID']));
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
          $Where = SPrintF('`OrderID` = %u AND `DaysRemainded` > 0',$ISPswOrder['OrderID']);
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
                  'ServiceID'  => $ISPswOrder['ServiceID'],
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
switch($ISPswOrder['StatusID']){
  case 'SchemeChange':
    #---------------------------------------------------------------------------
    $ISPswScheme = DB_Select('ISPswSchemes','CostDay',Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($ISPswScheme)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Cost = $ISPswScheme['CostDay'];
        #-----------------------------------------------------------------------
        $IsQuery = DB_Query(SPrintF('UPDATE `OrdersConsider` SET `DaysRemainded` = `DaysRemainded`*(`Cost`/%f), `DaysConsidered` = `DaysConsidered`*(`Cost`/%f), `Cost` = %f WHERE `DaysRemainded` > 0 AND `OrderID` = %u AND `Cost` != %f',$Cost,$Cost,$Cost,$ISPswOrder['OrderID'],$Cost));
        if(Is_Error($IsQuery))
          return ERROR | @Trigger_Error(500);
      break 2;
      default:
         return ERROR | @Trigger_Error(101);
    }
  case 'Suspended':
    #---------------------------------------------------------------------------
    $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$ISPswOrder['UserID'],'TypeID'=>'ISPswActive','Params'=>Array($ISPswOrder['ID'])));
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
$IsUpdate = DB_Update('ISPswOrders',Array('ConsiderDay'=>0),Array('ID'=>$ISPswOrder['ID']));
if(Is_Error($IsUpdate))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
