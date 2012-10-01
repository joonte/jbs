
ALTER TABLE `Services` ADD `IsConditionally` ENUM('no','yes') NOT NULL DEFAULT 'no' AFTER `IsProlong`;
-- SEPARATOR
UPDATE `Users` SET `LayPayMaxDays` = 31;
-- SEPARATOR
UPDATE `Users` SET `LayPayThreshold` = 200;
-- SEPARATOR
UPDATE `Users` SET `LayPayMaxSumm`=((SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID`=`Users`.`ID` AND `StatusID` = 'Payed') / 10);
-- SEPARATOR
UPDATE `Users` SET `LayPayMaxSumm` = 0 WHERE `LayPayMaxSumm` IS NULL;

