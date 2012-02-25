{extends file='base.tpl'}

{block name=title}Вход в биллинговую систему1{/block}

{block name=menuLeft}
    {menu_left MenuPath='Site'}
{/block}

{block name=into}
<!-- Main Content -->
<TABLE class="Notice" cellspacing="5" align="center">
    <TR>
        <TD style="padding:5px;">Вы авторизованы в биллинговой системе. Для выхода из системы, нажмите кнопку [выход] на
            панели верхнего меню.
        </TD>
    </TR>
</TABLE>
<!-- End Main Content -->
{/block}