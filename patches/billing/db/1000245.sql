UPDATE `Clauses` SET `IsPublish` = 'yes';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','/Documents','Шаблоны документов','<COMP path="Clauses/Menu" args="Contracts%/Content">[шаблоны документов]</COMP>');
