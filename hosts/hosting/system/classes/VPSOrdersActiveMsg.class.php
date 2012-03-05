<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class VPSOrdersActiveMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('VPSOrdersActive', $toUser);

         $this->setParams($params);
     }
 }