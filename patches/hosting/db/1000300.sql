DELETE FROM `Clauses` WHERE `Partition` = 'Header:/Administrator/Update';

-- SEPARATOR

DELETE FROM `Clauses` WHERE `Partition` = 'Header:/Administrator/UpdateSystem';

-- SEPARATOR

INSERT INTO `Clauses` (`GroupID`,`AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(9,100, 100, 'Header:/Administrator/UpdateSystem', 'Инструкция по обновлению биллинга', 'yes', 'yes', 'no', 'yes', '<ul class="Standard">\n<li>Продукт предоставляется &quot;как есть&quot;.\n</li>\n<li>Все обновления вы выполняете на свой страх и риск - если у вас всё работает, и функционал вас устраивает — ничего не обновляйте.\n</li>\n<li>Разработчики не несут ответственности за простой вашей системы, и за недополученную прибыль.\n</li>\n<li>В случае любых проблем при обновлении, скопируйте вывод программы обновления (при необходимости, удалив из него пароли и прочую секретную информацию), и создайте тему на официальном <a href="http://forum.joonte.com/" target="_blank">форуме поддержки биллинговой системы</a>.\n</li>\n<li>Ознакомьтесь со <a href="http://forum.joonte.com/viewforum.php?f=12" target="_blank">списком изменений</a> в версии на которую обновляетесь, также, вполне возможно что для успешного обновления от вас потребуются какие-то действия &mdash; это будет описано там же, в теме посвящённой обновлению.</li>\n<li>Если вы пропустили какое-то обновление &mdash; будте готовы к проблемам. Обновления, конечно, последовательные, но &mdash; вероятность проблем возрастает.</li>\n<li><strong>Всегда</strong> делайте полную копию базы и всех файлов биллинга <strong>до</strong> обновления. </li></ul>');

-- SEPARATOR

DELETE FROM `Clauses` WHERE `Partition` = 'Header:/Administrator/ISPswOrders';

-- SEPARATOR

INSERT INTO `Clauses` (`GroupID`,`AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(9,100, 100, 'Header:/Administrator/ISPswOrders', 'Предупреждение для раздела софта от ISPsystem', 'no', 'yes', 'no', 'yes', 'Данная возможность является экспериментальной. Разработчики не дают никаких гарантий, что работа этого раздела будет корректной, и полностью функциональной.<br />\n    <br />\n    На данный момент, работает заказ &quot;внутренних&quot; лицензий, из расчёта что реально в ISPsystems заказываются вечные. Реализован пробный период (тариф с нулевой ценой без продления), смена тарифа, блокировка, удаление. До заказа лицензии ищется свободная по базе, если такая есть - используется она, а не новая.');

-- SEPARATOR

DELETE FROM `Clauses` WHERE `Partition` = 'Header:/Administrator/ISPswSchemes';

-- SEPARATOR

INSERT INTO `Clauses` (`GroupID`,`AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(9,100, 100, 'Header:/Administrator/ISPswSchemes', 'Тарифы на ПО ISPsystem', 'no', 'yes', 'no', 'yes', 'Даннный раздел используется для создания, удаления, изменения тарифных планов на программное обеспечение компании ISPsystem\n');

-- SEPARATOR

DELETE FROM `Clauses` WHERE `Partition` = 'Header:/Administrator/ISPswGroups';

-- SEPARATOR

INSERT INTO `Clauses` (`GroupID`,`AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(9,100, 100, 'Header:/Administrator/ISPswGroups', 'Группы ПО ISPsystem', 'no', 'yes', 'no', 'yes', 'Группы ПО используются для объединения тарифных планов.<br />\n    <br />\n    Например, для возможности создания триального заказа, и дальнейшего перехода на обычный тарифный план - оба тарифа необходимо объединить в одну группу.');







