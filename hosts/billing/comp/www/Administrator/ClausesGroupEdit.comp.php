<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$GroupID = (integer) @$Args['GroupID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($GroupID){
	#-------------------------------------------------------------------------------
	$ClausesGroup = DB_Select('ClausesGroupsOwners',Array('*'),Array('UNIQ','ID'=>$GroupID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ClausesGroup)){
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
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ClausesGroup = Array(
				'Name'		=> '',
				'Notice'	=> '',
				'IsProtected'	=> FALSE,
				'IsPublish'	=> FALSE
				);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
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
$Title = ($GroupID?'Редактирование группы статей':'Добавление новой группы статей');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'size'	=> 40,
			'name'	=> 'Name',
			'value'	=> $ClausesGroup['Name'],
			'prompt'=> 'Имя группы статей'

			)
		);
#-------------------------------------------------------------------------------
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Имя',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'size'	=> 40,
			'name'	=> 'Notice',
			'value'	=> $ClausesGroup['Notice'],
			'prompt'=> 'Описание группы статей'

			)
		);
#-------------------------------------------------------------------------------
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Описание',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры доступа';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsProtected','id'=>'IsProtected','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ClausesGroup['IsProtected'])
	$Comp->AddAttribs(Array('checked'=>'true'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsProtected'),'Защищённая'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsPublish','id'=>'IsPublish','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ClausesGroup['IsPublish'])
	$Comp->AddAttribs(Array('checked'=>'true'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsPublish'),'Опубликована'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
		Array(
			'type'		=> 'button',
			'onclick'	=> SPrintF("FormEdit('/Administrator/API/ClausesGroupEdit','ClausesGroupEditForm','%s');",$Title),
			'value'		=> ($GroupID?'Сохранить':'Добавить')
			)
		);
#-------------------------------------------------------------------------------
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ClausesGroupEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($GroupID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'name'	=> 'GroupID',
				'type'	=> 'hidden',
				'value'	=> $GroupID
				)
			);
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
