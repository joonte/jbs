<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once dirname(__FILE__)."/../Graph.class.php";

/**
 * Common font characteristics and methods.
 * Declared abstract only so that it can't be instanciated.
 * Users have to call 'new awPHPFont' or 'new awFileFont',
 * or any of their inherited classes (awFont1, awTuffy, awTTFFont, etc.)
 *
 * @package Artichow
 */
abstract class awFont {

	/**
	 * Build the font
	 *
	 */
	public function __construct() {
		
	}

	/**
	 * Draw a text
	 *
	 * @param awDriver $driver
	 * @param awPoint $p Draw text at this point
	 * @param awText $text The text
	 * @param int $width Text box width
	 */
	public function draw(awDriver $driver, awPoint $point, awText $text, $width = NULL) {
		
		$driver->string($this, $text, $point, $width);
		
	}

}

registerClass('Font', TRUE);

/**
 * Class for fonts that cannot be transformed,
 * like the built-in PHP fonts for example.
 * 
 * @package Artichow
 */
class awPHPFont extends awFont {
	
	/**
	 * The used font identifier
	 * 
	 * @var int
	 */
	public $font;
	
	public function __construct($font = NULL) {
		parent::__construct();
		
		if($font !== NULL) {
			$this->font = (int)$font;
		}
	}
	
}

registerClass('PHPFont');

/**
 * Class for fonts that can be transformed (rotated, skewed, etc.),
 * like TTF or FDB fonts for example.
 *
 * @package Artichow
 */
class awFileFont extends awFont {
	
	/**
	 * The name of the font, without the extension
	 *
	 * @var string
	 */
	public $name;
	
	/**
	 * The size of the font
	 *
	 * @var int
	 */
	public $size;
	
	/**
	 * The font filename extension
	 * 
	 * @var string
	 */
	protected $extension;
	
	public function __construct($name, $size) {
		parent::__construct();
		
		$this->name = (string)$name;
		$this->size = (int)$size;
	}
	
	/**
	 * Set the extension, without the dot
	 *
	 * @param string $extension
	 */
	public function setExtension($extension) {
		$this->extension = (string)$extension;
	}
	
	/**
	 * Get the filename extension for that font
	 * 
	 * @return string
	 */
	public function getExtension() {
		return $this->extension;
	}

}

registerClass('FileFont');

/**
 * Class representing TTF fonts
 * 
 * @package Artichow
 */
class awTTFFont extends awFileFont {
	
	public function __construct($name, $size) {
		parent::__construct($name, $size);
		
		$this->extension = 'ttf';
	}

}

registerClass('TTFFont');

/**
 * Class representing FDB fonts (used with the Ming driver)
 * 
 * @package Artichow
 */
class awFDBFont extends awFileFont {
	
	public function __construct($name, $size) {
		parent::__construct($name, $size);
		
		$this->extension = 'fdb';
	}
	
}

registerClass('FDBFont');



$php = '';

for($i = 1; $i <= 5; $i++) {

	$php .= '
	class awFont'.$i.' extends awPHPFont {

		public function __construct() {
			parent::__construct('.$i.');
		}

	}
	';

	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.'Font'.$i.' extends awFont'.$i.' {
		}
		';
	}

}

eval($php);

$php = '';

foreach($fonts as $font) {

	$php .= '
	class aw'.$font.' extends awFileFont {

		public function __construct($size) {
			parent::__construct(\''.$font.'\', $size);
		}

	}
	';

	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.$font.' extends aw'.$font.' {
		}
		';
	}

}

eval($php);



/*
 * Environment modification for GD2 and TTF fonts
 */
if(function_exists('putenv')) {
	putenv('GDFONTPATH='.ARTICHOW_FONT);
}

?>