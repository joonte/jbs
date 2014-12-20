<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class HostingPasswordChangeMsg extends Message {
    public function __construct(array $params, $toUser) {
        parent::__construct('HostingPasswordChange', $toUser, $params);
    }

    public function getParams() {
    	#-------------------------------------------------------------------------------
        #$Server = DB_Select('HostingServers', Array('Address', 'Url', 'Ns1Name', 'Ns2Name', 'MySQL'), Array('UNIQ', 'ID' => $this->params['ServerID']));
	$Server = DB_Select('Servers', Array('Address', 'Params'), Array('UNIQ', 'Where' => SPrintF('(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = %u) = `Servers`.`ID`',$this->params['OrderID'])));
        if (!Is_Array($Server))
            return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
        $this->params['Server'] = $Server;
	#-------------------------------------------------------------------------------
        return $this->params;
	#-------------------------------------------------------------------------------
    }
}
