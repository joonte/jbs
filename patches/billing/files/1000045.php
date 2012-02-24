<?php
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles','*',Array('Where'=>"`TemplateID` IN ('Juridical')"));
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
      $Attribs['aSourname'] = $Attribs['dSourname'];
      $Attribs['aName']     = $Attribs['dName'];
      $Attribs['aLastname'] = $Attribs['dLastname'];
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