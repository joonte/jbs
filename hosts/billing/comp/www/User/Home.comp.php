<?php


#-------------------------------------------------------------------------------
Header(SPrintF('Location: %s',Str_Replace('/User/Home','/Home',@$_SERVER['REQUEST_URI'])));
#-------------------------------------------------------------------------------
return NULL;
#-------------------------------------------------------------------------------

?>
