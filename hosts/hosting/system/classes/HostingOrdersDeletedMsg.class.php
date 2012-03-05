<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class HostingOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('HostingOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }