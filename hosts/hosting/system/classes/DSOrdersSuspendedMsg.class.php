<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *
 */
class DSOrdersSuspendedMsg extends Message {
	#-------------------------------------------------------------------------------
	public function __construct(array $params, $toUser) {
		#-------------------------------------------------------------------------------
		parent::__construct('DSOrdersSuspended', $toUser, $params);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function getParams() {
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('params = %s',print_r($this->params,true)));
		#-------------------------------------------------------------------------------
		$DSScheme = DB_Select('DSSchemes', Array('*'), Array('UNIQ', 'Where' => SPrintF('`ID` = %u',$this->params['SchemeID'])));
		#-------------------------------------------------------------------------------
		if (!Is_Array($DSScheme))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$this->params['DSScheme'] = $DSScheme;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// ссылка на продление заказа
		$this->params['ProlongLink'] = SPrintF('%s://%s/v2/DSOrderPay/%u/',URL_SCHEME,HOST_ID,$this->params['OrderID']);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return $this->params;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}

