<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$MotionDocumentID = (integer) @$Args['MotionDocumentID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$MotionDocument = DB_Select('MotionDocumentsOwners','*',Array('UNIQ','ID'=>$MotionDocumentID));
#-------------------------------------------------------------------------------
switch(ValueOf($MotionDocument)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('MotionDocumentRead',(integer)$__USER['ID'],$MotionDocument['UserID']?(integer)$MotionDocument['UserID']:100);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Документ');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/MotionDocument/Number',$MotionDocument['ID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$MotionDocument['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата регистрации',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/MotionDocument/Type',$MotionDocument['TypeID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тип',$Comp);
        #-----------------------------------------------------------------------
        $Contract = DB_Select('Contracts','Customer',Array('UNIQ','ID'=>$MotionDocument['ContractID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($Contract)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Table[] = Array('Контрагент',$Contract['Customer']);
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Statuses/State','MotionDocuments',$MotionDocument);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table = Array_Merge($Table,$Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Tables/Standard',$Table);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DOM->AddChild('Into',$Comp);
            #-------------------------------------------------------------------
            if(Is_Error($DOM->Build(FALSE)))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            return Array('Status'=>'Ok','DOM'=>$DOM->Object);
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
