<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class ExtraIPOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ExtraIPOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }