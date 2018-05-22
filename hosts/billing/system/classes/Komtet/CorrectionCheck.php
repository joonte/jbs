<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

class CorrectionCheck
{
    const INTENT_SELL = 'sellCorrection';
    const INTENT_SELL_RETURN = 'sellReturnCorrection';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $intent;

    /**
     * @var string
     */
    private $printerNumber;

    /**
     * @var int
     */
    private $taxSystem;

    /**
     * @var Correction
     */
    private $correction;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var AuthorisedPerson
     */
    private $authorised_person;

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $intent One of CorrectionCheck::INTENT_* constants
     * @param string $printerNumber Printer's serial number
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public function __construct($id, $intent, $printerNumber, $taxSystem, Correction $correction)
    {
        $this->id = $id;
        $this->intent = $intent;
        $this->printerNumber = $printerNumber;
        $this->taxSystem = $taxSystem;
        $this->correction = $correction;
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $printerNumber Printer's serial number
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public static function createSell($id, $printerNumber, $taxSystem, Correction $correction)
    {
        return new static($id, static::INTENT_SELL, $printerNumber, $taxSystem, $correction);
    }

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $printerNumber Printer's serial number
     * @param int $taxSystem One of TaxSystem::* constants
     * @param Correction $correction Correction data
     *
     * @return CorrectionCheck
     */
    public static function createSellReturn($id, $printerNumber, $taxSystem, Correction $correction)
    {
        return new static($id, static::INTENT_SELL_RETURN, $printerNumber, $taxSystem, $correction);
    }

    /**
     * @param Payment $payment
     * @param Vat $vat
     *
     * @return CorrectionCheck
     */
    public function setPayment(Payment $payment, Vat $vat)
    {
        $sum = $payment->getSum();
        $this->payment = $payment;
        $this->position = [
            'name' => $this->intent == static::INTENT_SELL
                ? 'Коррекция прихода'
                : 'Коррекция расхода',
            'price' => $sum,
            'quantity' => 1,
            'total' => $sum,
            'vat' => $vat->getRate()
        ];

        return $this;
    }

    /**
     * @param AuthorisedPerson $authorised_person
     *
     * @return Check
     */
    public function setAuthorisedPerson(AuthorisedPerson $authorised_person)
    {
      $this->authorised_person = $authorised_person;

      return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'intent' => $this->intent,
            'task_id' => $this->id,
            'printer_number' => $this->printerNumber,
            'sno' => $this->taxSystem,
            'payments' => [$this->payment->asArray()],
            'positions' => [$this->position],
            'correction' => $this->correction->asArray(),
            'authorised_person' => $this->authorised_person->asArray()
        ];
    }
}
