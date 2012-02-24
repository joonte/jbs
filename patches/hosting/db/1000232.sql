
UPDATE `ISPswLicenses` SET `StatusDate` = UNIX_TIMESTAMP()  WHERE `StatusDate`=0;

