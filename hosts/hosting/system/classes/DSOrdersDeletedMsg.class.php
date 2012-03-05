<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DSOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DSOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }