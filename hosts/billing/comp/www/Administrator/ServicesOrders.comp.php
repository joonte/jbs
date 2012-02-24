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
$ServiceID = (integer) @$Args['ServiceID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddAttribs('MenuLeft',Array('args'=>'Administrator/Services'));
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Заказы на услуги');
#-------------------------------------------------------------------------------
if($ServiceID){
  #-----------------------------------------------------------------------------
  $Service = DB_Select('Services','Item',Array('UNIQ','ID'=>$ServiceID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Service)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $DOM->AddText('Title',SPrintF('Услуги → %s',$Service['Item']),TRUE);
      #-------------------------------------------------------------------------
      $Template = Array('Source'=>Array('Conditions'=>Array('Where'=>Array(UniqID()=>SPrintF('`ServiceID` = %u',$ServiceID)))));
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Tables/Super','ServicesOrders',$Template);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $DOM->AddChild('Into',$Comp);
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $Service = DB_Select('Services',Array('ID','Code'),Array('UNIQ','Where'=>"`IsActive` = 'yes' AND `IsHidden` = 'no'",'SortOn'=>'SortID','Limits'=>Array('Start'=>0,'Length'=>1)));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Service)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Information','Активные услуги не найдены. Пожалуйста, по всем вопросам обращайтесь в центр поддержки.','Notice');
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $DOM->AddChild('Into',$Comp);
    break;
    case 'array':
      ##-------------------------------------------------------------------------
      #$Code = $Service['Code'];
      ##-------------------------------------------------------------------------
      #Header(SPrintF('Location: /Administrator/%s',($Code != 'Default'?SPrintF('%sOrders',$Code):SPrintF('ServicesOrders?ServiceID=%s',$Service['ID']))));
      ##-------------------------------------------------------------------------
      #return NULL;
      $DOM->AddText('Title','Услуги → Все услуги → Заказы',TRUE);
#      $Template = Array('Source'=>Array('Conditions'=>Array('Where'=>Array(UniqID()=>SaPrintF('`ServiceID` = %u',$ServiceID)))));
#      $Comp = Comp_Load('Tables/Super','ServicesOrders',$Template);
      $Template = Array('Source'=>Array('Conditions'=>Array('Where'=>Array(UniqID()=>'`StatusID` IS NOT NULL'))));
      $Comp = Comp_Load('Tables/Super','AllServicesOrders',$Template);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      $DOM->AddChild('Into',$Comp);


      break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
