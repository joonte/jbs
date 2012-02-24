
UPDATE `ServicesGroups` SET `ID`=(`ID` + 1000) WHERE `ID` > 1000;
-- SEPARATOR
INSERT INTO `ServicesGroups` (`ID`, `Name`, `IsProtected`, `IsActive`, `SortID`) VALUES (1100, 'Дополнительные услуги', 'yes', 'yes', 20);
-- SEPARATOR
INSERT INTO `Services` (`ID`, `GroupID`, `UserID`, `ServicesGroupID`, `Name`, `Code`, `Item`, `Emblem`, `Measure`, `ConsiderTypeID`, `CostOn`, `Cost`, `IsHidden`, `IsProtected`, `IsActive`, `IsProlong`, `SortID`) VALUES ('50000', '2000000', '1', '1100', 'Поддержка заказа дополнительного IP адреса', 'ExtraIP', 'IP адреса', NULL, 'дн.', 'Daily', '0.00', '0.00', 'no', 'yes', 'yes', 'yes', '10');

-- SEPARATOR
INSERT INTO `Tasks`
	(`ID`,`UserID`,`TypeID`,`Params`,`IsActive`)
VALUES
	(76,1,'ExtraIPConsider','[]','yes'),
	(77,1,'ExtraIPNoticeDelete','[]','yes'),
	(78,1,'ExtraIPForDelete','[]','yes')
;


