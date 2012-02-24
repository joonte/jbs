<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
if(!Count($Args))
  return "No args...\n";
#-------------------------------------------------------------------------------
$ArgsIDs = Array('WMI_AUTO_ACCEPT','WMI_CREATE_DATE','WMI_CURRENCY_ID','WMI_DESCRIPTION','WMI_EXPIRED_DATE','WMI_FAIL_URL','WMI_LAST_NOTIFY_DATE','WMI_MERCHANT_ID','WMI_NOTIFY_COUNT','WMI_ORDER_ID','WMI_ORDER_STATE','WMI_PAYMENT_AMOUNT','WMI_PAYMENT_NO','WMI_PAYMENT_TYPE','WMI_SUCCESS_URL','WMI_TO_USER_ID','WMI_UPDATE_DATE','WMI_SIGNATURE');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
$OrderID = $Args['WMI_PAYMENT_NO'];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['W1'];
#-------------------------------------------------------------------------------
#uksort($ArgsIDs, "strcasecmp");
$sha_string = "";
foreach($ArgsIDs as $ArgID){
        if($ArgID != "WMI_SIGNATURE"){
                $sha_string .= @$Args[$ArgID];
        }
}
if(base64_encode(pack("H*", sha1($sha_string . $Settings['Hash']))) != $Args['WMI_SIGNATURE'])
  return ERROR | @Trigger_Error('[comp/www/Merchant/W1]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$OrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $InvoiceID = $Invoice['ID'];
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
      #-----------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',
        Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$InvoiceID,'Comment'=>'Автоматическое зачисление '.'['.$Args['WMI_PAYMENT_TYPE'].']'));
      #-----------------------------------------------------------------------
      switch(ValueOf($Comp)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'array':
          #-------------------------------------------------------------------------------
          $Result = "WMI_RESULT=OK\n";
          #-------------------------------------------------------------------------------
          return $Result;
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
