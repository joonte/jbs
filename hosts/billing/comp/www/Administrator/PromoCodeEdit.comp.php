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
$PromoCodeID = (integer) @$Args['PromoCodeID'];
#-------------------------------------------------------------------------------
if($PromoCodeID){
	#-----------------------------------------------------------------------------
	$PromoCode = DB_Select('PromoCodesOwners','*',Array('UNIQ','ID'=>$PromoCodeID));
	#-----------------------------------------------------------------------------
	switch(ValueOf($PromoCode)){
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
	$PromoCode = Array(
		#--------------------------------------------------------------------------
		'Code'		=> FALSE,
		'ExpirationDate'=> Time() + 365 * 24 * 3600,
		'ServiceID'	=> 0,
		'SchemeID'	=> 0,
		'SchemesGroupID'=> 0,
		'Discont'	=> 0.5,
		'DaysDiscont'	=> 363,
		'MaxAmount'	=> 100,
		'OwnerID'	=> FALSE,
		'ForceOwnerID'	=> FALSE,
		'Comment'	=> 'Промокод размещён на форуме профильного сайта forum.joonte.ru'
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
$DOM->AddAttribs('Body',Array('onload'=>SPrintF("GetSchemes(%s,'SchemeID','%s');",$PromoCode['ServiceID'],$PromoCode['SchemeID'])));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/GetSchemes.js}')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Title = ($PromoCodeID?'Редактирование ПромоКода':'Добавление нового ПромоКода');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$PromoCodeExample = StrToUpper(SPrintF('%s-%s-%s-%s',SubStr(md5(MicroTime()),1,4),SubStr(md5(MicroTime()),6,4),SubStr(md5(MicroTime()),12,4),SubStr(md5(MicroTime()),20,4)));
#-------------------------------------------------------------------------------
if(!$PromoCodeID){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'  => 'text',
				'name'  => 'Code',
				'value' => $PromoCode['Code'],
				'prompt'=> 'Английские буквы и цифры, дефисы и подчёркивания'
				)
			);
	#-------------------------------------------------------------------------------
	$Comp1 = new Tag('NOBODY',new Tag('SPAN','ПромоКод'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Например: '),new Tag('SPAN',Array('class'=>'Comment','style'=>'cursor: pointer;','onclick'=>SPrintF('document.getElementsByName("Code")[0].value = "%s";',$PromoCodeExample)),$PromoCodeExample));
}else{
	$Comp = $PromoCode['Code'];
	$Comp1 = 'ПромоКод';
}
#-------------------------------------------------------------------------------
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array($Comp1,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		'`IsActive` = "yes"',
		'`IsHidden` != "yes"',
		);
#-------------------------------------------------------------------------------
$Services = DB_Select('ServicesOwners','*',Array('Where'=>$Where,'SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Services)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVICES_NOT_FOUND','Для назначения бонуса необходим хотя бы один активный сервис');
	break;
case 'array':
	#---------------------------------------------------------------------------
	$Options = Array('Любой активный сервис');
	#---------------------------------------------------------------------------
	foreach($Services as $Service)
		$Options[$Service['ID']] = SPrintF('%s (%s)',$Service['Code'],$Service['Name']);
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'ServiceID','onchange'=>SPrintF("GetSchemes(this.value,'SchemeID','%s');",$PromoCode['SchemeID'])),$Options,$PromoCode['ServiceID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервис',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Options = Array('Любой тариф');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID','id'=>'SchemeID','disabled'=>TRUE),$Options,$PromoCode['SchemeID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тариф',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$SchemesGroups = DB_Select('SchemesGroups','*');
#-------------------------------------------------------------------------------
switch(ValueOf($SchemesGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	$Options = Array('Нет групп тарифов');
	break;
case 'array':
	#---------------------------------------------------------------------------
	$Options = Array('Не использовать');
	#---------------------------------------------------------------------------
	foreach($SchemesGroups as $SchemesGroup)
		$Options[$SchemesGroup['ID']] = $SchemesGroup['Name'];
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemesGroupID'),$Options,$PromoCode['SchemesGroupID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа тарифов',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('jQuery/DatePicker','ExpirationDate',$PromoCode['ExpirationDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата окончания',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'DaysDiscont',
    'value' => $PromoCode['DaysDiscont'],
    'prompt'=> 'На какой срок выдаётся скидка - дни, годы, штуки - в зависимости от типа учёта'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('На какой срок выдаётся скидка',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'Discont',
    'value' => $PromoCode['Discont']*100,
    'prompt'=> 'Число от 5 до 100'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Размер скидки в %',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'MaxAmount',
    'value' => $PromoCode['MaxAmount'],
    'prompt'=> 'Сколько раз можно ввести этот промокод'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Количество использований',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Настройки реферальной программы';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'name'		=> 'UseOwnerID',
			'type'		=> 'checkbox',
			'onclick'	=> "form.SearchOwnerID.disabled = !checked; form.ForceOwnerID.disabled = !checked;"
		)
	);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox("UseOwnerID"); document.getElementsByName("SearchOwnerID")[0].disabled = document.getElementsByName("UseOwnerID")[0].checked?false:true; document.getElementsByName("ForceOwnerID")[0].disabled = document.getElementsByName("UseOwnerID")[0].checked?false:true; return false;'),'Делать рефералами'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'name'		=> 'ForceOwnerID',
			'type'		=> 'checkbox',
			'prompt'	=> 'Если пользователь уже чей-то реферал, менять реферала на указанного'
		)
	);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
if(IntVal($PromoCode['OwnerID']) < 2001)
	$Comp->AddAttribs(Array('disabled'=>TRUE));
#-------------------------------------------------------------------
$Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox("ForceOwnerID"); return false;'),'Менять реферала'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
			'Users/Select',
			'OwnerID',						# имя скрытой формы, для передачи значения
			$PromoCode['OwnerID']?$PromoCode['OwnerID']:'1',	# если не задан - то используем 1 - система
			'SearchOwnerID',					# имя формы, где выбираем юзера
			$PromoCode['OwnerID']?FALSE:TRUE,			# если не задан - дисаблим форму поиска
			'Все кто введёт промокод, автоматически станут рефералами указанного тут пользователя'
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сделать рефералом',$Comp);

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/TextArea',
  Array(
    'name'  => 'Comment',
    'style' => 'width:100%;',
    'rows'  => 5,
    'prompt'=> 'Цель/причина создания этой скидки клиенту'
  ),
  $PromoCode['Comment']
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = 'Комментарий';
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => SPrintF("FormEdit('/Administrator/API/PromoCodeEdit','PromoCodeEditForm','%s');",$Title),
    'value'   => ($PromoCodeID?'Сохранить':'Добавить')
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
$Form = new Tag('FORM',Array('name'=>'PromoCodeEditForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($PromoCodeID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'PromoCodeID',
      'type'  => 'hidden',
      'value' => $PromoCodeID
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
