<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
interface Msg {
    public function getTemplate();

    public function setTo($toUser);

    public function getTo();

    public function setFrom($fromUser);

    public function getFrom();

    public function setParams(&$params);

    public function getParams();

    public function setParam($key, $val);

    public function &getParam($key);
}
