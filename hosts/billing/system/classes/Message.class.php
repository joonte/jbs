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

    public function __construct($template) {
        $this->template = $template;
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

    public function setParams(&$params) {
        $this->params = $params;
    }

    public function getParams() {
        return $this->params;
    }
}