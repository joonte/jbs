<?php
#-------------------------------------------------------------------------------
$Redirects = Array('/'=>'/Index');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Redirects) as $Location){
  #-----------------------------------------------------------------------------
  if($GLOBALS['__URI'] == $Location){
    #---------------------------------------------------------------------------
    Header(SPrintF('Location: %s',$Redirects[$Location]));
    #---------------------------------------------------------------------------
    Exit;
  }
}
#-------------------------------------------------------------------------------
# <!TMP
#-------------------------------------------------------------------------------
if(Preg_Match('/\/~[a-zA-Z0-9]/',$GLOBALS['__URI']))
  $GLOBALS['__URI'] = Str_Replace('/~','/API/',$GLOBALS['__URI']);
#-------------------------------------------------------------------------------
# TMP!>
#-------------------------------------------------------------------------------
?>