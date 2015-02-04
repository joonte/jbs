<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsOrdersSuspendedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainsOrdersSuspended', $toUser);

         $this->setParams($params);
     }
 }