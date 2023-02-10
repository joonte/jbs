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
$HostingSchemeID = (integer) @$Args['HostingSchemeID'];
#-------------------------------------------------------------------------------
if($HostingSchemeID){
  #-----------------------------------------------------------------------------
  $HostingScheme = DB_Select('HostingSchemes','*',Array('UNIQ','ID'=>$HostingSchemeID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingScheme)){
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
  $HostingScheme = Array(
    #---------------------------------------------------------------------------
    'GroupID'               => 1,
    'UserID'                => 1,
    'Name'                  => 'default',
    'PackageID'             => 'MB500',
    'CostDay'               => 40,
    'CostMonth'             => 1200,
    'Discount'		    => -1,
    'ServersGroupID'        => 1,
    'HardServerID'          => 0,
    'Comment'               => 'Идеальный тариф для ...',
    'IsReselling'           => FALSE,
    'IsActive'              => TRUE,
    'IsProlong'             => TRUE,
    'IsSchemeChangeable'    => TRUE,
    'IsSchemeChange'        => TRUE,
    'MinDaysPay'            => 31,
    'MinDaysProlong'        => 14,
    'MaxDaysPay'            => 1460,
    'MaxOrders'             => 0,
    'MinOrdersPeriod'		=> 0,
    'SortID'                => 10,
    'QuotaDisk'             => 999,
    'QuotaEmail'            => 999,
    'QuotaDomains'          => 999,
    'QuotaFTP'              => 999,
    'QuotaParkDomains'      => 999,
    'QuotaDBs'              => 999,
    'QuotaTraffic'          => 99999,
    'QuotaUsers'            => 9999,
    'IsShellAccess'         => FALSE,
    'IsSSLAccess'           => FALSE,
    'IsCGIAccess'           => FALSE,
    'QuotaWWWDomains'       => 999,
    'QuotaEmailDomains'     => 999,
    'QuotaUsersDBs'         => 999,
    'QuotaCPU'              => '1.00',
    'MaxExecutionTime'      => 60,
    'QuotaMEM'              => '0.00',
    'QuotaPROC'             => 0,
    'QuotaMPMworkers'       => 4,
    'mysqluserconnectlimit' => 100000000,
    'mysqlconnectlimit'     => 100000000,
    'mysqlupdateslimit'     => 100000000,
    'mysqlquerieslimit'     => 100000000,
    'mailrate'	            => 100,
    'IsSSIAccess'           => FALSE,
    'IsPHPModAccess'        => FALSE,
    'IsPHPCGIAccess'        => FALSE,
    'IsPHPFastCGIAccess'    => FALSE,
    'IsPHPSafeMode'         => FALSE,
    'field1'		    => '',
    'field2'                => '',
    'field3'                => ''
  );
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Messages = Messages();
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
$Title = ($HostingSchemeID?'Редактирование тарифа на хостинг':'Добавление нового тарифа на хостинг');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Owner','Владелец тарифа',$HostingScheme['GroupID'],$HostingScheme['UserID']);
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
    'value' => $HostingScheme['Name'],
    'prompt'=> 'Это название тарифа используется для показа пользователям'
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
    'name'  => 'PackageID',
    'value' => $HostingScheme['PackageID'],
    'prompt'=> 'Внутренний идентификатор тарифа. Рекомендуется делать уникальными, только английские буквы и цифры'
  ),
  'Точное имя пакета в панели управления'
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Идентификатор пакета в панели',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostDay','value'=>SPrintF('%01.2f',$HostingScheme['CostDay']),'prompt'=>'Используется при расчётах итоговой цены заказа, и во всех финансовых операциях по заказу хостинга'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость дня'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется в расчетах стоимости')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('name'=>'CostMonth','value'=>SPrintF('%01.2f',$HostingScheme['CostMonth']),'prompt'=>'Эта сумма отображается при заказе хостинга пользователем'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Стоимость месяца'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется для отображения')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
                'Form/Input',
                Array(
                        'type'  => 'text', 
                        'name'  => 'Discount',
                        'value' => SPrintF('%01.0f',$HostingScheme['Discount']),
                        'prompt'=> 'Если указано число от нуля до 100, то при оплате используется именно указанная скидка, все глобальные скидки и бонусы игнорируются. При указании отрицательного числа - используются глобальные скидки и бонусы',
                        )
                );
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Скидка'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Скидка на этот тариф')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
$ServersGroups = DB_Select('ServersGroups','*',Array('Where'=>'`ServiceID` = 10000'));
#-------------------------------------------------------------------------------
switch(ValueOf($ServersGroups)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SERVERS_GROUPS_NOT_FOUND','Группы серверов не найдены. Необходимо добавить группу серверов для сервиса "Hosting", в разделе "Дополнения -> Мастера настройки -> Сервера"');
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
$Comp = Comp_Load('Form/Select',Array('name'=>'ServersGroupID','style'=>'width: 240px','prompt'=>'Группа серверов, на которых будут размещаться заказы этого тарифа'),$Options,$HostingScheme['ServersGroupID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа серверов',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers','*',Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 10000','SortOn'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SERVERS_NOT_FOUND','Сервера хостинга не найдены');
  case 'array':
    # No more...
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Options = Array('0'=>'Любой сервер');
#-------------------------------------------------------------------------------
foreach($Servers as $Server)
  $Options[$Server['ID']] = $Server['Address'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'HardServerID','style'=>'width: 240px','prompt'=>'Для размещения всех заказов этого тарифа на определённом сервере - выберите его из списка. Обратите внимание, что сервер должен быть из той же группы серверов к которой относится тарифный план.'),$Options,$HostingScheme['HardServerID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервер размещения',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name'  => 'Comment',
    'style' => 'width:100%;',
    'rows'  => 3
  ),
  $HostingScheme['Comment']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = 'Описание тарифа';
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsReselling','id'=>'IsReselling','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsReselling'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsReselling'),'Права реселлера'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsActive','id'=>'IsActive','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsActive'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsActive'),'Тариф активен'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsProlong','id'=>'IsProlong','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsProlong'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsProlong'),'Возможность продления'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsSchemeChangeable','id'=>'IsSchemeChangeable','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsSchemeChangeable'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsSchemeChangeable'),'Возможность перехода на тариф'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsSchemeChange','id'=>'IsSchemeChange','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsSchemeChange'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsSchemeChange'),'Возможность перехода с тарифа'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'MinDaysPay',
    'value' => $HostingScheme['MinDaysPay'],
    'prompt'=> 'Минимальное число дней, на которое можно производить первую оплату заказа'
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
    'name'  => 'MinDaysProlong',
    'value' => $HostingScheme['MinDaysProlong'],
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
    'name'  => 'MaxDaysPay',
    'value' => $HostingScheme['MaxDaysPay']
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
   'name'  => 'MaxOrders',
   'value' => $HostingScheme['MaxOrders'],
   'prompt'=> $Messages['Prompts']['MaxOrders']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Максимальное кол-во заказов',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'text','name'=>'MinOrdersPeriod','value'=>$HostingScheme['MinOrdersPeriod'],'prompt'=>$Messages['Prompts']['MinOrdersPeriod']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Минимальный период между заказами',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'SortID',
    'value' => $HostingScheme['SortID']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порядок сортировки',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = '-Общие ограничения';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaDisk',
    'value' => $HostingScheme['QuotaDisk']
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
    'name'  => 'QuotaEmail',
    'value' => $HostingScheme['QuotaEmail']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Почтовые ящики'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaDomains',
    'value' => $HostingScheme['QuotaDomains']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Кол-во доменов'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'ISPmanager')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaFTP',
    'value' => $HostingScheme['QuotaFTP']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','FTP пользователи'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'ISPmanager')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaParkDomains',
    'value' => $HostingScheme['QuotaParkDomains']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Кол-во псевдонимов домена'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Brainy')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaWWWDomains',
    'value' => $HostingScheme['QuotaWWWDomains']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Кол-во WWW доменов'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'ISPmanager, Brainy')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------




