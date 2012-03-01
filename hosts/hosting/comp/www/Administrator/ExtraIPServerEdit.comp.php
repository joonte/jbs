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
$ExtraIPServerID = (integer) @$Args['ExtraIPServerID'];
#-------------------------------------------------------------------------------
if($ExtraIPServerID){
  #-----------------------------------------------------------------------------
  $ExtraIPServer = DB_Select('ExtraIPs','*',Array('UNIQ','ID'=>$ExtraIPServerID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPServer)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      $ExtraIPServer['Password'] = 'Default';
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $ExtraIPServer = Array(
    #---------------------------------------------------------------------------
    'SystemID'       => 'Default',
    'sGroupID' => 1,
    'IsDefault'      => TRUE,
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
    'Services'       => "HTTP=80\nFTP=21\nMySQL=3306\nSMTP=25\nPOP=110\nIMAP=143",
    'Notice'         => "Intel Xeon 1.8\nАдминистратор: Иванов Иван\n",
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ExtraIPServerEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($ExtraIPServerID?'Редактирование сервера':'Добавление сервера');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Systems = $Config['ExtraIP']['Systems'];
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
if(!$ExtraIPServerID)
  $DOM->AddAttribs('Body',Array('onload'=>'SettingsUpdate();'));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SystemID','onchange'=>'SettingsUpdate();'),$Options,$ExtraIPServer['SystemID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Система управления',$Comp);
#-------------------------------------------------------------------------------
$sGroups = DB_Select('ExtraIPsGroups',Array('ID','Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($sGroups)){
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
foreach($sGroups as $sGroup)
  $Options[$sGroup['ID']] = $sGroup['Name'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'sGroupID','prompt'=>'Группа серверов, в которую входит сервер'),$Options,$ExtraIPServer['sGroupID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа серверов',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'IsDefault','type'=>'checkbox','value'=>'yes'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ExtraIPServer['IsDefault'])
  $Comp->AddAttribs(Array('checked'=>'yes'));
#-------------------------------------------------------------------------------
$Table[] = Array('Основной в группе',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Пользовательские аккаунты';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'   => 'text',
    'name'   => 'Domain',
    'prompt' => 'Используется при создании служебных доменов для аккаунтов клиентов',
    'value'  => $ExtraIPServer['Domain']
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
    'value'  => $ExtraIPServer['Prefix']
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
    'value'  => $ExtraIPServer['Address']
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
    'value' => $ExtraIPServer['Port']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порт',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'Protocol'),Array('ssl'=>'ssl','tcp'=>'tcp'),$ExtraIPServer['Protocol']);
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
    'value'  => $ExtraIPServer['Login']
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
    'type'  => ($ExtraIPServerID?'password':'text'),
    'size'  => 15,
    'name'  => 'Password',
    'value' => $ExtraIPServer['Password']
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
    'value' => $ExtraIPServer['IP']
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
  $ExtraIPServer['IPsPool']
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
    'value' => $ExtraIPServer['Theme']
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
    'value' => $ExtraIPServer['Language']
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
    'value' => $ExtraIPServer['Url']
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
    'value' => $ExtraIPServer['Ns1Name']
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
    'value' => $ExtraIPServer['Ns2Name']
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
    'value' => $ExtraIPServer['Ns3Name']
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
    'value' => $ExtraIPServer['Ns4Name']
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
  $ExtraIPServer['Services']
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
  $ExtraIPServer['Notice']
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
    'onclick' => 'ExtraIPServerEdit();',
    'value'   => ($ExtraIPServerID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'ExtraIPServerEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($ExtraIPServerID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'ExtraIPServerID',
      'type'  => 'hidden',
      'value' => $ExtraIPServerID
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
