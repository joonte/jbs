UPDATE `Clauses` SET `Text` = REPLACE(`Text`,',"IsStamp":"1"','');
-- SEPARATOR
UPDATE `Clauses` SET `Text` = REPLACE(`Text`,',"IsStamp":"0"','');