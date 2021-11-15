<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *
 */

class ProxyOrdersSuspendedMsg extends Message {
	#-------------------------------------------------------------------------------
	public function __construct(array $params, $toUser) {
		#-------------------------------------------------------------------------------
		parent::__construct('ProxyOrdersSuspended', $toUser, $params);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function getParams() {
		#-------------------------------------------------------------------------------
		$ProxyScheme = DB_Select('ProxySchemes', Array('*'), Array('UNIQ', 'Where' => SPrintF('`ID` = %u',$this->params['SchemeID'])));
		#-------------------------------------------------------------------------------
		if(!Is_Array($ProxyScheme))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$this->params['ProxyScheme'] = $ProxyScheme;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// ссылка на продление заказа
		$this->params['ProlongLink'] = SPrintF('%s://%s/ProxyOrders/%u/',URL_SCHEME,HOST_ID,$this->params['OrderID']);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return $this->params;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}

