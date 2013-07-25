<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package Core
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace CssSprite;


/**
 * Class SpriteGenImageField
 *
 * Provide methods to display the spritegen image.
 * @copyright  Richard Henkenjohann 2013
 * @author     Richard Henkenjohann
 * @package    CssSpriteGenerator
 */
class SpriteGenImageField extends \Widget
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';


	/**
	 * Construct the widget
	 * @param array
	 */
	public function __construct($arrAttributes=null)
	{
		parent::__construct($arrAttributes);
	}



	/**
	 * Add specific settings
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'imageWidth':
				$this->imageWidth = $varValue;
				break;

			case 'imageHeight':
				$this->imageHeight = $varValue;
				break;

			case 'imageMode':
				$this->imageMode = $varValue;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}

	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$width = $this->imageWidth ?: 30;
		$height = $this->imageHeight ?: 20;
		$mode = $this->imageMode ?: 'center_center';

		$objImage = new \File($this->varValue);
		$objImage->close();

		// Return the image and a text field for database
		if ($objImage->width <= $width && $objImage->height <= $height)
		{
			return $this->generateImage($objImage->path, $this->strName) .
			       sprintf('<input type="text" name="%s" id="ctrl_%s" value="%s" style="display:none">',
							$this->strName,
							$this->strId,
							$objImage->path);
		}
		else
		{
			return $this->generateImage(\Image::get($objImage->path, $width, $height, $mode), $this->strName) .
			       sprintf('<input type="text" name="%s" id="ctrl_%s" value="%s" style="display:none">',
							$this->strName,
							$this->strId,
							$objImage->path);
		}
	}
}
