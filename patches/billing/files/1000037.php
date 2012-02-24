<?php
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles','*',Array('Where'=>"`TemplateID` IN ('Natural','Individual','Juridical')"));
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
      $Template = System_XML(SPrintF('profiles/%s.xml',$Profile['TemplateID']));
      if(Is_Error($Template))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      foreach(Array_Keys($Template['Attribs']) as $AttribID){
        #-----------------------------------------------------------------------
        if(!IsSet($Attribs[$AttribID]))
          $Attribs[$AttribID] = $Template['Attribs'][$AttribID]['Value'];
      }
      #-------------------------------------------------------------------------
      foreach(Array_Keys($Attribs) as $AttribID){
        #-----------------------------------------------------------------------
        if(!IsSet($Template['Attribs'][$AttribID]))
          UnSet($Attribs[$AttribID]);
      }
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