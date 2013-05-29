
UPDATE `Clauses` SET `Partition` = REPLACE(`Partition`,'/Administrator/Buttons:','') WHERE `Partition` LIKE '/Administrator/Buttons:%';

