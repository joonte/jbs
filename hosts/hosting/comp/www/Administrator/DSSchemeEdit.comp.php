<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
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
$DSSchemeID = (integer) @$Args['DSSchemeID'];
#-------------------------------------------------------------------------------
if($DSSchemeID){
  #-----------------------------------------------------------------------------
  $DSScheme = DB_Select('DSSchemes','*',Array('UNIQ','ID'=>$DSSchemeID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DSScheme)){
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
  $DSScheme = Array(
    #---------------------------------------------------------------------------
    'GroupID'			=> 1,
    'UserID'			=> 1,
    'Name'			=> 'DedicOne',
    'PackageID'			=> 'd1',
    'CostDay'			=> 100,
    'CostMonth'			=> 3000,
    'CostInstall'		=> 300,
    'ServersGroupID'		=> 1,
    'NumServers'		=> 10,
    'RemainServers'		=> 10,
    'IsCalculateNumServers'	=> TRUE,
    'IsActive'			=> TRUE,
    'IsProlong'			=> TRUE,
    'MinDaysPay'		=> 31,
    'MinDaysProlong'		=> 14,
    'MaxDaysPay'		=> 1460,
    'MaxOrders'			=> 0,
    'SortID'			=> 10,
    'cputype'			=> 'Opteron',
    'cpuarch'			=> 'x32',
    'numcpu'			=> 2,
    'numcores'			=> 8,
    'cpufreq'			=> 2000,
    'ram'			=> 2048,
    'raid'			=> '3Ware 9650SE-4LPML, 256Mb cache',
    'disk1'			=> 'SATA 500Gb',
    'disk2'			=> 'SATA 500Gb',
    'disk3'			=> 'no',
    'disk4'			=> 'no',
    'chrate'			=> 8,
    'trafflimit'		=> 1000,
    'traffcorrelation'		=> '1:4',
    'OS'			=> 'FreeBSD 8.2',
    'UserComment'		=> 'Идеальный сервер для высоконагруженного проекта ...',
    'AdminComment'		=> 'второй диск скоро посыпется, надо заменить',
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
$Title = ($DSSchemeID?'Редактирование нового тарифа DS':'Добавление нового тарифа DS');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец тарифа',$DSScheme['GroupID'],$DSScheme['UserID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Name',
    'value' => $DSScheme['Name']
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
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'PackageID',
    'value' => $DSScheme['PackageID']
  ),
  'Точное имя пакета в панели управления'
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Идентификатор пакета в панели',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostDay','value'=>SPrintF('%01.2f',$DSScheme['CostDay'])));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость дня'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется в расчетах стоимости')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostMonth','value'=>SPrintF('%01.2f',$DSScheme['CostMonth'])));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость месяца'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется для отображения')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostInstall','value'=>SPrintF('%01.2f',$DSScheme['CostInstall'])));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Стоимость установки/подключения',$Comp);
#-------------------------------------------------------------------------------

