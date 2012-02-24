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
$ProfileID    = (integer) @$Args['ProfileID'];
$TemplateID   =  (string) @$Args['TemplateID'];
$TemplatesIDs =  (string) @$Args['TemplatesIDs'];
$Simple       =  (string) @$Args['Simple'];
$Window       =  (string) @$Args['Window'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($ProfileID){
  #-----------------------------------------------------------------------------
  $Profile = DB_Select('Profiles',Array('ID','UserID','TemplateID','Attribs','LENGTH(`Document`) as `Length`','Format'),Array('UNIQ','ID'=>$ProfileID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Profile)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      #-------------------------------------------------------------------------
      $TemplateID = $Profile['TemplateID'];
      #-------------------------------------------------------------------------
      $IsPermission = Permission_Check('ProfileEdit',(integer)$__USER['ID'],(integer)$Profile['UserID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsPermission)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'false':
          return ERROR | @Trigger_Error(700);
        case 'true':
          # No more...
        break 2;
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
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
$Config = Config();
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ProfileEditForm','onsubmit'=>'return false;'));
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
if(!$TemplateID){
  #-----------------------------------------------------------------------------
  $DOM->AddText('Title','Новый профиль');
  #-----------------------------------------------------------------------------
  $Config = Config();
  #-----------------------------------------------------------------------------
  $Templates = $Config['Profiles']['Templates'];
  #-----------------------------------------------------------------------------
  $Options = Array();
  #-----------------------------------------------------------------------------
  $TemplatesIDs = ($TemplatesIDs?Explode(',',$TemplatesIDs):Array());
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($Templates) as $TemplateID){
    #---------------------------------------------------------------------------
    if(Count($TemplatesIDs) && !In_Array($TemplateID,$TemplatesIDs))
      continue;
    #---------------------------------------------------------------------------
    $Template = $Templates[$TemplateID];
    #---------------------------------------------------------------------------
    if($Template['IsActive'])
      $Options[$TemplateID] = $Templates[$TemplateID]['Name'];
  }
  #-----------------------------------------------------------------------------
  if(!Count($Options))
    return new gException('TEMPLATES_NOT_DEFINED','Шаблоны не определены');
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Form/Select',Array('name'=>'TemplateID'),$Options);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table = Array(Array('Шаблон',$Comp));
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'onclick' => "ShowWindow('/ProfileEdit',FormGet(form));",
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
  $DOM->AddText('Title',$Config['Profiles']['Templates'][$TemplateID]['Name']);
  #-----------------------------------------------------------------------------
  $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ProfileEdit.js}'));
  #-----------------------------------------------------------------------------
  $DOM->AddChild('Head',$Script);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'TemplateID',
      'type'  => 'hidden',
      'value' => $TemplateID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
  #-----------------------------------------------------------------------------
  if($ProfileID){
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'ProfileID',
        'type'  => 'hidden',
        'value' => $Profile['ID']
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
  }
  #-----------------------------------------------------------------------------
  $Template = System_XML(SPrintF('profiles/%s.xml',$TemplateID));
  if(Is_Error($Template))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if($Simple){
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'Simple',
        'type'  => 'hidden',
        'value' => $Simple
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    $Simple = @JSON_Decode(Base64_Decode($Simple),TRUE);
    if(!$Simple)
      return ERROR | @Trigger_Error(500);
  }else
    $Simple = Array();
  #-----------------------------------------------------------------------------
  $Attribs = $Template['Attribs'];
  #-----------------------------------------------------------------------------
  $Replace = Array_ToLine($__USER,'%');
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($Attribs) as $AttribID){
    #---------------------------------------------------------------------------
    $Attrib = $Attribs[$AttribID];
    #---------------------------------------------------------------------------
    if(Count($Simple)){
      #-------------------------------------------------------------------------
      if(IsSet($Simple[$AttribID]))
        $Attrib['IsDuty'] = $Simple[$AttribID];
      else
        continue;
    }
    #---------------------------------------------------------------------------
    if(IsSet($Attrib['Title']))
      $Table[] = $Attrib['Title'];
    #---------------------------------------------------------------------------
    if($ProfileID)
      $Value = (string)@$Profile['Attribs'][$AttribID];
    else{
      #-------------------------------------------------------------------------
      $Value = $Attrib['Value'];
      #-------------------------------------------------------------------------
      foreach(Array_Keys($Replace) as $Key)
        $Value = Str_Replace($Key,$Replace[$Key],$Value);
    }
    #---------------------------------------------------------------------------
    $Params = &$Attrib['Attribs'];
    #---------------------------------------------------------------------------
    $Params['name'] = $AttribID;
    #---------------------------------------------------------------------------
    if($Attrib['IsDuty'])
      $Params['class'] = 'Duty';
    #---------------------------------------------------------------------------
    switch($Attrib['Type']){
      case 'Input':
        #-----------------------------------------------------------------------
        $Params['value'] = $Value;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Input',$Params);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(101);
      break;
      case 'TextArea':
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/TextArea',$Params,$Value);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(101);
      break;
      case 'Select':
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',$Params,$Attrib['Options'],$Value);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(101);
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $NoBody = new Tag('NOBODY',new Tag('SPAN',$Attrib['Comment']));
    #---------------------------------------------------------------------------
    $NoBody->AddChild(new Tag('BR'));
    #---------------------------------------------------------------------------
    if(IsSet($Attrib['Example']))
      $NoBody->AddChild(new Tag('SPAN',Array('class'=>'Comment'),SPrintF('Например: %s',$Attrib['Example'])));
    #---------------------------------------------------------------------------
    $Table[] = Array($NoBody,$Comp);
  }
  #-----------------------------------------------------------------------------
  if(!$Simple){
    #---------------------------------------------------------------------------
    $Table[] = 'Подтверждение введенных данных';
    #---------------------------------------------------------------------------
    $Document = $ProfileID?$Profile['Length']:0;
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Upload','Document',$Document?SPrintF('%01.2f Кб.',$Document/1024):'не загружены');
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Копия документа подтверждающего достоверность данных',$Comp);
  }
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'type'    => 'button',
      'onclick' => 'ProfileEdit();',
      'value'   => ($ProfileID?'Сохранить':'Зарегистрировать')
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = $Comp;
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Tables/Standard',$Table,Array('style'=>'width:500px;'));
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
