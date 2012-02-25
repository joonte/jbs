{* @author Великодный В.В. (Joonte Ltd.) *}
{extends file='TopPanel/TopPanelBase.tpl'}
{block name=TopPanelMenu}
<!-- User's Top Panel. -->
<SCRIPT type="text/javascript" src="/styles/billing/Js/Events.js"></SCRIPT>
<SCRIPT type="text/javascript">
    $(document).ready(function() {

    });
</SCRIPT>
<SCRIPT type="text/javascript" src="/styles/billing/Js/Logout.js"></SCRIPT>

<TR>
{foreach $items as $item}
    <TD valign="bottom">
        {if isset($item.IsActive) && $item.IsActive}
          {assign "postfix" "Active"}
        {elseif isset($item.IsActive) && !$item.IsActive}
          {assign "postfix" "UnActive"}
        {/if}

        {if isset($item.IsActive) && !$item.IsActive && isset($item.Pick)}
          {assign "picked" 1}
        {else}
          {assign "picked" 0}
        {/if}

        {if $picked}
          {assign "centerPostfix" "{$postfix}Pick"}
        {else}
          {assign "centerPostfix" $postfix}
        {/if}

        <TABLE id="TopPanel" cellspacing="0" cellpadding="0">
          <TR>
            <TD width="10">
              <IMG id="TopPanelTabLeft" style="display:block;" width="10" height="25" src="/styles/billing/Images/TopPanelTabLeft{$postfix}.png" />
            </TD>
            <TD id="TopPanelTabCenter" style="white-space:nowrap; background-image:url(/styles/billing/Images/TopPanelTabCenter{$centerPostfix}.png);">
                {if $picked}
                  <A class="TopPanelPick" href="{$item.Href}">{$item.Text}</A>
                {else}
                  <A class="TopPanel" href="{$item.Href}">{$item.Text}</A>
                {/if}
            </TD>
            <TD width="10">
              <IMG id="TopPanelTabRight" style="display:block;" width="10" height="25" src="/styles/billing/Images/TopPanelTabRight{$postfix}.png" />
            </TD>
          </TR>
        </TABLE>
    </TD>
{/foreach}
    <TD valign="bottom">
      <TABLE height="25">
        <TR>
          <TD>
            <A class="Button" title="Мои настройки" href="javascript:ShowWindow('/UserPersonalDataChange');">Мои настройки</A>
          </TD>
          <TD class="TopPanelSeparator" align="center" width="5" style="color:#848484;">|</TD>
          <TD>
            <A class="Button" title="Выход из системы" href="javascript:ShowConfirm('Вы действительно хотите выйти из системы?','Logout();');">Выход</A>
          </TD>
        </TR>
      </TABLE>
    </TD>
</TR>
{/block}

{block name=UserList}
<DIV style="font-size:12px;color:#505050;padding-left:5px;">
  <SPAN>Пользователь:</SPAN>
  <SPAN>
    <SPAN style="font-weight:bold;text-decoration:underline;color:#6F9006;">{$userName}</SPAN>
  </SPAN>
</DIV>
{/block}