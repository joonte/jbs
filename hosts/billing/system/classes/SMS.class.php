<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class SMS implements Dispatcher {
    /** Instance of email dispatcher. */
    private static $instance;

    /** Private. This dispatcher have only one instance. */
    private function __construct() {

    }

    public static function get() {
        if (!isset(self::$instance)) {
            self::$instance = new SMS();
        }

        return self::$instance;
    }

    public function send(Msg $msg) {
        // Get template file path.
        $templateFile = SPrintF('Notifies/SMS/%s.tpl', $msg->getTemplate());

        $smarty= JSmarty::get();

        if (!$smarty->templateExists($templateFile)) {
            return new jException('Template file not found: '.$templateFile);
        }

        $smarty->assign('Params', $msg->getParams());
        $smarty->assign('Config', Config());

        try {
            $message = $smarty->fetch($templateFile);
        }
        catch(Exception $e){
            return new gException("Can't create template.", 'CREATE_TEMPLATE_ERROR', $e);
        }

        $User = $msg->getParam('User');

        if(!$User['Mobile'])
            return new gException('RECIPIENT_MOBILE_PHOME_NUM_NOT_FILLED','Получатель не заполнил мобильный номер');

        $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$User['ID'],'TypeID'=>'SMS','Params'=>Array($User['Mobile'],$message,$User['ID'])));
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
