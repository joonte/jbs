<?php
/**
 *
 *  Joonte Billing System
 *
 *  Alex Keda, for www.host-food.ru
 *
 */
class ProxyOrdersActiveMsg extends Message {
	#-------------------------------------------------------------------------------
	public function __construct(array $params, $toUser) {
		#-------------------------------------------------------------------------------
		parent::__construct('ProxyOrdersActive', $toUser, $params);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function getParams() {
		#-------------------------------------------------------------------------------
		$Server = DB_Select('Servers', Array('Address','Params'), Array('UNIQ', 'Where' => SPrintF('(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = %u) = `Servers`.`ID`',$this->params['OrderID'])));
		#-------------------------------------------------------------------------------
		if (!Is_Array($Server))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$this->params['Server'] = $Server;
		#-------------------------------------------------------------------------------
		return $this->params;
		#-------------------------------------------------------------------------------
	}
}

?>
