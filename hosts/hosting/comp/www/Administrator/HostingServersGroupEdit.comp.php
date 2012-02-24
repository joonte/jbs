<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$HostingServersGroupID = (integer) @$Args['HostingServersGroupID'];
#-------------------------------------------------------------------------------
if($HostingServersGroupID){
  #-----------------------------------------------------------------------------
  $HostingServersGroup = DB_Select('HostingServersGroups','*',Array('UNIQ','ID'=>$HostingServersGroupID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingServersGroup)){
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
  $HostingServersGroup = Array(
    #---------------------------------------------------------------------------
    'Name'       => 'По умолчанию',
    'FunctionID' => 'NotDefined',
    'Comment'    => 'Москва, M9',
    'SortID'     => 10
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/HostingServersGroupEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($HostingServersGroupID?'Редактирование группы серверов':'Добавление группы серверов');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Name',
    'value' => $HostingServersGroup['Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Название группы',$Comp);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Functions = $Config['Hosting']['Balancing']['Functions'];
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($Functions) as $FunctionID)
	$Options[$FunctionID] = $Functions[$FunctionID]['Name'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'FunctionID'),$Options,$HostingServersGroup['FunctionID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Функция балансировки',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/TextArea',Array('rows'=>3,'cols'=>30,'name'=>'Comment'),$HostingServersGroup['Comment']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Комментарий',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'SortID',
    'size'  => 5,
    'value' => $HostingServersGroup['SortID']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порядок сортировки',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'HostingServersGroupEdit();',
    'value'   => ($HostingServersGroupID?'Сохранить':'Добавить')
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'HostingServersGroupEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($HostingServersGroupID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'HostingServersGroupID',
      'type'  => 'hidden',
      'value' => $HostingServersGroupID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