$ServersGroups = DB_Select('DSServersGroups','*');
#-------------------------------------------------------------------------------
switch(ValueOf($ServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVERS_GROUPS_NOT_FOUND','Группы серверов не найдены');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
foreach($ServersGroups as $ServersGroup)
	$Options[$ServersGroup['ID']] = $ServersGroup['Name'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'ServersGroupID'),$Options,$DSScheme['ServersGroupID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа серверов',$Comp);

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 5,
    'name'  => 'NumServers',
    'value' => $DSScheme['NumServers']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
$Table[] = Array('Всего серверов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 5,
    'name'  => 'RemainServers',
    'value' => $DSScheme['RemainServers']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
$Table[] = Array('Осталось серверов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsCalculateNumServers','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
if($DSScheme['IsCalculateNumServers'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsCalculateNumServers\'); return false;'),'Автоматический пересчёт числа серверов'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'тариф отключается, если нет серверов')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsActive','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($DSScheme['IsActive'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsActive\'); return false;'),'Тариф активен'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsProlong','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($DSScheme['IsProlong'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsProlong\'); return false;'),'Возможность продления'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 5,
    'name'  => 'MinDaysPay',
    'value' => $DSScheme['MinDaysPay']
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
    'type'  => 'text',
    'size'  => 5,
    'name'  => 'MinDaysProlong',
    'value' => $DSScheme['MinDaysProlong'],
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
    'type'  => 'text',
    'size'  => 5,
    'name'  => 'MaxDaysPay',
    'value' => $DSScheme['MaxDaysPay']
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
     'type'  => 'text',
     'size'  => 5,
     'name'  => 'MaxOrders',
     'value' => $DSScheme['MaxOrders'],
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
    'type'  => 'text',
    'name'  => 'SortID',
    'size'  => 5,
    'value' => $DSScheme['SortID']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порядок сортировки',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Технические характеристики сервера';
#-------------------------------------------------------------------------------

# Load CPUType => CPUName Array
$CpuArray = Comp_Load('Formats/DSOrder/CPUTypesList');
if(Is_Error($CpuArray))
	return ERROR | @Trigger_Error(500);

$Comp = Comp_Load('Form/Select',
	Array('name'=>'cputype'),
	$CpuArray,
	$DSScheme['cputype']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Тип процессора',$Comp);
#-------------------------------------------------------------------------------

$Comp = Comp_Load('Form/Select',
	Array('name'=>'cpuarch'),
	Array(	'x32'	=> '32-битная',
		'x64'	=> '64-битная'
	),
	$DSScheme['cpuarch']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Архитектура процессора',$Comp);
#-------------------------------------------------------------------------------


$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 5,
		'name'  => 'numcpu',
		'value' => $DSScheme['numcpu']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Число физических процессоров',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 5,
		'name'  => 'numcores',
		'value' => $DSScheme['numcores']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Число ядер в процессоре',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 5,
		'name'  => 'cpufreq',
		'value' => $DSScheme['cpufreq']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Частота процессора, MHz',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 5,
		'name'  => 'ram',
		'value' => $DSScheme['ram']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Объём оперативной памяти, Mb',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 25,
		'name'  => 'raid',
		'value' => $DSScheme['raid']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Тип RAID контроллера',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 25,
		'name'  => 'disk1',
		'value' => $DSScheme['disk1']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Характеристики 1 жёсткого диска',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 25,
		'name'  => 'disk2',
		'value' => $DSScheme['disk2']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Характеристики 2 жёсткого диска',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 25,
		'name'  => 'disk3',
		'value' => $DSScheme['disk3']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Характеристики 3 жёсткого диска',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 25,
		'name'  => 'disk4',
		'value' => $DSScheme['disk4']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = Array('Характеристики 4 жёсткого диска',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Прочая информация';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'chrate',
    'value' => $DSScheme['chrate']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
$Table[] = Array('Скорость канала, мегабит',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	 Array(
		'type'  => 'text',
		'size'  => 10,
		'name'  => 'trafflimit',
		'value' => $DSScheme['trafflimit']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Месячный трафик, Gb',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	 Array(
		'type'  => 'text',
		'size'  => 10,
		'name'  => 'traffcorrelation',
		'value' => $DSScheme['traffcorrelation']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Соотношения трафика, входящий/исходящий',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	 Array(
		'type'  => 'text',
		'size'  => 10,
		'name'  => 'OS',
		'value' => $DSScheme['OS']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Предустановленная ОС',$Comp);

#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/TextArea',
	Array(
		'name'  => 'UserComment',
		'style' => 'width:100%;',
		'rows'  => 3
	),
	$DSScheme['UserComment']
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = 'Описание сервера, для пользователя';
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/TextArea',
	Array(
		'name'  => 'AdminComment',
		'style' => 'width:100%;',
		'rows'  => 3
	),
	$DSScheme['AdminComment']
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

$Table[] = 'Заметка по серверу, для администратора';
$Table[] = $Comp;

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('align'=>'right'));
#-------------------------------------------------------------------------------
if($DSSchemeID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'type'    => 'checkbox',
      'onclick' => 'form.DSSchemeID.value = (checked?0:value);',
      'value'   => $DSSchemeID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Div->AddChild($Comp);
  #-----------------------------------------------------------------------------
  $Div->AddChild(new Tag('SPAN',Array('class'=>'Comment'),'создать новый тариф'));
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/DSSchemeEdit','DSSchemeEditForm','%s');",$Title),
    'value'   => ($DSSchemeID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'DSSchemeEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'DSSchemeID',
    'type'  => 'hidden',
    'value' => $DSSchemeID
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
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
