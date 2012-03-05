{extends file='base.tpl'}

{block name=title}{$Title|default:'$Title'}{/block}

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

{block name=css}
<LINK href="/styles/license/Css/Standard.css" rel="stylesheet" type="text/css" />
{/block}

{block name=menuLeft}
    {menu_left MenuPath='Site'}
{/block}

{block name=into}
    {$Into}
{/block}