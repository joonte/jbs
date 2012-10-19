<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class HostingOrdersActiveMsg extends Message {
    public function __construct(array $params, $toUser) {
        parent::__construct('HostingOrdersActive', $toUser, $params);
    }

    public function getParams() {
        $Server = DB_Select('HostingServers', Array('Address', 'Url', 'Ns1Name', 'Ns2Name', 'MySQL'), Array('UNIQ', 'ID' => $this->params['ServerID']));
        if (!Is_Array($Server))
            return ERROR | @Trigger_Error(500);

        $this->params['Server'] = $Server;

        return $this->params;
    }
}
