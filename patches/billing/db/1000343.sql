

ALTER TABLE `Notifies` ADD `ContactID` INT(11) DEFAULT '1' AFTER `ID` ;

-- SEPARATOR

ALTER TABLE `Notifies` ADD KEY `NotifiesContactID` (`ContactID`);

-- SEPARATOR

ALTER TABLE `Notifies` CHANGE `ContactID` `ContactID` INT(11) NOT NULL;



