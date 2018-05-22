<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

/**
* Агент по предмету расчета
*/
class Agent
{
    /**
     * Оказание услуг покупателю (клиенту) пользователем, являющимся банковским платежным агентом
     * банковским платежным агентом
     */
    const BANK_PAYMENT_AGENT = 'bank_payment_agent';

    /**
     * Оказание услуг покупателю (клиенту) пользователем, являющимся банковским платежным агентом
     * банковским платежным субагентом
     */
    const BANK_PAYMENT_SUBAGENT = 'bank_payment_subagent';

    /**
     * Оказание услуг покупателю (клиенту) пользователем, являющимся платежным агентом
     */
    const PAYMENT_AGENT = 'payment_agent';

    /**
     * Оказание услуг покупателю (клиенту) пользователем, являющимся платежным субагентом
     */
    const PAYMENT_SUBAGENT = 'payment_subagent';

    /**
     * Осуществление расчета с покупателем (клиентом) пользователем, являющимся поверенным
     */
    const SOLICITOR = 'solicitor';

    /**
     * Осуществление расчета с покупателем (клиентом) пользователем, являющимся комиссионером
     */
    const COMMISSIONAIRE = 'commissionaire';

    /**
     * Осуществление расчета с покупателем (клиентом) пользователем, являющимся агентом и не
     * являющимся банковским платежным агентом (субагентом), платежным агентом (субагентом),
     * поверенным, комиссионером
     */
    const AGENT = 'agent';

    /**
     * @var string
     */
    private $agent_type;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $inn;


    public function __construct($agent_type, $phone, $name, $inn)
    {
        $this->agent_type = $agent_type;
        $this->phone = $phone;
        $this->name = $name;
        $this->inn = $inn;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'agent_type' => $this->agent_type,
            'phone' => $this->phone,
            'name' => $this->name,
            'inn' => $this->inn
        ];
    }
}
