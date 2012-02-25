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

{block name=css}
<LINK href="/styles/license/Css/Standard.css" rel="stylesheet" type="text/css" />
{/block}

{block name=menuLeft}
    {menu_left MenuPath='Site'}
{/block}

{block name=into}
<ul id="versions">
{foreach from=$versions item=version}
  {if $version->released}
    {if isset($latestVersion) && $version->name eq $latestVersion}
      <li class="current">
    {else}
      <li>
    {/if}
        {if $version->released}
          <div style="float: left; margin: 5px;">
            <img src="/styles/license/Images/Icons/Version/release.gif" />
          </div>
        {else}
          <div style="float: left; margin: 5px;">
            <img src="/styles/license/Images/Icons/Version/progress.gif" />
          </div>
        {/if}
          {$version->name}
          <div style="float: right; padding-top:5px;margin: 5px;font-size: 14px">
            <a href="http://jira.joonte.com/secure/IssueNavigator.jspa?reset=true&jqlQuery=project+%3D+JBS+AND+fixVersion+%3D+%22{$version->id}%22+AND+status+%3D+Resolved+ORDER+BY+priority+DESC&mode=hide">История изменений</a>
          </div>
          <div style="font-size:11px; color:#848484;">
            <cname>Дата выпуска: {$version->releaseDate|date_format: "%d.%m.%Y"}</cname>
          </div>
      </li>
 {/if}
{/foreach}
</ul>
{/block}