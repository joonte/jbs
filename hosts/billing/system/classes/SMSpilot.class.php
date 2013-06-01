<?php

/* SMS Pilot.Class API/PHP v1.8.5
 * SEE: http://www.smspilot.ru/apikey.php

  Example (send):
  require_once('smspilot.class.php');
  $sms = new SMSPilot( 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ' );
  $sms->send( '79087964781', 'Привет, разработчик!');

  Example 2 (replace sender + send):
  require_once('smspilot.class.php');
  $sms = new SMSPilot( 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ', 'WINDOWS-1251' );
  if ($sms->send('79121234512','Завтра контрольная!', 'SCHOOL_N2')) {
  echo 'Сообщение успешно отправлено<br />';
  echo 'Цена='.$sms->cost.' кредитов<br />';
  echo 'Баланс='.$sms->balance.' кредитов<br />';
  } else
  echo 'Ошибка! '.$sms->error;

  Example 3 (send + get sms status):
  require_once('smspilot.class.php');
  $sms = new SMSPilot( 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ' );
  $sms->send('79087964781', 'Novy zakaz http://www.smspilot.ru!');
  print_r( $sms->status[0] ); // Array ( [id] => 94 [phone] => 79087964781 [zone] => 2 [status] => 0 )
  print_r( $sms->statusByPhone( '79087964781' ) ); // // Array ( [id] => 94 [phone] => 79087964781 [zone] => 2 [status] => 0 )

  Example 4: (указываем что наша кодировка Windows-1251, проверяем автозамену 89.. получателя)
  require_once('smspilot.class.php');
  $sms = new SMSPilot('XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ','WINDOWS-1251');
  $status = $sms->send('89087964781', 'Сообщение в Windows-1251 кодировке!', '89088038965');
  print_r( $status )


  Array
  (
  [0] => Array
  (
  [id] => 129
  [phone] => 79087964781
  [zone] => 2
  [status] => 0
  )
  )


  Example 5 (check status):
  require_once('smspilot.class.php');
  $sms = new SMSPilot( 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ' );
  $status = $sms->check( array( 94,95) );
  print_r( $status );


  Array
  (
  [0] => Array
  (
  [id] => 94
  [phone] => 79087964781
  [zone] => 2
  [status] => 2
  )

  [1] => Array
  (
  [id] => 95
  [phone] => 79087964781
  [zone] => 2
  [status] => -1
  )

  )

  Example 6 ( balance  ):
  require_once('smspilot.class.php');
  $sms = new SMSPilot( 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ' );
  echo $sms->balance(); // 23004

 */

class SMSPilot {

    public $api = 'http://smspilot.ru/api.php';
    public $apikey = 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ';
    public $charset = 'UTF-8';
    public $to;
    public $text;
    public $from = false;
    public $send_datetime = false; // 1.8.4
    public $error;
    public $success;
    public $status; // new in 1.7
    public $info;
    public $cost; // new in 1.8
    public $balance; // new in 1.8

    public function __construct($login = false, $password = false, $apikey = false, $from = false, $charset = false) {

	if ($apikey)
	    $this->apikey = $apikey;
	else if (defined('SMSPILOT_APIKEY'))
	    $this->apikey = SMSPILOT_APIKEY;

	if ($charset)
	    $this->charset = $charset;
	else if (defined('SMSPILOT_CHARSET'))
	    $this->charset = SMSPILOT_CHARSET;

	if ($from)
	    $this->from = $from;
	else if (defined('SMSPILOT_FROM'))
	    $this->from = SMSPILOT_FROM;

	if (defined('SMSPILOT_API'))
	    $this->api = SMSPILOT_API;
    }

    // send sms via smspilot.ru
    public function send($to = false, $text = false, $from = false, $send_datetime = false) {

	if ($to)
	    $this->to = $to;

	if ($text)
	    $this->text = $text;

	if ($from)
	    $this->from = $from;

	if ($send_datetime)
	    $this->send_datetime = $send_datetime;

	$this->error = false;
	$this->success = false;
	$this->status = array();

	$text = ($this->charset != 'UTF-8') ? mb_convert_encoding($this->text, 'utf-8', $this->charset) : $this->text;

	$result = $this->http_post($this->api, array(
	    'send' => $text,
	    'to' => ((is_array($this->to)) ? implode(',', $this->to) : $this->to),
	    'from' => $this->from,
	    'send_datetime' => $this->send_datetime,
	    'apikey' => $this->apikey,
	));

	//echo $result;
	if ($result) {
	    if (substr($result, 0, 6) == 'ERROR=') {
		$this->error = substr($result, 6);
		return false;
	    }
	    elseif (substr($result, 0, 8) == 'SUCCESS=') {

		$this->success = substr($result, 8, ($p = strpos($result, "\n")) - 8);
		if (preg_match('~(\d+)/(\d+)~', $this->success, $matches)) {
		    $this->cost = $matches[1]; // new in 1.8
		    $this->balance = $matches[2]; // new in 1.8
		}

		$status_csv = substr($result, $p + 1);
		//status
		$status_csv = explode("\n", $status_csv);
		foreach ($status_csv as $line) {
		    $s = explode(',', $line);
		    $this->status[] = array(
			'id' => $s[0],
			'phone' => $s[1],
			'zone' => $s[2],
			'status' => $s[3]
		    );
		}
		return true;
	    }
	    else {
		$this->error = 'UNKNOWN RESPONSE';
		return false;
	    }
	}
	else {
	    $this->error = 'CONNECTION ERROR';
	    return false;
	}
    }

