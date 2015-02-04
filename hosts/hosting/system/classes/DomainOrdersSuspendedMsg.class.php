<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainOrdersSuspendedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainOrdersSuspended', $toUser);

         $this->setParams($params);
     }
 }
