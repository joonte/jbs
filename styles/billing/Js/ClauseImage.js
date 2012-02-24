//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function ClauseImageShow($ImageID,$Object){
  //----------------------------------------------------------------------------
  var $ClauseImage = document.getElementById('ClauseImage');
  //----------------------------------------------------------------------------
  if($ClauseImage)
    $ClauseImage.parentNode.removeChild($ClauseImage);
  //----------------------------------------------------------------------------
  var $ClauseImage = document.createElement('IMG');
  $ClauseImage.id = 'ClauseImage';
  $ClauseImage.onclick = ClauseImageDelete;
  $ClauseImage.onload = ClauseImageOnload;
  //----------------------------------------------------------------------------
  $ClauseImage.src = SPrintF('/ClauseImage?ImageID=%u&Scale=100',$ImageID);
  //----------------------------------------------------------------------------
  with($ClauseImage.style){
    //--------------------------------------------------------------------------
    position    = 'absolute';
    display     = 'none';
  }
  //----------------------------------------------------------------------------
  document.body.appendChild($ClauseImage);
  //----------------------------------------------------------------------------
  ShowProgress('Загрузка изображения');
}
//------------------------------------------------------------------------------
function ClauseImageOnload(){
  //----------------------------------------------------------------------------
  LockPage('ClauseImage');
  //----------------------------------------------------------------------------
  var $ClauseImage = document.getElementById('ClauseImage');
  //----------------------------------------------------------------------------
  with($ClauseImage.style){
    //--------------------------------------------------------------------------
    zIndex  =  GetMaxZIndex() + 1;
    display = 'block';
    //--------------------------------------------------------------------------
    var $Body = document.body;
    //--------------------------------------------------------------------------
    left = ($Body.clientWidth - $ClauseImage.offsetWidth)/2;
    top  = ($Body.clientHeight - $ClauseImage.offsetHeight)/2 + $Body.scrollTop;
  }
  //----------------------------------------------------------------------------
  HideProgress();
}
//------------------------------------------------------------------------------
function ClauseImageDelete(){
  //----------------------------------------------------------------------------
  UnLockPage('ClauseImage');
  //----------------------------------------------------------------------------
  var $ClauseImage = document.getElementById('ClauseImage');
  //----------------------------------------------------------------------------
  $ClauseImage.parentNode.removeChild($ClauseImage);
}