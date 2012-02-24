
ALTER TABLE `Edesks` ADD `SeenByPersonal` INT(11) NOT NULL AFTER `StatusDate` ,
ADD `LastSeenBy` INT(11) NOT NULL AFTER `SeenByPersonal` ,
ADD `SeenByUser` INT(11) NOT NULL AFTER `LastSeenBy`;


