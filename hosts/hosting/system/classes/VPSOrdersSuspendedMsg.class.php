<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class VPSOrdersSuspendedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('VPSOrdersSuspended', $toUser);

         $this->setParams($params);
     }
 }