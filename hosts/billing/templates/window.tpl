{* Smarty *}
<TABLE id="Window" style="display:none;position:absolute;left:-1000;top:-1000;" cellspacing="0" cellpadding="0">
 <TR>
  <TD>
   <TABLE class="WindowHeader" width="100%" cellspacing="0" cellpadding="0">
    <TR>
     <TD class="WindowTitle" id="WindowTitle">{$title}</TD>
     <TD width="20">
      <BUTTON class="Transparent" title="Закрыть окно" onclick="HideWindow();">
       <IMG src="/styles/root/Images/Icons/Close.gif" width="15" height="15" alt="Закрыть" border="0" />
      </BUTTON>
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
 <TR>
  <TD id="WindowBody" style="padding-top: 5px; padding-right: 5px; padding-bottom: 5px; padding-left: 5px; width: 405px; height: 253px; ">
    <!-- Window Content -->
    {block name=into}{/block}
    <!-- End Window Content -->
  </TD>
 </TR>
</TABLE>