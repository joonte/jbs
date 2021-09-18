

UPDATE `OrdersHistory` SET `Parked` = REPLACE(`Parked`,',,',',');
-- SEPARATOR
UPDATE `OrdersHistory` SET `Parked` = RIGHT(`Parked`,LENGTH(`Parked`)-1) WHERE `Parked` LIKE ',%';

