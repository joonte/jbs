<?php

// SMSC.RU API (www.smsc.ru) версия 2.5 (22.10.2011)

class SMSC {
    private static $instance;

    const SEND_CMD = "send";

    private $https = false;

    private $config;

    public function __construct() {
        $this->init();
    }

    private function init() {
        $config = Config();

        if (!isset($config['SMSC'])) {
            throw new jException("SMSC configuration not found!");
        }

        $this->config = $config['SMSC'];

        if (!isset($this->config['Server'])) {
            throw new jException("SMSC 'Server' param not found!");
        }

        if (!isset($this->config['Login'])) {
            throw new jException("SMSC 'Login' param not found!");
        }

        if (!isset($this->config['Password'])) {
            throw new jException("SMSC 'Password' param not found!");
        }

        if (isset($this->config['UseSSL'])) {
            $this->https = (boolean)$this->config['UseSSL'];
        }
    }

    public static function get() {
        if (!isset(self::$instance)) {
            self::$instance = new SMSC();
        }

        return self::$instance;
    }

    public function send($Mobile, $Message) {
        return $this->sendSmsCmd($Mobile, $Message);
    }

    private function sendSmsCmd($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = false, $query = "") {
        $params = Array(
            "login" => $this->config["Login"],
            "psw" => $this->config["Password"],
            "mes" => $message,
            "phones" => $phones,
            "fmt" => 1
        );

        if (isSet($this->config['Send'])) {
            $params = array_merge($params, $this->config['Send']);
        }

        //Debug(print_r($params,true));

        $urlParams = "";

        foreach ($params as $param => $value) {
            $urlParams .= sprintf("%s=%s&", $param, urlencode($value));
        }

        $result = $this->execCmd(self::SEND_CMD, $urlParams);

        // (id, cnt, cost, balance) или (id, -error)

        if (Count($result) == 2 && $result[0] == "0") {
            throw new jException(sprintf("Error while sending sms message! [id=%d, error=%s]",$result[0], $result[1]));
        }

        return $result;
    }

    function execCmd($cmd, $arg = "") {
        $proto = $this->https ? "https" : "http";

        $url = sPrintF("%s://%s/sys/%s.php?%s", $proto, $this->config["Server"], $cmd, $arg);
        //Debug($url);

        $i = 0;
        do {
            if ($i) { sleep(2); }

            $ret = $this->readUrl($url);
        }
        while ($ret == "" && ++$i < 3);

        if ($ret == "") {
            throw new jException("Error reading URL: ".$url);
        }

        return explode(",", $ret);
    }

    /**
     * @param  $url
     * @return mixed|string
     */
    // Функция чтения URL. Для работы должно быть доступно:
    // curl или fsockopen (только http) или включена опция allow_url_fopen для file_get_contents

    function readUrl($url) {
        $ret = "";
        $post = strlen($url) > 2000;

        if (function_exists("curl_init")) {
            static $c = 0; // keepalive

            if (!$c) {
                $c = curl_init();
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($c, CURLOPT_TIMEOUT, 10);
                curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
            }

            if ($post) {
                list($url, $post) = explode('?', $url, 2);
                curl_setopt($c, CURLOPT_POST, true);
                curl_setopt($c, CURLOPT_POSTFIELDS, $post);
            }

            curl_setopt($c, CURLOPT_URL, $url);

            $ret = curl_exec($c);
        }
        elseif (!$this->https && function_exists("fsockopen"))
        {
            $m = parse_url($url);

            $fp = @fsockopen($m["host"], 80, $errno, $errstr, 10);

            if ($fp) {
                fwrite($fp, ($post ? "POST $m[path]" : "GET $m[path]?$m[query]") . " HTTP/1.1\r\nHost: smsc.ru\r\nUser-Agent: PHP" . ($post ? "\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($m['query']) : "") . "\r\nConnection: Close\r\n\r\n" . ($post ? $m['query'] : ""));

                while (!feof($fp))
                    $ret = fgets($fp, 100);

                fclose($fp);
            }
        }
        else
            $ret = file_get_contents($url);

        return $ret;
    }
}

?>
