
ALTER TABLE `WorksComplite` ADD `OrderID` int(11) default '0' AFTER `ServiceID`;

-- SEPARATOR

UPDATE `Services` SET `PartnersRewardPercent` = 0 WHERE `ID` < 10000;

-- SEPARATOR

UPDATE `Services` SET `PartnersRewardPercent` = 5 WHERE `PartnersRewardPercent` = -1;

-- SEPARATOR

ALTER TABLE `Services` CHANGE `PartnersRewardPercent` `PartnersRewardPercent` DOUBLE NOT NULL DEFAULT '0';

