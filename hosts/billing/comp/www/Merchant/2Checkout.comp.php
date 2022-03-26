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
if(!Count($Args))
  return 'No args...';
#-------------------------------------------------------------------------------
$ArgsIDs = Array('message_type','invoice_status','md5_hash','sale_id','invoice_id','item_id_1','invoice_list_amount','fraud_status','payment_type');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
if($Args['message_type'] != 'FRAUD_STATUS_CHANGED')
  return 'Message Type not accepted.';
#-------------------------------------------------------------------------------
if(($Args['invoice_status'] != 'approved' && $Args['invoice_status'] != 'deposited') || $Args['fraud_status'] != 'pass')
  return 'Invoice Status or Fraud Status not accepted.';
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['Checkout'];
#-------------------------------------------------------------------------------
$Hash = $Args['sale_id'].
        $Args['vendor_id'].
        $Args['invoice_id'].
        $Settings['Hash'];
#-------------------------------------------------------------------------------
$Hash = StrToUpper(Md5($Hash));
#-------------------------------------------------------------------------------
if($Hash != $Args['md5_hash'])
  return ERROR | @Trigger_Error('[comp/Merchant/2Checkout]: проверка подлинности завершилась неудачей');
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['item_id_1']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['invoice_list_amount'])
      return ERROR | @Trigger_Error('[comp/Merchant/2Checkout]: проверка суммы платежа завершилась неудачей');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/StatusSet', Array('ModeID'=>'Invoices', 'StatusID'=>'Payed', 'RowsIDs'=>$Invoice['ID'], 'Comment'=>'Автоматическое зачисление [' . $Args['payment_type'] . ']'));
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        return 'Ok';
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
