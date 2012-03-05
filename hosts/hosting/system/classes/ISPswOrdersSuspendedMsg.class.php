<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class ISPswOrdersSuspendedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ISPswOrdersSuspended', $toUser);

         $this->setParams($params);
     }
 }