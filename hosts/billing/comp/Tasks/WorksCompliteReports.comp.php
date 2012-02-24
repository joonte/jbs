<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/HTMLDoc.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$RegistrationMonth = (Date('Y') - 1970)*12 + Date('n') - 1;
#-------------------------------------------------------------------------------
$Reports = DB_Select('WorksCompliteReports',Array('ContractID','Month','UserID'),Array('Where'=>SPrintF('`Month` = %u',$RegistrationMonth)));
#-------------------------------------------------------------------------------
switch(ValueOf($Reports)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Config = Config();
    #---------------------------------------------------------------------------
    $Types = $Config['Contracts']['Types'];
    #---------------------------------------------------------------------------
    foreach($Reports as $Report){
      #-------------------------------------------------------------------------
      $ContractID = (integer)$Report['ContractID'];
      #-------------------------------------------------------------------------
      $Contract = DB_Select('Contracts','TypeID',Array('UNIQ','ID'=>$ContractID));
      #-------------------------------------------------------------------------
      switch(ValueOf($Contract)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'array':
          #---------------------------------------------------------------------
          if($Contract['TypeID'] == 'Default' || !$Types[$Contract['TypeID']]['IsUsedMotionDocuments'])
            continue;
          #---------------------------------------------------------------------
          $IsSend = Notify_Send('WorksCompliteReport',(integer)$Report['UserID'],Array('ContractID'=>$ContractID,'Month'=>$RegistrationMonth));
          #---------------------------------------------------------------------
          switch(ValueOf($IsSend)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              # No more...
            case 'true':
              # No more...
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
          #---------------------------------------------------------------------
          $UniqID = SPrintF('Report:%u/%u',$ContractID,$Report['Month']);
          #---------------------------------------------------------------------
          $Count = DB_Count('MotionDocuments',Array('Where'=>SPrintF("`UniqID` = '%s'",$UniqID)));
          if(Is_Error($Count))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if($Count)
            continue;
          #---------------------------------------------------------------------
          $MotionDocument = Comp_Load('www/Administrator/API/MotionDocumentEdit',Array('TypeID'=>'WorksCompliteReport','ContractID'=>$ContractID,'AjaxCall'=>Array('Url'=>'/WorksCompliteReportDownload','Args'=>Array('ContractID'=>$ContractID,'Month'=>$RegistrationMonth,'IsStamp'=>'yes')),'UniqID'=>$UniqID));
          #---------------------------------------------------------------------
          switch(ValueOf($MotionDocument)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return ERROR | @Trigger_Error(400);
            case 'array':
              continue;
            default:
              return ERROR | @Trigger_Error(101);
          }
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return MkTime(4,0,0,Date('n')+1,1,Date('Y'));
#-------------------------------------------------------------------------------

?>
