<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DSOrdersSuspendedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DSOrdersSuspended', $toUser);

         $this->setParams($params);
     }
 }