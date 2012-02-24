DROP VIEW IF EXISTS `EdeskMessages`;
-- SEPARATOR
DROP TABLE IF EXISTS `EdeskMessages`;
-- SEPARATOR
CREATE
  VIEW `EdeskMessages` AS
SELECT
  `Edesks`.*,
  `EdesksMessages`.`Content` as `Content`
FROM
  `Edesks`
LEFT JOIN `EdesksMessages`
ON (`Edesks`.`ID` = `EdesksMessages`.`EdeskID`);