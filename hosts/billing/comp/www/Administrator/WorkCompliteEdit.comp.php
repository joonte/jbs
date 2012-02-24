<?php


#-----------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContractID     = (integer) @$Args['ContractID'];
$WorkCompliteID = (integer) @$Args['WorkCompliteID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($WorkCompliteID){
  #-----------------------------------------------------------------------------
  $WorkComplite = DB_Select('WorksComplite','*',Array('UNIQ','ID'=>$WorkCompliteID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($WorkComplite)){
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
}else{
  #-----------------------------------------------------------------------------
  $WorkComplite = Array(
    #---------------------------------------------------------------------------
    'ContractID' => $ContractID,
    'Month'      => (Date('Y') - 1970)*12 + Date('n'),
    'ServiceID'  => 1,
    'Comment'    => '',
    'Amount'     => 1,
    'Cost'       => 10.00,
    'Discont'    => 0
  );
}
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$WorkCompliteID?'Редактировать выполненную работу':'Добавить выполненную работу');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/WorkCompliteEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'WorkCompliteEditForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
if($WorkCompliteID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'WorkCompliteID',
      'type'  => 'hidden',
      'value' => $WorkCompliteID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Contracts/Select','ContractID',$WorkComplite['ContractID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор клиента',$Comp);
#-------------------------------------------------------------------------------
$CurrentMonth = (Date('Y') - 1970)*12 + Date('n');
#-------------------------------------------------------------------------------
for($Month=$CurrentMonth;$Month > $CurrentMonth-12;$Month--){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Date/Month',$Month);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Options[$Month] = $Comp;
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'Month'),$Options,$WorkComplite['Month']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Месяц',$Comp);
#-------------------------------------------------------------------------------
$Services = DB_Select('Services',Array('ID','Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($Services)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array();
    #---------------------------------------------------------------------------
    foreach($Services as $Service)
      $Options[$Service['ID']] = $Service['Name'];
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'ServiceID'),$Options,$WorkComplite['ServiceID']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Услуга',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/TextArea',
      Array(
        'name'  => 'Comment',
        'style' => 'width:100%;',
        'rows'  => 3
      ),
      $WorkComplite['Comment']
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Комментарий',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'Amount',
        'style' => 'text-align:right',
        'type'  => 'text',
        'size'  => 4,
        'value' => $WorkComplite['Amount']
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr = new Tag('TR',new Tag('TD',$Comp));
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Summ',Array('name'=>'Cost','value'=>$WorkComplite['Cost']));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('TD',$Comp));
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'Discont',
        'style' => 'text-align:right',
        'type'  => 'text',
        'size'  => 4,
        'value' => $WorkComplite['Discont']*100
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('TD',new Tag('NOBODY',$Comp,new Tag('SPAN','%'))));
    #---------------------------------------------------------------------------
    $Table[] = new Tag('TD',Array('colspan'=>2,'width'=>350),new Tag('TABLE',Array('class'=>'Standard','align'=>'right','cellspacing'=>5),new Tag('TR',new Tag('TD',Array('class'=>'Head'),'Кол-во:'),new Tag('TD',Array('class'=>'Head'),'Цена:'),new Tag('TD',Array('class'=>'Head'),'Скидка:')),$Tr));
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'type'    => 'button',
        'onclick' => 'WorkCompliteEdit();',
        'value'   => ($WorkCompliteID?'Сохранить':'Добавить')
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
