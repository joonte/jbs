
DELETE FROM `Tasks` WHERE `ID` IN(81,82,83,84,85);
-- SEPARATOR
INSERT INTO `Tasks`
	(`ID`,`UserID`,`TypeID`,`Params`,`IsActive`)
VALUES
	(81,1,'ISPswNoticeSuspend','[]','yes'),
	(82,1,'ISPswConsider','[]','yes'),
	(83,1,'ISPswNoticeDelete','[]','yes'),
	(84,1,'ISPswForDelete','[]','yes'),
	(85,1,'ISPswCheckLicenses','[]','yes')

;


