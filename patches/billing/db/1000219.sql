UPDATE `MotionDocuments` SET `UniqID` = CONCAT('CN:',`ContractID`) WHERE `TypeID` = 'Contract';
-- SEPARATOR
UPDATE `MotionDocuments` SET `UniqID` = CONCAT('EN:',SUBSTR(`Link`,LOCATE('=',`Link`)+1)) WHERE `TypeID` = 'ContractEnclosure';
-- SEPARATOR
UPDATE `MotionDocuments` SET `UniqID` = CONCAT('AC:',`ContractID`,'/',SUBSTR(`Link`,LOCATE('=',`Link`,LENGTH(`Link`)-5)+1)) WHERE `TypeID` = 'WorksCompliteAct';
-- SEPARATOR
CREATE TABLE `Temp` ( `ID` int(11) );
-- SEPARATOR
INSERT INTO `Temp` SELECT `ID` FROM `MotionDocuments` GROUP BY `UniqID`;
-- SEPARATOR
DELETE FROM `MotionDocuments` WHERE `ID` NOT IN(SELECT `ID` FROM `Temp`);
-- SEPARATOR
DROP TABLE `Temp`;