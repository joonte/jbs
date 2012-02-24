<?php
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles','*',Array('Where'=>"`TemplateID` IN ('Individual')"));
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
      if(IsSet($Attribs['dSourname']))
        continue;
      #-------------------------------------------------------------------------
      $Director = $Attribs['Director'];
      #-------------------------------------------------------------------------
      $Director = Preg_Split('/\s+/',$Director);
      #-------------------------------------------------------------------------
      $Attribs['dSourname'] = Current($Director);
      $Attribs['dName']     = Next($Director);
      $Attribs['dLastname'] = Next($Director);
      #-------------------------------------------------------------------------
      $Attribs['NameEn']     = Translit($Attribs['dName']);
      $Attribs['LastnameEn'] = Translit(Mb_SubStr($Attribs['dLastname'],0,1));
      $Attribs['SournameEn'] = Translit($Attribs['dSourname']);
      #-------------------------------------------------------------------------
      #-------------------------------------------------------------------------
      $Attribs['jCountry']  = $Attribs['Country'];
      $Attribs['pCountry']  = $Attribs['Country'];
      #-------------------------------------------------------------------------
      $Attribs['jState']  = $Attribs['State'];
      $Attribs['pState']  = $Attribs['State'];
      $Attribs['StateEn'] = Translit($Attribs['State']);
      #-------------------------------------------------------------------------
      $Attribs['jCity']  = $Attribs['City'];
      $Attribs['pCity']  = $Attribs['City'];
      $Attribs['CityEn'] = Translit($Attribs['City']);
      #-------------------------------------------------------------------------
      $Attribs['jAddress']  = Trim(Preg_Replace('/(ул|пр)\./iu','',$Attribs['jAddress']));
      $Attribs['pAddress']  = Trim(Preg_Replace('/(ул|пр)\./iu','',$Attribs['jAddress']));
      $Attribs['AddressEn'] = Translit($Attribs['jAddress']);
      #-------------------------------------------------------------------------
      $Attribs['jIndex'] = $Attribs['PostIndex'];
      $Attribs['pIndex'] = $Attribs['PostIndex'];
      #-------------------------------------------------------------------------
      #-------------------------------------------------------------------------
      $Attribs['dPasportWhom'] = $Attribs['PasportWhom'];
      $Attribs['dPasportDate'] = $Attribs['PasportDate'];
      #-------------------------------------------------------------------------
      if(Preg_Match('/(.*)\s(.*)$/',$Attribs['PasportNum'],$Matches)){
        #-----------------------------------------------------------------------
        $Attribs['dPasportLine'] = Str_Replace(' ','',$Matches[1]);
        $Attribs['dPasportNum']  = $Matches[2];
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