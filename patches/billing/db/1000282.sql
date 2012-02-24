INSERT INTO `Services` (`ID`, `GroupID`, `UserID`, `ServicesGroupID`, `Name`, `Emblem`, `Code`, `Item`, `Measure`, `ConsiderTypeID`, `CostOn`, `Cost`, `IsHidden`, `IsProtected`, `IsActive`, `IsProlong`, `SortID`) VALUES ('1100', '2000000', '1', '1000', 'Партнёрское начисление', NULL, 'Default', '', 'шт.', 'Upon', '', '', 'yes', 'yes', 'no', 'yes', '10');
-- SEPARATOR
INSERT INTO `Tasks` (`ID`, `CreateDate`, `UserID`, `TypeID`, `ExecuteDate`, `Params`, `Errors`, `Result`, `IsExecuted`, `IsActive`) VALUES (11, UNIX_TIMESTAMP(), 1, 'CaclulatePartnersReward', UNIX_TIMESTAMP(), '[]', 7, '', 'no', 'yes');


