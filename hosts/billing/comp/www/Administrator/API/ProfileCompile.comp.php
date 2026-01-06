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
if(Is_Null($Args)){
  #-----------------------------------------------------------------------------
  if(Is_Error(System_Load('modules/Authorisation.mod')))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ProfileID =  (integer) @$Args['ProfileID'];
$IsFull    =  (boolean) @$Args['IsFull'];
#-------------------------------------------------------------------------------
$Profile = DB_Select('Profiles',Array('ID','Name','TemplateID','Attribs','StatusID'),Array('UNIQ','ID'=>$ProfileID));
#-----------------------------------------------------------------------------
switch(ValueOf($Profile)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('PROFILE_NOT_FOUND','Профиль не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Attribs = $Profile['Attribs'];
    #---------------------------------------------------------------------------
    $Template = System_XML(SPrintF('profiles/%s.xml',$Profile['TemplateID']));
    if(Is_Error($Template))
      return ERROR | @Trigger_Error('[Profile_Compile]: не удалось загрузить шаблон профиля');
    #---------------------------------------------------------------------------
    $Result = Array();
    #---------------------------------------------------------------------------
    $Params = $Template['Attribs'];
    #---------------------------------------------------------------------------
    foreach(Array_Keys($Params) as $AttribID){
      #-------------------------------------------------------------------------
      $Attrib = $Params[$AttribID];
      #-------------------------------------------------------------------------
      switch($Attrib['Type']){
        case 'Input':
          # No more...
        case 'TextArea':
          $Value = $Attribs[$AttribID];
        break;
        case 'Select':
          $Value = $Attrib['Options'][$Attribs[$AttribID]];
        break;
        default:
          return ERROR | @Trigger_Error('[Profile_Compile]: тип атрибута не определён');
      }
      #-------------------------------------------------------------------------
      $Result[$AttribID] = ($IsFull?Array('Comment'=>$Attrib['Comment'],'Value'=>$Value):$Value);
    }
    #---------------------------------------------------------------------------
    return Array('ID'=>$ProfileID,'Name'=>$Profile['Name'],'TemplateID'=>$Profile['TemplateID'],'StatusID'=>$Profile['StatusID'],'Attribs'=>$Result);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
