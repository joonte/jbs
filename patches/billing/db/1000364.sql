
DELETE FROM `Statistics` WHERE `TableID` = 'DS' AND `PackageID` IS NOT NULL;
-- SEPARATOR
ALTER TABLE `Statistics` ADD KEY `TableID` (`TableID`);
-- SEPARATOR
ALTER TABLE `Statistics` ADD KEY `PackageID` (`PackageID`);


