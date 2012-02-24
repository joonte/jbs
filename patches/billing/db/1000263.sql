set @Text = '<NOBODY>
  <SPAN>Ваша ссылка как партнера: </SPAN>
  <SPAN class="Standard">http://%HOST_ID%/Index?OwnerID=%__USER.ID%</SPAN>
  <SPAN> разместите ее у себя на сайте.</SPAN>
</NOBODY>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','yes','Header:/DependUsers','Информация для партнеров',@Text);