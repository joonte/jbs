<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */

class OrdersSuspendedMsg extends Message {
	public function __construct(array $params, $toUser) {
		parent::__construct('OrdersSuspended', $toUser, $params);
		#$this->setParams($params);
	}

	public function getParams() {
		Debug(print_r($this->params,true));
		$Service = DB_Select('Services', Array('ID', 'Code', 'Name', 'NameShort'), Array('UNIQ', 'ID' => $this->params['ServiceID']));
		if (!Is_Array($Service))
			return ERROR | @Trigger_Error(500);
		$this->params['Service'] = $Service;
		return $this->params;
	}
}

