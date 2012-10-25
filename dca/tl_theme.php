<?php 

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package   CssSpriteGenerator 
 * @author    Richard Henkenjohann 
 * @license   LGPL 
 * @copyright Richard Henkenjohann 2012 
 */

/**
 * Table tl_theme
 */
// Config
$GLOBALS['TL_DCA']['tl_theme']['config']['onsubmit_callback'][] = array('tl_theme_spritegen', 'generateSprite');

// Palettes
$GLOBALS['TL_DCA']['tl_theme']['palettes']['__selector__'] = array('spritegen_enable');
$GLOBALS['TL_DCA']['tl_theme']['palettes']['default'] = str_replace(',vars', ',vars;{spritegen_legend},spritegen_enable', $GLOBALS['TL_DCA']['tl_theme']['palettes']['default']);

// Subpalettes
$GLOBALS['TL_DCA']['tl_theme']['subpalettes']['spritegen_enable'] = 'spritegen_source,spritegen_output_folder,spritegen_output_file,spritegen_direction,spritegen_output_width';

// Fields
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_enable']		= array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_enable'],
	'exclude'					=> true,
	'inputType'					=> 'checkbox',
	'eval'						=> array('submitOnChange' => true),
	'sql'						=> "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_source']		= array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_source'],
	'exclude'					=> true,
	'inputType'					=> 'fileTree',
	'foreignKey'				=> 'tl_page.title',
	'eval'						=> array('fieldType'=>'radio', 'tl_class'=>'long', 'mandatory'=>true),
	'sql'						=> "blob NULL"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_output_folder']	= array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_folder'],
	'exclude'					=> true,
	'inputType'					=> 'fileTree',
	'foreignKey'				=> 'tl_page.title',
	'eval'						=> array('fieldType'=>'radio', 'tl_class'=>'long', 'mandatory'=>true),
	'sql'						=> "blob NULL"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_output_file']	= array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_file'],
	'exclude'					=> true,
	'inputType'					=> 'text',
	'eval'						=> array('tl_class'=>'w50', 'mandatory'=>true),
	'sql'						=> "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_direction']		= array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_direction'],
	'exclude'					=> true,
	'inputType'					=> 'select',
	'options'					=> array(0, 1),
	'eval'						=> array('mandatory'=>true, 'tl_class'=>'w50'),
	'sql'						=> "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_output_width']	= array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_width'],
	'exclude'					=> true,
	'inputType'					=> 'checkbox',
	'eval'						=> array('tl_class' => 'w50'),
	'sql'						=> "char(1) NOT NULL default ''"
);

/**
 * Class tl_theme_spritegen
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Richard Henkenjohann 2012
 * @author     Richard Henkenjohann
 * @package    Core
 */
class tl_theme_spritegen extends Backend
{

	/**
	 * Initialize the system
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Generate sprite from given data
	 */
	public function generateSprite(\DataContainer $row)
	{
		// Get the theme meta data
		$objTheme = $this->Database->prepare("SELECT * FROM tl_theme WHERE id=?")
								   ->limit(1)
								   ->execute($row->id);

		if ($objTheme->numRows < 1)
		{
			return;
		}

		if ($objTheme->spritegen_enable != '')
		{
			// Replace the numeric folder IDs
			$objFolder			= FilesModel::findByPk($objTheme->spritegen_source);
			$objOutputFolder	= FilesModel::findByPk($objTheme->spritegen_output_folder);

			if ($objFolder !== null)
			{
				$input		= TL_ROOT . '/' . $objFolder->path;
				$output		= TL_ROOT . '/' . $objOutputFolder->path;

				ini_set('memory_limit', '256M');

				// Provide settings for \SpriteGen()
				$cssSprites = new \SpriteGen();
				$cssSprites->addImageFolder($input, $objTheme->spritegen_output_file);
				$cssSprites->setOutputFolder($output);
				$cssSprites->setCacheTime(0);
				// Generate Sprite
				$cssSprites->generateSprite($objTheme->spritegen_direction, $objTheme->spritegen_output_file, $objTheme->spritegen_output_width);
				
					// Display success confirmation
					\Message::addConfirmation($GLOBALS['TL_LANG']['MSC']['spritegen_successful']);
					$this->log('Generated image and style sheet for sprite ' . $objTheme->spritegen_output_file, 'SpriteGen generateSprite()', CRON);
			}
		}
	}
}
