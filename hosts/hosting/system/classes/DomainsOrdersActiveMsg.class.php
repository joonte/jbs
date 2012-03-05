<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsOrdersActiveMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainsOrdersActive', $toUser);

         $this->setParams($params);
     }
 }