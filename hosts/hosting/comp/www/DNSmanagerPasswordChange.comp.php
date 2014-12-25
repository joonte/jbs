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
$DNSmanagerOrderID = (integer) @$Args['DNSmanagerOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',Array('ID','UserID','StatusID'),Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($DNSmanagerOrder['StatusID'] != 'Active')
      return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ вторичного DNS не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DNSmanagerManage',(integer)$__USER['ID'],(integer)$DNSmanagerOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Смена пароля для заказа вторичного DNS');
        #-----------------------------------------------------------------------
        $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/PasswordCheck.js}'));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',$Script);
        #-----------------------------------------------------------------------
        $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/DNSmanagerPasswordChange.js}'));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',$Script);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $Table = Array();
        #-----------------------------------------------------------------------
        $Password = SubStr(Md5(UniqID(Time())),0,8);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'    => 'IsPasswordCreate',
            'value'   => $Password,
            'type'    => 'checkbox',
            'onclick' => 'PasswordMode();'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $NoBody = new Tag('NOBODY',new Tag('DIV',Array('style'=>'margin-bottom:5px;'),$Comp,new Tag('SPAN',Array('style'=>'font-size:10px; cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsPasswordCreate\'); PasswordMode(); return false;'),'Вставить из примера')));
        #-----------------------------------------------------------------------
        $Messages = Messages();
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'   => 'Password',
            'size'   => 16,
            'prompt' => $Messages['Prompts']['User']['Password'],
            'type'   => 'password',
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $NoBody->AddChild($Comp);
        #-----------------------------------------------------------------------
        $Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Новый пароль'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),new Tag('SPAN',SPrintF('Например: %s',$Password)))),$NoBody);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'   => '_Password',
            'size'   => 16,
            'prompt' => $Messages['Prompts']['User']['Password'],
            'type'   => 'password'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Подтверждение пароля'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Аналогично полю [Новый пароль]')),$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'    => 'button',
            'onclick' => "if(PasswordCheck(this.form,'Password')){DNSmanagerPasswordChange();}",
            'value'   => 'Сменить'
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
        $Form = new Tag('FORM',Array('name'=>'DNSmanagerPasswordChangeForm','onsubmit'=>'return false;'),$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'  => 'hidden',
            'name'  => 'DNSmanagerOrderID',
            'value' => $DNSmanagerOrder['ID']
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
