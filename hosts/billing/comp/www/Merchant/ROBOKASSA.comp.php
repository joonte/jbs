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
$ArgsIDs = Array('OutSum','InvId','SignatureValue');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['ROBOKASSA'];
#-------------------------------------------------------------------------------
$Hash = Array(
  #-----------------------------------------------------------------------------
  $Args['OutSum'],
  $Args['InvId'],
  $Settings['MerchantPass2']
);
#-------------------------------------------------------------------------------
if(StrToUpper(MD5(Implode(':',$Hash))) != $Args['SignatureValue'])
  return ERROR | @Trigger_Error('[comp/Merchant/ROBOKASSA]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['InvId']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['OutSum'])
      return ERROR | @Trigger_Error('[comp/Merchant/ROBOKASSA]: проверка суммы платежа завершилась не удачей');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],'Comment'=>SPrintF('Автоматическое зачисление [%s/%s]',$Args['PaymentMethod'],$Args['IncCurrLabel'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        return 'OK' . $Args['InvId'];
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
