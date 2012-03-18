{extends file='base.tpl'}

{block name=title}{$LANG.Notifies|default:'$LANG.Notifies'}{/block}

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
<LINK href="/styles/license/Css/Standard.css" rel="stylesheet" type="text/css"/>
{/block}

{block name=menuLeft}
    {menu_left MenuPath='Administrator/AddIns'}
{/block}

{block name=into}
<div>
    <b>{$LANG.NotifiesType|default:'$LANG.NotifiesType'}</b>
    {foreach $methods as $method}
        <a href="?Method={$method}">{$method}</a>
    {/foreach}
</div>
<table class="Standard" width="100%">
    {if isset($clauses)}
        {foreach $clauses as $clause}
            <tr>
                <td><input type="checkbox"></td>
                <td class="Standard"><a href="#" onclick="window.open('/Administrator/ClauseEdit?ClauseID={$clause.ID}','ClauseEdit',SPrintF('left=%u,top=%u,width=800,height=680,toolbar=0, scrollbars=1, location=0',(screen.width-800)/2,(screen.height-600)/2));">{$clause.Title}</a></td>
                <td class="Standard">включить</td>
            </tr>
        {/foreach}
        {else}
        <tr>
            <td>Нет шаблонов.</td>
        </tr>
    {/if}
</table>
{/block}