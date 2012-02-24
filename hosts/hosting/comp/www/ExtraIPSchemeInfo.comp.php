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
$ExtraIPSchemeID = (string) @$Args['ExtraIPSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPScheme = DB_Select('ExtraIPSchemes','*',Array('UNIQ','ID'=>$ExtraIPSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $DOM = new DOM();
    #---------------------------------------------------------------------------
    $Links = &Links();
    # Коллекция ссылок
    $Links['DOM'] = &$DOM;
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Load('Window')))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddText('Title','Тариф виртуального сервера');
    #---------------------------------------------------------------------------
    $Table = Array('Общая информация');
    #---------------------------------------------------------------------------
    $Table[] = Array('Название тарифа',$ExtraIPScheme['Name']);

    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Currency',$ExtraIPScheme['CostMonth']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Цена за месяц',$Comp);



    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Currency',$ExtraIPScheme['CostDay']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Цена 1 дн.',$Comp);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$ExtraIPScheme['IsActive']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Тариф активен',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$ExtraIPScheme['IsProlong']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Возможность продления',$Comp);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Table[] = 'Где используется';
    #-------------------------------------------------------------------------------
    #-------------------------------------------------------------------------------
    # Hosting
    if($ExtraIPScheme['HostingGroupID'] > 0){
	$hGroups = DB_Select('HostingServersGroups','*',Array('UNIQ','ID'=>$ExtraIPScheme['HostingGroupID']));
	switch(ValueOf($hGroups)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		$Table[] = Array('Группа серверов хостинга',$hGroups['Name']);
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
    }else{
	# no hosting servers groups
	$Table[] = Array('Группа серверов хостинга','Не используется');
    }
    #-------------------------------------------------------------------------------
    #-------------------------------------------------------------------------------
    # VPS
    if($ExtraIPScheme['VPSGroupID'] > 0){
	$vGroups = DB_Select('VPSServersGroups','*',Array('UNIQ','ID'=>$ExtraIPScheme['VPSGroupID']));
	switch(ValueOf($vGroups)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		$Table[] = Array('Группа серверов VPS',$vGroups['Name']);
		break;
	default:
		return ERROR | @Trigger_Error(101);
    }
    }else{
	# no VPS servers groups
	$Table[] = Array('Группа серверов VPS','Не используется');
    }
    #-------------------------------------------------------------------------------
    #-------------------------------------------------------------------------------
    # DS
    if($ExtraIPScheme['DSGroupID'] > 0){
	$dGroups = DB_Select('DSServersGroups','*',Array('UNIQ','ID'=>$ExtraIPScheme['DSGroupID']));
	switch(ValueOf($dGroups)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		$Table[] = Array('Группа выделенных серверов',$dGroups['Name']);
		break;
	default:
		return ERROR | @Trigger_Error(101);
    }
    }else{
	# no dedicated servers groups
	$Table[] = Array('Группа выделенных серверов','Не используется');
    }
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Comp);
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Build(FALSE)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','DOM'=>$DOM->Object);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
