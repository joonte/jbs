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
$ISPswOrderID = (integer) @$Args['ISPswOrderID'];
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
$Form = new Tag('FORM',Array('name'=>'ISPswCompensationForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
if($ISPswOrderID){
  #-----------------------------------------------------------------------------
  $ISPswOrder = DB_Select('ISPswOrdersOwners',Array('ID','IP','StatusID'),Array('UNIQ','ID'=>$ISPswOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ISPswOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('ISPsw_ORDER_NOT_FOUND','Заказ на ПО не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if($ISPswOrder['StatusID'] != 'Active')
        return new gException('ISPsw_ORDER_NOT_ACTIVE','Заказ ПО не активен');
      #-------------------------------------------------------------------------
      $Table[] = Array('IP лицензии',SPrintF('%s',$ISPswOrder['IP']));
      #-------------------------------------------------------------------------
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
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $ISPswGroups = DB_Select('ISPswSchemes',Array('ID','Name'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ISPswGroups)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('ISPsw_SCHEMES_NOT_FOUND','Тарифы на ПО не обнаружены');
    case 'array':
      #-------------------------------------------------------------------------
      $Options = Array();
      #-------------------------------------------------------------------------
      foreach($ISPswGroups as $ISPswGroup)
        $Options[$ISPswGroup['ID']] = $ISPswGroup['Name'];
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Form/Select',Array('name'=>'ISPswSchemeID'),$Options);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Тариф',$Comp);
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
    'onclick' => "ShowConfirm('Подверждаете выполнение операции?','FormEdit(\'/Administrator/API/ISPswCompensation\',\'ISPswCompensationForm\',\'Компенсация времени\');')",
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
