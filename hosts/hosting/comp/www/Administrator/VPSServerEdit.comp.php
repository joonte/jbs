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
$VPSServerID = (integer) @$Args['VPSServerID'];
#-------------------------------------------------------------------------------
if($VPSServerID){
  #-----------------------------------------------------------------------------
  $VPSServer = DB_Select('VPSServers','*',Array('UNIQ','ID'=>$VPSServerID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($VPSServer)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      $VPSServer['Password'] = 'Default';
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $VPSServer = Array(
    #---------------------------------------------------------------------------
    'SystemID'       => 'Default',
    'ServersGroupID' => 1,
    'IsDefault'      => TRUE,
    'IsAutoBalancing'=> TRUE,
    'BalancingFactor'=> 1,
    'Address'        => 'isp.su',
    'Domain'         => 'isp.su',
    'Prefix'         => 'v',
    'Port'           => 80,
    'Protocol'       => 'tcp',
    'Login'          => 'root',
    'Password'       => 'Default',
    'IP'             => '127.0.0.1',
    'IPsPool'        => "127.0.0.1\n127.0.0.2",
    'Theme'          => '',
    'Language'       => 'Default',
    'Url'            => 'http://isp.su/manage',
    'Ns1Name'        => 'ns1.isp.su',
    'Ns2Name'        => 'ns2.isp.su',
    'Ns3Name'        => '',
    'Ns4Name'        => '',
    'Services'       => "HTTP=80",
    'Notice'         => "AMD Opteron 6100, Magny-Cours\nАдминистратор: Иванов Иван\n",
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
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/VPSServerEdit.js}')));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/FormEdit.js}')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Title = ($VPSServerID?'Редактирование сервера':'Добавление сервера');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Systems = $Config['VPS']['Systems'];
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
if(!$VPSServerID)
  $DOM->AddAttribs('Body',Array('onload'=>'SettingsUpdate();'));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SystemID','onchange'=>'SettingsUpdate();'),$Options,$VPSServer['SystemID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Система управления',$Comp);
#-------------------------------------------------------------------------------
$ServersGroups = DB_Select('VPSServersGroups',Array('ID','Name'));
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
$Comp = Comp_Load('Form/Select',Array('name'=>'ServersGroupID','prompt'=>'Группа серверов, в которую входит сервер'),$Options,$VPSServer['ServersGroupID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа серверов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'IsDefault','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSServer['IsDefault'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsDefault\'); return false;'),'Основной в группе'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'IsAutoBalancing','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($VPSServer['IsAutoBalancing'])
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
    'value'  => $VPSServer['BalancingFactor']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Приоритет балансировки',$Comp);
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
    'value'  => $VPSServer['Domain']
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
    'value'  => $VPSServer['Prefix']
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
    'value'  => $VPSServer['Address']
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
    'value' => $VPSServer['Port']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порт',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'Protocol'),Array('ssl'=>'ssl','tcp'=>'tcp'),$VPSServer['Protocol']);
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
    'value'  => $VPSServer['Login']
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
    'type'  => ($VPSServerID?'password':'text'),
    'size'  => 15,
    'name'  => 'Password',
    'value' => $VPSServer['Password']
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
    'value' => $VPSServer['IP']
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
  $VPSServer['IPsPool']
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
    'value' => $VPSServer['Theme']
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
    'value' => $VPSServer['Language']
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
    'value' => $VPSServer['Url']
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
    'value' => $VPSServer['Ns1Name']
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
    'value' => $VPSServer['Ns2Name']
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
    'value' => $VPSServer['Ns3Name']
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
    'value' => $VPSServer['Ns4Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Расширенный',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name' => 'Services',
    'cols' => 15,
    'rows' => 5
  ),
  $VPSServer['Services']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = 'Служба мониторинга';
#-------------------------------------------------------------------------------
$Table[] = Array('Сервисы',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name'  => 'Notice',
    'style' => 'width:100%;',
    'rows'  => 5
  ),
  $VPSServer['Notice']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = 'Заметка';
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/VPSServerEdit','VPSServerEditForm','%s');",$Title),
    'value'   => ($VPSServerID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'VPSServerEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($VPSServerID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'VPSServerID',
      'type'  => 'hidden',
      'value' => $VPSServerID
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
