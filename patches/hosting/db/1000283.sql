
DELETE FROM `Tasks` WHERE `ID` = 1 AND `TypeID` != 'GC';
-- SEPARATOR
UPDATE `Tasks` SET `ID` = 1 WHERE `TypeID` = 'GC';
-- SEPARATOR
DELETE FROM `Tasks` WHERE `ID` = 8 AND `TypeID` != 'CaclulatePartnersReward';
-- SEPARATOR
UPDATE `Tasks` SET `ID` = 8 WHERE `TypeID` = 'CaclulatePartnersReward';
-- SEPARATOR
DELETE FROM `Tasks` WHERE `ID` = 10;
-- SEPARATOR
INSERT INTO `Tasks` (`ID`,`UserID`,`TypeID`,`Params`,`IsActive`) VALUES (10,1,'RecoveryProfiles','[]','yes');
-- SEPARATOR
DELETE FROM `Tasks` WHERE `ID` = 11;
-- SEPARATOR
INSERT INTO `Tasks` (`ID`,`UserID`,`TypeID`,`Params`,`IsActive`) VALUES (11,1,'RecoveryServers','[]','yes');

