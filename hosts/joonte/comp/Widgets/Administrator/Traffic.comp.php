
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Server = Array('Address'=>'dcbilling.mnogobyte.ru','Charset'=>'CP1251','Host'=>'dcbilling.mnogobyte.ru');
#-------------------------------------------------------------------------------
$Answer = Http_Send('/index.php',$Server);
if(Is_Error($Answer))
  return FALSE;
#-------------------------------------------------------------------------------
$Heads = Explode("\r\n",$Answer['Heads']);
#-------------------------------------------------------------------------------
$Cookie = 'none';
#-------------------------------------------------------------------------------
foreach($Heads as $Head){
  #-----------------------------------------------------------------------------
  if(Preg_Match('/^Set\-Cookie:\sPHPSESSID=([a-zA-Z0-9]+)/',$Head,$Matches)){
    #---------------------------------------------------------------------------
    $Cookie = Next($Matches);
    #---------------------------------------------------------------------------
    break;
  }
}
#-------------------------------------------------------------------------------
$Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5));
#-------------------------------------------------------------------------------
for($i=1;$i<=Max(1,Date('j') - 1);$i++){
  #-----------------------------------------------------------------------------
  $Answer = Http_Send('/login.php',$Server,Array(),Array('customer'=>'joonte','login'=>'joonte','password'=>'rtns57mn','submit'=>'login'),Array(SPrintF('Cookie: PHPSESSID=%s',$Cookie)));
  if(Is_Error($Answer))
    return FALSE;
  #-----------------------------------------------------------------------------
  $Answer = Http_Send('/index.php',$Server,Array('ed'=>'traf','ip'=>53,'from_date'=>Date(SPrintF('Y-m-%02u',$i)),'to_date'=>Date(SPrintF('Y-m-%02u',$i)),'Submit'=>'show'),Array(),Array(SPrintF('Cookie: PHPSESSID=%s',$Cookie)));
  if(Is_Error($Answer))
    return FALSE;
  #-----------------------------------------------------------------------------
  $Body = $Answer['Body'];
  #-----------------------------------------------------------------------------
  $Table->AddChild(new Tag('TR',new Tag('TD',Array('colspan'=>2,'class'=>'Separator'),Date(SPrintF('Y-m-%02u',$i)))));
  #-----------------------------------------------------------------------------
  if(Preg_Match("/\<td\>Суммарный\sстатистический\,\sГб\:\<\/td\>[\s\n\r]+\<td\>([0-9\,]+)<\/td\>[\s\n\r]+\<td\>([0-9\,]+)<\/td\>[\s\n\r]+\<td\>([0-9\,]+)<\/td\>[\s\n\r]+\<td\>([0-9\,]+)<\/td\>/siU",$Body,$Matches)){
    #---------------------------------------------------------------------------
    Array_Shift($Matches);
    #---------------------------------------------------------------------------
    $Matches = Array_Combine(Array('Суммарный исходящий','Суммарный входящий','Зарубежный исходящий','Зарубежный входящий'),$Matches);
    #---------------------------------------------------------------------------
    foreach($Matches as $Key=>$Value){
      #-------------------------------------------------------------------------
      $Tr = new Tag('TR',new Tag('TD',Array('class'=>'Standard'),$Key),new Tag('TD',Array('class'=>'Standard','style'=>SPrintF('background-color:#%s;',(double)Str_Replace(',','.',$Value) > 6?'FF0000':'FFFFFF')),$Value));
      #-------------------------------------------------------------------------
      $Table->AddChild($Tr);
    }
  }
}
#-------------------------------------------------------------------------------
return Array('Title'=>'Стастистика трафика','DOM'=>new Tag('DIV',Array('style'=>'overflow:scroll;overflow-x:auto;height:200px;padding-right:5px;'),$Table));
#-------------------------------------------------------------------------------

