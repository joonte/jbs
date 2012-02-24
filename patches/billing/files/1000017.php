<?php
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles','*',Array('Where'=>"`TemplateID` IN ('Natural','Juridical','Individual')"));
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
      if($TemplateID != 'Natural'){
        #-----------------------------------------------------------------------
        $Attribs['jAddress']  = $Attribs['UridicalAddress'];
        $Attribs['pAddress']  = $Attribs['PostAddress'];
        $Attribs['nAddress']  = $Attribs['NowAddress'];
        #-----------------------------------------------------------------------
        UnSet($Attribs['UridicalAddress']);
        UnSet($Attribs['PostAddress']);
        UnSet($Attribs['NowAddress']);
      }
      #-------------------------------------------------------------------------
      #$Attribs['AddressEn'] = $Attribs['AdressEn'];
      #UnSet($Attribs['AdressEn']);
      #-------------------------------------------------------------------------
      $Attribs['StateEn'] = '';
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