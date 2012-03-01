<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function Email_Send(Msg $msg){
  /****************************************************************************/
  $Path = SPrintF('Notifies/Email/%s',$msg->getTemplate());
  #---------------------------------------------------------------------
  if(Is_Error(System_Element(SPrintF('comp/%s.comp.php',$Path))))
    return new gException('TEMPLATE_EMAIL_LETTER_NOT_FOUND','Шаблон сообщения не найден');
  #---------------------------------------------------------------------
/*          $Comp = Comp_Load($Path,$Replace);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error('[Email_Send]: ошибка загрузки шаблона письма');*/
  #---------------------------------------------------------------------
  $smarty= JSmarty::get();
  die(print_r($msg->getParams()));
  $smarty->assign('Params', $msg->getParams());

  $smarty->assign('Config', Config());
  #---------------------------------------------------------------------
  try {
    $templateFile = SPrintF('Notifies/Email/%s.tpl', $msg->getTemplate());

    $Comp['Message'] = $smarty->fetch($templateFile);

    if (!Isset($Comp['Theme'])) {
        $theme = $smarty->getTemplateVars('Theme');

        $theme ? $Comp['Theme'] = $theme : '$Theme' ;
    }
  }
  catch(Exception $e){
      return new gException('TEMPLATE_ERROR','Can\'t found: '.$templateFile.':'.$e);
  }
  #---------------------------------------------------------------------
  if(!Is_Array($Comp))
    return new gException('TEMPLATE_DISABLE','Шаблон не активен');
  #---------------------------------------------------------------------
  $Heads = Array(SPrintF('From: %s',$msg->getParams['User']['Email']),'MIME-Version: 1.0','Content-Type: text/plain; charset=UTF-8','Content-Transfer-Encoding: 8bit');
  #---------------------------------------------------------------------
  if(IsSet($Comp['Heads']))
    $Heads = Array_Merge($Heads,$Comp['Heads']);
  #---------------------------------------------------------------------
  $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$msg->getTo(),'TypeID'=>'Email','Params'=>Array($msg->getParams['From']['Email'],$Comp['Theme'],$Comp['Message'],Implode("\r\n",$Heads),$msg->getParams['User']['ID'])));
  #---------------------------------------------------------------------
  switch(ValueOf($IsAdd)){
    case 'error':
      return ERROR | @Trigger_Error('[Email_Send]: не удалось установить задание в очередь');
    case 'exception':
      return ERROR | @Trigger_Error('[Email_Send]: не удалось установить задание');
    case 'array':
      return TRUE;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
?>
