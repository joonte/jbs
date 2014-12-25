<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DNSmanagerOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DNSmanagerOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }
