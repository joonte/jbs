ALTER TABLE `Events` ADD KEY (`IsReaded`);
-- SEPARATOR
ALTER TABLE `Events` ADD KEY (`CreateDate`);
-- SEPARATOR
ALTER TABLE `Clauses` ADD KEY `ClausesPartition` (`Partition`) /* IF NOT EXISTS (SHOW KEYS FROM `Clauses` WHERE Key_name = 'ClausesPartition')*/;
-- SEPARATOR
ALTER TABLE `Clauses` ADD KEY `ClausesPublicDate` (`PublicDate`);
-- SEPARATOR
ALTER TABLE `Invoices` ADD KEY `InvoicesStatusID` (`StatusID`);
-- SEPARATOR
ALTER TABLE `Invoices` ADD KEY `InvoicesStatusDate` (`StatusDate`);
-- SEPARATOR
ALTER TABLE `Edesks` ADD KEY `EdesksStatusID` (`StatusID`);
-- SEPARATOR
ALTER TABLE `Edesks` ADD KEY `EdesksUpdateDate` (`UpdateDate`);
-- SEPARATOR
ALTER TABLE `Contracts` ADD KEY `ContractsTypeID` (`TypeID`);
-- SEPARATOR
ALTER TABLE `Tasks` ADD KEY `TasksCreateDate` (`CreateDate`);
-- SEPARATOR
ALTER TABLE `Tasks` ADD KEY `TasksIsActive` (`IsActive`);
-- SEPARATOR
ALTER TABLE `Tasks` ADD KEY `TasksIsExecuted` (`IsExecuted`);
-- SEPARATOR
ALTER TABLE `Tasks` ADD KEY `TasksExecuteDate` (`ExecuteDate`);
-- SEPARATOR
ALTER TABLE `Tasks` ADD KEY `TasksErrors` (`Errors`);
-- SEPARATOR
ALTER TABLE `MotionDocuments` ADD KEY `MotionDocumentsTypeID` (`TypeID`);
-- SEPARATOR
ALTER TABLE `MotionDocuments` ADD KEY `MotionDocumentsUniqID` (`UniqID`);
-- SEPARATOR
ALTER TABLE `Groups` ADD KEY `GroupsIsDepartment` (`IsDepartment`);
-- SEPARATOR
ALTER TABLE `Permissions` ADD KEY `PermissionsName` (`Name`);
-- SEPARATOR
ALTER TABLE `Permissions` ADD KEY `PermissionsMetric` (`Metric`);
-- SEPARATOR
ALTER TABLE `Profiles` ADD KEY `ProfilesTemplateID` (`TemplateID`);
-- SEPARATOR
ALTER TABLE `Profiles` ADD KEY `ProfilesStatusID` (`StatusID`);
-- SEPARATOR
ALTER TABLE `Profiles` ADD KEY `ProfilesStatusDate` (`StatusDate`);
-- SEPARATOR
ALTER TABLE `Users` ADD KEY `UsersEmail` (`Email`);
-- SEPARATOR
ALTER TABLE `Users` ADD KEY `UsersName` (`Name`);


