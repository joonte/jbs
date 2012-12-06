<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = &Args();
#-------------------------------------------------------------------------------
if(!Count($Args))
  return 'No args...';
#-------------------------------------------------------------------------------
$ArgsIDs = Array('LMI_PREREQUEST','LMI_PAYMENT_NO','LMI_SYS_PAYMENT_ID','LMI_SYS_PAYMENT_DATE','LMI_PAYMENT_AMOUNT','LMI_CURRENCY','LMI_PAID_AMOUNT','LMI_PAID_CURRENCY','LMI_PAYMENT_SYSTEM','LMI_SIM_MODE','LMI_HASH');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
  $Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
# В качестве ответа на запрос Компания может выдать пустой документ или текст “YES”
# (case-insensitive) - это означает, что Компания согласна принять платеж.
# Любой другой ответ от Компании воспринимается как отказ принять платеж,
# и выводится пользователю.
if($Args['LMI_PREREQUEST']){
  Debug("[comp/Merchant/PayMaster]: LMI_PREREQUEST is set to: " . $Args['LMI_PREREQUEST']);
  return 'YES';
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$LMI_PAYMENT_SYSTEMs = Array(
				2	=> 'WebMoney Check',
				3	=> 'WebMoney Test',
				5	=> 'Банковские карты',
				6	=> 'Наличными в отделении банка',
				8	=> 'Альфа-банк',
				21	=> 'Сбербанк "Спасибо"',
				22	=> 'ВТБ24',
				24	=> 'Русский Стандарт Банк',
				31	=> 'WebMoney',
				34	=> 'WebMoney карты',
				41	=> 'Билайн',
				63	=> 'Банковские карты',
				64	=> 'Промсвязьбанк',
				75	=> 'VISA',
				76	=> 'MasterCard',
			    );
#-------------------------------------------------------------------------------
if(IsSet($LMI_PAYMENT_SYSTEMs[$Args['LMI_PAYMENT_SYSTEM']])){
	$PS = $LMI_PAYMENT_SYSTEMs[$Args['LMI_PAYMENT_SYSTEM']];
}else{
	$PS = "LMI_PAYMENT_SYSTEM=" . $Args['LMI_PAYMENT_SYSTEM'];
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
$Settings = $Config['Invoices']['PaymentSystems']['PayMaster'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Hash =   $Args['LMI_MERCHANT_ID']	. ";"
	. $Args['LMI_PAYMENT_NO']	. ";"
	. $Args['LMI_SYS_PAYMENT_ID']	. ";"
	. $Args['LMI_SYS_PAYMENT_DATE'] . ";"
	. $Args['LMI_PAYMENT_AMOUNT']	. ";"
	. $Args['LMI_CURRENCY']		. ";"
	. $Args['LMI_PAID_AMOUNT']	. ";"
	. $Args['LMI_PAID_CURRENCY']	. ";"
	. $Args['LMI_PAYMENT_SYSTEM']	. ";"
	. $Args['LMI_SIM_MODE']		. ";"
	. $Settings['Hash'];
#-------------------------------------------------------------------------------
#Debug("[comp/Merchant/PayMaster]: " . $Hash);
$Hash = Md5($Hash, true);
#Debug("[comp/Merchant/PayMaster]: " . $Hash);
$Hash = base64_encode($Hash);
Debug("[comp/Merchant/PayMaster]: Local hash = " . $Hash);
#-------------------------------------------------------------------------------
if($Hash != $Args['LMI_HASH'])
  return ERROR | @Trigger_Error('[comp/Merchant/PayMaster]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['LMI_PAYMENT_NO']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['LMI_PAYMENT_AMOUNT'])
      return ERROR | @Trigger_Error('[comp/Merchant/PayMaster]: проверка суммы платежа завершилась не удачей');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Init',100);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],'Comment'=>SPrintF('Автоматическое зачисление [%s]',$PS)));
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        # В качестве ответа на данный запрос Компания может возвращать что угодно:
        # ответ игнорируется системой Paymaster. Единственное требование - запрос
        # должен быть обработан, т.е. вернуть HTTP-код 200.
        return 'YES';
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
