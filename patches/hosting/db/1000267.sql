
DELETE FROM `Tasks` WHERE ID = 60;
-- SEPARATOR
INSERT INTO `Tasks` (`ID`,`CreateDate`,`UserID`,`TypeID`,`Params`,`IsExecuted`,`IsActive`) VALUES (60,UNIX_TIMESTAMP(),1,'HostingServersQuestioning','[]','no','yes');

