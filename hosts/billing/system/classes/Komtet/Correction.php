<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

class Correction
{
    const TYPE_SELF = 'self';
    const TYPE_FORCED = 'forced';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $document;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $type Correction type (Correction::TYPE_*)
     * @param string $date Document date (yyyy-mm-dd)
     * @param string $document Document number
     * @param string $description Description
     *
     * @return Correction
     */
    public function __construct($type, $date, $document, $description)
    {
        $this->type = $type;
        $this->date = $date;
        $this->document = $document;
        $this->description = $description;
    }

    /**
     * @param string $date Document date (yyyy-mm-dd)
     * @param string $document Document number
     * @param string $description Description
     *
     * @return Correction
     */
    public static function createSelf($date, $document, $description)
    {
        return new static(static::TYPE_SELF, $date, $document, $description);
    }

    /**
     * @param string $date Document date (yyyy-mm-dd)
     * @param string $document Document number
     * @param string $description Description
     *
     * @return Correction
     */
    public static function createForced($date, $document, $description)
    {
        return new static(static::TYPE_FORCED, $date, $document, $description);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'type' => $this->type,
            'date' => $this->date,
            'document' => $this->document,
            'description' => $this->description
        ];
    }
}
