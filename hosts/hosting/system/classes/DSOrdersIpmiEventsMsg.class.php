<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2022, Alex Keda for www.host-food.ru
 *
 */
class DSOrdersIpmiEventsMsg extends Message {
	#-------------------------------------------------------------------------------
	public function __construct(array $params, $toUser) {
		#-------------------------------------------------------------------------------
		parent::__construct('DSOrdersIpmiEvents', $toUser, $params);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function getParams() {
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('params = %s',print_r($this->params,true)));
		#-------------------------------------------------------------------------------
#		$DSScheme = DB_Select('DSSchemes', Array('*'), Array('UNIQ', 'Where' => SPrintF('`ID` = %u',$this->params['SchemeID'])));
#		#-------------------------------------------------------------------------------
#		if (!Is_Array($DSScheme))
#			return ERROR | @Trigger_Error(500);
#		#-------------------------------------------------------------------------------
#		$this->params['DSScheme'] = $DSScheme;
#		#-------------------------------------------------------------------------------
		return $this->params;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
