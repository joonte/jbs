<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DNSmanagerOrdersSuspendedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DNSmanagerOrdersSuspended', $toUser);

         $this->setParams($params);
     }
 }
