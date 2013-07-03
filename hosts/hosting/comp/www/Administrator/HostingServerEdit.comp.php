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
$HostingServerID = (integer) @$Args['HostingServerID'];
#-------------------------------------------------------------------------------
if($HostingServerID){
  #-----------------------------------------------------------------------------
  $HostingServer = DB_Select('HostingServers','*',Array('UNIQ','ID'=>$HostingServerID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingServer)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      $HostingServer['Password'] = 'Default';
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $HostingServer = Array(
    #---------------------------------------------------------------------------
    'SystemID'			=> 'Default',
    'ServersGroupID'		=> 1,
    'IsDefault'			=> TRUE,
    'IsAutoBalancing'		=> TRUE,
    'BalancingFactor'		=> 1,
    'NoRestartCreate'		=> FALSE,
    'NoRestartActive'		=> FALSE,
    'NoRestartSuspend'		=> FALSE,
    'NoRestartDelete'		=> FALSE,
    'NoRestartSchemeChange'	=> FALSE,
    'Address'			=> 'isp.su',
    'Domain'			=> 'test.su',
    'Prefix'			=> 'h',
    'Port'			=> 80,
    'Protocol'			=> 'tcp',
    'Login'			=> 'root',
    'Password'			=> 'Default',
    'IP'			=> '127.0.0.1',
    'IPsPool'			=> "127.0.0.1\n127.0.0.2",
    'Theme'			=> '',
    'Language'			=> 'Default',
    'Url'			=> 'http://isp.su/manage',
    'Ns1Name'			=> 'ns1.isp.su',
    'Ns2Name'			=> 'ns2.isp.su',
    'Ns3Name'			=> '',
    'Ns4Name'			=> '',
    'MySQL'			=> 'localhost',
    'Services'			=> "HTTP=80\nFTP=21\nMySQL=3306\nSMTP=25\nPOP=110\nIMAP=143",
    'Notice'			=> "Intel Xeon 1.8\nАдминистратор: Иванов Иван\n",
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
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/HostingServerEdit.js}')));
#-------------------------------------------------------------------------------
$Title = ($HostingServerID?SPrintF('Редактирование сервера %s',$HostingServer['Address']):'Добавление нового сервера');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Systems = $Config['Hosting']['Systems'];
#-------------------------------------------------------------------------------
$Script = Array('var Settings = {};');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Systems) as $SystemID){
  #-----------------------------------------------------------------------------
  $System = $Systems[$SystemID];
  #-----------------------------------------------------------------------------
  $Options[$SystemID] = $System['Name'];
  #-----------------------------------------------------------------------------
  $Script[] = SPrintF("Settings['%s'] = %s;",$SystemID,JSON_Encode($System['Settings']));
}
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Implode("\n",$Script)));
#-------------------------------------------------------------------------------
if(!$HostingServerID)
  $DOM->AddAttribs('Body',Array('onload'=>'SettingsUpdate();'));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SystemID','onchange'=>'SettingsUpdate();'),$Options,$HostingServer['SystemID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Система управления',$Comp);
