<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainOrdersOnCheckMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainOrdersOnCheck', $toUser);

         $this->setParams($params);
     }
 }
