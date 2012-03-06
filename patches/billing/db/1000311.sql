DROP VIEW IF EXISTS `EdeskMessages`;
-- SEPARATOR
DROP VIEW IF EXISTS `EdesksOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `EdesksOwners`;
-- SEPARATOR
CREATE
  VIEW `EdesksOwners` AS
SELECT
  `Edesks`.*,
  `EdesksMessages`.`Content` as `Content`
FROM
  `Edesks`
LEFT JOIN `EdesksMessages`
ON (`Edesks`.`ID` = `EdesksMessages`.`EdeskID`);
