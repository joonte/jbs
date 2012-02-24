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
$ArgsIDs = Array('requestDatetime','action','md5','shopId','orderNumber','customerNumber','orderCreatedDatetime','orderSumAmount','orderSumCurrencyPaycash','orderSumBankPaycash','shopSumAmount','shopSumCurrencyPaycash','shopSumBankPaycash','orderIsPaid');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
$OrderID = $Args['orderNumber'];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['Yandex'];
#-------------------------------------------------------------------------------
$Md5 = Array(
  #-----------------------------------------------------------------------------
  $Args['orderIsPaid'],
  $Args['orderSumAmount'],
  $Args['shopSumCurrencyPaycash'],
  $Args['orderSumBankPaycash'],
  $Args['shopId'],
  $Args['invoiceId'],
  $Args['customerNumber'],
  $Settings['Hash']
);
#-------------------------------------------------------------------------------
if(StrToUpper(Md5(Implode(';',$Md5))) != $Args['md5'])
  return ERROR | @Trigger_Error('[comp/www/Merchant/Yandex]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
$Date = Date('c', Time());
#-------------------------------------------------------------------------------
$ShopID = $Settings['Send']['ShopID'];
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
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['orderSumAmount'])
      return ERROR | @Trigger_Error('[comp/Merchant/Yandex]: проверка суммы платежа завершилась неудачей');
    #---------------------------------------------------------------------------
    $InvoiceID = $Invoice['ID'];
    #---------------------------------------------------------------------------
    switch($Args['action']){
      case 'Check':
#-------------------------------------------------------------------------------
$Result = <<<EOD
<?xml version="1.0" encoding="windows-1251"?>
<response performedDatetime="%s">
	<result code="0" action="Check" shopId="%s" invoiceId="%s" />
</response>
EOD;
#-------------------------------------------------------------------------------
	return SPrintF(trim($Result),$Date,$Args['shopId'],$Args['invoiceId']);
      case 'PaymentSuccess':
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Users/Init',100);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$InvoiceID,'Comment'=>'Автоматическое зачисление'));
        #-----------------------------------------------------------------------
        switch(ValueOf($Comp)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
#-------------------------------------------------------------------------------
$Result = <<<EOD
<?xml version="1.0" encoding="windows-1251"?>
<response performedDatetime="%s">
	<result code="0" action="PaymentSuccess" shopId="%s" invoiceId="%s" />
</response>
EOD;
#-------------------------------------------------------------------------------
            return SPrintF(trim($Result),$Date,$Args['shopId'],$Args['invoiceId']);
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
