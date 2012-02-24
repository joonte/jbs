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
$InvoicesIDs  = (array) @$Args['RowsIDs'];
#-------------------------------------------------------------------------------
if(Count($InvoicesIDs) < 1)
  return new gException('ACCOUNTS_NOT_SELECTED','Счета на отмену не выбраны');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach($InvoicesIDs as $InvoiceID)
  $Array[] = (integer)$InvoiceID;
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID','StatusID','IsPosted'),Array('Where'=>SPrintF('`ID` IN (%s)',Implode(',',$Array))));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoices)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    if(Is_Error(DB_Transaction($TransactionID = UniqID('InvoicesReject'))))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    foreach($Invoices as $Invoice){
      #-------------------------------------------------------------------------
      $IsPermission = Permission_Check('InvoiceEdit',(integer)$__USER['ID'],(integer)$Invoice['UserID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsPermission)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'false':
          return ERROR | @Trigger_Error(700);
        case 'true':
          #---------------------------------------------------------------------
          $Number = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
          if(Is_Error($Number))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if($Invoice['IsPosted'])
            return new gException('ACCOUNT_PAYED',SPrintF('Счет №%s оплачен и не может быть отменен',$Number));
          #---------------------------------------------------------------------
          if($Invoice['StatusID'] == 'Rejected')
            return new gException('ACCOUNT_REJECTED',SPrintF('Счет №%s уже отменен',$Number));
          #---------------------------------------------------------------------
          $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Rejected','RowsIDs'=>$Invoice['ID']));
          #---------------------------------------------------------------------
          switch(ValueOf($Comp)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return $StatusSet;
            case 'array':
              # No more...
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
    #---------------------------------------------------------------------------
    if(Is_Error(DB_Commit($TransactionID)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok');
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
