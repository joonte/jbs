<?php
#-------------------------------------------------------------------------------
$NoTypesDB = &Link_Get('NoTypesDB','boolean');
#-------------------------------------------------------------------------------
$NoTypesDB = TRUE;
#-------------------------------------------------------------------------------
$MotionDocuments = DB_Select('MotionDocuments');
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
          $AjaxCall = Array('Url'=>'/ContractDownload','Args'=>Array('ContractID'=>$MotionDocument['ContractID']));
        break;
        case 'ContractEnclosure':
          #---------------------------------------------------------------------
          $UniqID = Explode(':',$MotionDocument['UniqID']);
          #---------------------------------------------------------------------
          $AjaxCall = Array('Url'=>'/ContractEnclosureDownload','Args'=>Array('ContractEnclosureID'=>Next($UniqID)));
        break;
        case 'WorksCompliteReport':
          #---------------------------------------------------------------------
          $UniqID = Explode(':',$MotionDocument['UniqID']);
          #---------------------------------------------------------------------
          $UniqID = Explode('/',Next($UniqID));
          #---------------------------------------------------------------------
          $AjaxCall = Array('Url'=>'/WorksCompliteReportDownload','Args'=>Array('ContractID'=>$MotionDocument['ContractID'],'Month'=>Next($UniqID)));
        break;
        default:
          continue 2;

      }
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('MotionDocuments',Array('AjaxCall'=>JSON_Encode($AjaxCall)),Array('ID'=>$MotionDocument['ID']));
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