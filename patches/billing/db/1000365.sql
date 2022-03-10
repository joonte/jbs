
set names utf8;

-- SEPARATOR

DELETE FROM `PaymentSystemsCollation` WHERE `Destination` = 'Картой МИР';

-- SEPARATOR

INSERT INTO `PaymentSystemsCollation` VALUES (NULL,'no',15,'SberBank','Картой МИР','MIR.png','Оплата при помощи пластиковой карты МИР','картой МИР','');

