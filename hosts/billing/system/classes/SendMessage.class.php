<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Joonte Software
 * 
 *  rewritten by Alex Keda, for www.host-food.ru, 2019-09-10 in 13:00 MSK
 *
 */

/**
 *
 */
class SendMessage implements Dispatcher {
    /** Instance of email dispatcher. */
    private static $instance;

    /** Private. This dispatcher have only one instance. */
    private function __construct() {

    }

    public static function get() {
        if (!isset(self::$instance)) {
            #self::$instance = new Email();
	    self::$instance = new SendMessage();
        }

        return self::$instance;
    }

    public function send(Msg $msg) {
        // Get template file path.
        $templatePath = SPrintF('Notifies/%s/%s.tpl',$msg->getParam('MethodID'),$msg->getTemplate());

        $smarty = JSmarty::get();
        $smarty->clearAllAssign();

	// конфиг
	$Config = Config();
	$smarty->assign('Config',$Config);

	//Debug(SPrintF('[system/classes/SendMessage.class.php]: Msg = %s',print_r($msg,true)));

        if (!$smarty->templateExists($templatePath)) {
		Debug(SPrintF('[system/classes/SendMessage]: шаблон по типу сообщения не найден (%s)',$templatePath));
		// пробуем новый шаблон
		$templatePath = SPrintF('Notifies/%s/%s.tpl',$msg->getParam('MethodSettings')['MessageTemplate'],$msg->getTemplate());
		if (!$smarty->templateExists($templatePath)) {
	            throw new jException('Template file not found: '.$templatePath);
		}
        }

        //$smarty->assign('Config', Config());

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

	Debug(SPrintF('[system/classes/SendMessage] sender = %s',print_r($sender,true)));

        $emailHeads = Array(
				SPrintF('From: %s', $sender['Email']),
				'MIME-Version: 1.0',
				'Content-Transfer-Encoding: 8bit',
				SPrintF('Content-Type: multipart/mixed; boundary="----==--%s"',HOST_ID)
				);
        // added by lissyara 2013-02-13 in 15:45 MSK, for JBS-609
        if($msg->getParam('Message-ID'))
          $emailHeads[] = SPrintF('Message-ID: %s',$msg->getParam('Message-ID'));

        $Params = Array();
	$Attribs = Array();
	$Params[] = $msg->getParam('ToRecipient');
	$Params[] = $message;
	$Params[] = Array(
				'Theme'		=> $theme,
				'Heads'		=> Implode("\r\n", $emailHeads),
				'Attachments'	=> $msg->getParam('EmailAttachments'),
				'UserID'	=> $recipient['ID'],
				'TimeBegin'	=> $msg->getParam('TimeBegin'),
				'TimeEnd'	=> $msg->getParam('TimeEnd'),
				'ChargeFree'	=> ($msg->getParam('ChargeFree'))?TRUE:FALSE

				);
        $taskParams = Array(
            'UserID' => $recipient['ID'],
            'TypeID' => $msg->getParam('MethodID'),
            'Params' => $Params
        );
	#Debug(SPrintF('[system/classes/SendMessage] taskParams = %s',print_r($taskParams,true)));
	#Debug(SPrintF('[system/classes/SendMessage] msg = %s',print_r($msg,true)));
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
