<?php

#-------------------------------------------------------------------------------
/** @author Кунич А.С. */
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
$ArgsIDs = Array('order_amount','pay_for','md5','type','order_currency','onpay_id','balance_currency');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['OnPay'];
#-------------------------------------------------------------------------------
$Hash = Array(
  #-----------------------------------------------------------------------------
  $Args['type'],
  $Args['pay_for'],
  $Args['onpay_id'],
  $Args['order_amount'],
  $Args['order_currency'],
  $Settings['MerchantPass']
);
#-------------------------------------------------------------------------------
if(!isset($Args['type']) || ($Args['type']!='check' && $Args['type']!='pay')) 
  return ERROR | @Trigger_Error('[comp/Merchant/OnPay]: не указан тип запроса');
  
		if ($Args['type'] == 'check') {
			return onPayAnswer($Args['type'], 0, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Settings['MerchantPass']);
		}

if(StrToUpper(MD5(Implode(';',$Hash))) != $Args['md5'])
  return onPayAnswerpay($Args['type'], 7, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error('[comp/Merchant/OnPay]: проверка подлинности завершилась не удачей'), $Settings['MerchantPass']);
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['pay_for']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error(500), $Settings['MerchantPass']);
  case 'exception':
    return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error(400), $Settings['MerchantPass']);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != round($Args['order_amount'], 2))
      return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error('[comp/Merchant/OnPay]: проверка суммы платежа завершилась не удачей'), $Settings['MerchantPass']);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error(500), $Settings['MerchantPass']);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],
       'Comment'=>'Автоматическое зачисление через систему OnPay.ru'.'['.$Args['balance_currency'].'/'.$Args['order_currency'].']'));
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error(500), $Settings['MerchantPass']);
      case 'exception':
        return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error(400), $Settings['MerchantPass']);
      case 'array':
        return onPayAnswerpay($Args['type'], 0, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id'], $Settings['MerchantPass']);
      default:
        return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error(101), $Settings['MerchantPass']);
    }
  default:
    return onPayAnswerpay($Args['type'], 3, $Args['pay_for'], $Args['order_amount'], $Args['order_currency'], 'OK', $Args['onpay_id']."\n".ERROR | @Trigger_Error(101), $Settings['MerchantPass']);
}
#-------------------------------------------------------------------------------


//функция выдает ответ для сервиса onpay в формате XML на чек запрос
function onPayAnswer($type, $code, $pay_for, $order_amount, $order_currency, $text, $private_code) {
	$md5 = strtoupper(md5("$type;$pay_for;$order_amount;$order_currency;$code;" . $private_code));
	return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
	"<result>\n".
	"<code>$code</code>\n".
	"<pay_for>$pay_for</pay_for>\n".
	"<comment>$text</comment>\n".
	"<md5>$md5</md5>\n".
	"</result>";
}

//функция выдает ответ для сервиса onpay в формате XML на pay запрос
function onPayAnswerpay($type, $code, $pay_for, $order_amount, $order_currency, $text, $onpay_id, $private_code) {
	$md5 = strtoupper(md5("$type;$pay_for;$onpay_id;$pay_for;$order_amount;$order_currency;$code;" . $private_code));
	return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
	"<result>\n".
	"<code>$code</code>\n".
	"<comment>$text</comment>\n".
	"<onpay_id>$onpay_id</onpay_id>\n".
	"<pay_for>$pay_for</pay_for>\n".
	"<order_id>$pay_for</order_id>\n".
	"<md5>$md5</md5>\n".
	"</result>";
}

?>