$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaDBs',
    'value' => $HostingScheme['QuotaDBs']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Кол-во баз данных'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaTraffic',
    'value' => $HostingScheme['QuotaTraffic']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Месячный трафик (Мб.)'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Все системы')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'	=> 'text',
		'name'	=> 'mailrate',
		'value'	=> $HostingScheme['mailrate']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Ограничение отправки почты [в час]'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'ISPmanager, Brainy')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaUsers',
    'value' => $HostingScheme['QuotaUsers']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Кол-во пользователей'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Для реселлеров в ISPmanager')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsShellAccess','id'=>'IsShellAccess','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsShellAccess'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('LABEL',Array('for'=>'IsShellAccess'),'Secure Shell (SSH)'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'ISPmanager')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsSSLAccess','id'=>'IsSSLAccess','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsSSLAccess'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('LABEL',Array('for'=>'IsSSLAccess'),'Secure Sockets Layer (SSL)'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'ISPmanager')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsCGIAccess','id'=>'IsCGIAccess','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsCGIAccess'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('LABEL',Array('for'=>'IsCGIAccess'),'Common Gateway Interface (CGI)'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'ISPmanager')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = '-Ограничения для ISPmanager';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaEmailDomains',
    'value' => $HostingScheme['QuotaEmailDomains']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Кол-во почтовых доменов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaUsersDBs',
    'value' => $HostingScheme['QuotaUsersDBs']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Кол-во пользователей баз данных',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaCPU',
    'prompt'=> 'Ограничение на использование процессорного времени, в процентах',
    'value' => $HostingScheme['QuotaCPU']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Ограничение на CPU',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'MaxExecutionTime',
    'prompt'=> 'Максимальное время выполнения скриптов, в секундах',
    'value' => $HostingScheme['MaxExecutionTime']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Ограничение на время выполнения',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaMEM',
    'value' => $HostingScheme['QuotaMEM']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Ограничение на память',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'QuotaPROC',
    'value' => $HostingScheme['QuotaPROC']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Кол-во процессов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'QuotaMPMworkers',
		'value' => $HostingScheme['QuotaMPMworkers']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Кол-во воркеров apache MPM-ITK',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'mysqlquerieslimit',
		'value' => $HostingScheme['mysqlquerieslimit']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Запросов к MySQL',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'mysqlupdateslimit',
		'value' => $HostingScheme['mysqlupdateslimit']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Обновлений MySQL',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'mysqlconnectlimit',
		'value' => $HostingScheme['mysqlconnectlimit']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Соединений к MySQL',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'mysqluserconnectlimit',
		'value' => $HostingScheme['mysqluserconnectlimit']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Одновременных соединений к MySQL',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsSSIAccess','id'=>'IsSSIAccess','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsSSIAccess'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsSSIAccess'),'Server Side Includes (SSI)'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsPHPModAccess','id'=>'IsPHPModAccess','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsPHPModAccess'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsPHPModAccess'),'PHP как модуль'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsPHPCGIAccess','id'=>'IsPHPCGIAccess','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsPHPCGIAccess'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsPHPCGIAccess'),'PHP как CGI'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsPHPFastCGIAccess','id'=>'IsPHPFastCGIAccess','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsPHPFastCGIAccess'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsPHPFastCGIAccess'),'PHP как FastCGI'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsPHPSafeMode','id'=>'IsPHPSafeMode','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingScheme['IsPHPSafeMode'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'IsPHPSafeMode'),'Безопасный режим PHP'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = '-Дополнительные поля (для собственных нужд)';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'field1',
		'value' => $HostingScheme['field1']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Поле 1',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'field2',
		'value' => $HostingScheme['field2']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Поле 2',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
	'Form/Input',
	Array(
		'type'  => 'text',
		'name'  => 'field3',
		'value' => $HostingScheme['field3']
	)
);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Table[] = Array('Поле 3',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('align'=>'right'));
#-------------------------------------------------------------------------------
if($HostingSchemeID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'type'    => 'checkbox',
      'onclick' => 'form.HostingSchemeID.value = (checked?0:value);',
      'id'      => 'IsCreateNewScheme',
      'value'   => $HostingSchemeID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Div->AddChild($Comp);
  #-----------------------------------------------------------------------------
  $Div->AddChild(new Tag('LABEL',Array('class'=>'Comment','for'=>'IsCreateNewScheme'),'создать новый тариф'));
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/HostingSchemeEdit','HostingSchemeEditForm','%s');",$Title),
    'value'   => ($HostingSchemeID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'HostingSchemeEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'HostingSchemeID',
    'type'  => 'hidden',
    'value' => $HostingSchemeID
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
