<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class JSmarty extends Smarty {
    private static $instance;

    public function __construct() {
        parent::__construct();
    }

    public static function get() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }

        return self::$instance;
    }
}
