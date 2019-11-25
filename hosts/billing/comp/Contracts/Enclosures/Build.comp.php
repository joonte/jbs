<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ContractEnclosureID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DOM.class.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ContractEnclosure = DB_Select('ContractsEnclosures',Array('ID','CreateDate','TypeID','ContractID','Number'),Array('UNIQ','ID'=>$ContractEnclosureID));
#-------------------------------------------------------------------------------
switch(ValueOf($ContractEnclosure)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $ContractEnclosureID = (integer)$ContractEnclosure['ID'];
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Contract/Enclosure/Number',$ContractEnclosure['Number']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $ContractEnclosure['Number'] = $Comp;
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Date/Standard',$ContractEnclosure['CreateDate']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $ContractEnclosure['CreateDate'] = $Comp;
    #---------------------------------------------------------------------------
    $Replace = Array('ContractEnclosure'=>$ContractEnclosure,'MotionDocumentID'=>'NO');
    #---------------------------------------------------------------------------
    $Contract = DB_Select('Contracts',Array('ID','CreateDate','TypeID','ProfileID'),Array('UNIQ','ID'=>$ContractEnclosure['ContractID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contract)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $ProfileID = (integer)$Contract['ProfileID'];
        #-----------------------------------------------------------------------
        if(!$ProfileID)
          return TRUE;
        #-----------------------------------------------------------------------
        $ContractID = (integer)$Contract['ID'];
        #-----------------------------------------------------------------------
        $IsQuery = DB_Query(SPrintF('UPDATE `ContractsEnclosures` SET `CreateDate` = IF(`CreateDate` < %u,%u,`CreateDate`) WHERE `ContractID` = %u',$Contract['CreateDate'],$Contract['CreateDate'],$ContractID));
        if(Is_Error($IsQuery))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $UniqID = SPrintF('Enclosure:%u',$ContractEnclosureID);
        #-----------------------------------------------------------------------
        $MotionDocument = DB_Select('MotionDocuments','ID',Array('UNIQ','Where'=>SPrintF("`ContractID` = %u AND `TypeID` = 'ContractEnclosure' AND `UniqID` = '%s'",$ContractID,$UniqID)));
        #-----------------------------------------------------------------------
        switch(ValueOf($MotionDocument)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            #-------------------------------------------------------------------
            $Config = Config();
            #-------------------------------------------------------------------
            if(!$Config['Contracts']['Types'][$Contract['TypeID']]['IsUsedMotionDocuments'])
              break;
            #-------------------------------------------------------------------
            $MotionDocument = Comp_Load('www/Administrator/API/MotionDocumentEdit',Array('TypeID'=>'ContractEnclosure','ContractID'=>$ContractID,'AjaxCall'=>Array('Url'=>'/ContractEnclosureDownload','Args'=>Array('ContractEnclosureID'=>$ContractEnclosureID)),'UniqID'=>$UniqID));
            #-------------------------------------------------------------------
            switch(ValueOf($MotionDocument)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                # No more...
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
          case 'array':
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/MotionDocument/Number',$MotionDocument['ID']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(400);
            #-------------------------------------------------------------------
            $Replace['MotionDocumentID'] = $Comp;
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$Contract['ID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Contract['Number'] = $Comp;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Standard',$Contract['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Contract['CreateDate'] = $Comp;
        #-----------------------------------------------------------------------
        $Replace['Contract'] = $Contract;
        #-----------------------------------------------------------------------
        $Profile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
        #-----------------------------------------------------------------------
        switch(ValueOf($Profile)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Replace['Customer'] = $Profile['Attribs'];
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Clauses/Load','Contracts/Enclosures/Template');
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DOM = new DOM($Comp['DOM']);
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Clauses/Load',SPrintF('Contracts/Enclosures/Types/%s/Content',$ContractEnclosure['TypeID']));
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DOM->AddText('Header',$Comp['Title'],TRUE);
            #-------------------------------------------------------------------
            $DOM->AddChild('Content',$Comp['DOM']);
            #-------------------------------------------------------------------
            $Executor = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>100));
            #-------------------------------------------------------------------
            switch(ValueOf($Executor)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return TRUE;
              case 'array':
                #---------------------------------------------------------------
                $Replace['Executor'] = $Executor['Attribs'];
                #---------------------------------------------------------------
                $Comp = Comp_Load('Clauses/Load',SPrintF('Contracts/Types/%s/Footer/%s',$Contract['TypeID'],$Executor['TemplateID']));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $DOM->AddChild('Footer',$Comp['DOM']);
                #---------------------------------------------------------------
                $Replace['SignDate'] = $ContractEnclosure['CreateDate'];
                #---------------------------------------------------------------
                $Document = $DOM->Build();
                if(Is_Error($Document))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Replace = Array_ToLine($Replace);
                #---------------------------------------------------------------
                foreach(Array_Keys($Replace) as $LinkID){
                  #-------------------------------------------------------------
                  $Text = (string)$Replace[$LinkID];
                  #-------------------------------------------------------------
                  $Document = Str_Replace(SPrintF('%%%s%%',$LinkID),$Text?$Text:'-',$Document);
                }
                #---------------------------------------------------------------
		if(!SaveUploadedFile(Array(Array('Data'=>$Document,'Name'=>SPrintF('ContractEnclosure%s.html',$ContractEnclosureID),'Size'=>Mb_StrLen($Document,'8bit'),'Mime'=>'text/html')),'ContractsEnclosures',$ContractEnclosureID))
                  return new gException('CANNOT_SAVE_FILE','Не удалось сохранить файл');
                #---------------------------------------------------------------
                return TRUE;
              default:
                return ERROR | @Trigger_Error(101);
            }
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
