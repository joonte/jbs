<!-- Left Menu. -->
<TD align="right" valign="top" width="180">
<TABLE class="MenuLeft" cellspacing="0" cellpadding="0" width="100%">
  <TR>
    <TD>
      <TABLE border="0" cellspacing="0" cellpadding="0" width="100%">
        {foreach $items as $item}
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
          {assign var="leftPostfix" value="{$postfix}Pick"}
        {else}
          {assign "leftPostfix" $postfix}
        {/if}
        <TR>
          <TD>
              <TABLE border="0" height="100%" width="100%" cellspacing="0" cellpadding="0">
               <TR>
                <TD>
                 <TABLE border="0" width="100%" height="100%" cellspacing="0" cellpadding="0">
                  <TR>
                   <TD height="5" width="10" style="background-image:url(/styles/billing/Images/MenuLeftLeft{$leftPostfix}.png);"></TD>
                   <TD rowspan="2" id="MenuLeftCenter" style="padding-top:5px;padding-bottom:5px;background-image:url(/styles/billing/Images/MenuLeftCenter{$postfix}.png);">
                    {if isset($item.Href) && $item.Href}
                        <A class="MenuLeft" href="{$item.Href}">{$item.Text}</A>
                    {else}
                        <SPAN class="MenuLeft">{$item.Text}</SPAN>
                    {/if}
                   </TD>
                  </TR>
                  <TR>
                   <TD height="5" style="background-image:url(/styles/billing/Images/MenuLeftLeft{$leftPostfix}.png);background-position:left bottom;"></TD>
                  </TR>
                 </TABLE>
                </TD>
               </TR>
              </TABLE>
          </TD>
        </TR>
        {/foreach}
      </TABLE>
    </TD>
    <TD width="2" style="background-image:url(/styles/billing/Images/MenuLeftLine.png);"></TD>
  </TR>
</TABLE>
</TD>