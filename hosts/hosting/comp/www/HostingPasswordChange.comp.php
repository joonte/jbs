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
$HostingOrderID = (integer) @$Args['HostingOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',Array('ID','UserID','StatusID'),Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($HostingOrder['StatusID'] != 'Active')
      return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ хостинга не активен');
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('HostingManage',(integer)$__USER['ID'],(integer)$HostingOrder['UserID']);
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
        $DOM->AddText('Title','Смена пароля для заказа хостинга');
        #-----------------------------------------------------------------------
        $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/PasswordCheck.js}'));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',$Script);
        #-----------------------------------------------------------------------
        $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/HostingPasswordChange.js}'));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',$Script);
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
        $NoBody = new Tag('NOBODY',new Tag('DIV',Array('style'=>'margin-bottom:5px;'),$Comp,new Tag('SPAN',Array('style'=>'font-size:10px;'),'Вставить из примера')));
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
            'onclick' => "if(PasswordCheck(this.form,'Password')){HostingPasswordChange();}",
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
        $Form = new Tag('FORM',Array('name'=>'HostingPasswordChangeForm','onsubmit'=>'return false;'),$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'  => 'hidden',
            'name'  => 'HostingOrderID',
            'value' => $HostingOrder['ID']
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
