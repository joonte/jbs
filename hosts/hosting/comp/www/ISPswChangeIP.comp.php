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
$ISPswOrderID = (integer) @$Args['ISPswOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array(
                 'ID','IP','UserID','StatusID','StatusDate','LicenseID',
                 '(SELECT `ISPtype` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID`=`ISPswSchemes`.`ID`) AS `ISPtype`'
                );
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($ISPswOrder['StatusID'] != 'Active')
      return new gException('ISPsw_ORDER_NOT_ACTIVE','Заказ программного обеспечения не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ISPswManage',(integer)$__USER['ID'],(integer)$ISPswOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
	# проверяем, можно ли менять IP для этой лицензии
	# 1. внутренним, могут менять только сотрудники
	# 2. время - прошёл ли 31 день от последней смены адреса
	$ISPswLicense = DB_Select('ISPswLicenses',Array('ID','StatusDate','IsInternal'),Array('UNIQ','ID'=>$ISPswOrder['LicenseID']));
	switch(ValueOf($ISPswLicense)){
	case 'error':
	  return ERROR | @Trigger_Error(500);
	case 'exception':
	  return ERROR | @Trigger_Error(400);
	case 'array':
          # license found
	  break;
        default:
	  return ERROR | @Trigger_Error(101);
	}
	#-----------------------------------------------------------------------
        if(!$__USER['IsAdmin'] && $ISPswLicense['IsInternal'])
	  return new gException('INTERNAL_LICENSE','Данная лицензия предназначена для использования на заказах VPS и выделенных серверов. Вы не можете изменить её IP адрес. Если вам нужна лицензия для другого заказа VPS или выделенного сервера - сделайте заказ на новую лицензию.');
	#-----------------------------------------------------------------------
	$m_time = $ISPswOrder['StatusDate'] + 31 * 24 * 3600 - Time();
	if($m_time > 0){
	  $Comp = Comp_Load('Formats/Date/Remainder', $m_time);
          return new gException('LICENSE_PERIOD_NOT_EXCESSED','IP адрес лицензии можно менять один раз в месяц. До момента когда его можно будет сменить, осталось ' . $Comp);
	}
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Смена IP адреса для заказа ПО');
        #-----------------------------------------------------------------------
        $Table = Array();
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Table[] = Array('Текущий адрес',$ISPswOrder['IP']);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'   => 'IP',
            'size'   => 16,
            'prompt' => "Введите новый IP адрес лицензии",
            'type'   => 'text'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Новый адрес',$Comp);
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'    => 'button',
            'onclick' => "FormEdit('/API/ISPswChangeIP','ISPswChangeIPForm','Смена IP адреса лицензии ISPsystem');",
            'value'   => 'Сменить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
	#-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('name'=>'ISPswChangeIPForm','onsubmit'=>'return false;'),$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'  => 'hidden',
            'name'  => 'ISPswOrderID',
            'value' => $ISPswOrder['ID']
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'  => 'hidden',
            'name'  => 'LicenseID',
            'value' => $ISPswLicense['ID']
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
	#-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Form);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
