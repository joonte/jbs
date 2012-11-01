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
$VPSOrderID = (integer) @$Args['VPSOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',Array('ID','UserID','StatusID','Login'),Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($VPSOrder['StatusID'] != 'Active')
      return new gException('VPS_ORDER_NOT_ACTIVE','Заказ виртуального сервера не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('VPSManage',(integer)$__USER['ID'],(integer)$VPSOrder['UserID']);
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
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	$Title = SPrintF('Перезагрузка виртуального сервера %s',$VPSOrder['Login']);
	#-----------------------------------------------------------------------
        $DOM->AddText('Title',$Title);
        #-----------------------------------------------------------------------
        $Text = new Tag('SPAN',SPrintF('Вы действительно хотите перезагрузить виртуальный сервер %s?',$VPSOrder['Login']));
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'    => 'button',
				'onclick' => SPrintF("FormEdit('/API/VPSReboot','VPSRebootForm','%s');",$Title),
				'value'   => 'Перезагрузить'
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Button = new Tag('CENTER',$Comp);
	#-----------------------------------------------------------------------
	$Form = new Tag('FORM',Array('name'=>'VPSRebootForm','onsubmit'=>'return false;'),$Text,new Tag('BR'),new Tag('BR'),$Button);
	#-----------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'  => 'hidden',
				'name'  => 'VPSOrderID',
				'value' => $VPSOrder['ID']
			)
		);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	$Form->AddChild($Comp);
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
