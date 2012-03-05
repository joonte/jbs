<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class VPSOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('VPSOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }