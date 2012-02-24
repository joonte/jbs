DROP FUNCTION IF EXISTS `EDESKS_MESSAGES`;
DELIMITER |
CREATE FUNCTION EDESKS_MESSAGES($TicketID INT, $UserID INT)
RETURNS INT
 DETERMINISTIC
  BEGIN
   DECLARE $OwnerID INT;
   DECLARE $CreateDate INT;
   DECLARE $Result INT;
   SET $OwnerID = (SELECT `UserID` FROM `Edesks` WHERE `ID` =  $TicketID);
   IF $UserID = $OwnerID
     THEN
       SET $CreateDate = (SELECT MAX(`CreateDate`) FROM `EdesksMessages` WHERE `EdeskID` = $TicketID AND `UserID` = $OwnerID);
     ELSE
       SET $CreateDate = (SELECT MAX(`CreateDate`) FROM `EdesksMessages` WHERE `EdeskID` = $TicketID AND `UserID` != $OwnerID);
   END IF;
   IF ISNULL($CreateDate)
     THEN
       SET $Result = (SELECT COUNT(*) FROM `EdesksMessages` WHERE `EdeskID` = $TicketID);
     ELSE
       SET $Result = (SELECT COUNT(*) FROM `EdesksMessages` WHERE `EdeskID` = $TicketID AND `CreateDate` > $CreateDate);
   END IF;
   RETURN $Result;
  END
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `BEGIN_MONTH`;
DELIMITER |
CREATE FUNCTION BEGIN_MONTH()
RETURNS INT
 DETERMINISTIC
  BEGIN
   DECLARE Result INT;
   SET Result = (SELECT UNIX_TIMESTAMP() - ((DAY(CURDATE()) - 1) * 86400 + HOUR(CURTIME()) * 3600 + MINUTE(CURTIME()) * 60 + SECOND(CURTIME())));
   RETURN Result;
  END
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `BEGIN_PREVIOS_MONTH`;
DELIMITER |
CREATE FUNCTION BEGIN_PREVIOS_MONTH()
RETURNS INT
 DETERMINISTIC
  BEGIN
   DECLARE Result INT;
   SET Result = UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME(BEGIN_MONTH()),INTERVAL 1 MONTH));
   RETURN Result;
  END
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `BEGIN_WEEK`;
DELIMITER |
CREATE FUNCTION BEGIN_WEEK()
RETURNS INT
 DETERMINISTIC
  BEGIN
   DECLARE Result INT;
   SET Result = (SELECT UNIX_TIMESTAMP() - (WEEKDAY(CURDATE()) * 86400 + HOUR(CURTIME()) * 3600 + MINUTE(CURTIME()) * 60 + SECOND(CURTIME())));
   RETURN Result;
  END
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `BEGIN_DAY`;
DELIMITER |
CREATE FUNCTION BEGIN_DAY()
RETURNS INT
 DETERMINISTIC
  BEGIN
   DECLARE Result INT;
   SET Result = (SELECT UNIX_TIMESTAMP() - (HOUR(CURTIME()) * 3600 + MINUTE(CURTIME()) * 60 + SECOND(CURTIME())));
   RETURN Result;
  END
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `GET_QUARTER_FROM_TIMESTAMP`;
DELIMITER |
CREATE FUNCTION GET_QUARTER_FROM_TIMESTAMP(Stamp INT)
RETURNS INT
 DETERMINISTIC
  BEGIN
   DECLARE dMonth INT;
   SET dMonth = MONTH(FROM_UNIXTIME(Stamp));
   IF dMonth > 0 AND dMonth < 4
     THEN
       RETURN 1;
   END IF;
   IF dMonth > 3 AND dMonth < 7
     THEN
       RETURN 2;
   END IF;
   IF dMonth > 6 AND dMonth < 10
     THEN
       RETURN 3;
   END IF;
   RETURN 4;
  END
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `GET_DAY_FROM_TIMESTAMP`;
DELIMITER |
CREATE FUNCTION GET_DAY_FROM_TIMESTAMP(Stamp INT)
RETURNS INT
 DETERMINISTIC
  BEGIN
   RETURN TO_DAYS(FROM_UNIXTIME(Stamp))-719528;
  END
|
DELIMITER ;
#-------------------------------------------------------------------------------