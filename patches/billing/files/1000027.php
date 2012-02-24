<?php
#-------------------------------------------------------------------------------
if(HOST_ID != 'manager.host-food.ru')
  return TRUE;
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles','*',Array('Where'=>"`TemplateID` IN ('Natural')"));
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
      #-------------------------------------------------------------------------
      $Attribs['pCountry']   = $Attribs['Country'];
      $Attribs['pState']     = $Attribs['State'];
      $Attribs['pCity']      = $Attribs['City'];
      $Attribs['pAddress']   = Trim(Preg_Replace('/ул./iu','',$Attribs['Address']));
      $Attribs['AddressEn']  = Translit($Attribs['pAddress']);
      $Attribs['pIndex']     = $Attribs['PostIndex'];
      $Attribs['pRecipient'] = $Attribs['Recipient'];
      #-------------------------------------------------------------------------
      #-------------------------------------------------------------------------
      $Attribs['PasportWhom'] = $Attribs['PasportWhom'];
      $Attribs['PasportDate'] = $Attribs['PasportDate'];
      #-------------------------------------------------------------------------
      if(Preg_Match('/(.*)\s(.*)$/',$Attribs['PasportNum'],$Matches)){
        #-----------------------------------------------------------------------
        $Attribs['PasportLine'] = Str_Replace(' ','',$Matches[1]);
        $Attribs['PasportNum']  = $Matches[2];
      }
      #-------------------------------------------------------------------------
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