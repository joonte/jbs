
UPDATE `Invoices` SET `PaymentSystemID` = 'YooMoney' WHERE `PaymentSystemID` = 'Yandex';

-- SEPARATOR

UPDATE `Clauses` SET `Partition` = 'Invoices/PaymentSystems/YooMoney' WHERE `Partition` = 'Invoices/PaymentSystems/Yandex';

-- SEPARATOR

INSERT INTO `PaymentSystemsCollation` VALUES (NULL,'yes',30,'YooMoney','ЮMoney','YooMoney.png','ЮMoney (ранее: Яндекс.Деньги), это платежная система, которая позволяет оплачивать различные товары и услуги; совершать безопасные платежи в интернете; надежно хранить всю информацию о ваших зачислениях и платежах.','ЮMoney','');

