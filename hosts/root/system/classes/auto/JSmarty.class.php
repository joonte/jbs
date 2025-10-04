<?php

require_once 'Smarty.class.php';

/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class JSmarty extends Smarty {
    const DFLT_LANG = 'ru';

    private $currentLang;

    private $supportedLang = Array('ru','en');

    private static $instance;

    public function __construct() {
        parent::__construct();

        $this->initLang();
    }

    public static function get() {
        if (!isset(self::$instance)) {
            self::$instance = new JSmarty();
        }

        return self::$instance;
    }

    private function initLang() {
        if (isset($_GET['lang'])) {
            $this->currentLang = $_GET['lang'];
        }
        else if (isset($_COOKIE['lang'])){
            $this->currentLang = $_COOKIE['lang'];
        }
        else {
            $this->currentLang = self::DFLT_LANG;
        }

        if (!in_array($this->currentLang, $this->supportedLang)) {
            $this->currentLang = self::DFLT_LANG;
        }

        setcookie('lang', $this->currentLang,0,'/',SPrintF('.%s',HOST_ID));

        $langPath = System_Element('templates/lang/'.$this->currentLang.'.php');
        $langMsgs = include_once($langPath);
        //die(print_r($langMsgs,true));

        if (count($langMsgs) > 0) {
            $this->assign('a', "2.5.2");
            $this->assign('LANG', $langMsgs);
        }
    }
}
