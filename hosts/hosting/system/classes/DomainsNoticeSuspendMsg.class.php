<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsNoticeSuspendMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainsNoticeSuspend', $toUser, $params);
     }
 }