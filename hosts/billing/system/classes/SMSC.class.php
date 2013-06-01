<?php

// SMSC.RU API (www.smsc.ru) версия 2.5 (22.10.2011)

class SMSC {
    private static $instance;

    const SEND_CMD = "send";

    private $https = false;

    private $config;

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function __construct($login = false, $password = false, $apikey = false, $sender = false, $charset = false) {
		#-------------------------------------------------------------------------------
		if($login)
			$this->login = $login;
		#-------------------------------------------------------------------------------
	    	if($password)
			$this->password = $password;
		#-------------------------------------------------------------------------------
		if ($apikey)
			$this->apikey = $apikey;
		#-------------------------------------------------------------------------------
		if ($sender)
			$this->sender = $sender;
		#-------------------------------------------------------------------------------
		if ($charset)
			$this->charset = $charset;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
    public static function get() {
        if (!isset(self::$instance)) {
            self::$instance = new SMSC();
        }

        return self::$instance;
    }
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function send($Mobile, $Message) {
		#-------------------------------------------------------------------------------
		$Result = $this->sendSmsCmd($Mobile, $Message);
		if(Is_Array($Result)){
			$this->success = SPrintF('id = %s; cnt = %s',$Result['id'],$Result['cnt']);;
			return TRUE;
		}
		#-------------------------------------------------------------------------------
		$this->error = print_r($Result,true);
		return $Result;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function balance() {
		#-------------------------------------------------------------------------------
		$Result = $this->execCmd('balance');
		$this->balance = $Result['balance'];
		return true;
		#Debug(SPrintF('[system/classes/SMSC.class.php]: balance = %s',print_r($balance,true)));
		#-------------------------------------------------------------------------------
	}
    	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	private function sendSmsCmd($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = false, $query = "") {
		#-------------------------------------------------------------------------------
		$urlParams = Array(
					'mes'		=> $message,
					'phones'	=> $phones,
					'sender'	=> $this->sender
				);
		#-------------------------------------------------------------------------------
		if(isSet($this->Send))
			$urlParams = array_merge($urlParams, $this->Send);
		#-------------------------------------------------------------------------------
        	//Debug(print_r($params,true));
        	$result = $this->execCmd(self::SEND_CMD, $urlParams);
        	// (id, cnt, cost, balance) или (id, -error)
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[system/classes/SMSC.class.php]: result = %s',print_r($result,true)));
	        return $result;
    }
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	function execCmd($cmd, $arg = Array()){
		#-------------------------------------------------------------------------------
		$proto = $this->https ? "https" : "http";
		#-------------------------------------------------------------------------------
		$params = Array(
		        		'login'		=> $this->login,
					'psw'		=> $this->password,
					'charset'	=> 'utf-8',
					'fmt'		=> 3,
				);
		#-------------------------------------------------------------------------------
		$params = array_merge($params, $arg);
	        $urlParams = "";
		#-------------------------------------------------------------------------------
		foreach ($params as $param => $value)
			$urlParams .= sprintf("%s=%s&", $param, urlencode($value));
		#-------------------------------------------------------------------------------
	        $url = sPrintF("%s://smsc.ru/sys/%s.php?%s", $proto, $cmd, $urlParams);
		#Debug(SPrintF('[system/classes/SMSC.class.php]: url = %s',$url));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$i = 0;
		do {
			if ($i) { sleep(2); }
			$ret = $this->readUrl($url);
		}
		#-------------------------------------------------------------------------------
		while ($ret == "" && ++$i < 3);
		if ($ret == "") {
			throw new jException("Error reading URL: ".$url);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Result = Json_Decode($ret,TRUE);
		if(IsSet($Result['error'])){
			$this->error = SPrintF('error_code = %s; error = (%s)',$Result['error_code'],$Result['error']);
			throw new jException(SPrintF('error_code = %s; error = (%s)',$Result['error_code'],$Result['error']));
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
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
