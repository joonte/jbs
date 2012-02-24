{extends file='base.tpl'}

{block name=title}{/block}

{block name=js}
<SCRIPT type="text/javascript" src="/styles/billing/Js/Pages/Administrator/GroupEdit.js"></SCRIPT>
{/block}

{block name=into}
<!-- Main Content -->
<TABLE class="ButtonsPanel" cellspacing="5">
    <TR>
        <TD>
            <BUTTON onclick="ShowWindow('/Administrator/GroupEdit2');" class="Standard"
                    onmouseover="PromptShow(event,'Новая группа',this);">
                <IMG alt="Новая группа" height="22" width="22" src="/styles/root/Images/Icons/Add.gif"/>
            </BUTTON>
        </TD>
        <TD class="Standard">Новая группа</TD>
        <TD>
            <BUTTON onclick="ShowWindow('/Administrator/GroupsMap');" class="Standard"
                    onmouseover="PromptShow(event,'Структура групп',this);">
                <IMG alt="Структура групп" height="22" width="22" src="/styles/billing/Images/Icons/Structure.gif"/>
            </BUTTON>
        </TD>
        <TD class="Standard">Структура групп</TD>
    </TR>
</TABLE>
{table name="Groups"}
<!-- End Main Content -->
{/block}