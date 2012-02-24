<?php


#-------------------------------------------------------------------------------
Header('Content-type: text/plain; charset=utf-8');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Http = Array(
  #-----------------------------------------------------------------------------
  'Address'  => 'cp.mastername.ru',
  'Port'     => 443,
  'Host'     => 'cp.mastername.ru',
  'Protocol' => 'ssl',
  'Charset'  => 'CP1251'
);
#-------------------------------------------------------------------------------
$Request = <<<EOD
[request]
login: 171375/RD-G
password: 
action: create_client
request-id: %s

[client]
client-type: ФИЗЛИЦО
resident:
person-r: Бреславский Антон Вадимович
person: Anton V Breslavskiy
email: anton@breslavsky.ru
phone: +7 905 1551336
fax: +7 905 1551336
birth-date: 1986-03-18
passport-series: 24 05
passport-number: 229835
passport-date: 2003-04-07
passport-org: ОВД Ленинского района г. Иваново
post-country: RU
post-zip-code: 153051
post-region: Ивановская область
post-city: Иваново
post-street: Кохомское, д. 7, кв. 52, Бреславскому Антону Вадимовичу
reg-country: RU
reg-region: Ивановская область
reg-city: Иваново
reg-street: Кохомское, д. 7, кв. 52
EOD;
#-------------------------------------------------------------------------------
$Query = Array('request'=>SPrintF($Request,UniqID('ID')));
#-------------------------------------------------------------------------------
$Result = Http_Send('/partner_gateway',$Http,Array(),$Query);
if(Is_Error($Result))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
print_r($Result);
#-------------------------------------------------------------------------------


?>
