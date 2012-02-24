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
$ArgsIDs = Array('pay_to_email','pay_from_email','merchant_id','transaction_id','mb_transaction_id','mb_amount','mb_currency','status','md5sig','amount','currency');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
if($Args['status'] != 2)
  return 'Check 2 accepted';
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['Moneybookers'];
#-------------------------------------------------------------------------------
/*$Hash = Array(
  #-----------------------------------------------------------------------------
);
#-------------------------------------------------------------------------------
if(MD5(Implode('::',$Hash)) != $Args['md5sig'])
  return ERROR | @Trigger_Error('[comp/Merchant/Moneybookers]: проверка подлинности завершилась не удачей');*/
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['transaction_id']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['mb_amount'])
      return ERROR | @Trigger_Error('[comp/Merchant/Moneybookers]: проверка суммы платежа завершилась не удачей');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],'Comment'=>'Автоматическое зачисление'));
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
