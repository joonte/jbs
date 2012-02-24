<?php
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles','*');
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
      $TemplateID = $Profile['TemplateID'];
      #-------------------------------------------------------------------------
      $Template = System_XML(SPrintF('profiles/%s.xml',$TemplateID));
      if(Is_Error($Template))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Replace = Array_ToLine($Attribs,'%');
      #-------------------------------------------------------------------------
      $ProfileName = $Template['ProfileName'];
      #-------------------------------------------------------------------------
      foreach(Array_Keys($Replace) as $Key)
        $ProfileName = Str_Replace($Key,$Replace[$Key],$ProfileName);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Profiles',Array('Name'=>$ProfileName),Array('ID'=>$Profile['ID']));
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