#-------------------------------------------------------------------------------
$ServersGroups = DB_Select('HostingServersGroups',Array('ID','Name'));
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
$Comp = Comp_Load('Form/Select',Array('name'=>'ServersGroupID','prompt'=>'Группа серверов, в которую входит сервер'),$Options,$HostingServer['ServersGroupID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа серверов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'IsDefault','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingServer['IsDefault'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsDefault\'); return false;'),'Основной в группе'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'IsAutoBalancing','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($HostingServer['IsAutoBalancing'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsAutoBalancing\'); return false;'),'Автобалансировка'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'   => 'text',
    'name'   => 'BalancingFactor',
    'size'   => 10,
    'prompt' => 'Число, может быть дробным (разделитель - точка). Используется для определения приоритета сервера, при балансировке. Может задаваться по числу процессоров, или, как какой-то абстрактный множитель, по производительности сервера.',
    'value'  => $HostingServer['BalancingFactor']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Приоритет балансировки',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'NoRestartCreate','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
if($HostingServer['NoRestartCreate'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'NoRestartCreate\'); return false;'),'Не перезапускать apache, Create'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'NoRestartActive','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
if($HostingServer['NoRestartActive'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'NoRestartActive\'); return false;'),'Не перезапускать apache, Active'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'NoRestartSuspend','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
if($HostingServer['NoRestartSuspend'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'NoRestartSuspend\'); return false;'),'Не перезапускать apache, Suspend'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'NoRestartDelete','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
if($HostingServer['NoRestartDelete'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'NoRestartDelete\'); return false;'),'Не перезапускать apache, Delete'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'NoRestartSchemeChange','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
if($HostingServer['NoRestartSchemeChange'])
	$Comp->AddAttribs(Array('checked'=>'yes'));
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'NoRestartSchemeChange\'); return false;'),'Не перезапускать apache, SchemeChange'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Пользовательские аккаунты';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'   => 'text',
    'name'   => 'Domain',
    'prompt' => 'Используется при создании служебных доменов для аккаунтов клиентов',
    'value'  => $HostingServer['Domain']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Доменный адрес'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Для служебных доменов')),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'   => 'text',
    'name'   => 'Prefix',
    'size'   => 5,
    'prompt' => 'Используется при назначении имени пользовательского аккаунта. Имена аккаунтов для клиентов, с целью уникальности назначаются в виде: префикс00000, где 00000 - номер заказа, например: h10212.',
    'value'  => $HostingServer['Prefix']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Префикс имени аккаунта',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Параметры соединения';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'   => 'text',
    'name'   => 'Address',
    'prompt' => 'Используется для связи с сервером',
    'value'  => $HostingServer['Address']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Адрес сервера',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 6,
    'name'  => 'Port',
    'value' => $HostingServer['Port']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порт',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'Protocol'),Array('ssl'=>'ssl','tcp'=>'tcp'),$HostingServer['Protocol']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Протокол',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'   => 'text',
    'size'   => 15,
    'prompt' => 'Имя администратора или реселлера имеющего права на создание новых клиентов на сервере через систему управления',
    'name'   => 'Login',
    'value'  => $HostingServer['Login']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Логин',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => ($HostingServerID?'password':'text'),
    'size'  => 15,
    'name'  => 'Password',
    'value' => $HostingServer['Password']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Пароль',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 15,
    'name'  => 'IP',
    'value' => $HostingServer['IP']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('IP адрес',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name' => 'IPsPool',
    'cols' => 15,
    'rows' => 5
  ),
  $HostingServer['IPsPool']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Пул IP адресов'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Используется при создании заказа')),$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Параметры панели управления';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 5,
    'name'  => 'Theme',
    'value' => $HostingServer['Theme']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тема',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 15,
    'name'  => 'Language',
    'value' => $HostingServer['Language']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Язык',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Url',
    'value' => $HostingServer['Url']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Адрес входа для клиентов',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Сервера имен';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Ns1Name',
    'value' => $HostingServer['Ns1Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Первичный',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Ns2Name',
    'value' => $HostingServer['Ns2Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Вторичный',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Ns3Name',
    'value' => $HostingServer['Ns3Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дополнительный',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Ns4Name',
    'value' => $HostingServer['Ns4Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Расширенный',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры подключения к БД MySQL';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'MySQL',
    'value' => $HostingServer['MySQL'],
    'prompt'=> 'Адрес используемого сервера MySQL. Если БД MySQL расположена на том же сервере - то используйте значение localhost'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Хост MySQL',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Служба мониторинга';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name' => 'Services',
    'cols' => 15,
    'rows' => 5
  ),
  $HostingServer['Services']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервисы',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Заметка';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name'  => 'Notice',
    'style' => 'width:100%;',
    'rows'  => 5
  ),
  $HostingServer['Notice']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/HostingServerEdit','HostingServerEditForm','%s');",$Title),
    'value'   => ($HostingServerID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'HostingServerEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($HostingServerID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'HostingServerID',
      'type'  => 'hidden',
      'value' => $HostingServerID
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
