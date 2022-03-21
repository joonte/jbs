<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('SystemID','InvoiceID','Summ');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['PayBox'];
#-------------------------------------------------------------------------------
$Send = $Settings['Send'];
#-------------------------------------------------------------------------------
// Идентификатор платежа в системе мерчанта. Рекомендуется поддерживать уникальность этого поля.
$Send['pg_order_id'] = $InvoiceID;
#-------------------------------------------------------------------------------
// Идентификатор мерчанта в PayBox.money Выдается при подключении.
$Send['pg_merchant_id'] = $Settings['ShopId'];
#-------------------------------------------------------------------------------
// Сумма платежа в валюте pg_currency.
$Send['pg_amount'] = Round($Summ/$Settings['Course'],2);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Invoice/Number',$InvoiceID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
// Описание товара или услуги. Отображается покупателю в процессе платежа.
$Send['pg_description'] = SPrintF('%s%s, %s (%s)',$Send['description'],$Comp,Translit($__USER['Name']),$__USER['Email']);
#-------------------------------------------------------------------------------
// Случайная строка
$Send['pg_salt'] = Md5(MicroTime());
#-------------------------------------------------------------------------------
// Валюта, в которой указана сумма. 
$Send['pg_currency'] = $Settings['Valute'];
#-------------------------------------------------------------------------------
// Контактный адрес электронной почты пользователя.
$Send['pg_user_contact_email'] = $__USER['Email'];
#-------------------------------------------------------------------------------
// Телефон пользователя (начиная с кода страны)
foreach($__USER['Contacts'] as $Contact){
	#-------------------------------------------------------------------------------
	if($Contact['MethodID'] == 'SMS'){
		#-------------------------------------------------------------------------------
		$Send['pg_user_phone'] = $Contact['Address'];
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
// сортируем параметры по алфавиту (ключи) 
KSort($Send);
#-------------------------------------------------------------------------------
// строка подписи
$Sign = SPrintF('payment.php;%s;%s;%s',$GLOBALS['CSRF'],Implode(';',$Send),$Settings['Hash']);
#-------------------------------------------------------------------------------
//Debug(SPrintF('[comp/Invoices/PaymentSystems/PayBox]: Sign = %s',$Sign));
$Send['pg_sig'] = Md5($Sign);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Send;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
