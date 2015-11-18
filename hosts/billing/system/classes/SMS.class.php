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
        $templatePath = SPrintF('Notifies/SMS/%s.tpl', $msg->getTemplate());

        $smarty= JSmarty::get();

        if (!$smarty->templateExists($templatePath)) {
            throw new jException('Template file not found: '.$templatePath);
        }

        $smarty->assign('Config', Config());

        foreach(array_keys($msg->getParams()) as $paramName) {
            $smarty->assign($paramName, $msg->getParam($paramName));
        }

        try {
            $message = $smarty->fetch($templatePath);
        }
        catch(Exception $e){
            throw new jException(SPrintF("Can't fetch template: %s", $templatePath), $e->getCode(), $e);
        }

        $recipient = $msg->getParam('User');

        if(!$recipient['Params']['NotificationMethods']['SMS']['Address'])
            throw new jException('Mobile phone number not found for user: '.$recipient['ID']);

        $taskParams = Array(
            'UserID' => $recipient['ID'],
            'TypeID' => 'SMS',
            'Params' => Array(
                $recipient['Params']['NotificationMethods']['SMS']['Address'],
                $message,
                $recipient['ID'],
		($msg->getParam('ChargeFree'))?TRUE:FALSE
            )
        );
        
	#Debug(SPrintF('[system/classes/SMS.class.php]: msg = %s,',print_r($msg,true)));
        $result = Comp_Load('www/Administrator/API/TaskEdit',$taskParams);
        switch(ValueOf($result)) {
            case 'error':
              throw new jException("Couldn't add task to queue: ".$result);
            case 'exception':
              throw new jException("Couldn't add task to queue: ".$result->String);
            case 'array':
              return TRUE;
            default:
              throw new jException("Unexpected error.");
        }
    }
}
?>
