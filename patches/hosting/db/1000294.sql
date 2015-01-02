
ALTER TABLE `VPSSchemes` 
	ADD `isolimitsize` INT(11) NOT NULL DEFAULT '0' AFTER `blkiotune`,
	ADD `isolimitnum` INT(2) NOT NULL DEFAULT '0' AFTER `isolimitsize`,
	ADD `snapshot_limit` INT(2) NOT NULL DEFAULT '0' AFTER `isolimitnum`;

