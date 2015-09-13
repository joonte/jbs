
ALTER TABLE `DNSmanagerOrders` ADD `Domain` char(255) AFTER `OldSchemeID`;
-- SEPARATOR
ALTER TABLE `DNSmanagerOrders` ADD `Parked` text AFTER `Domain`;
-- SEPARATOR
DELETE FROM `Tasks` WHERE `ID` IN (60);
-- SEPARATOR
INSERT INTO `Tasks` (`ID`,`UserID`,`TypeID`,`Params`,`IsActive`) VALUES (60,1,'ServersQuestioning','[]','yes');

