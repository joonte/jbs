<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

if(!isset($Table)){
	$Table = 'Users';
}

$User = DB_Select($Table,Array('ID','AdminNotice'),Array('UNIQ','ID'=>$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Links = Links();
    #---------------------------------------------------------------------------
    $Links['DOM']->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/NoticeEdit.js}')));
    #---------------------------------------------------------------------------
    $AdminNotice = ($User['AdminNotice']?$User['AdminNotice']:'Добавить административную заметку');
    #---------------------------------------------------------------------------
    $FormID = UniqID('ID');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/TextArea',
      Array(
       'id'         => SPrintF('t%s',$FormID),
       'name'       => 'AdminNotice',
       'onmouseout' => SPrintF("this.style.display = 'none';with(document.getElementById('p%s')){ innerHTML = this.value?this.value:'Добавить административную заметку'; style.display = 'block'; } AdminNoticeEdit('%s');",$FormID,$FormID),
       'style'      => 'display:none;',
       'cols'       => 50,
       'rows'       => 10
      ),
      $AdminNotice
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $NoBody = new Tag('NOBODY',new Tag('PRE',Array('id'=> SPrintF('p%s',$FormID),'onclick'=>SPrintF("document.getElementById('t%s').style.display = 'block';this.style.display = 'none';",$FormID),'class'=>'Standard'),$AdminNotice),$Comp);
    #---------------------------------------------------------------------------
    $Form = new Tag('FORM',Array('name'=>$FormID,'onsubmit'=>'return false;'),$NoBody);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'TableID',
        'type'  => 'hidden',
        'value' => $Table
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'RowID',
        'type'  => 'hidden',
        'value' => $User['ID']
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    return Array('Title'=>'Заметка','DOM'=>$Form);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
