{extends file='base.tpl'}

{block name=title}Вход в биллинговую систему{/block}

{block name=js}
  <SCRIPT type="text/javascript" src="/styles/billing/Js/Logon.js"></SCRIPT>
  <SCRIPT type="text/javascript">
      function TopPanelLogon(){
        Logon(document.getElementById('TopPanelEmail').value,
              document.getElementById('TopPanelPassword').value,
              document.getElementById('TopPanelIsRemember').checked);
      }
  </SCRIPT>
{/block}

{block name=into}
<!-- Login Form. -->
<FORM onsubmit="return false;">
<TABLE class="Standard" cellspacing="5">
 <TBODY>
  <TR>
   <TD class="Comment" valign="bottom">E-mail:</TD>
   <TD>
    <INPUT onmouseover="PromptShow(event, 'Электронный адрес должен состоять из букв латинского алфавитов, цифр и быть стандартного формата user@domain.&lt;BR /&gt;&lt;B&gt;Например:&lt;/B&gt; ivanov@mail.ru',this);" name="Email" onclick="" type="text" class="InputField" value="" />
   </TD>
  </TR>
  <TR>
   <TD class="Comment" valign="bottom">Пароль:</TD>
   <TD>
    <INPUT onmouseover="PromptShow(event, 'Пароль должен содержать не менее 6 и не более 15 символов, состоять из букв латинского алфавита и цифр.',this);" name="Password" onkeydown="if(IsEnter(event)) Logon(form.Email.value,form.Password.value,form.IsRemember.checked);" type="password" class="InputField" />
   </TD>
  </TR>
 </TBODY>
 <TR>
  <TD colspan="2">
   <TABLE cellspacing="0" cellpadding="0" align="right">
    <TR>
     <TD>запомнить меня</TD>
     <TD style="padding: 0px 5px 0px 5px;">
      <INPUT type="checkbox" name="IsRemember" />
     </TD>
     <TD>
      <INPUT type="button" onclick="Logon(form.Email.value, form.Password.value, form.IsRemember.checked);" value="Войти" />
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
</TABLE>
</FORM>
<!-- End Login Form. -->
{/block}