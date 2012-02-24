UPDATE `Clauses` SET `Text` = CONCAT('<NOBODY>',`Text`,'</NOBODY>') WHERE `Partition`='/Documents' AND `Text` NOT LIKE '%</NOBODY>%';


