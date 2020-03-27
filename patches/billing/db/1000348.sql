

ALTER TABLE `Config` MODIFY `Value` text CHARACTER SET utf8mb4;

-- SEPARATOR

DELETE FROM `Config` WHERE `Param` = 'EmailSign';

-- SEPARATOR

INSERT INTO `Config` (`HostID`,`Param`,`Value`)
VALUES ('billing','EmailSign','');

