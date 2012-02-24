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
$RegistratorID = (integer) @$Args['RegistratorID'];
#-------------------------------------------------------------------------------
if($RegistratorID){
  #-----------------------------------------------------------------------------
  $Registrator = DB_Select('Registrators','*',Array('UNIQ','ID'=>$RegistratorID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Registrator)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      $Registrator['Password'] = 'Default';
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $Registrator = Array(
    #---------------------------------------------------------------------------
    'Name'      => 'RuCenter',
    'TypeID'    => 'Default',
    'Comment'   => 'Региональный сетевой центр',
    'SortID'    => 10,
    'Address'   => 'isp.su',
    'Port'      => 80,
    'Protocol'  => 'tcp',
    'PrefixAPI' => '',
    'Login'     => 'root',
    'Password'  => 'Default',
    'Ns1Name'   => 'ns1.company.com',
    'Ns2Name'   => 'ns2.company.com',
    'Ns3Name'   => '',
    'Ns4Name'   => '',
    'ParentID'  => 1000,
    'PrefixNic' => 'RU-CENTER',
    'PartnerLogin'    => 'root',
    'PartnerContract' => '123456',
    'JurName'   => 'ЗАО "Региональный Сетевой Информационный Центр"'
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/RegistratorEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Title = ($RegistratorID?'Редактирование регистратора':'Добавление регистратора');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Общая информация';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Name',
    'value' => $Registrator['Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Название',$Comp);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Types = $Config['Domains']['Registrators'];
#-------------------------------------------------------------------------------
$Script = Array('var Settings = {};');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Types) as $TypeID){
  #-----------------------------------------------------------------------------
  $Type = $Types[$TypeID];
  #-----------------------------------------------------------------------------
  $Options[$TypeID] = $Type['Name'];
  #-----------------------------------------------------------------------------
  $Script[] = SPrintF("Settings['%s'] = %s;",$TypeID,JSON_Encode($Type['Settings']));
}
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Implode("\n",$Script)));
#-------------------------------------------------------------------------------
if(!$RegistratorID)
  $DOM->AddAttribs('Body',Array('onload'=>'SettingsUpdate();'));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'TypeID','onchange'=>'SettingsUpdate();'),$Options,$Registrator['TypeID']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тип регистратора',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/TextArea',Array('rows'=>3,'cols'=>30,'name'=>'Comment'),$Registrator['Comment']);
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
    'value' => $Registrator['SortID']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порядок сортировки',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Параметры соединения';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Address',
    'value' => $Registrator['Address']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Адрес',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 6,
    'name'  => 'Port',
    'value' => $Registrator['Port']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Порт',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'Protocol'),Array('tcp'=>'tcp','ssl'=>'ssl'),$Registrator['Protocol']);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Протокол',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 20,
    'name'  => 'PrefixAPI',
    'value' => $Registrator['PrefixAPI']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Адрес API',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 10,
    'name'  => 'Login',
    'value' => $Registrator['Login']
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
    'type'  => ($RegistratorID?'password':'text'),
    'size'  => 15,
    'name'  => 'Password',
    'value' => $Registrator['Password']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Пароль',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Сервера имен';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Ns1Name',
    'value' => $Registrator['Ns1Name']
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
    'value' => $Registrator['Ns2Name']
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
    'value' => $Registrator['Ns3Name']
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
    'value' => $Registrator['Ns4Name']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Расширенный',$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Дополнительные параметры';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'size'  => 15,
    'name'  => 'ParentID',
    'value' => $Registrator['ParentID']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Родительский номер'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Только для LogicBoxes')),$Comp);
#-------------------------------------------------------------------------------
$Table[] = 'Параметры трансфера доменов';
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'PrefixNic',
    'value' => $Registrator['PrefixNic']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Префикс nic-hdl',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'PartnerLogin',
    'value' => ($Registrator['PartnerLogin']?$Registrator['PartnerLogin']:$Registrator['Login'])
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Партнерский аккаунт',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'PartnerContract',
    'value' => $Registrator['PartnerContract']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Партнерский договор',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'rows'=>2,
    'cols'=>30,
    'name'=>'JurName'
    ),
  $Registrator['JurName']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Оф. наименование',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'RegistratorEdit();',
    'value'   => ($RegistratorID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'RegistratorEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($RegistratorID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'RegistratorID',
      'type'  => 'hidden',
      'value' => $RegistratorID
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
