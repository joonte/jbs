<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('LinkID');
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Links = &Links();
# Коллекция ссылок
$Template = &$Links[$LinkID];
/******************************************************************************/
/******************************************************************************/
$Tr = new Tag('TR');
#-------------------------------------------------------------------------------
$ISPswSchemes = DB_Select('ISPswSchemes',Array('ID','Name','CostMonth',Array('SortOn'=>'Name'));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array();
    #---------------------------------------------------------------------------
    $Options['Default'] = 'Не указан';
    #---------------------------------------------------------------------------
    foreach($ISPswSchemes as $ISPswScheme){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Currency',$ISPswScheme['CostMonth']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Options[$ISPswScheme['ID']] = SPrintF('%s, %s',$ISPswScheme['Name'],$Comp);
    }
    #---------------------------------------------------------------------------
    $SchemeID = 'Default';
    #---------------------------------------------------------------------------
    $Session = &$Template['Session'];
    #---------------------------------------------------------------------------
    if(IsSet($Session['SchemeID']))
      $SchemeID = $Session['SchemeID'];
    #---------------------------------------------------------------------------
    $Args = Args();
    #---------------------------------------------------------------------------
    if(IsSet($Args['SchemeID']))
      $SchemeID = $Args['SchemeID'];
    #---------------------------------------------------------------------------
    $Session['SchemeID'] = $SchemeID;
    #---------------------------------------------------------------------------
    $AddingWhere = &$Template['Source']['Adding']['Where'];
    #---------------------------------------------------------------------------
    if($SchemeID != 'Default')
      $AddingWhere[] = SPrintF('`SchemeID` = %u',$SchemeID);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID','onchange'=>'TableSuperReload();'),$Options,$SchemeID);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('NOBODY',new Tag('TD',Array('class'=>'Comment'),'Тарифный план'),new Tag('TD',$Comp)));
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!Count($Tr->Childs))
  return FALSE;
#-------------------------------------------------------------------------------
return new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5),$Tr);
#-------------------------------------------------------------------------------

?>
