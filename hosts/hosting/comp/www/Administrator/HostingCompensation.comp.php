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
$HostingOrderID = (integer) @$Args['HostingOrderID'];
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
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/FormEdit.js}')));
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Компенсация времени');
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'HostingCompensationForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
if($HostingOrderID){
  #-----------------------------------------------------------------------------
  $HostingOrder = DB_Select('HostingOrdersOwners',Array('ID','Login','Domain','StatusID'),Array('UNIQ','ID'=>$HostingOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('HOSTING_ORDER_NOT_FOUND','Заказ на хостинг не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if($HostingOrder['StatusID'] != 'Active')
        return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ хостинга не активен');
      #-------------------------------------------------------------------------
      $Table[] = Array('Заказ хостинга',SPrintF('%s (%s)',$HostingOrder['Login'],$HostingOrder['Domain']));
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'type'  => 'hidden',
          'name'  => 'HostingOrderID',
          'value' => $HostingOrder['ID']
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
  $HostingServers = DB_Select('HostingServers',Array('ID','Address'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingServers)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('SERVERS_NOT_FOUND','Серверы на хостинг не настроены');
    case 'array':
      #-------------------------------------------------------------------------
      $Options = Array();
      #-------------------------------------------------------------------------
      foreach($HostingServers as $HostingServer)
        $Options[$HostingServer['ID']] = $HostingServer['Address'];
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Form/Select',Array('name'=>'HostingServerID'),$Options);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Сервер хостинга',$Comp);
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
    'onclick' => "ShowConfirm('Подверждаете выполнение операции?','FormEdit(\'/Administrator/API/HostingCompensation\',\'HostingCompensationForm\',\'Компенсация времени\');')",
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
