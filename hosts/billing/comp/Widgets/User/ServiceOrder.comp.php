<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Path = Tree_Path('Groups',(integer)$__USER['GroupID'],'ID');
#-------------------------------------------------------------------------------
switch(ValueOf($Path)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Where = Array(SPrintF("(`GroupID` IN (%s) OR `UserID` = %u) AND `IsActive` = 'yes'",Implode(',',$Path),$__USER['ID']),"`IsHidden` != 'yes'");
    #---------------------------------------------------------------------------
    $Services = DB_Select('Services',Array('ID','Code','Item','Name','ServicesGroupID','IsActive','(SELECT `Name` FROM `ServicesGroups` WHERE `ServicesGroups`.`ID` = `Services`.`ServicesGroupID`) as `ServicesGroupName`','(SELECT `SortID` FROM `ServicesGroups` WHERE `ServicesGroups`.`ID` = `Services`.`ServicesGroupID`) as `ServicesSortID`'),Array('SortOn'=>Array('ServicesSortID','SortID'),'Where'=>$Where));
    #---------------------------------------------------------------------------
    switch(ValueOf($Services)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
         return FALSE;
      case 'array':
        #-----------------------------------------------------------------------
        $Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5));
        #-----------------------------------------------------------------------
        $ServicesGroupID = UniqID();
        #-----------------------------------------------------------------------
        $Tr = new Tag('TR');
        #-----------------------------------------------------------------------
        foreach($Services as $Service){
          #---------------------------------------------------------------------
          if($Service['ServicesGroupID'] != $ServicesGroupID){
            #-------------------------------------------------------------------
            $ServicesGroupID = $Service['ServicesGroupID'];
            #-------------------------------------------------------------------
            if(Count($Tr->Childs)){
              #-----------------------------------------------------------------
              $Table->AddChild($Tr);
              #-----------------------------------------------------------------
              $Tr = new Tag('TR');
            }
            #-------------------------------------------------------------------
            $Table->AddChild(new Tag('TR',new Tag('TD',Array('colspan'=>6,'class'=>'Separator'),$Service['ServicesGroupName'])));
          }
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Formats/String',$Service['Item'],20);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Code = $Service['Code'];
	  # added by lissyara 2011-10-10 in 16:03 MSK, for JBS-176
	  if($Code == "Domains"){$Code = "Domain";}
          #---------------------------------------------------------------------
          $OnClick = SPrintF("ShowWindow('%s');",($Code != 'Default'?SPrintF('%sOrder',$Code):SPrintF('ServiceOrder?ServiceID=%s',$Service['ID'])));
          #---------------------------------------------------------------------
          $Tr->AddChild(new Tag('TD',Array('class'=>'Standard','onclick'=>$OnClick,'align'=>'center','width'=>'100px','valign'=>'top','style'=>'padding:5px;'),new Tag('IMG',Array('class'=>'Button','alt'=>$Service['Name'],'width'=>72,'height'=>72,'align'=>'center','src'=>SPrintF('/ServiceEmblem?ServiceID=%u',$Service['ID']))),new Tag('BR'),new Tag('SPAN',Array('style'=>'color:#5B8B15;'),$Service['Item'])));
          #---------------------------------------------------------------------
          if(Count($Tr->Childs)%6 == 0){
            #-------------------------------------------------------------------
            $Table->AddChild($Tr);
            #-------------------------------------------------------------------
            $Tr = new Tag('TR');
          }
        }
        #-----------------------------------------------------------------------
        if(Count($Tr->Childs))
         $Table->AddChild($Tr);
        #-----------------------------------------------------------------------
        return Array('Title'=>'Быстрый заказ услуг','DOM'=>$Table);
      default:
        return ERROR | @Trigger_Error(101);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
