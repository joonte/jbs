<?php


/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Null($Args)){
  #-----------------------------------------------------------------------------
  if(Is_Error(System_Load('modules/Authorisation.mod')))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$DomainOrderID = (integer) @$Args['DomainOrderID'];
#--------------------------------TRANSACTION------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainOrderRestore'))))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners',Array('ID','OrderID','UserID','ContractID'),Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $SummRemainded = DB_Select('DomainConsider','SUM(`YearsRemainded`*`Cost`*(1-`Discont`)) as `SummRemainded`',Array('UNIQ','Where'=>SPrintF('`DomainOrderID` = %u AND `YearsRemainded` > 0',$DomainOrder['ID'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($SummRemainded)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $SummRemainded = (double)$SummRemainded['SummRemainded'];
        #-----------------------------------------------------------------------
        if($SummRemainded){
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Formats/Order/Number',$DomainOrder['OrderID']);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$DomainOrder['ContractID'],'Summ'=>$SummRemainded,'ServiceID'=>3000,'Comment'=>SPrintF('Заказ домена №%s',$Comp)));
          #---------------------------------------------------------------------
          switch(ValueOf($IsUpdate)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return ERROR | @Trigger_Error(400);
            case 'array':
              #-----------------------------------------------------------------
              $IsUpdate = DB_Update('DomainConsider',Array('YearsRemainded'=>0),Array('Where'=>SPrintF('`DomainOrderID` = %u',$DomainOrderID)));
              if(Is_Error($IsUpdate))
                return ERROR | @Trigger_Error(500);
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        }
        #-----------------------------------------------------------------------
        if(Is_Error(DB_Commit($TransactionID)))
          return ERROR | @Trigger_Error(500);
        #--------------------------END TRANSACTION------------------------------
        return Array('Status'=>'Ok');
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