    // check status by sms id or ids
    public function check($ids) { // new in 1.7
	if (is_array($ids))
	    $ids = implode(',', $ids);

	$this->error = false;
	$this->success = false;
	$this->status = array();

	$result = $this->http_post($this->api, array(
	    'check' => $ids,
	    'apikey' => $this->apikey
	));

	if ($result) {

	    if (substr($result, 0, 6) == 'ERROR=') {

		$this->error = substr($result, 6);
		return false;
	    }
	    else {

		$status_csv = $result;
		//status
		$status_csv = explode("\n", $status_csv);
		foreach ($status_csv as $line) {
		    $s = explode(',', $line);
		    $this->status[] = array(
			'id' => $s[0],
			'phone' => $s[1],
			'zone' => $s[2],
			'status' => $s[3]
		    );
		}
		return $this->status;
	    }
	}
	else {
	    $this->error = 'CONNECTION ERROR';
	    return false;
	}
    }

    // helper to find status by phone
    public function statusByPhone($phone) {

	foreach ($this->status as $s)
	    if ($s['phone'] == $phone)
		return $s;

	return false;
    }

    public function balance($currency = 'sms') {

	$result = $this->http_post($this->api, array(
	    'balance' => $currency,
	    'apikey' => $this->apikey
	));

	if (strlen($result)) {
	    if (substr($result, 0, 6) == 'ERROR=') {
		$this->error = substr($result, 6);
		return false;
	    }
	    else
		$this->balance = $result;
	    return true;
	} else {
	    $this->error = 'CONNECTION ERROR';
	    return false;
	}
    }

    // apikey info
    public function info() {

	$result = $this->http_post($this->api, array(
	    'apikey' => $this->apikey
	));

	if ($result) {
	    if (substr($result, 0, 6) == 'ERROR=') {
		$this->error = substr($result, 6);
		return false;
	    }
	    elseif (substr($result, 0, 8) == 'SUCCESS=') {
		$s = substr($result, 8, ($p = strpos($result, "\n")) - 8);

		$this->success = $s;

		$lines = explode("\n", substr($result, $p));

		$this->info = array();
		foreach ($lines as $line)
		    if ($p = strpos($line, '='))
			$this->info[substr($line, 0, $p)] = substr($line, $p + 1);


		if ($this->charset != 'UTF-8')
		    foreach ($this->info as $k => $v)
			$this->info[$k] = mb_convert_encoding($v, $this->charset, 'UTF-8');

		if (isset($this->info['balance']))
		    $this->balance = $this->info['balance'];

		return true;
	    } else {
		$this->error = 'UNKNOWN RESPONSE';
		return false;
	    }
	}
	else {
	    $this->error = 'CONNECTION ERROR';
	    return false;
	}
    }

    // sockets version HTTP/POST
    public function http_post($url, $data) {

	$eol = "\r\n";

	$post = '';

	if (is_array($data)) {
	    foreach ($data as $k => $v)
		$post .= $k.'='.urlencode($v).'&';
	    $post = substr($post, 0, -1);
	    $content_type = 'application/x-www-form-urlencoded';
	}
	else {
	    $post = $data;
	    if (strpos($post, '<?xml') === 0)
		$content_type = 'text/xml';
	    else if (strpos($post, '{') === 0)
		$content_type = 'application/json';
	    else
		$content_type = 'text/html';
	}
	if ((($u = parse_url($url)) === false) || !isset($u['host']))
	    return false;

	if (!isset($u['scheme']))
	    $u['scheme'] = 'http';

	$request = 'POST '.(isset($u['path']) ? $u['path'] : '/').((isset($u['query'])) ? '?'.$u['query'] : '' ).' HTTP/1.1'.$eol
		.'Host: '.$u['host'].$eol
		.'Content-Type: '.$content_type.$eol
		.'Content-Length: '.mb_strlen($post, 'latin1').$eol
		.'Connection: close'.$eol.$eol
		.$post;

	$host = ($u['scheme'] == 'https') ? 'ssl://'.$u['host'] : $u['host'];

	if (isset($u['port']))
	    $port = $u['port'];
	else
	    $port = ($u['scheme'] == 'https') ? 443 : 80;

	$fp = @fsockopen($host, $port, $errno, $errstr, 10);
	if ($fp) {

	    $content = '';
	    $content_length = false;
	    $chunked = false;

	    fwrite($fp, $request);

	    // read headers
	    while ($line = fgets($fp)) {

		if (preg_match('~Content-Length: (\d+)~i', $line, $matches)) {
		    $content_length = (int) $matches[1];
		}
		else if (preg_match('~Transfer-Encoding: chunked~i', $line)) {
		    $chunked = true;
		}
		else if ($line == "\r\n") {
		    break;
		}
	    }
	    // read content
	    if ($content_length !== false) {

		$_size = 4096;
		do {
		    $_data = fread($fp, $_size);
		    $content .= $_data;
		    $_size = min($content_length - strlen($content), 4096);
		} while ($_size > 0);

//				$content = fread($fp, $content_length);
	    }
	    else if ($chunked) {

		while ($chunk_length = hexdec(trim(fgets($fp)))) {

		    $chunk = '';
		    $read_length = 0;

		    while ($read_length < $chunk_length) {

			$chunk .= fread($fp, $chunk_length - $read_length);
			$read_length = strlen($chunk);
		    }
		    $content .= $chunk;

		    fgets($fp);
		}
	    }
	    else {
		while (!feof($fp))
		    $content .= fread($fp, 4096);
	    }
	    fclose($fp);

	    return $content;
	}
	else {
	    return false;
	}
    }

}

?>
