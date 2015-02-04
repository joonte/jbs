<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainNoticeSuspendMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainNoticeSuspend', $toUser, $params);
     }
 }
