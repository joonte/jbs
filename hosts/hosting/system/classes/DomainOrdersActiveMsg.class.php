<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainOrdersActiveMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainOrdersActive', $toUser);

         $this->setParams($params);
     }
 }
