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
$ModeID =  (string) @$Args['ModeID'];
$RowID  = (integer) @$Args['RowID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['ID'],$ModeID))
  return ERROR | @Trigger_Error(201);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Statuses = $Config['Statuses'][$ModeID];
#-------------------------------------------------------------------------------
//$Row = DB_Select(SPrintF('%sOwners',$ModeID),'UserID',Array('UNIQ','ID'=>$RowID));
$Row = DB_Select(SPrintF('%sOwners',$ModeID),'UserID',Array('UNIQ','Limits'=>Array(0,1),'Where'=>SPrintF("`ID` = %u",$RowID)));
#-------------------------------------------------------------------------------
switch(ValueOf($Row)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('StatusesHistory',(integer)$GLOBALS['__USER']['ID'],(integer)$Row['UserID']);
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
        $DOM->AddText('Title','История изменений');
        #-----------------------------------------------------------------------
        $Row = Array();
        #-----------------------------------------------------------------------
        $Row[] = new Tag('TD',Array('class'=>'Head'),'Дата изменения');
        $Row[] = new Tag('TD',Array('class'=>'Head'),'Статус');
        $Row[] = new Tag('TD',Array('class'=>'Head'),'Инициатор');
        $Row[] = new Tag('TD',Array('class'=>'Head'),'Комментарий');
        #-----------------------------------------------------------------------
        $Table = Array($Row);
        #-----------------------------------------------------------------------
        $StatusesHistory = DB_Select('StatusesHistory','*',Array('SortOn'=>'StatusDate','Where'=>SPrintF("`ModeID` = '%s' AND `RowID` = %u",$ModeID,$RowID)));
        #-----------------------------------------------------------------------
        switch(ValueOf($StatusesHistory)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return new gException('STATUSES_HISTORY_NOT_FOUND','История изменений не найдена');
          case 'array':
            #-------------------------------------------------------------------
            foreach($StatusesHistory as $StatusHistory){
              #-----------------------------------------------------------------
	      $Tr = new Tag('TR');
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Date/Extended',$StatusHistory['StatusDate']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
	      $Tr->AddChild(new Tag('TD',Array('class'=>'Standard'),$Comp));
	      #-----------------------------------------------------------------
              #-----------------------------------------------------------------
	      # костыли для JBS-512
	      if($ModeID == 'Edesks'){
	        $Mode = 'Tickets';
              }elseif($ModeID == 'ContractsEnclosures'){
	        $Mode = 'Contracts/Enclosures';
              }elseif($ModeID == 'Orders'){
                $Mode = 'Services/Orders';
              }else{
	        $Mode = $ModeID;
	      }
	      #-----------------------------------------------------------------
              $Comp = Comp_Load(SPrintF('%s/Color',$Mode),$StatusHistory['StatusID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-------------------------------------------------------------------------------
	      $Style = Array('class'=>'Standard','style'=>SPrintF('background-color:%s;',$Comp['bgcolor']));
	      #-------------------------------------------------------------------------------
	      $Tr->AddChild(new Tag('TD',$Style,$Statuses[$StatusHistory['StatusID']]['Name']));
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Initiator = $GLOBALS['__USER']['IsAdmin']?$StatusHistory['Initiator']:preg_replace('/\s(\(\H+\))$/', '', $StatusHistory['Initiator']);
	      #-----------------------------------------------------------------
	      $Tr->AddChild(new Tag('TD',Array('class'=>'Standard'),$Initiator));
              #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Comment = $StatusHistory['Comment']?$StatusHistory['Comment']:'-';
	      #-----------------------------------------------------------------
	      $Comment = Comp_Load('Formats/String',$Comment,45);
	      if(Is_Error($Comment))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
	      $Tr->AddChild(new Tag('TD',Array('class'=>'Standard'),$Comment));
              #-----------------------------------------------------------------
	      $Table[] = $Tr;
            }
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Extended',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Comp);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
