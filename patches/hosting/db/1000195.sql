
INSERT INTO `Tasks` (
	`ID` ,
	`CreateDate` ,
	`UserID` ,
	`TypeID` ,
	`ExecuteDate` ,
	`Params` ,
	`Errors` ,
	`Result` ,
	`IsExecuted` ,
	`IsActive`
	)
VALUES (
	66 , UNIX_TIMESTAMP() , '1', 'VPSServersQuestioning', UNIX_TIMESTAMP() , '[]', '0', NULL , 'no', 'yes'
);

-- SEPARATOR
INSERT INTO `Tasks` (`ID`, `CreateDate`, `UserID`, `TypeID`, `ExecuteDate`, `Params`, `Errors`, `Result`, `IsExecuted`, `IsActive`) VALUES (67, UNIX_TIMESTAMP(), '1', 'VPSNoticeSuspend', UNIX_TIMESTAMP(), '[]', '0', NULL, 'no', 'yes');
-- SEPARATOR
INSERT INTO `Tasks` (`ID`, `CreateDate`, `UserID`, `TypeID`, `ExecuteDate`, `Params`, `Errors`, `Result`, `IsExecuted`, `IsActive`) VALUES (68, UNIX_TIMESTAMP(), '1', 'VPSConsider', UNIX_TIMESTAMP(), '[]', '0', NULL, 'no', 'yes');
-- SEPARATOR
INSERT INTO `Tasks` (`ID`, `CreateDate`, `UserID`, `TypeID`, `ExecuteDate`, `Params`, `Errors`, `Result`, `IsExecuted`, `IsActive`) VALUES (69, UNIX_TIMESTAMP(), '1', 'VPSNoticeDelete', UNIX_TIMESTAMP(), '[]', '0', NULL, 'no', 'yes');
-- SEPARATOR
INSERT INTO `Tasks` (`ID`, `CreateDate`, `UserID`, `TypeID`, `ExecuteDate`, `Params`, `Errors`, `Result`, `IsExecuted`, `IsActive`) VALUES (70, UNIX_TIMESTAMP(), '1', 'VPSForDelete', UNIX_TIMESTAMP(), '[]', '0', NULL, 'no', 'yes');

