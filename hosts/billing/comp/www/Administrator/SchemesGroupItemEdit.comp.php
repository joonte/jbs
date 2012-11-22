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
$SchemesGroupID     = (integer) @$Args['SchemesGroupID'];
$SchemesGroupItemID = (integer) @$Args['SchemesGroupItemID'];
#-------------------------------------------------------------------------------
if($SchemesGroupItemID){
	#-----------------------------------------------------------------------------
	$SchemesGroupItem = DB_Select('SchemesGroupsItems','*',Array('UNIQ','ID'=>$SchemesGroupItemID));
	#-----------------------------------------------------------------------------
	switch(ValueOf($SchemesGroupItem)){
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
	$SchemesGroupItem = Array(
					'ServiceID'	=> 0,
					'SchemeID'	=> 0
				);
}
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Standard')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/GetSchemes.js}')));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/SchemesGroupItemEdit.js}')));
#-------------------------------------------------------------------------------
$DOM->AddAttribs('Body',Array('onload'=>SPrintF("GetSchemes(%s,'SchemeID','%s');",$SchemesGroupItem['ServiceID'],$SchemesGroupItem['SchemeID'])));
#-------------------------------------------------------------------------------
$Title = ($SchemesGroupItemID?'Редактирование тарифа группы':'Добавление нового тарифа в группу');
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$Title);
#-------------------------------------------------------------------------------
$DOM->Delete('Title');
#-------------------------------------------------------------------------------
$Table = Array();
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
	return new gException('SERVICES_NOT_FOUND','Для создания группы необходим хотя бы один активный сервис');
	break;
case 'array':
	#---------------------------------------------------------------------------
	$Options = Array('Любой активный сервис');
	#---------------------------------------------------------------------------
	foreach($Services as $Service)
		$Options[$Service['ID']] = SPrintF('%s (%s)',$Service['Code'],$Service['NameShort']);
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'ServiceID','onchange'=>SPrintF("GetSchemes(this.value,'SchemeID','%s');",$SchemesGroupItem['SchemeID'])),$Options,$SchemesGroupItem['ServiceID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервис',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Options = Array('Любой тариф');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID','id'=>'SchemeID','disabled'=>TRUE),$Options,$SchemesGroupItem['SchemeID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тариф',$Comp);














    #---------------------------------------------------------------------------
    $Options = Array();
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'type'    => 'button',
        'onclick' => 'SchemesGroupItemEdit()',
        'value'   => ($SchemesGroupItemID?'Сохранить':'Добавить')
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = $Comp;
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form = new Tag('FORM',Array('name'=>'SchemesGroupItemEditForm','onsubmit'=>'return false;'),$Comp);
    #---------------------------------------------------------------------------
    if($SchemesGroupID){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'name'  => 'SchemesGroupID',
          'type'  => 'hidden',
          'value' => $SchemesGroupID
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
    }
    #---------------------------------------------------------------------------
    if($SchemesGroupItemID){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'name'  => 'SchemesGroupItemID',
          'type'  => 'hidden',
          'value' => $SchemesGroupItemID
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
    }
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Form);
#  break;
#  default:
#    return ERROR | @Trigger_Error(101);
#}
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
