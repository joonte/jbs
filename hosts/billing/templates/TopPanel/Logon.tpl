{* @author Великодный В.В. (Joonte Ltd.) *}
{extends file='TopPanel/TopPanelBase.tpl'}
{block name=TopPanelMenu}
<!-- Logon Panel. -->
<TR>
<TD>
 <TABLE cellspacing="0" cellpadding="0">
  <TR>
    <TD>
     <TABLE cellspacing="0" cellpadding="0">
      <TR>
       <TD>Email:</TD>
       <TD width="10" />
       <TD width="10">
        <IMG src="/styles/billing/Images/TopPanelInputLeft.png" border="0" style="display:block;" width="15" height="25" />
       </TD>
       <TD style="background-image:url(/styles/billing/Images/TopPanelInputCenter.png);">
        <INPUT class="TopPanel" type="text" id="TopPanelEmail" size="20">{if isset($email)}{$email}{/if}</TD>
       </TD>
       <TD width="10">
        <IMG src="/styles/billing/Images/TopPanelInputRight.png" style="display:block;" width="15" height="25" />
       </TD>
      </TR>
     </TABLE>
    </TD>
    <TD width="10" />
    <TD>
     <TABLE cellspacing="0" cellpadding="0">
      <TR>
       <TD>Пароль:</TD>
       <TD width="10" />
       <TD width="10">
        <IMG src="/styles/billing/Images/TopPanelInputLeft.png" style="display:block;" width="15" height="25" />
       </TD>
       <TD style="background-image:url(/styles/billing/Images/TopPanelInputCenter.png);">
        <INPUT class="TopPanel" type="password" id="TopPanelPassword" size="10" onkeydown="if(event.keyCode == 13) TopPanelLogon();" />
       </TD>
       <TD width="10">
        <IMG src="/styles/billing/Images/TopPanelInputRight.png" style="display:block;" width="15" height="25" />
       </TD>
      </TR>
     </TABLE>
    </TD>
    <TD width="5" />
    <TD valign="middle">
     <INPUT type="checkbox" id="TopPanelIsRemember" />
    </TD>
    <TD valign="middle">
     <SPAN style="font-size:11px;">[запомнить]</SPAN>
    </TD>
    <TD width="5" />
    <TD>
     <TABLE cellspacing="0" cellpadding="0">
      <TR>
       <TD width="10">
        <IMG src="/styles/billing/Images/TopPanelButtonLeft.png" style="display:block;" width="15" height="25" />
       </TD>
       <TD style="background-image:url(/styles/billing/Images/TopPanelButtonCenter.png);">
        <A class="TopPanel" href="javascript:TopPanelLogon();">Вход</A>
        </TD>
       <TD width="10">
        <IMG src="/styles/billing/Images/TopPanelButtonRight.png" style="display:block;" width="15" height="25" />
       </TD>
      </TR>
     </TABLE>
    </TD>
    <TD width="20" align="center">
     <IMG src="/styles/billing/Images/TopPanelLine.png" width="1" height="20" />
    </TD>
    <TD>
     <A class="Image" href="/Rss/News">
      <IMG alt="Новости компании" border="0" src="/styles/billing/Images/Icons/TopPanelRss.gif" style="display:block;" width="32" height="32" />
     </A>
    </TD>
  </TR>
 </TABLE>
</TD>
</TR>
{/block}