
DELETE FROM `Tasks` WHERE `ID`=86;

-- SEPARATOR

INSERT INTO `Tasks`
	(`ID`,`UserID`,`TypeID`,`Params`,`IsActive`)
VALUES
	(86,1,'HostingSetPrimaryServer','[]','yes');

