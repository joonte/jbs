<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainNoticeDeleteMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainNoticeDelete', $toUser, $params);
     }
 }
