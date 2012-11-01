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
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
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
$DOM->AddText('Title','Компенсация времени');
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ExtraIPCompensationForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
if($ExtraIPOrderID){
  #-----------------------------------------------------------------------------
  $ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','Login','Domain','StatusID'),Array('UNIQ','ID'=>$ExtraIPOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('ExtraIP_ORDER_NOT_FOUND','Заказ на выделенный IP адрес не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if($ExtraIPOrder['StatusID'] != 'Active')
        return new gException('ExtraIP_ORDER_NOT_ACTIVE','Заказ выделенного IP адреса не активен');
      #-------------------------------------------------------------------------
      $Table[] = Array('Заказ выделенного IP адреса',SPrintF('%s',$ExtraIPOrder['Login']));
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'type'  => 'hidden',
          'name'  => 'ExtraIPOrderID',
          'value' => $ExtraIPOrder['ID']
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $ExtraIPs = DB_Select('ExtraIPs',Array('ID','Address'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPs)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('SERVERS_NOT_FOUND','Серверы ExtraIP не настроены');
    case 'array':
      #-------------------------------------------------------------------------
      $Options = Array();
      #-------------------------------------------------------------------------
      foreach($ExtraIPs as $ExtraIPServer)
        $Options[$ExtraIPServer['ID']] = $ExtraIPServer['Address'];
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Form/Select',Array('name'=>'ExtraIPServerID'),$Options);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Сервер ExtraIP',$Comp);
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'  => 'text',
    'name'  => 'DaysReserved',
    'size'  => 5,
    'value' => 10
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дней компенсации',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => "ShowConfirm('Подверждаете выполнение операции?','FormEdit(\'/Administrator/API/ExtraIPCompensation\',\'ExtraIPCompensationForm\',\'Компенсация времени\');')",
    'value'   => 'Компенсировать'
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
