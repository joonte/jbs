//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
var $LiveInternets = document.getElementsByTagName('LIVEINTERNET');
//------------------------------------------------------------------------------
for($i=0;$i<$LiveInternets.length;$i++){
  //----------------------------------------------------------------------------
  $LiveInternet = $LiveInternets[$i];
  //----------------------------------------------------------------------------
  var $NoIndex = document.createElement('NOINDEX');
  //----------------------------------------------------------------------------
  var $A = document.createElement('A');
  $A.setAttribute('class','Image');
  $A.setAttribute('href','http://www.liveinternet.ru/click');
  $A.setAttribute('target','_blank');
  //----------------------------------------------------------------------------
  var $Img = document.createElement('IMG');
  $Img.setAttribute('style','margin:5');
  $Img.setAttribute('src','http://counter.yadro.ru/hit?'+$LiveInternet.getAttribute('skin')+';r'+escape(document.referrer)+((typeof(screen) == 'undefined')?'':';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?screen.colorDepth:screen.pixelDepth))+';u'+escape(document.URL)+';'+Math.random());
  $Img.setAttribute('title','Показано число просмотров и посетителей за 24 часа и за сегодн\я...');
  $Img.setAttribute('border',0);
  //----------------------------------------------------------------------------
  $A.appendChild($Img);
  //----------------------------------------------------------------------------
  $NoIndex.appendChild($A);
  //----------------------------------------------------------------------------
  $LiveInternet.parentNode.appendChild($NoIndex);
}
//------------------------------------------------------------------------------