<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class ISPswOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ISPswOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }