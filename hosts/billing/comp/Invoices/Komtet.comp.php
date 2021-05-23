<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Settings','Invoice','Number','InvoicesItems');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# https://github.com/Komtet/komtet-kassa-php-sdk
if(Is_Error(System_Load('classes/Komtet/Client.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/QueueManager.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// PSR-совместимый логгер (опциональный параметр)
$logger = null;
$client = new Client($Settings['ShopId'], $Settings['Hash'], $logger);
$manager = new QueueManager($client);
#-------------------------------------------------------------------------------
#После чего зарегистрировать очереди:
$manager->registerQueue(HOST_ID, $Settings['QueueId']);
# и установить очередь по умолчанию
$manager->setDefaultQueue(HOST_ID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Отправка чека на печать:
if(Is_Error(System_Load('classes/Komtet/Agent.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Check.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Cashier.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Payment.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Position.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/TaxSystem.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Vat.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/CalculationSubject.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/AuthorisedPerson.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Correction.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/TaskManager.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/CalculationMethod.php',)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/CorrectionCheck.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Exception/SdkException.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Komtet/Exception/ClientException.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$check = Check::createSell($Invoice['ID'],$Invoice['Email'],TaxSystem($Settings['TaxationSystem'])); // или Check::createSellReturn для оформления возврата
// Говорим, что чек нужно распечатать
$check->setShouldPrint(true);
#-------------------------------------------------------------------------------
$vat = new Vat(Vat::RATE_18);
#-------------------------------------------------------------------------------
switch(ValueOf($InvoicesItems)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	// Позиция в чеке: имя, цена, кол-во, общая стоимость, скидка, налог
	$position = new Position(SprintF('Оплата по счёту #%s',$Number), (float) $Invoice['Summ'], 1, (float) $Invoice['Summ'], 0, $vat);
	#-------------------------------------------------------------------------------
	$check->addPosition($position);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	foreach($InvoicesItems as $Item){
		#-------------------------------------------------------------------------------
		// Позиция в чеке: имя, цена, кол-во, общая стоимость, скидка, налог
		$position = new Position(SprintF('%s%s',$Item['Name'],(StrLen($Item['Comment']) > 0)?SPrintF(' / %s',$Item['Comment']):''), (float) $Item['Summ'], 1, (float) $Item['Summ'], 0, $vat);
		#-------------------------------------------------------------------------------
		// Идентификатор позиции
		$position->setId($Item['ID']);
		#-------------------------------------------------------------------------------
		$check->addPosition($position);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
// Итоговая сумма расчёта
$payment = new Payment(Payment::TYPE_CARD, (float) $Invoice['Summ']);
$check->addPayment($payment);

// Добавление кассира (опционально)
#$cashier = new Cashier('Иваров И.П.', '1234567890123');
#$check->addCashier($cashier);

Debug(SPrintF('[comp/Invoices/Komtet]: check = %s',print_r($check,true)));

#-------------------------------------------------------------------------------
// Добавляем чек в очередь.
try {
	#-------------------------------------------------------------------------------
	$manager->putCheck($check);
	#-------------------------------------------------------------------------------
} catch (SdkException $e) {
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Invoices/Komtet]: Invoice = %s; getMessage = %s',$Number,$e->getMessage()));
	#-------------------------------------------------------------------------------
	return FALSE;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
