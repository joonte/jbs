<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Alex Keda, for www.host-food.ru
 *
 */
 class NegativeContractBalanceMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('NegativeContractBalance', $toUser, $params);
     }
 }
