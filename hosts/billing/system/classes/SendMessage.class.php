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

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
class SendMessage implements Dispatcher{
	#-------------------------------------------------------------------------------
	/** Instance of email dispatcher. */
	private static $instance;
	#-------------------------------------------------------------------------------
	/** Private. This dispatcher have only one instance. */
	private function __construct(){}
	#-------------------------------------------------------------------------------
	public static function get(){
		#-------------------------------------------------------------------------------
		if(!isset(self::$instance)){
			#-------------------------------------------------------------------------------
			self::$instance = new SendMessage();
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		return self::$instance;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function send(Msg $msg){
		#-------------------------------------------------------------------------------
		$smarty = JSmarty::get();
		#-------------------------------------------------------------------------------
		$smarty->clearAllAssign();
		#-------------------------------------------------------------------------------
		$smarty->assign('Config',Config());
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Contact = $msg->getParam('Contact');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// Get template file path.
		$templatePath = SPrintF('Notifies/%s/%s.tpl',$Contact['MethodID'],$msg->getTemplate());
		#-------------------------------------------------------------------------------
		if(!$smarty->templateExists($templatePath)){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[system/classes/SendMessage]: шаблон по типу сообщения не найден (%s)',$templatePath));
			#-------------------------------------------------------------------------------
			// пробуем новый шаблон
			$MethodSettings = $msg->getParam('MethodSettings');
			#-------------------------------------------------------------------------------
			$templatePath = SPrintF('Notifies/%s/%s.tpl',$MethodSettings['MessageTemplate'],$msg->getTemplate());
			#-------------------------------------------------------------------------------
			if(!$smarty->templateExists($templatePath)){
				#-------------------------------------------------------------------------------
				throw new jException(SPrintF('Template file not found: %s',$templatePath));
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[system/classes/SendMessage]: используемый шаблон сообщения: %s',$templatePath));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($msg->getParams()) as $paramName)
			$smarty->assign($paramName, $msg->getParam($paramName));
		#-------------------------------------------------------------------------------
		$message = $smarty->fetch($templatePath);
		#-------------------------------------------------------------------------------
		try{
			#-------------------------------------------------------------------------------
			if($msg->getParam('Theme')){
				#-------------------------------------------------------------------------------
				$theme = $msg->getParam('Theme');
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$theme = $smarty->getTemplateVars('Theme');
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// костыль для JBS-1380
			if($theme){
				#-------------------------------------------------------------------------------
				$GLOBALS['JBS-1380-Theme'] = $theme;
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$theme = IsSet($GLOBALS['JBS-1380-Theme'])?$GLOBALS['JBS-1380-Theme']:'$Theme';
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		catch (Exception $e){
			#-------------------------------------------------------------------------------
			throw new jException(SPrintF("Can't fetch template: %s", $templatePath), $e->getCode(), $e);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$recipient = $msg->getParam('User');
		#-------------------------------------------------------------------------------
		if(!$recipient['Email'])
			throw new jException(SPrintF('E-mail address not found for user: %s',$recipient['ID']));
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[system/classes/SendMessage] sender = %s',print_r($msg->getParam('From'),true)));
		#-------------------------------------------------------------------------------
		// JBS-1315, возможны дополнительные заголовки
		if($msg->getParam('Headers')){
			#-------------------------------------------------------------------------------
			$Lines = Explode("\n", Trim($msg->getParam('Headers')));
			#-------------------------------------------------------------------------------
			foreach($Lines as $Line)
				$emailHeads[] = Trim($Line);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$emailHeads = Array();
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// обработка вложений, JBS-1295
		$Attachments = ($Contact['IsSendFiles'])?$msg->getParam('Attachments'):Array();
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Params = Array();
		#-------------------------------------------------------------------------------
		$Params[] = $Contact['Address'];
		$Params[] = $message;
		$Params[] = Array(
					'Theme'		=> $theme,					// тема сообщения
					'Heads'		=> $emailHeads,					// почтовые заголовки
					'Attachments'	=> $Attachments,				// массив с вложениями, TODO разобраться а чё иногда вдруг не массив?
					'UserID'	=> $recipient['ID'],				// идентфикатор пользователя
					'From'		=> $msg->getParam('From'),			// от кого письмо (данные пользователя)
					'ChargeFree'	=> ($msg->getParam('ChargeFree'))?TRUE:FALSE,	// платно или бесплатно отправлять
					'MessageID'	=> $msg->getParam('MessageID'),			// идентфикатор сообщения, из тикетниы
					'TicketID'	=> $msg->getParam('TicketID'),			// номер тикета
					'UserName'	=> $msg->getParam('UserName'),			// имя пользователя, для приветствия в задаче
					'Contact'	=> $Contact,					// массив, данные контакта, чтоб параметры по одному не передавать
					'HTML'		=> $msg->getParam('HTML'),			// текст сообщения в HTML (используется в рассылках, только для Email)
					'TypeID'	=> $msg->getParam('TypeID'),			// тип оповещения, для ссылки на отписку
					'IsImmediately'	=> ($msg->getParam('IsImmediately'))?TRUE:FALSE,// немедленная доставка, без учёта разрешённого времени (для восстановления пароля, например)
				);
		#-------------------------------------------------------------------------------
		$taskParams = Array(
					'UserID'	=> $recipient['ID'],				// идентифкатор юзера-получателя
					'TypeID'	=> $Contact['MethodID'],			// метод оповещения
					'Params'	=> $Params					// массив параметров
					);
		#-------------------------------------------------------------------------------
		//Debug(SPrintF('[system/classes/SendMessage] taskParams = %s',print_r($taskParams,true)));
		//Debug(SPrintF('[system/classes/SendMessage] msg = %s',print_r($msg,true)));
		#-------------------------------------------------------------------------------
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
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
