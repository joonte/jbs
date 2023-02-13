
ALTER TABLE `VPSSchemes` ADD `SchemeParams` VARCHAR(16384) NOT NULL AFTER `SortID`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `disklimit`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `ncpu`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `cpu`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `mem`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `chrate`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `preset`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `blkiotune`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `isolimitsize`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `isolimitnum`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `snapshot_limit`;

