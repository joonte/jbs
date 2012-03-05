<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsOrdersOnTransferMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainsOrdersOnTransfer', $toUser);

         $this->setParams($params);
     }
 }