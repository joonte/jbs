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
$VPSSchemeID = (integer) @$Args['VPSSchemeID'];
#-------------------------------------------------------------------------------
if($VPSSchemeID){
  #-----------------------------------------------------------------------------
  $VPSScheme = DB_Select('VPSSchemes','*',Array('UNIQ','ID'=>$VPSSchemeID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($VPSScheme)){
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
  $VPSScheme = Array(
    #---------------------------------------------------------------------------
    'GroupID'            => 1,
    'UserID'            => 1,
    'Name'              => 'default1',
    'PackageID'         => 'MB500',
    'CostDay'           => 40,
    'CostMonth'         => 1200,
    'CostInstall'	=> 100,
    'ServersGroupID'    => 1,
    'Comment'           => 'Идеальный тариф для ...',
    'IsReselling'       => FALSE,
    'IsActive'          => TRUE,
    'IsProlong'         => TRUE,
    'IsSchemeChangeable'=> TRUE,
    'IsSchemeChange'    => TRUE,
    'MinDaysPay'        => 31,
    'MinDaysProlong'    => 14,
    'MaxDaysPay'        => 1460,
    'MaxOrders'		=> 0,
    'SortID'            => 10,
    'vdslimit'		=> 1,
    'disklimit'         => 999,
    'maxdesc'	        => 1000,
    'maxswap'	        => 10,
    'traf'	        => 1000000,
    'chrate'		=> 8,
    'QuotaUsers'	=> 20,
    'cpu'	        => 100,
    'ncpu'	        => 1,
    'mem'		=> 128,
    'bmem'		=> 128,
    'proc'              => 64,
    'ipalias'		=> 0,
    'disktempl'         => '',
    'extns'		=> 'dnsprovider',
    'limitpvtdns'	=> 256,
    'limitpubdns'	=> 256,
    'backup'		=> 'bmonth'
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
$Title = ($VPSSchemeID?'Редактирование тарифа виртуального сервера':'Добавление нового тарифа виртуального сервера');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец тарифа',$VPSScheme['GroupID'],$VPSScheme['UserID']);
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
    'value' => $VPSScheme['Name']
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
    'value' => $VPSScheme['PackageID']
  ),
  'Точное имя пакета в панели управления'
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Идентификатор пакета в панели',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostDay','value'=>SPrintF('%01.2f',$VPSScheme['CostDay'])));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость дня'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется в расчетах стоимости')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostMonth','value'=>SPrintF('%01.2f',$VPSScheme['CostMonth'])));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость месяца'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется для отображения')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostInstall','value'=>SPrintF('%01.2f',$VPSScheme['CostInstall'])));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость подключения'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Цена за инсталляцию')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
$ServersGroups = DB_Select('ServersGroups','*',Array('Where'=>'`ServiceID` = 30000'));
#-------------------------------------------------------------------------------
switch(ValueOf($ServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVERS_GROUPS_NOT_FOUND','Группы серверов не найдены. Необходимо добавить группу серверов для сервиса "VPS"');
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
$Comp = Comp_Load('Form/Select',Array('name'=>'ServersGroupID'),$Options,$VPSScheme['ServersGroupID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа серверов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name'  => 'Comment',
    'style' => 'width:100%;',
    'rows'  => 3
  ),
  $VPSScheme['Comment']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = 'Описание тарифа';
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsReselling','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSScheme['IsReselling'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsReselling\'); return false;'),'Права реселлера'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsActive','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSScheme['IsActive'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsActive\'); return false;'),'Тариф активен'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsProlong','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSScheme['IsProlong'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsProlong\'); return false;'),'Возможность продления'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsSchemeChangeable','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSScheme['IsSchemeChangeable'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsSchemeChangeable\'); return false;'),'Возможность перехода на тариф'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsSchemeChange','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSScheme['IsSchemeChange'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsSchemeChange\'); return false;'),'Возможность перехода с тарифа'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 5,
    'name'  => 'MinDaysPay',
    'value' => $VPSScheme['MinDaysPay']
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
     'value' => $VPSScheme['MinDaysProlong'],
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
    'value' => $VPSScheme['MaxDaysPay']
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
    'value' => $VPSScheme['MaxOrders'],
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
    'value' => $VPSScheme['SortID']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порядок сортировки',$Comp);
#-------------------------------------------------------------------------------
$Table[] = '-Общие ограничения';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'vdslimit',
    'value' => $VPSScheme['vdslimit']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Число VDS (для реселлеров)'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'QuotaUsers',
    'value' => $VPSScheme['QuotaUsers']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Число пользователей (для реселлеров)'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'disklimit',
    'value' => $VPSScheme['disklimit']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Дисковое пространство (Мб.)'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'ncpu',
    'prompt'=> 'количество/число процессоров выделыемых виртуальной машине',
    'value' => $VPSScheme['ncpu']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Количество процессоров'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'cpu',
    'prompt'=> 'частота каждого выделенного процессора',
    'value' => $VPSScheme['cpu']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Частота процессора'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'mem',
    'value' => $VPSScheme['mem'],
    'prompt'=> 'сколько оперативной памяти выделить машине'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Память'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'bmem',
    'value' => $VPSScheme['bmem'],
    'prompt'=> 'количество дополнительной оперативной памяти, которое может использовать виртуальная машина, при наличии свободной оперативной памяти на хост-машине'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Burstable RAM'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'maxswap',
    'value' => $VPSScheme['maxswap']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Свап'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'maxdesc',
    'value' => $VPSScheme['maxdesc'],
    'prompt'=> 'Максимальное количество открытых файлов'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Число дескрипторов'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'proc',
    'value' => $VPSScheme['proc']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Число процессов'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'size'  => 10,
		'name'  => 'traf',
		'value' => $VPSScheme['traf']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Месячный трафик, Mb'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'chrate',
    'value' => $VPSScheme['chrate'],
    'prompt'=> 'Ограничение скорости канала, в мегабитах'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Скорость канала, MBit/s'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'  => 'text',
			'name'  => 'disktempl',
			'value' => $VPSScheme['disktempl'],
			'prompt'=> 'Шаблон используемый при создании VPS, если тут не указано, используется соответствующее поле из настроек сервера'
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Шаблон диска',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = '-Ограничения для VdsManager4';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',
	Array('name'=>'extns'),
	Array('dnsnone'=>'нет','dnsprovider'=>'провайдера','dnsprivate'=>'собственные'),
	$VPSScheme['extns']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Сервера DNS',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 12,
    'name'  => 'limitpvtdns',
    'value' => $VPSScheme['limitpvtdns'],
    'prompt'=> 'Лимит приватных DNS. Параметр зависим от возможности DnsLimits. Используется для ограничения числа создаваемых доменов.'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Ограничение собственных DNS:',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 12,
    'name'  => 'limitpubdns',
    'value' => $VPSScheme['limitpubdns'],
    'prompt'=> 'Лимит DNS провайдера. Параметр зависим от возможности DnsLimits. Используется для ограничения числа создаваемых доменов.'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Ограничение DNS провайдера',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',
	Array('name'=>'backup'),
	Array('bnone'=>'не делается','bday'=>'ежедневно','bweek'=>'еженедельно','bmonth'=>'ежемесячно'),
	$VPSScheme['backup']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Резервное копирование',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('align'=>'right'));
#-------------------------------------------------------------------------------
if($VPSSchemeID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'type'    => 'checkbox',
      'onclick' => 'form.VPSSchemeID.value = (checked?0:value);',
      'value'   => $VPSSchemeID
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
    'onclick' => SPrintF("FormEdit('/Administrator/API/VPSSchemeEdit','VPSSchemeEditForm','%s');",$Title),
    'value'   => ($VPSSchemeID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'VPSSchemeEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'VPSSchemeID',
    'type'  => 'hidden',
    'value' => $VPSSchemeID
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
