<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

class Cashier
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int|float
     */
    private $inn;

    /**
     * @param string $name
     * @param string $inn
     *
     * @return Cashier
     */
    public function __construct($name, $inn)
    {
        $this->name = $name;
        $this->inn = $inn;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'name' => $this->name,
            'inn' => $this->inn
        ];
    }
}
