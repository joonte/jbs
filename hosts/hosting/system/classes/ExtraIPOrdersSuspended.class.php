<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class ExtraIPOrdersSuspendedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ExtraIPOrdersSuspended', $toUser);

         $this->setParams($params);
     }
 }