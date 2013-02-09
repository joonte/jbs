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
$ArgsIDs = Array('eshopId','paymentId','orderId','eshopAccount','serviceName','recipientAmount','recipientCurrency','paymentStatus','userName','userEmail','paymentData','secretKey','hash','paymentMethod');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
if($Args['paymentStatus'] != 5)
  return 'Check 2 accepted';
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['RBKMoney'];
#-------------------------------------------------------------------------------
$Hash = Array(
  #-----------------------------------------------------------------------------
  $Args['eshopId'],
  $Args['orderId'],
  $Args['serviceName'],
  $Args['eshopAccount'],
  $Args['recipientAmount'],
  $Args['recipientCurrency'],
  $Args['paymentStatus'],
  $Args['userName'],
  $Args['userEmail'],
  $Args['paymentData'],
  $Settings['secretKey']
);
#-------------------------------------------------------------------------------
if(MD5(Implode('::',$Hash)) != $Args['hash'])
  return ERROR | @Trigger_Error('[comp/Merchant/RBKMoney]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['orderId']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['recipientAmount'])
      return ERROR | @Trigger_Error('[comp/Merchant/RBKMoney]: проверка суммы платежа завершилась не удачей');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],'Comment'=>SPrintF('Автоматическое зачисление [%s]',IsSet($Args['paymentMethod'])?$Args['paymentMethod']:'not set')));
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
