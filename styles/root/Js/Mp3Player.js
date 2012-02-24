//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function Mp3PlayerShow($Mp3Url){
  //----------------------------------------------------------------------------
  LockPage('Mp3Player');
  //----------------------------------------------------------------------------
  document.getElementById('Mp3PlayerInto').innerHTML = SPrintF('<EMBED align="align" quality="high" width="100%" height="100%" type="application/x-shockwave-flash" src="/styles/root/Swf/Mp3Player.swf?%s" />',$Mp3Url);
  //----------------------------------------------------------------------------
  var $Mp3Player = document.getElementById('Mp3Player');
  //----------------------------------------------------------------------------
  with($Mp3Player.style){
    //--------------------------------------------------------------------------
    display = 'block';
    zIndex  = GetMaxZIndex() + 1;
    //--------------------------------------------------------------------------
    var $Body = document.body;
    //--------------------------------------------------------------------------
    left = $Body.scrollLeft + ($Body.clientWidth  - $Mp3Player.offsetWidth)/2;
    top  = $Body.scrollTop  + ($Body.clientHeight - $Mp3Player.offsetHeight)/2;
  }
}
//------------------------------------------------------------------------------
function Mp3PlayerHide(){
  //----------------------------------------------------------------------------
  UnLockPage('Mp3Player');
  //----------------------------------------------------------------------------
  document.getElementById('Mp3PlayerInto').innerHTML = '';
  //----------------------------------------------------------------------------
  document.getElementById('Mp3Player').style.display = 'none';
}