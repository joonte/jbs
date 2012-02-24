<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
#-------------------------------------------------------------------------------
$DOM = &$Links['DOM'];
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/WhoIs.js}')));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Messages = Messages();
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'   => 'DomainName',
    'size'   => 12,
    'onkeydown' => 'if(IsEnter(event)) WhoIs();',
    'prompt' => $Messages['Prompts']['Domain']['Name'],
    'type'   => 'text'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Span = new Tag('SPAN',$Comp);
#-------------------------------------------------------------------------------
$DomainsZones = System_XML('config/DomainsZones.xml');
if(Is_Error($DomainsZones))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Options = Array();
#-------------------------------------------------------------------------------
foreach($DomainsZones as $DomainZone)
  $Options[$DomainZone['Name']] = SPrintF('.%s',$DomainZone['Name']);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'DomainZone'),$Options);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Span->AddChild($Comp);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменное имя',$Span);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'WhoIs();',
    'value'   => 'Проверить'
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
$Form = new Tag('FORM',Array('name'=>'WhoIsForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY',$Form,new Tag('DIV',Array('id'=>'WhoIsInfo')));
#-------------------------------------------------------------------------------
return $NoBody;
#-------------------------------------------------------------------------------

?>
