
UPDATE `Contacts` SET `Confirmed` = UNIX_TIMESTAMP() WHERE `ExternalID` != '' AND `Confirmed` < 1;

