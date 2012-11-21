
INSERT INTO `Bonuses`
	(`CreateDate`, `ExpirationDate`, `UserID`, `ServiceID`, `SchemeID`, `DaysReserved`, `DaysRemainded`, `Discont`, `Comment`)
	SELECT `CreateDate`, (UNIX_TIMESTAMP() + 365*24*3600), `UserID`,'10000',`SchemeID`,`DaysReserved`,`DaysRemainded`,`Discont`,`Comment` FROM `HostingBonuses`;

-- SEPARATOR

INSERT INTO `Bonuses`
	(`CreateDate`, `ExpirationDate`, `UserID`, `ServiceID`, `SchemeID`, `DaysReserved`, `DaysRemainded`, `Discont`, `Comment`)
	SELECT `CreateDate`, (UNIX_TIMESTAMP() + 365*24*3600), `UserID`,'30000',`SchemeID`,`DaysReserved`,`DaysRemainded`,`Discont`,`Comment` FROM `VPSBonuses`;

-- SEPARATOR

INSERT INTO `Bonuses`
	(`CreateDate`, `ExpirationDate`, `UserID`, `ServiceID`, `SchemeID`, `DaysReserved`, `DaysRemainded`, `Discont`, `Comment`)
	SELECT `CreateDate`, (UNIX_TIMESTAMP() + 365*24*3600), `UserID`,'40000',`SchemeID`,`DaysReserved`,`DaysRemainded`,`Discont`,`Comment` FROM `DSBonuses`;

-- SEPARATOR

INSERT INTO `Bonuses`
	(`CreateDate`, `ExpirationDate`, `UserID`, `ServiceID`, `SchemeID`, `DaysReserved`, `DaysRemainded`, `Discont`, `Comment`)
	SELECT `CreateDate`, (UNIX_TIMESTAMP() + 365*24*3600), `UserID`,'50000',`SchemeID`,`DaysReserved`,`DaysRemainded`,`Discont`,`Comment` FROM `ExtraIPBonuses`;

-- SEPARATOR

INSERT INTO `Bonuses`
	(`CreateDate`, `ExpirationDate`, `UserID`, `ServiceID`, `SchemeID`, `DaysReserved`, `DaysRemainded`, `Discont`, `Comment`)
	SELECT `CreateDate`, (UNIX_TIMESTAMP() + 365*24*3600), `UserID`,'51000',`SchemeID`,`DaysReserved`,`DaysRemainded`,`Discont`,`Comment` FROM `ISPswBonuses`;

-- SEPARATOR

DROP VIEW IF EXISTS `HostingBonusesOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `HostingBonuses`;

-- SEPARATOR

DROP VIEW IF EXISTS `VPSBonusesOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `VPSBonuses`;

-- SEPARATOR

DROP VIEW IF EXISTS `DSBonusesOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `DSBonuses`;

-- SEPARATOR

DROP VIEW IF EXISTS `ExtraIPBonusesOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `ExtraIPBonuses`;

-- SEPARATOR

DROP VIEW IF EXISTS `ISPswBonusesOwners`;
-- SEPARATOR
DROP TABLE  IF EXISTS `ISPswBonuses`;



