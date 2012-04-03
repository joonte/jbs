<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Joonte Software
 *
 */
class NotificationManager {
    public static function sendMsg(Msg $msg) {
        $Executor = Comp_Load('www/Administrator/API/ProfileCompile', Array('ProfileID' => 100));
        switch (ValueOf($Executor)) {
            case 'error':
                return ERROR | @Trigger_Error(500);
            case 'exception':
                # No more...
                break;
            case 'array':
                $msg->setParam('Executor', $Executor['Attribs']);

                break;
            default:
                return ERROR | @Trigger_Error(101);
        }

        $User = DB_Select('Users',Array('ID','Name','Sign','ICQ','Email','Mobile','JabberID','UniqID','IsNotifies'),Array('UNIQ','ID'=>$msg->getTo()));
        switch(ValueOf($User)) {
            case 'error':
              return ERROR | @Trigger_Error('[Email_Send]: не удалось выбрать получателя');
            case 'exception':
              return new gException('EMAIL_RECIPIENT_NOT_FOUND','Получатель письма не найден');
            case 'array':
              #-------------------------------------------------------------------------
              if(!$User['IsNotifies'])
                return new gException('NOTIFIES_RECIPIENT_DISABLED','Уведомления для получателя отключены');
              #-------------------------------------------------------------------------
              $msg->setParam('User', $User);

              break;
            default:
              return ERROR | @Trigger_Error(101);
        }
        #-------------------------------------------------------------------------
        $From = DB_Select('Users',Array('ID','Name','Sign','ICQ','Email','Mobile','UniqID'),Array('UNIQ','ID'=>$msg->getFrom()));
        #-------------------------------------------------------------------------
        switch(ValueOf($From)){
            case 'error':
              return ERROR | @Trigger_Error('[Email_Send]: не удалось выбрать отправителя');
            case 'exception':
              return new gException('EMAIL_SENDER_NOT_FOUND','Отправитель не найден');
            case 'array':
              #---------------------------------------------------------------------
              $msg->setParam('From', $From);

              break;
            default:
              return ERROR | @Trigger_Error(101);
        }

        $Config = Config();

        $Notifies = $Config['Notifies'];

        $sentMsgCnt = 0;
        foreach (Array_Keys($Notifies['Methods']) as $MethodID) {
            if (!$Notifies['Methods'][$MethodID]['IsActive'])
                continue;

            $Count = DB_Count('Notifies', Array('Where' => SPrintF("`UserID` = %u AND `MethodID` = '%s' AND `TypeID` = '%s'", $msg->getTo(), $MethodID, $msg->getTemplate())));
            if (Is_Error($Count))
                return ERROR | @Trigger_Error(500);

            if ($Count) {
                continue;
            }

            if (!class_exists($MethodID)) {
                return new gException('DISPATCHER_NOT_FOUND', 'Dispatcher not found: '.$MethodID);
            }

            $dispatcher = $MethodID::get();

            try {
                $dispatcher->send($msg);

                $sentMsgCnt++;
            } catch (jException $e) {
                Debug(SPrintF("Error while sending message [userId=%s, message=%s]", $User['ID'], $e->getMessage()));
            }
        }

        if ($sentMsgCnt < 1) {
            Debug("Couldn't send notify by any methods to user :" . $User['ID']);

            return new gException('USER_NOT_NOTIFIED','Не удалось оповестить пользователя ни одним из методов');
        }

        return TRUE;
    }
}
