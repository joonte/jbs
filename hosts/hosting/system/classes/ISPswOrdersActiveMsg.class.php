<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class ISPswOrdersActiveMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ISPswOrdersActive', $toUser, $params);
     }
 }