<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task', 'JabberID', 'Message', 'ID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#if(!$Theme)
$Theme = 'message theme is empty';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/Jabber]: отправка Jabber сообщения для (%s)', $JabberID));
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = $JabberID;
#-------------------------------------------------------------------------------
if (Is_Error(System_Load('classes/JabberClient.class.php')))
    return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
$Settings = $Config['JabberClient'];
#-------------------------------------------------------------------------------
$Links = &Links();
$LinkID = Md5('JabberClient');
#-------------------------------------------------------------------------------
if (!IsSet($Links[$LinkID])) {
    $Links[$LinkID] = NULL;
    $JabberClient = &$Links[$LinkID];

    $JabberClient = new Jabber(
        $Settings['Server'],
        $Settings['Port'],
        $Settings['JabberID'],
        $Settings['Password'],
        $Settings['UseSSL']
    );

    // TODO тут надо переделать, ошибки из функций не вернутся
    Debug("[comp/Tasks/Jabber]: " . $JabberClient->get_log());
    if (Is_Error($JabberClient))
        return ERROR | @Trigger_Error(500);

    $IsConnect = $JabberClient->connect();
    Debug("[comp/Tasks/Jabber]: " . $JabberClient->get_log());
    if (Is_Error($IsConnect))
        return ERROR | @Trigger_Error(500);

    $IsLogin = $JabberClient->login();
    Debug("[comp/Tasks/Jabber]: " . $JabberClient->get_log());
    if (Is_Error($IsLogin))
        return ERROR | @Trigger_Error(500);
}

$JabberClient = &$Links[$LinkID];

$IsMessage = $JabberClient->send_message($JabberID, $Message, $Theme);
if (Is_Error($IsMessage)) {
    UnSet($Links[$LinkID]);
    Debug("[comp/Tasks/Jabber]: error sending message, error is '" . $JabberClient->get_log() . "'");
    return 3600;
}
#-------------------------------------------------------------------------------
$Event = Array(
    'UserID' => $ID,
    'Text' => SPrintF('Сообщение для (%s) через службу Jabber успешно отправлено', $JabberID)
);
$Event = Comp_Load('Events/EventInsert', $Event);
if (!$Event)
    return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
