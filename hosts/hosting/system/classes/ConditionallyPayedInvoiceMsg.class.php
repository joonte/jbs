<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class ConditionallyPayedInvoiceMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ConditionallyPayedInvoice', $toUser, $params);
     }
 }