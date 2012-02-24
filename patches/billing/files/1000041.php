<?php
#-------------------------------------------------------------------------------
$Contracts = DB_Select('Contracts','*');
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
      if(Is_Error(DB_Transaction($TransactionID = UniqID('ID'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Statuses = Array();
      #-------------------------------------------------------------------------
      $MotionDocuments = DB_Select('MotionDocuments',Array('UniqID','StatusID','StatusDate'),Array('Where'=>SPrintF("`ContractID` = %u AND `TypeID` = 'WorksCompliteAct'",$Contract['ID'])));
      #-------------------------------------------------------------------------
      switch(ValueOf($MotionDocuments)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        break;
        case 'array':
          #---------------------------------------------------------------------
          foreach($MotionDocuments as $MotionDocument)
            $Statuses[$MotionDocument['UniqID']] = Array($MotionDocument['StatusID'],$MotionDocument['StatusDate']);
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $IsDelete = DB_Delete('MotionDocuments',Array('Where'=>SPrintF("`ContractID` = %u AND `TypeID` = 'WorksCompliteAct'",$Contract['ID'])));
      if(Is_Error($IsDelete))
       return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Acts = DB_Select('WorksCompliteActs','Month',Array('Where'=>SPrintF("`ContractID` = %u",$Contract['ID'])));
      #-------------------------------------------------------------------------
      switch(ValueOf($Acts)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        break;
        case 'array':
          #---------------------------------------------------------------------
          foreach($Acts as $Act){
            #-------------------------------------------------------------------
            $Number = Comp_Load('Formats/WorkComplite/Act/Number',$Contract['ID'],$Act['Month']);
            if(Is_Error($Number))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $UniqID = SPrintF('Number:%s',$Number);
            #-------------------------------------------------------------------
            $IMotionDocument = Array(
              #-----------------------------------------------------------------
              'TypeID'     => 'WorksCompliteAct',
              'ContractID' => $Contract['ID'],
              'Link'       => SPrintF('/WorksCompliteActDownload?ContractID=%s&Month=%s',$Contract['ID'],$Act['Month']),
              'UniqID'     => $UniqID,
              'StatusID'   => 'Waiting',
              'StatusDate' => Time()
            );
            #-------------------------------------------------------------------
            if(IsSet($Statuses[$UniqID])){
              #-----------------------------------------------------------------
              $Element = $Statuses[$UniqID];
              #-----------------------------------------------------------------
              $IMotionDocument['StatusID']   = Current($Element);
              $IMotionDocument['StatusDate'] = Next($Element);
            }
            #-------------------------------------------------------------------
            $MotionDocumentID = DB_Insert('MotionDocuments',$IMotionDocument);
            if(Is_Error($MotionDocumentID))
              return ERROR | @Trigger_Error(500);
          }
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      if(Is_Error(DB_Commit($TransactionID)))
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