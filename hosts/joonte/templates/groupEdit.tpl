{extends file='window.tpl'}

{block name=into}
<div style="height: 100%; width: 100%; overflow-y: scroll; overflow-x: auto; ">
    <form name="GroupEditForm" onsubmit="return false;">
        <table class="Standard" cellspacing="5">
            <tbody id="ID4f2c5f2ac124b">
            <tr>
                <td class="Comment" valign="bottom">Группа родитель</td>
                <td>
                    {html_options name=ParentID options=$groups selected=$selectedGroup}
                </td>
            </tr>
            <tr>
                <td class="Comment" valign="bottom">Название группы</td>
                <td>
                    <input type="text" name="Name" value="{$groupName}">
                </td>
            </tr>
            <tr>
                <td class="Comment" valign="bottom">Группа по умолчанию</td>
                <td>
                    <input type="checkbox" name="IsDefault" value="yes" {if isset($isDefault)}checked{/if}">
                </td>
            </tr>
            <tr>
                <td class="Comment" valign="bottom">Является отделом</td>
                <td>
                    <input type="checkbox" name="IsDepartment" value="yes" {if isset($isDepartment)}checked{/if}">
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td colspan="2" class="Separator">Комментарий</td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea name="Comment" style="width:100%;" rows="5">{$comment}</textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    {if isset($groupId)}
                        <input type="hidden" name="GroupID" value="{$groupId}">
                    {/if}
                    <input type="button" onclick="GroupEdit();" value="Сохранить">
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
{/block}