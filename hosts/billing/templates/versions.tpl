{extends file='base.tpl'}

{block name=title}Версии и изменения{/block}

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

{block name=menuLeft}
    {menu_left MenuPath='Site'}
{/block}

{block name=into}
<ul id="versions">
{foreach from=$versions item=version}
  {if $version->released}
      <li>
          {$version->name}

          <div style="float: right; padding-top:5px;margin: 5px;font-size: 14px">
            <a href="http://jira.joonte.com/browse/JBS/fixforversion/{$version->id}">История изменений</a>
          </div>
          <div style="font-size:11px; color:#848484;">
            <cname>Дата выпуска: {$version->releaseDate|date_format: "%d.%m.%Y"}</cname>
          </div>
      </li>
  {/if}
{/foreach}
</ul>
{/block}