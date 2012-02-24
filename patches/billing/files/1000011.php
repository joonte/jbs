<?php
#-------------------------------------------------------------------------------
$NoTypesDB = &Link_Get('NoTypesDB','boolean');
#-------------------------------------------------------------------------------
$NoTypesDB = TRUE;
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles',Array('ID','Attribs'));
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
      $XML = String_XML_Parse($Profile['Attribs']);
      if(Is_Exception($XML))
        continue;
      #-------------------------------------------------------------------------
      $Array = $XML->ToArray();
      #-------------------------------------------------------------------------
      if(!IsSet($Array['XML']))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Profiles',Array('Attribs'=>JSON_Encode($Array['XML'])),Array('ID'=>$Profile['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Contracts = DB_Select('Contracts',Array('ID','Customer'));
#-------------------------------------------------------------------------------
switch(ValueOf($Contracts)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Contracts as $Contract){
      #-------------------------------------------------------------------------
      $XML = String_XML_Parse($Contract['Customer']);
      if(Is_Exception($XML))
        continue;
      #-------------------------------------------------------------------------
      $Array = $XML->ToArray();
      #-------------------------------------------------------------------------
      if(!IsSet($Array['XML']))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Contracts',Array('Customer'=>JSON_Encode($Array['XML'])),Array('ID'=>$Contract['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$MotionDocuments = DB_Select('MotionDocuments',Array('ID','Attribs'));
#-------------------------------------------------------------------------------
switch(ValueOf($MotionDocuments)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($MotionDocuments as $MotionDocument){
      #-------------------------------------------------------------------------
      $XML = String_XML_Parse($MotionDocument['Attribs']);
      if(Is_Exception($XML))
        continue;
      #-------------------------------------------------------------------------
      $Array = $XML->ToArray();
      #-------------------------------------------------------------------------
      if(!IsSet($Array['XML']))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('MotionDocuments',Array('Attribs'=>JSON_Encode($Array['XML'])),Array('ID'=>$MotionDocument['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Tasks = DB_Select('Tasks',Array('ID','Params'));
#-------------------------------------------------------------------------------
switch(ValueOf($Tasks)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Tasks as $Task){
      #-------------------------------------------------------------------------
      $XML = String_XML_Parse($Task['Params']);
      if(Is_Exception($XML))
        continue;
      #-------------------------------------------------------------------------
      $Array = $XML->ToArray();
      #-------------------------------------------------------------------------
      if(!IsSet($Array['XML']))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Tasks',Array('Params'=>JSON_Encode($Array['XML'])),Array('ID'=>$Task['ID']));
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