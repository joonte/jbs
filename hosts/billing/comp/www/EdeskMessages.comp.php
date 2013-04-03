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
$EdeskID = (integer) @$Args['EdeskID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Edesk = DB_Select('Edesks',Array('ID','Theme','TargetGroupID'),Array('UNIQ','ID'=>$EdeskID));
#-------------------------------------------------------------------------------
switch(ValueOf($Edesk)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $DOM = new DOM();
    #---------------------------------------------------------------------------
    $Links = &Links();
    # Коллекция ссылок
    $Links['DOM'] = &$DOM;
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Load('Main')))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $NoBody = new Tag('NOBODY',new Tag('NOBODY',new Tag('IMG',Array('width'=>12,'height'=>10,'src'=>'SRC:{Images/ArrowLeft.gif}')),new Tag('A',Array('href'=>'Edesks'),'Все обсуждения')));
    #---------------------------------------------------------------------------
    $Entrance = Tree_Entrance('Groups',(integer)$Edesk['TargetGroupID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($Entrance)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        if(In_Array($GLOBALS['__USER']['GroupID'],$Entrance)){
          #---------------------------------------------------------------------
          $Theme = Comp_Load('Edesks/Text',Array('String'=>$Edesk['Theme']));
          if(Is_Error($Theme))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Theme = SPrintF('<NOBODY>%s</NOBODY>',$Theme);
          #---------------------------------------------------------------------
          $DOM->AddText('Title',Strip_Tags($Theme));
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Buttons/Standard',Array('onclick'=>SPrintF("ShowWindow('/EdeskMessageEdit',{EdeskID:%u});",$Edesk['ID'])),'Добавить сообщение','Add.gif');
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Panel = Comp_Load('Buttons/Panel',Array('Comp'=>$Comp,'Name'=>'Добавить сообщение'));
          if(Is_Error($Panel))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $NoBody->AddChild($Panel);
          #---------------------------------------------------------------------
          $IsQuery = DB_Query(SPrintF('SET @local.EdeskID = %u',$Edesk['ID']));
          if(Is_Error($IsQuery))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Tables/Super','EdeskMessages');
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $NoBody->AddChild($Comp);
          #---------------------------------------------------------------------
          $NoBody->AddChild($Panel);
        }else{
          #---------------------------------------------------------------------
          $DOM->AddText('Title','Ошибка доступа');
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Information','Вы не можете участвовать в данном обсуждении.','Notice');
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $NoBody->AddChild($Comp);
        }
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$NoBody);
        #-----------------------------------------------------------------------
        $Out = $DOM->Build();
        #-----------------------------------------------------------------------
        if(Is_Error($Out))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return $Out;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
