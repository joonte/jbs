

UPDATE `Services` SET `OperationSign` = '+' WHERE `Services`.`ID` =3000;

-- SEPARATOR

UPDATE `Services` SET `Name` = 'Возврат средств на балланс' WHERE `Services`.`ID` =3000;

-- SEPARATOR

INSERT INTO `Services` (`ID`, `GroupID`, `UserID`, `ServicesGroupID`, `Name`, `Emblem`, `Code`, `OperationSign`, `Item`, `Measure`, `ConsiderTypeID`, `CostOn`, `Cost`, `IsHidden`, `IsProtected`, `IsActive`, `IsProlong`, `SortID`) VALUES
(4000, 2000000, 1, 1000, 'Возврат средств на реквизиты пользователя', NULL, 'Default', '-', '', 'шт.', '', 0.00, 0.00, 'yes', 'yes', 'no', 'yes', 10);



