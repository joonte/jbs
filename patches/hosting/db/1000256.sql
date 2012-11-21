
INSERT INTO `Politics` (`CreateDate`, `ExpirationDate`, `UserID`, `GroupID`, `FromServiceID`, `FromSchemeID`, `ToServiceID`, `ToSchemeID`, `DaysPay`, `DaysDiscont`, `Discont`, `Comment`)
SELECT UNIX_TIMESTAMP(), (UNIX_TIMESTAMP() + 10*365*24*3600), `UserID`, `GroupID`, '10000', `SchemeID`, '10000', `SchemeID`,`DaysPay`,0,`Discont`,'moved from old politics' FROM `HostingPolitics`;
-- SEPARATOR
DROP VIEW IF EXISTS `HostingPoliticsOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `HostingPolitics`;

-- SEPARATOR
INSERT INTO `Politics` (`CreateDate`, `ExpirationDate`, `UserID`, `GroupID`, `FromServiceID`, `FromSchemeID`, `ToServiceID`, `ToSchemeID`, `DaysPay`, `DaysDiscont`, `Discont`, `Comment`)
SELECT UNIX_TIMESTAMP(), (UNIX_TIMESTAMP() + 10*365*24*3600), `UserID`, `GroupID`, '30000', `SchemeID`, '30000', `SchemeID`,`DaysPay`,0,`Discont`,'moved from old politics' FROM `VPSPolitics`;
-- SEPARATOR
DROP VIEW IF EXISTS `VPSPoliticsOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `VPSPolitics`;

-- SEPARATOR
INSERT INTO `Politics` (`CreateDate`, `ExpirationDate`, `UserID`, `GroupID`, `FromServiceID`, `FromSchemeID`, `ToServiceID`, `ToSchemeID`, `DaysPay`, `DaysDiscont`, `Discont`, `Comment`)
SELECT UNIX_TIMESTAMP(), (UNIX_TIMESTAMP() + 10*365*24*3600), `UserID`, `GroupID`, '40000', `SchemeID`, '40000', `SchemeID`,`DaysPay`,0,`Discont`,'moved from old politics' FROM `DSPolitics`;
-- SEPARATOR
DROP VIEW IF EXISTS `DSPoliticsOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `DSPolitics`;

-- SEPARATOR
INSERT INTO `Politics` (`CreateDate`, `ExpirationDate`, `UserID`, `GroupID`, `FromServiceID`, `FromSchemeID`, `ToServiceID`, `ToSchemeID`, `DaysPay`, `DaysDiscont`, `Discont`, `Comment`)
SELECT UNIX_TIMESTAMP(), (UNIX_TIMESTAMP() + 10*365*24*3600), `UserID`, `GroupID`, '50000', `SchemeID`, '50000', `SchemeID`,`DaysPay`,0,`Discont`,'moved from old politics' FROM `ExtraIPPolitics`;
-- SEPARATOR
DROP VIEW IF EXISTS `ExtraIPPoliticsOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `ExtraIPPolitics`;

-- SEPARATOR
INSERT INTO `Politics` (`CreateDate`, `ExpirationDate`, `UserID`, `GroupID`, `FromServiceID`, `FromSchemeID`, `ToServiceID`, `ToSchemeID`, `DaysPay`, `DaysDiscont`, `Discont`, `Comment`)
SELECT UNIX_TIMESTAMP(), (UNIX_TIMESTAMP() + 10*365*24*3600), `UserID`, `GroupID`, '51000', `SchemeID`, '51000', `SchemeID`,`DaysPay`,0,`Discont`,'moved from old politics' FROM `ISPswPolitics`;
-- SEPARATOR
DROP VIEW IF EXISTS `ISPswPoliticsOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `ISPswPolitics`;



