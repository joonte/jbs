<?php
//------------------------------------------------------------------------------
$HTML = <<<EOD
<FORM action="http://domain/UserRegister" method="POST">
 <INPUT type="hidden" name="Eval" value="%s" />
 <INPUT type="submit" value="Go!" />
</FORM>
EOD;
//------------------------------------------------------------------------------
$DomainName = 'testdomain';
$DomainSchemeID = 1;
//------------------------------------------------------------------------------
$Eval = SPrintF("ShowWindow('/DomainOrder',[{Name:'DomainName',Value:'%s'},{Name:'DomainSchemeID',Value:%u}]);",$DomainName,$DomainSchemeID);
//------------------------------------------------------------------------------
echo SPrintF($HTML,Base64_Encode($Eval));
//------------------------------------------------------------------------------
?>