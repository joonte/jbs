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
				23	=> 'Банковский перевод',
				24	=> 'Русский Стандарт Банк',
				30	=> 'Яндекс.Деньги',
				31	=> 'WebMoney',
				33	=> 'Тест',
				34	=> 'WebMoney карты',
				39	=> 'Сбербанк "Спасибо"',
				41	=> 'Билайн',
				45	=> 'WebMoney X20 с ЭДС',
				46	=> 'QIWI-кошелек',
				47	=> 'CONTACT',
				48	=> 'UniStream',
				49	=> 'Anelik',
				50	=> 'Сбербанк "Спасибо"',
				51	=> 'Webmoney X20 PIN',
				56	=> 'Электронные деньги ККБ',
				57	=> 'WebMoney Trust Test',
				58	=> 'WebMoney Trust',
				59	=> 'WebMoney X20 PIN Тест',
				62	=> 'Евросеть',
				63	=> 'Банковские карты',
				64	=> 'Промсвязьбанк',
				65	=> 'Связной',
				66	=> 'WebMoney x20 Test',
				68	=> 'WebMoney X20',
				70	=> 'Альфа-банк',
				71	=> 'WebMoney Invoice Test',
				72	=> 'WebMoney Invoice',
				73	=> 'МТС',
				74	=> 'МегаФон',
				75	=> 'VISA',
				76	=> 'MasterCard',
				77	=> 'Boleto Bancario EBANX',
				78	=> 'Transferencia Bancaria EBANX',
				79	=> 'WebMoney KKB e-money',
				80	=> 'WebMoney KKB e-money Тест',
				81	=> 'Онлайн-банкинг',
				82	=> 'Ростелеком',
				92	=> 'Пластиковые карты',
				93	=> 'Банковские карты',
				102	=> 'МТС',
				162	=> 'Банковская карта',
				301	=> 'WebMoney WMR',
				303	=> 'WebMoney',
			    );
#-------------------------------------------------------------------------------
$PS = SPrintF('%s / %s',((IsSet($LMI_PAYMENT_SYSTEMs[$Args['LMI_PAYMENT_SYSTEM']]))?$LMI_PAYMENT_SYSTEMs[$Args['LMI_PAYMENT_SYSTEM']]:'LMI_PAYMENT_SYSTEM'),$Args['LMI_PAYMENT_SYSTEM']);
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
