<?php
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles',Array('ID','Attribs'),Array('Where'=>"`TemplateID` IN ('Envelope')"));
#-------------------------------------------------------------------------------
switch(ValueOf($Profiles)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Profiles as $Profile){
      #-------------------------------------------------------------------------
      $Attribs = $Profile['Attribs'];
      #-------------------------------------------------------------------------
      if(IsSet($Attribs['pIndex']))
        continue;
      #-------------------------------------------------------------------------
      $Attribs['pIndex'] = $Attribs['PostIndex'];
      UnSet($Attribs['PostIndex']);
      #-------------------------------------------------------------------------
      $Attribs['pCity'] = $Attribs['City'];
      UnSet($Attribs['City']);
      #-------------------------------------------------------------------------
      $Attribs['pAddress'] = $Attribs['Address'];
      UnSet($Attribs['Address']);
      #-------------------------------------------------------------------------
      $Attribs['pRecipient'] = $Attribs['Recipient'];
      UnSet($Attribs['Recipient']);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Profiles',Array('Attribs'=>$Attribs),Array('ID'=>$Profile['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>