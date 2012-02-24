

INSERT INTO `Clauses` (`AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(100, 100, 'Header:/Administrator/ISPswOrders', 'Предупреждение для раздела софта от ISPsystem', 'no', 'yes', 'no', 'yes', '<table class="Notice">\n <tbody>\n  <tr>\n   <td>\n    Данная возможность является экспериментальной. Разработчики не дают никаких гарантий, что работа этого раздела будет корректной, и полностью функциональной.<br />\n    <br />\n    На данный момент, работает заказ &quot;внутренних&quot; лицензий, из расчёта что реально в ISPsystems заказываются вечные. Реализован пробный период (тариф с нулевой ценой без продления), смена тарифа, блокировка, удаление. До заказа лицензии ищется свободная по базе, если такая есть - используется она, а не новая.<br />\n    <br />\n    <strong>Учтите, что при последующих обновлениях могут быть удалены [а скорей всего и будут - т.к. структура таблиц раздела будет меняться] какие-то данные из этого раздела, в том числе из его таблиц в БД.</strong>\n   </td>\n  </tr>\n </tbody>\n</table><br />');

-- SEPARATOR

INSERT INTO `Clauses` (`AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(100, 100, 'Header:/Administrator/ISPswSchemes', 'Тарифы на ПО ISPsystem', 'no', 'yes', 'no', 'yes', '<table class="Notice">\n <tbody>\n  <tr>\n   <td>\n    Даннный раздел используется для создания, удаления, изменения тарифных планов на программное обеспечение компании ISPsystem\n   </td>\n  </tr>\n </tbody>\n</table><br />');

-- SEPARATOR

INSERT INTO `Clauses` (`AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(100, 100, 'Header:/Administrator/ISPswGroups', 'Группы ПО ISPsystem', 'no', 'yes', 'no', 'yes', '<table class="Notice">\n <tbody>\n  <tr>\n   <td>\n    Группы ПО используются для объединения тарифных планов.<br />\n    <br />\n    Например, для возможности создания триального заказа, и дальнейшего перехода на обычный тарифный план - оба тарифа необходимо объединить в одну группу.\n   </td>\n  </tr>\n </tbody>\n</table><br />');




