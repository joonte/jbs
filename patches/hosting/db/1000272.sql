
UPDATE `Tasks` SET `ExecuteDate` = (UNIX_TIMESTAMP() + 3600)  WHERE `ExecuteDate` > (UNIX_TIMESTAMP() + 365*24*3600);

