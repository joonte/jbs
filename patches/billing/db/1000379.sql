
ALTER TABLE `Users` ADD `ConfirmedWas` LONGTEXT AFTER `Params`;

-- SEPARATOR

UPDATE `Users` SET `ConfirmedWas` = '{"1672520401":"System User"}' WHERE `ID` < 2000;

