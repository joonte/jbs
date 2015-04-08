
set names utf8;

-- SEPARATOR

DROP TABLE IF EXISTS `PaymentSystemsCollation`;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `PaymentSystemsCollation` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`IsActive` enum('no','yes') DEFAULT 'no',
	`SortID` int(11) default '1000',
	`Source` varchar(1023),
	`Destination` varchar(1023),
	`Image` varchar(1023),
	`Prompt` longtext,
	`Description` text,
	`AdminNotice` text,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',10,'PayMaster','WebMoney','WebMoney.png','Оплата через WebMoney, банковскими картами VISA/MasterCard (без процентов),\nчерез интернет-банкинги, наличными через кассы магазинов и многими другими способами','WebMoney','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',20,'PayMaster','VISA','Visa.MasterCard.png','Оплата при помощи пластиковой карты VISA или MasterCard.','VISA/MasterCard','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',999,'PayMaster','BeeLine','BeeLine.png','Оплата со счета телефона, оператор BeeLine','BeeLine','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',999,'PayMaster','МТС','MTS.png','Оплата со счета телефона, оператор МТС','МТС','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',999,'ROBOKASSA','МegaFon','МegaFon.png','Оплата со счета телефона, оператор МegaFon','МegaFon','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',999,'PayMaster','ЕвроСеть','EuroSet.png','Оплата через офисы ЕвроСети','ЕвроСеть','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',999,'PayMaster','Связной','Svyaznoi.png','Оплата через офисы Связного','Связной','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'WebMoneyR','WebMoney WMR','WMRM.gif','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - Российский рубль.','WebMoney WMR','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'WebMoneyZ','WebMoney WMZ','WMZM.gif','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - Доллар США.','WebMoney WMZ','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'WebMoneyE','WebMoney WME','WMEM.gif','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - евро.','WebMoney WME','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',999,'WebMoneyU','WebMoney WMU','WebMoney.png','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - Украинская гривна.','WebMoney WMU','картинка от обычных вебманей');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',30,'Yandex','Яндекс.Деньги','Yandex.png','Яндекс.Деньги — это платежная система, которая позволяет оплачивать различные товары и услуги; совершать безопасные платежи в интернете; надежно хранить всю информацию о ваших зачислениях и платежах.','Яндекс.Деньги','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',40,'QIWI','QIWI','QIWI.png','Платёжная система использующая номер мобильного телефона в качестве номера кошелька. Большая сеть терминалов для оплаты.\n\nОбратите внимание, что мы используем Российскую систему QIWI, и ваш кошелёк тоже должен состоять из Российского мобильного телефона.\n\nНапример: +7 926 123 45 67','QIWI','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'ZPayment','Z-Payment','Z-Payment.png','Оплата при помощи электронных денег платёжной системы Z-Payment.','Z-Payment','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'EasyPay','EasyPay','EasyPay.png','Оплата при помощи электронных денег платёжной системы EasyPay.','EasyPay','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',9999,'Egold','E-Gold','','Оплата при помощи электронных денег платёжной системы E-Gold.','E-Gold','нет картинки.\nда и самой такой платёжной системы больше нет.');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',50,'RBKMoney','RBK Money','RBKMoney.png','(в прошлом — RUpay) — первая электронная платёжная система в России бесплатная для пользователей. Комиссия на оплату услуг и товаров в интернете — 0%','RBK Money','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',60,'ROBOKASSA','ROBOKASSA','ROBOKASSA.png','Оплата любыми способами - все виды электронных денег, VISA, MasterCard, Maestro, оплата через SMS, терминалы, электронный банкинг и много других способов','ROBOKASSA','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'InterKassa','ИнтерКасса','InterKassa.png','Оплата при помощи платёжной системы ИнтерКасса.','ИнтерКасса','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'MailRu','Деньги@Mail.Ru','','Оплата при помощи электронных денег платёжной системы Деньги@Mail.Ru.','Деньги@Mail.Ru','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'W1','Единый кошелек','W1R.gif','Оплата при помощи электронных денег платёжной системы Единый кошелек.','Единый кошелек','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','no',9999,'Moneybookers','Moneybookers','','Система электронных переводов Moneybookers','Moneybookers','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'Checkout','2Checkout','','Оплата при помощи пластиковых карт и PayPal через процессинговый центр 2Checkout','2Checkout','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'OKPAY','OKPAY','OKPAY.png','Оплата при помощи электронных денег платёжной системы OKPAY.','OKPAY','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',999,'OnPay','OnPay.ru','OnPay.png','Оплата при помощи электронных денег платёжной системы OnPay.ru','OnPay.ru','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',100,'Natural','Банк (физ. лица)','Bank.png','Оплата платежом через банк (СберБанк или любой другой).','Банк (физ. лица)','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',120,'Juridical','Банк без. нал. (юр. лица)','Bank.png','Оплата банковским платежом, для юридических лиц','Банк без. нал. (юр. лица)','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',110,'Individual','Банк без. нал. (ИП)','Bank.png','Оплата банковским платежом, для индивидульных предпренимателей','Банк без. нал. (ИП)','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',150,'InOffice','Наличными в офисе','Cash.png','Оплата наличными, в нашем офисе.','Наличными в офисе','');


