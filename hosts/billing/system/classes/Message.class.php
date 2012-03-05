<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class Message implements Msg {
    protected $toUser;

    protected $fromUser = 100;

    protected $template = 'default';

    protected $params = Array();

    public function __construct($template, $toUser, $params = NULL, $fromUser = 100) {
        $this->template = $template;
        $this->params = $params;
        $this->toUser = $toUser;
        $this->fromUser = $fromUser;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getTo() {
        return $this->toUser;
    }

    public function setTo($toUser) {
        $this->toUser = $toUser;
    }

    public function setFrom($fromUser) {
        $this->fromUser = $fromUser;
    }

    public function getFrom() {
        return $this->fromUser;
    }

    public function setParam($key, $val) {
        $params = & $this->params;
        $params[$key] = $val;
    }

    public function &getParam($key) {
        return $this->params[$key];
    }

    public function setParams(&$params) {
        $this->params = $params;
    }

    public function getParams() {
        return $this->params;
    }
}