

set names utf8;

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
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','PayMaster','WebMoney','WebMoney.gif','Оплата при помощи электронных денег платёжной системы WebMoney. Конвертация из любой валюты происходит автоматически.','WebMoney','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','PayMaster','VISA','Visa.MasterCard.gif','Оплата при помощи пластиковой карты VISA или MasterCard.','VISA/MasterCard','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('no','100','PayMaster','BeeLine','BeeLine.png','Оплата со счета телефона, оператор BeeLine','BeeLine','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('no','100','PayMaster','МТС','MTS.png','Оплата со счета телефона, оператор МТС','МТС','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('no','100','PayMaster','ЕвроСеть','EuroSet.png','Оплата через офисы ЕвроСети','ЕвроСеть','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('no','100','PayMaster','Связной','Svyaznoi.png','Оплата через офисы Связного','Связной','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','WebMoneyR','WebMoney WMR','WMRM.gif','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - Российский рубль.','WebMoney WMR','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','WebMoneyZ','WebMoney WMZ','WMZM.gif','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - Доллар США.','WebMoney WMZ','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','WebMoneyE','WebMoney WME','WMEM.gif','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - евро.','WebMoney WME','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('no','100','WebMoneyU','WebMoney WMU','WebMoney.gif','Оплата при помощи электронных денег платёжной системы WebMoney, валюта платежа - Украинская гривна.','WebMoney WMU','картинка от обычных вебманей');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','Yandex','Яндекс.Деньги','Yandex.png','Оплата при помощи электронных денег платёжной системы Яндекс.Деньги.','Яндекс.Деньги','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','QIWI','QIWI','QIWI.png','Оплата при помощи электронных денег платёжной системы QIWI.','QIWI','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','ZPayment','Z-Payment','','Оплата при помощи электронных денег платёжной системы Z-Payment.','Z-Payment','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','EasyPay','EasyPay','','Оплата при помощи электронных денег платёжной системы EasyPay.','EasyPay','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('no','100','Egold','E-Gold','','Оплата при помощи электронных денег платёжной системы E-Gold.','E-Gold','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','RBKMoney','RBK Money','RBKMoney.png','Оплата при помощи электронных денег платёжной системы RBK Money.','RBK Money','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','ROBOKASSA','ROBOKASSA','ROBOKASSA.png','Оплата при помощи платёжной системы ROBOKASSA.','ROBOKASSA','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','InterKassa','ИнтерКасса','','Оплата при помощи платёжной системы ИнтерКасса.','ИнтерКасса','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','MailRu','Деньги@Mail.Ru','','Оплата при помощи электронных денег платёжной системы Деньги@Mail.Ru.','Деньги@Mail.Ru','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','W1','Единый кошелек','W1R.gif','Оплата при помощи электронных денег платёжной системы Единый кошелек.','Единый кошелек','');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','Moneybookers','Moneybookers','','Система электронных переводов Moneybookers','Moneybookers','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','Checkout','2Checkout','','Оплата при помощи пластиковых карт и PayPal через процессинговый центр 2Checkout','2Checkout','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','OKPAY','OKPAY','','Оплата при помощи электронных денег платёжной системы OKPAY.','OKPAY','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','OnPay','OnPay.ru','','Оплата при помощи электронных денег платёжной системы OnPay.ru','OnPay.ru','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','Natural','Банк (физ. лица)','Bank.png','Оплата платежом через банк (СберБанк или любой другой).','Банк (физ. лица)','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','Juridical','Банк без. нал. (юр. лица)','Bank.png','Оплата банковским платежом, для юридических лиц','Банк без. нал. (юр. лица)','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','Individual','Банк без. нал. (ИП)','Bank.png','Оплата банковским платежом, для индивидульных предпренимателей','Банк без. нал. (ИП)','нет картинки');

-- SEPARATOR
INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`,`AdminNotice`)
VALUES ('yes','100','InOffice','Наличными в офисе','Cash.png','Оплата наличными, в нашем офисе.','Наличными в офисе','');


