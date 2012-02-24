
ALTER TABLE `Services` ADD `OperationSign` CHAR(1) NOT NULL DEFAULT '-' AFTER `Code` ;

-- SEPARATOR

UPDATE `Services` SET `OperationSign` = '+' WHERE `ID` = 1000;

-- SEPARATOR

UPDATE `Services` SET `OperationSign` = '+' WHERE `ID` = 1100;

