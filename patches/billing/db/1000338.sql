
DELETE FROM `Tasks` WHERE `ID` IN (15);

-- SEPARATOR

INSERT INTO `Tasks` (`ID`,`UserID`,`TypeID`,`Params`,`IsActive`) VALUES (15,1,'Taxation','[]','yes');

-- SEPARATOR

ALTER TABLE `Invoices` ADD `IsCheckSent` enum('no','yes') default 'yes' AFTER `IsPosted`;

