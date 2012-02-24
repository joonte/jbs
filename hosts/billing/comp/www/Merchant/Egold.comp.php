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
$ArgsIDs = Array('PAYMENT_ID','PAYEE_ACCOUNT','PAYMENT_AMOUNT','PAYMENT_UNITS','PAYMENT_METAL_ID','PAYMENT_BATCH_NUM','PAYER_ACCOUNT','ACTUAL_PAYMENT_OUNCES','USD_PER_OUNCE','FEEWEIGHT','TIMESTAMPGMT','V2_HASH');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['Egold'];
#-------------------------------------------------------------------------------
$Hash = Array(
  #-----------------------------------------------------------------------------
  $Args['PAYMENT_ID'],
  $Args['PAYEE_ACCOUNT'],
  $Args['PAYMENT_AMOUNT'],
  $Args['PAYMENT_UNITS'],
  $Args['PAYMENT_METAL_ID'],
  $Args['PAYMENT_BATCH_NUM'],
  $Args['PAYER_ACCOUNT'],
  StrToUpper(Md5($Settings['Hash'])),
  $Args['ACTUAL_PAYMENT_OUNCES'],
  $Args['USD_PER_OUNCE'],
  $Args['FEEWEIGHT'],
  $Args['TIMESTAMPGMT']
);
#-------------------------------------------------------------------------------
$Hash = StrToUpper(Md5(Implode(':',$Hash)));
#-------------------------------------------------------------------------------
if($Hash != $Args['V2_HASH'])
  return ERROR | @Trigger_Error('[comp/www/Merchant/Egold]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['PAYMENT_ID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['PAYEE_ACCOUNT'])
      return ERROR | @Trigger_Error('[comp/Merchant/Egold]: проверка суммы платежа завершилась не удачей');
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
        return 'YES';
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
