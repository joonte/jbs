<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContractID = (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('CreateDate','UserID','IsUponConsider','ProfileID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
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
    $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ContractEdit.js}'));
    #---------------------------------------------------------------------------
    $DOM->AddChild('Head',$Script);
    #---------------------------------------------------------------------------
    $DOM->AddText('Title','Изменение договора');
    #---------------------------------------------------------------------------
    $Table = Array();
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Users/Select','UserID',$Contract['UserID']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Пользователь',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('jQuery/DatePicker','CreateDate',$Contract['CreateDate']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Дата заключения',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'IsUponConsider'),Array('По факту','Ежемесячный'),$Contract['IsUponConsider']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Способ отчетности',$Comp);
    #---------------------------------------------------------------------------
    $Profiles = DB_Select('Profiles',Array('ID','Name'),Array('Where'=>SPrintF('`UserID` = %u OR `ID` = %u',$Contract['UserID'],$Contract['ProfileID'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($Profiles)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('PROFILES_NOT_FOUND','Профили клиента не найдены');
      case 'array':
        #-----------------------------------------------------------------------
        $Options = Array();
        #-----------------------------------------------------------------------
        foreach($Profiles as $Profile){
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Formats/Profile/Number',$Profile['ID']);
           if(Is_Error($Comp))
             return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Name = SPrintF('%s - %s',$Comp,$Profile['Name']);
          #---------------------------------------------------------------------
          if(Mb_StrLen($Name) > 30)
            $Name = SPrintF('%s...',Mb_SubStr($Name,0,30));
          #---------------------------------------------------------------------
          $Options[$Profile['ID']] = $Name;
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'ProfileID'),$Options,$Contract['ProfileID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Профиль',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
           'name'  => 'IsEnclosures',
           'type'  => 'checkbox',
           'value' => 'yes'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Синхронизировать приложения',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'    => 'button',
            'onclick' => 'ContractEdit();',
            'value'   => 'Изменить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('name'=>'ContractEditForm','onsubmit'=>'return false;'),$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'ContractID',
            'type'  => 'hidden',
            'value' => $ContractID
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Form);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
