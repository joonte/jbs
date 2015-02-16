
DELETE FROM `Tasks` WHERE `ID` = 86;
-- SEPARATOR
DELETE FROM `Tasks` WHERE `TypeID` = 'ServersAutoBalance';
-- SEPARATOR
DELETE FROM `Tasks` WHERE `TypeID` = 'HostingSetPrimaryServer';
-- SEPARATOR
DELETE FROM `Tasks` WHERE `ID` = 65;
-- SEPARATOR
DELETE FROM `Tasks` WHERE `TypeID` = 'VPSSetPrimaryServer';
-- SEPARATOR
INSERT INTO `Tasks` (`ID`,`UserID`,`TypeID`,`Params`,`IsActive`) VALUES (86,1,'ServersAutoBalance','[]','yes');


