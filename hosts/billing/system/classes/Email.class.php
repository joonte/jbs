<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Joonte Software
 *
 */

/**
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
        $templatePath = SPrintF('Notifies/Email/%s.tpl', $msg->getTemplate());

        $smarty = JSmarty::get();
        $smarty->clearAllAssign();

        if (!$smarty->templateExists($templatePath)) {
            throw new jException('Template file not found: '.$templatePath);
        }

        $smarty->assign('Config', Config());


        foreach(array_keys($msg->getParams()) as $paramName) {
            $smarty->assign($paramName, $msg->getParam($paramName));
        }

        $message = $smarty->fetch($templatePath);

        try {
            // Debug("msg->getParam('Theme'): "+ $msg->getParam('Theme'));
            if ($msg->getParam('Theme')) {
                // Debug("SET THEME FROM PARAMS");
                $theme = $msg->getParam('Theme');
            }
            else {
                // Debug("SET THEME FROM TEMPLATE");
                $theme = $smarty->getTemplateVars('Theme');
            }

            // Debug("THEME: "+$theme);
            if (!$theme) {
                $theme = '$Theme' ;
            }
        }
        catch (Exception $e) {
            throw new jException(SPrintF("Can't fetch template: %s", $templatePath), $e->getCode(), $e);
        }

        $recipient = $msg->getParam('User');

        if(!$recipient['Email'])
            throw new jException('E-mail address not found for user: '.$recipient['ID']);

        $sender = $msg->getParam('From');

        $emailHeads = Array(SPrintF('From: %s', $sender['Email']), 'MIME-Version: 1.0', 'Content-Type: text/plain; charset=UTF-8','Content-Transfer-Encoding: 8bit');

        $taskParams = Array(
            'UserID' => $recipient['ID'],
            'TypeID' => 'Email',
            'Params' => Array(
                $recipient['Email'],
                $theme,
                $message,
                Implode("\r\n", $emailHeads),
                $recipient['ID']
            )
        );

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
