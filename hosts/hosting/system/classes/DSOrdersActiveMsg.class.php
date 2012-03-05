<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DSOrdersActiveMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DSOrdersActive', $toUser);

         $this->setParams($params);
     }
 }