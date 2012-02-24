<?php
#-------------------------------------------------------------------------------
$IsDelete = DB_Delete('ContractsEnclosures',Array('Where'=>"`TypeID` IN ('HostingOrderBlank','HostingOrderSchemeChange','HostingOrderDelete')"));
if(Is_Error($IsDelete))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$MotionDocuments = DB_Select('MotionDocuments',Array('ID','TypeID','Attribs','Document'),Array('Where'=>"`TypeID` IN ('Contract','ContractEnclosure')"));
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
      switch($MotionDocument['TypeID']){
        case 'Contract':
          #---------------------------------------------------------------------
          $ID = $MotionDocument['Attribs']['ID'];
          #---------------------------------------------------------------------
          $IsUpdate = DB_Update('MotionDocuments',Array('Document'=>SPrintF('/ContractDownload?ContractID=%u',$ID)),Array('ID'=>$MotionDocument['ID']));
          if(Is_Error($IsUpdate))
            return ERROR | @Trigger_Error(101);
        break;
        case 'ContractEnclosure':
          #---------------------------------------------------------------------
          $ID = $MotionDocument['Attribs']['ID'];
          #---------------------------------------------------------------------
          $IsUpdate = DB_Update('MotionDocuments',Array('Document'=>SPrintF('/ContractEnclosureDownload?ContractEnclosureID=%u',$ID)),Array('ID'=>$MotionDocument['ID']));
          if(Is_Error($IsUpdate))
            return ERROR | @Trigger_Error(101);
        break;
        default:
          # No more...
      }
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$MotionDocuments = DB_Select('MotionDocuments',Array('ID','TypeID','Attribs','Document'),Array('Where'=>"`TypeID` IN ('Contract','ContractEnclosure')"));
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
      switch($MotionDocument['TypeID']){
        case 'Contract':
          #---------------------------------------------------------------------
          $ID = $MotionDocument['Attribs']['ID'];
          #---------------------------------------------------------------------
          $Count = DB_Count('Contracts',Array('ID'=>$ID));
          if(Is_Error($Count))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if(!$Count){
            #-------------------------------------------------------------------
            $IsDelete = DB_Delete('MotionDocuments',Array('ID'=>$MotionDocument['ID']));
            if(Is_Error($IsDelete))
              return ERROR | @Trigger_Error(500);
          }
        break;
        case 'ContractEnclosure':
          #---------------------------------------------------------------------
          $ID = $MotionDocument['Attribs']['ID'];
          #---------------------------------------------------------------------
          $Count = DB_Count('ContractsEnclosures',Array('ID'=>$ID));
          if(Is_Error($Count))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if(!$Count){
            #-------------------------------------------------------------------
            $IsDelete = DB_Delete('MotionDocuments',Array('ID'=>$MotionDocument['ID']));
            if(Is_Error($IsDelete))
              return ERROR | @Trigger_Error(500);
          }
        break;
        default:
          # No more...
      }
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>