<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
interface Dispatcher {
    /**
     * Sent message by implemented transport.
     *
     * @param $message Message to send by dispatcher. Should be implement {@see Msg}
     *
     */
    public function send(Msg $message);
}
