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
if(!Count($Args))
  return 'No args...';
#-------------------------------------------------------------------------------
$ArgsIDs = Array('ik_shop_id','ik_payment_amount','ik_payment_id','ik_payment_desc','ik_paysystem_alias','ik_baggage_fields','ik_payment_timestamp','ik_payment_state','ik_trans_id','ik_currency_exch','ik_fees_payer','ik_sign_hash');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['InterKassa'];
#-------------------------------------------------------------------------------
$Hash = Array(
  #-----------------------------------------------------------------------------
  $Args['ik_shop_id'],
  $Args['ik_payment_amount'],
  $Args['ik_payment_id'],
  $Args['ik_paysystem_alias'],
  $Args['ik_baggage_fields'],
  $Args['ik_payment_state'],
  $Args['ik_trans_id'],
  $Args['ik_currency_exch'],
  $Args['ik_fees_payer'],
  $Settings['Hash']
);
#-------------------------------------------------------------------------------
if(StrToUpper(MD5(Implode(':',$Hash))) != $Args['ik_sign_hash'])
  return ERROR | @Trigger_Error('[comp/Merchant/InterKassa]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Args['ik_payment_state'] != 'success')
  return ERROR | @Trigger_Error('[comp/Merchant/InterKassa]: платёж не был осуществлён');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['ik_payment_id']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['ik_payment_amount'])
      return ERROR | @Trigger_Error('[comp/Merchant/InterKassa]: проверка суммы платежа завершилась не удачей');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],
       'Comment'=>'Автоматическое зачисление '.'['.$Args['ik_paysystem_alias'].']'));
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        return 'OK';
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
