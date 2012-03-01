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
$TicketID = (integer) @$Args['TicketID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Ticket = DB_Select('Edesks',Array('ID','TargetGroupID','TargetUserID','PriorityID','Theme'),Array('UNIQ','ID'=>$TicketID));
#-------------------------------------------------------------------------------
switch(ValueOf($Ticket)){
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
    if(Is_Error($DOM->Load('Window')))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddText('Title','Изменение запроса');
    #---------------------------------------------------------------------------
    $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/TicketEdit.js}'));
    #---------------------------------------------------------------------------
    $DOM->AddChild('Head',$Script);
    #---------------------------------------------------------------------------
    $Table = Array();
    #---------------------------------------------------------------------------
    $Groups = DB_Select('Groups',Array('ID','Name'),Array('Where'=>"`IsDepartment` = 'yes'"));
    #---------------------------------------------------------------------------
    switch(ValueOf($Groups)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Options = Array();
        #-----------------------------------------------------------------------
        foreach($Groups as $Group){
          #---------------------------------------------------------------------
          $GroupID = $Group['ID'];
          #---------------------------------------------------------------------
          $Options[$GroupID] = $Group['Name'];
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'TargetGroupID'),$Options,$Ticket['TargetGroupID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Отдел',$Comp);
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $Config = Config();
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'PriorityID'),$Config['Edesks']['Priorities'],$Ticket['PriorityID']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Приоритет',$Comp);
    #---------------------------------------------------------------------------
    $Users = DB_Select('Users',Array('ID','Name'),Array('Where'=>SPrintF("(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = `Users`.`GroupID`) = 'yes' OR `ID` = 100")));
    #---------------------------------------------------------------------------
    switch(ValueOf($Users)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Options = Array();
        #-----------------------------------------------------------------------
        foreach($Users as $User){
          #---------------------------------------------------------------------
          $UserID = $User['ID'];
          #---------------------------------------------------------------------
          $Options[$UserID] = $User['Name'];
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'TargetUserID'),$Options,$Ticket['TargetUserID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Сотрудник',$Comp);
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/TextArea',
      Array(
        'name'  => 'Theme',
        'style' => 'width:100%;',
        'rows'  => 5
      ),
      $Ticket['Theme']
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = 'Тема запроса';
    #---------------------------------------------------------------------------
    $Table[] = $Comp;
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'type'    => 'button',
        'onclick' => 'TicketEdit();',
        'value'   => 'Сохранить'
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = $Comp;
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form = new Tag('FORM',Array('name'=>'TicketEditForm','method'=>'POST'),$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'TicketID','value'=>$Ticket['ID']));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Form);
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Build(FALSE)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','DOM'=>$DOM->Object);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
