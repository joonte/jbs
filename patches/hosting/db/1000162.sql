ALTER TABLE `HostingBonuses` CHANGE `SchemeID` `SchemeID` INT( 11 ) NULL;
-- SEPARATOR
ALTER TABLE `HostingPolitics` CHANGE `SchemeID` `SchemeID` INT( 11 ) NULL;
-- SEPARATOR
ALTER TABLE `DomainsBonuses` CHANGE `SchemeID` `SchemeID` INT( 11 ) NULL;
-- SEPARATOR
ALTER TABLE `HostingBonuses` ADD `Comment` char(255) default '' AFTER `Discont`;
-- SEPARATOR
ALTER TABLE `DomainsBonuses` ADD `Comment` char(255) default '' AFTER `Discont`;