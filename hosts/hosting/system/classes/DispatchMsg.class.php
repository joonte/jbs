<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DispatchMsg extends Message {
     public function __construct(array $params, $toUser, $fromUser = 100) {
         parent::__construct('Dispatch', $toUser, $params, $fromUser);
     }
 }