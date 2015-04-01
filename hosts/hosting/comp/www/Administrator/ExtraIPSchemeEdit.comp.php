<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ExtraIPSchemeID = (integer) @$Args['ExtraIPSchemeID'];
#-------------------------------------------------------------------------------
if($ExtraIPSchemeID){
	#-------------------------------------------------------------------------------
	$ExtraIPScheme = DB_Select('ExtraIPSchemes','*',Array('UNIQ','ID'=>$ExtraIPSchemeID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ExtraIPScheme)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ExtraIPScheme = Array(
				'GroupID'		=> 1,
				'UserID'		=> 1,
				'Name'			=> 'default1',
				'PackageID'		=> 'IP1',
				'CostDay'		=> 2.50,
				'CostMonth'		=> 75.00,
				'CostInstall'		=> 25.00,
				'AddressType'		=> 'IPv4',
				'HostingGroupID'	=> 0,
				'VPSGroupID'		=> 0,
				'DSGroupID'		=> 0,
				'Comment'		=> 'Один сайт - один IP адрес',
				'IsAutomatic'		=> TRUE,
				'IsActive'		=> TRUE,
				'IsProlong'		=> TRUE,
				'MinDaysPay'		=> 31,
				'MinDaysProlong'	=> 14,
				'MaxDaysPay'		=> 1460,
				'MaxOrders'		=> 0,
				'SortID'		=> 10
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
$Title = ($ExtraIPSchemeID?'Редактирование нового тарифа ExtraIP':'Добавление нового тарифа ExtraIP');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец тарифа',$ExtraIPScheme['GroupID'],$ExtraIPScheme['UserID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'name'	=> 'Name',
			'value'	=> $ExtraIPScheme['Name']
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Название тарифного плана',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'size'	=> 10,
			'name'	=> 'PackageID',
			'value'	=> $ExtraIPScheme['PackageID']
			),
		'Точное имя пакета в панели управления'
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Идентификатор пакета в панели',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostDay','value'=>SPrintF('%01.2f',$ExtraIPScheme['CostDay'])));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость дня'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется в расчетах стоимости')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostMonth','value'=>SPrintF('%01.2f',$ExtraIPScheme['CostMonth'])));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость месяца'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется для отображения')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostInstall','value'=>SPrintF('%01.2f',$ExtraIPScheme['CostInstall'])));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость подключения'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Плата за подключение IP адреса')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Options = Array('IPv4' => 'IPv4', 'IPv6' => 'IPv6');
$Comp = Comp_Load('Form/Select',Array('name'=>'AddressType','style'=>'width: 240px;'),$Options,$ExtraIPScheme['AddressType']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тип адреса',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры тарифа';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsAutomatic','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
if($ExtraIPScheme['IsAutomatic'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsAutomatic\'); return false;'),'Автоматическое подключение/отключение'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsActive','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ExtraIPScheme['IsActive'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsActive\'); return false;'),'Тариф активен'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsProlong','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ExtraIPScheme['IsProlong'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsProlong\'); return false;'),'Возможность продления'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'size'	=> 5,
			'name'	=> 'MinDaysPay',
			'value'	=> $ExtraIPScheme['MinDaysPay']
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Минимальное кол-во дней оплаты',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'size'	=> 5,
			'name'	=> 'MinDaysProlong',
			'value'	=> $ExtraIPScheme['MinDaysProlong'],
			'prompt'=> 'Минимальное число дней, на которое можно продлевать заказ'
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Минимальное кол-во дней продления',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'size'	=> 5,
			'name'	=> 'MaxDaysPay',
			'value'	=> $ExtraIPScheme['MaxDaysPay']
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Максимальное кол-во дней оплаты',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'size'	=> 5,
			'name'	=> 'MaxOrders',
			'value'	=> $ExtraIPScheme['MaxOrders'],
			'prompt'=> 'Максимально возможное число заказов по данному тарифу, на каждого клиента. Используется для создания "триальных" тарифных планов. Для снятия ограничений, введите ноль.'
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Максимальное кол-во заказов',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'	=> 'text',
			'name'	=> 'SortID',
			'size'	=> 5,
			'value'	=> $ExtraIPScheme['SortID']
			)
		);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порядок сортировки',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Где используется';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Hosting
$hGroups = DB_Select('ServersGroups','*',Array('Where'=>'`ServiceID` = 10000'));
switch(ValueOf($hGroups)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# no hosting servers groups
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		# create 'select' field for found groups
		$Options = Array('0' => 'Не используется');
		#-------------------------------------------------------------------------------
		foreach($hGroups as $hGroup)
			$Options[$hGroup['ID']] = $hGroup['Name'];
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Select',Array('name'=>'HostingGroupID','style'=>'width: 240px;'),$Options,$ExtraIPScheme['HostingGroupID']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Группа серверов хостинга',$Comp);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# VPS
$vGroups = DB_Select('ServersGroups','*',Array('Where'=>'`ServiceID` = 30000'));
switch(ValueOf($vGroups)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# no hosting servers groups
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		# create 'select' field for found groups
		$Options = Array('0' => 'Не используется');
		#-------------------------------------------------------------------------------
		foreach($vGroups as $vGroup)
			$Options[$vGroup['ID']] = $vGroup['Name'];
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Select',Array('name'=>'VPSGroupID','style'=>'width: 240px;'),$Options,$ExtraIPScheme['VPSGroupID']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Группа серверов VPS',$Comp);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# DS
$dGroups = DB_Select('ServersGroups','*',Array('Where'=>'`ServiceID` = 40000'));
switch(ValueOf($dGroups)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# no hosting servers groups
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		# create 'select' field for found groups
		$Options = Array('0' => 'Не используется');
		#-------------------------------------------------------------------------------
		foreach($dGroups as $dGroup)
			$Options[$dGroup['ID']] = $dGroup['Name'];
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Select',Array('name'=>'DSGroupID','style'=>'width: 240px;'),$Options,$ExtraIPScheme['DSGroupID']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Группа выделенных серверов',$Comp);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/TextArea',
		Array(
			'name'	=> 'Comment',
			'style'	=> 'width:100%;',
			'rows'	=> 3
			),
		$ExtraIPScheme['Comment']
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = 'Описание тарифа';
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('align'=>'right'));
#-------------------------------------------------------------------------------
if($ExtraIPSchemeID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'		=> 'checkbox',
				'onclick'	=> 'form.ExtraIPSchemeID.value = (checked?0:value);',
				'value'		=> $ExtraIPSchemeID
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Div->AddChild(new Tag('SPAN',Array('class'=>'Comment'),'создать новый тариф'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'		=> 'button',
			'onclick'	=> SPrintF("FormEdit('/Administrator/API/ExtraIPSchemeEdit','ExtraIPSchemeEditForm','%s');",$Title),
			'value'		=> ($ExtraIPSchemeID?'Сохранить':'Добавить')
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Div->AddChild($Comp);
#-------------------------------------------------------------------------------
$Table[] = $Div;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ExtraIPSchemeEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'name'	=> 'ExtraIPSchemeID',
			'type'	=> 'hidden',
			'value'	=> $ExtraIPSchemeID
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
