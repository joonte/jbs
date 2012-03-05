<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class Email implements Dispatcher {
    /** Instance of email dispatcher. */
    private static $instance;

    /** Private. This dispatcher have only one instance. */
    private function __construct() {

    }

    public static function get() {
        if (!isset(self::$instance)) {
            self::$instance = new Email();
        }

        return self::$instance;
    }

    public function send(Msg $msg) {
        // Get template file path.
        $templateFile = SPrintF('Notifies/Email/%s.tpl', $msg->getTemplate());

        $smarty= JSmarty::get();

        if (!$smarty->templateExists($templateFile)) {
            return new jException('Template file not found: '.$templateFile);
        }

        $smarty->assign('Params', $msg->getParams());
        $smarty->assign('Config', Config());

        try {
            $message = $smarty->fetch($templateFile);
            $theme = $smarty->getTemplateVars('Theme');

            if (!$theme) {
                $theme = '$Theme' ;
            }
        }
        catch(Exception $e){
            return new jException("Can't create template.", 'CREATE_TEMPLATE_ERROR', $e);
        }

        $User = $msg->getParam('User');

        if(!$User['Email'])
            return new gException('RECIPIENT_EMAIL_ADDRESS_NOT_FILLED','Получатель не заполнил электронный адрес');

        $From = $msg->getParam('From');

        $Heads = Array(SPrintF('From: %s',$User['Email']),'MIME-Version: 1.0','Content-Type: text/plain; charset=UTF-8','Content-Transfer-Encoding: 8bit');

        if(IsSet($Comp['Heads']))
            $Heads = Array_Merge($Heads,$Comp['Heads']);

        $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$User['ID'],'TypeID'=>'Email','Params'=>Array($From['Email'],$theme,$message,Implode("\r\n",$Heads),$User['ID'])));
        switch(ValueOf($IsAdd)) {
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
}
?>
