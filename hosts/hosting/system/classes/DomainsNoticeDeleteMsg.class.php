<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsNoticeDeleteMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainsNoticeDelete', $toUser, $params);
     }
 }