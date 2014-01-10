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

        $emailHeads = Array(
				SPrintF('From: %s', $sender['Email']),
				'MIME-Version: 1.0',
				'Content-Transfer-Encoding: 8bit',
				SPrintF('Content-Type: multipart/mixed;%sboundary="----==--%s"',"\r\n\t",HOST_ID)
				);
        // added by lissyara 2013-02-13 in 15:45 MSK, for JBS-609
        if($msg->getParam('Message-ID'))
          $emailHeads[] = SPrintF('Message-ID: %s',$msg->getParam('Message-ID'));

        $Params = Array();
	if($msg->getParam('Recipient')){
	  $Params[] = $msg->getParam('Recipient');
	}else{
	  $Params[] = $recipient['Email'];
	}
	$Params[] = $theme;
	$Params[] = $message;
	$Params[] = Implode("\r\n", $emailHeads);
	$Params[] = $recipient['ID'];
	if($msg->getParam('EmailAttachments')){
		$Params[] = $msg->getParam('EmailAttachments');
	}else{
		$Params[] = 'не определено';
	}
        $taskParams = Array(
            'UserID' => $recipient['ID'],
            'TypeID' => 'Email',
            'Params' => $Params
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
