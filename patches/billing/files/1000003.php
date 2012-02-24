<?php
#-------------------------------------------------------------------------------
$Count = DB_Count('Users',Array('Where'=>"`Login` = 'joonte'"));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count){
#-------------------------------------------------------------------------------
$Query = <<<EOD
INSERT INTO `Users`
  (`ID`,`GroupID`,`OwnerID`,`Name`,`Password`,`Email`,`Sign`,`HomePage`,`Settings`,`IsActive`,`IsProtected`)
VALUES
(50,3000000,1,'Joonte Software','bf4f00d4c5f44227066f5668a599bf9f','office@joonte.com','С уважением, Joonte Software.','/~','<XML>
 <Notifies>
  <Email type="array" />
  <ICQ type="array" />
  <SMS type="array" />
 </Notifies>
</XML>','yes','yes')
EOD;
#-------------------------------------------------------------------------------
  $IsQuery = DB_Query($Query);
  if(Is_Error($IsQuery))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>