<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsOrdersOnCheckMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainsOrdersOnCheck', $toUser);

         $this->setParams($params);
     }
 }