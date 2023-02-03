<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$TypeID		=  (string) @$Args['TypeID'];
$ProfileID	= (integer) @$Args['ProfileID'];
$IsSimple	= (boolean) @$Args['IsSimple'];
$TypesIDs	=  (string) @$Args['TypesIDs'];
$Window		=  (string) @$Args['Window'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
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
$DOM->AddText('Title','Новый договор');
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Types = $Config['Contracts']['Types'];
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ContractMakeForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
if($Window){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'Window',
      'type'  => 'hidden',
      'value' => $Window
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
}
#-------------------------------------------------------------------------------
if(!$TypeID){
  #-----------------------------------------------------------------------------
  $Options = Array();
  #-----------------------------------------------------------------------------
  $TypesIDs = ($TypesIDs?Explode(',',$TypesIDs):Array());
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($Types) as $TypeID){
    #---------------------------------------------------------------------------
    $Type = $Types[$TypeID];
    #---------------------------------------------------------------------------
    if($Type['IsActive']){
      #-------------------------------------------------------------------------
      if(!Count($TypesIDs) || In_Array($TypeID,$TypesIDs))
        $Options[$TypeID] = $Type['Name'];
    }
  }
  #-----------------------------------------------------------------------------
  if(!Count($Options))
    return new gException('CONTRACTS_TEMPLATES_NOT_FOUND','Активные шаблоны договоров не найдены');
  #-----------------------------------------------------------------------------
  if(Count($Options) < 2){
    #---------------------------------------------------------------------------
    $Args['TypeID'] = Current(Array_Keys($Options));
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/ContractMake',$Args);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return $Comp;
  }
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Form/Select',Array('name'=>'TypeID'),$Options);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table = Array(Array('Шаблон',$Comp));
  #-----------------------------------------------------------------------------
  /* убрал по просьбе бухгалтерии, 2023-01-26 in 14:59, by lissyara
  $Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','checked'=>'true','name'=>'IsSimple','id'=>'IsSimple'));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = Array('Сформировать договор позже',new Tag('NOBODY',$Comp,new Tag('LABEL',Array('class'=>'Comment','for'=>'IsSimple'),'(заполнить минимальные данные)')));
  #-----------------------------------------------------------------------------
  */
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'onclick' => "ShowWindow('/ContractMake',FormGet(form));",
      'type'    => 'button',
      'value'   => 'Продолжить'
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = $Comp;
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Tables/Standard',$Table);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
}else{
  #-----------------------------------------------------------------------------
  if(!IsSet($Types[$TypeID]))
    return ERROR | @Trigger_Error(201);
  #-----------------------------------------------------------------------------
  $Type = $Types[$TypeID];
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # если это NaturalPartner - проверяем что его ещё нет
  if($Type['ProfileTemplateID'] == "NaturalPartner"){
  	Debug("[comp/www/ContractMake]: profile type selected = NaturalPartner");
	$NaturalPartnerContracts = DB_Select('ContractsOwners','ID',Array('UNIQ','Where'=>"`TypeID`='NaturalPartner' AND `UserID` = " . $GLOBALS['__USER']['ID']));
	switch(ValueOf($NaturalPartnerContracts)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# all OK
		break;
	case 'array':
		# alredy exists
		return new gException('NaturalPartner_CONTRACT_ALREDY_EXISTS','Партнёрский договор уже заключён');
	default:
	      return ERROR | @Trigger_Error(101);
	}
	
  }
  #-----------------------------------------------------------------------------
  $Where = SPrintF("`UserID` = %u AND `TemplateID` = '%s' AND NOT EXISTS(SELECT * FROM `Contracts` WHERE `Contracts`.`ProfileID` = `Profiles`.`ID`)",$GLOBALS['__USER']['ID'],$Type['ProfileTemplateID']);
  #-----------------------------------------------------------------------------
  $Profiles = DB_Select('Profiles',Array('ID','Name'),Array('Where'=>$Where));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Profiles)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      #-------------------------------------------------------------------------
      $Args = Array('TypeID'=>$TypeID);
      #-------------------------------------------------------------------------
      if($Window)
        $Args['Window'] = $Window;
      #-------------------------------------------------------------------------
      $Window = JSON_Encode(Array('Url'=>'/ContractMake','Args'=>$Args));
      #-------------------------------------------------------------------------
      $Params = Array('TemplateID'=>$TypeID,'Window'=>Base64_Encode($Window));
      #-------------------------------------------------------------------------
      if($IsSimple){
        #-----------------------------------------------------------------------
        $Simple = @JSON_Encode($Type['Simple']);
        if(!$Simple)
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Params['Simple'] = Base64_Encode($Simple);
      }
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/ProfileEdit',$Params);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      return $Comp;
    case 'array':
      #-------------------------------------------------------------------------
      if($ProfileID)
        $DOM->AddAttribs('Body',Array('onload'=>'ContractMake();'));
      #-------------------------------------------------------------------------
      $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ContractMake.js}'));
      #-------------------------------------------------------------------------
      $DOM->AddChild('Head',$Script);
      #-------------------------------------------------------------------------
      $Table = Array(Array('Шаблон',$Type['Name']));
      #-------------------------------------------------------------------------
      $Options = Array();
      #-------------------------------------------------------------------------
      foreach($Profiles as $Profile)
        $Options[$Profile['ID']] = $Profile['Name'];
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Form/Select',Array('name'=>'ProfileID'),$Options,$ProfileID);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Использовать профиль',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'type'    => 'button',
          'onclick' => 'ContractMake();',
          'value'   => 'Сформировать'
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = $Comp;
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Tables/Standard',$Table);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'name'  => 'TypeID',
          'type'  => 'hidden',
          'value' => $TypeID
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
}
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
