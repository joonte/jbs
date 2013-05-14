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
$ClauseID = (integer) @$Args['ClauseID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ClauseID){
  #-----------------------------------------------------------------------------
  $Clause = DB_Select('Clauses','*',Array('UNIQ','ID'=>$ClauseID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Clause)){
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
  $Clause = Array(
    #---------------------------------------------------------------------------
    'PublicDate'  => Time(),
    'GroupID'     => 1,
    'Partition'   => '/NewClause',
    'TemplateID'  => 'Standard',
    'Title'       => 'Новая статья',
    'IsProtected' => FALSE,
    'IsXML'       => TRUE,
    'IsDOM'       => FALSE,
    'IsPublish'   => TRUE,
    'Text'        => '<P>Новая статья!</P>'
  );
  #-----------------------------------------------------------------------------
  foreach($Clause as $ParamID=>$Param){
    #---------------------------------------------------------------------------
    if(IsSet($Args[$ParamID]))
      $Clause[$ParamID] = $Args[$ParamID];
  }
}
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Standard')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->Delete('Title');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ClauseEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ClauseEditForm','action'=>'?IsReloaded=yes','onsubmit'=>'return false;','method'=>'POST'));
#-------------------------------------------------------------------------------
if($ClauseID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'type'  => 'hidden',
      'name'  => 'ClauseID',
      'value' => $Clause['ID']
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('jQuery/DatePicker','PublicDate',$Clause['PublicDate']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата публикации',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ClausesGroups = DB_Select('ClausesGroups',Array('*'),Array('Where'=>'`IsPublish` = "yes"'/*,'SortOn'=>'SortID'*/));
#-------------------------------------------------------------------------------
switch(ValueOf($ClausesGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$Options = Array();
	#-------------------------------------------------------------------------------
	foreach($ClausesGroups as $ClausesGroup)
		$Options[$ClausesGroup['ID']] = $ClausesGroup['Name'];
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'GroupID'),$Options,$Clause['GroupID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Категория статьи',$Comp);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'Partition',
    'size'  => 50,
    'value' => $Clause['Partition']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Адрес статьи',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'Title',
    'size'  => 80,
    'value' => $Clause['Title']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Заголовок статьи',$Comp);
#-------------------------------------------------------------------------------
if(IsSet($Args['IsReloaded']))
  $Clause['IsXML'] = (boolean)@$Args['IsXML'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('WYSIWYG','Text',$Clause['Text'],$Clause['IsXML'],SPrintF('/Administrator/ClauseImages?ClauseID=%u',$ClauseID));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('align'=>'right'));
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'checkbox',
    'name'    => 'IsXML',
    'onclick' => "form.submit();ShowProgress(checked?'Переход в режим визуального редактора':'Переход в текстовый режим');",
    'value'   => 'yes'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Clause['IsXML'])
  $Comp->AddAttribs(Array('checked'=>'true'));
#-------------------------------------------------------------------------------
/*if($Clause['IsProtected'])
  $Comp->AddAttribs(Array('disabled'=>'true'));*/
#-------------------------------------------------------------------------------
$Div->AddChild(new Tag('NOBODY',$Comp,new Tag('SPAN','редактор')));
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'checkbox',
    'name'  => 'IsDOM',
    'value' => 'yes'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
/*if($Clause['IsDOM'])
  $Comp->AddAttribs(Array('checked'=>'true'));*/
#-------------------------------------------------------------------------------
/*if($Clause['IsProtected'])
  $Comp->AddAttribs(Array('disabled'=>'true'));*/
#-------------------------------------------------------------------------------
$Div->AddChild(new Tag('NOBODY',$Comp,new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsDOM\'); return false;'),'valid DOM')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'checkbox',
    'name'    => 'IsPublish',
    'value'   => 'yes'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Clause['IsPublish'])
  $Comp->AddAttribs(Array('checked'=>'true'));
#-------------------------------------------------------------------------------
$Div->AddChild(new Tag('NOBODY',$Comp,new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsPublish\'); return false;'),'опубликовать')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'ClauseEdit();',
    'value'   => ($ClauseID?'Сохранить':'Добавить')
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Div->AddChild($Comp);
#-------------------------------------------------------------------------------
if($ClauseID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name' => 'IsReturn',
      'type' => 'checkbox'
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Div->AddChild(new Tag('NOBODY',$Comp,new Tag('SPAN',Array('class'=>'Comment','style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsReturn\'); return false;'),'(закрыть редактор)')));
}
#-------------------------------------------------------------------------------
$Table[] = $Div;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table,Array('width'=>'100%'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$Main = new Tag('TABLE',Array('width'=>'100%','cellspacing'=>0,'cellpadding'=>0),new Tag('TR',new Tag('TD',$Form)));
#-------------------------------------------------------------------------------
if($ClauseID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'Comment',
      'size'  => 15,
      'type'  => 'text'
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table = Array(Array('Комментарий',$Comp));
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Upload','ClauseFile');
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = Array('Файл',$Comp);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'type'    => 'button',
      'onclick' => 'ClauseFileEdit();',
      'value'   => 'Добавить'
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = $Comp;
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Tables/Standard',$Table,'Добавить файл');
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form = new Tag('FORM',Array('name'=>'ClauseFileEditForm','onsubmit'=>'return false;'),$Comp);
  #-----------------------------------------------------------------------------
  $Tr = new Tag('TR',new Tag('TD',$Form));
  #-----------------------------------------------------------------------------
  $IFrame = new Tag('IFRAME',Array('name'=>'ClauseFiles','src'=>SPrintF('/Administrator/ClauseFiles?ClauseID=%u',$Clause['ID']),'width'=>'400','height'=>'140px'),'Загрузка...');
  #-----------------------------------------------------------------------------
  $Tr->AddChild(new Tag('TD',Array('style'=>'padding:5px;'),$IFrame));
  #-----------------------------------------------------------------------------
  $Main->AddChild(new Tag('TR',new Tag('TD',new Tag('TABLE',Array('cellspacing'=>0),$Tr))));
}
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Main);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
