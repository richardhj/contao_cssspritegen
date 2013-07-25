<?php 

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package   CssSpriteGenerator 
 * @author    Richard Henkenjohann 
 * @license   LGPL 
 * @copyright Richard Henkenjohann 2013
 */

/**
 * Table tl_theme
 */
// Config
$GLOBALS['TL_DCA']['tl_theme']['config']['onsubmit_callback'][] = array('tl_theme_spritegen', 'generateSprite');

// Palettes
$GLOBALS['TL_DCA']['tl_theme']['palettes']['__selector__'] = array('spritegen_enable', 'spritegen_modify_selectors');
$GLOBALS['TL_DCA']['tl_theme']['palettes']['default'] = str_replace
(
	',vars',
	',vars;{spritegen_legend},spritegen_enable',
	$GLOBALS['TL_DCA']['tl_theme']['palettes']['default']
);

// Subpalettes
$GLOBALS['TL_DCA']['tl_theme']['subpalettes']['spritegen_enable'] = 'spritegen_source,spritegen_output_folder,spritegen_output_file,spritegen_direction,spritegen_output_width,spritegen_modify_selectors';
$GLOBALS['TL_DCA']['tl_theme']['subpalettes']['spritegen_modify_selectors'] = 'spritegen_selectors';

// Fields
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_enable'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_enable'],
	'exclude'					=> true,
	'inputType'					=> 'checkbox',
	'eval'						=> array('submitOnChange' => true),
	'sql'						=> "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_source'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_source'],
	'exclude'					=> true,
	'inputType'					=> 'fileTree',
	'foreignKey'				=> 'tl_page.title',
	'eval'						=> array('fieldType'=>'radio', 'mandatory'=>true),
	'sql'						=> "blob NULL"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_output_folder'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_folder'],
	'exclude'					=> true,
	'inputType'					=> 'fileTree',
	'foreignKey'				=> 'tl_page.title',
	'eval'						=> array('fieldType'=>'radio', 'mandatory'=>true),
	'sql'						=> "blob NULL"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_output_file'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_file'],
	'exclude'					=> true,
	'inputType'					=> 'text',
	'eval'						=> array('tl_class'=>'w50', 'mandatory'=>true),
	'sql'						=> "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_direction'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_direction'],
	'exclude'					=> true,
	'inputType'					=> 'select',
	'options'					=> array(0, 1),
	'reference'                 => &$GLOBALS['TL_LANG']['tl_theme']['spritegen_direction']['options'],
	'eval'						=> array('mandatory'=>true, 'tl_class'=>'w50'),
	'sql'						=> "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_output_width'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_width'],
	'exclude'					=> true,
	'inputType'					=> 'checkbox',
	'eval'						=> array('tl_class' => 'w50'),
	'sql'						=> "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_modify_selectors'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_modify_selectors'],
	'exclude'					=> true,
	'inputType'					=> 'checkbox',
	'eval'						=> array('tl_class' => 'w50', 'submitOnChange' => true),
	'sql'						=> "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_theme']['fields']['spritegen_selectors'] = array(
	'label'						=> &$GLOBALS['TL_LANG']['tl_theme']['spritegen_selectors'],
	'exclude'					=> true,
	'inputType'					=> 'multiColumnWizard',
	'eval'						=> array
	(
		'columnFields' => array
		(
			'sg_css_image' => array
			(
				'label'         => &$GLOBALS['TL_LANG']['tl_theme']['spritegen_mcw_image'],
				'inputType'     => 'spritegen_image',
				'eval'          => array('valign' => 'center')
			),
			'sg_css_selector' => array
			(
				'label'         => &$GLOBALS['TL_LANG']['tl_theme']['spritegen_mcw_selector'],
				'inputType'     => 'text',
				'eval'          => array('style' => 'width:640px')
			)
		),
		'hideButtons' => true
	),
	'sql'						=> "blob NULL"
);

/**
 * Class tl_theme_spritegen
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Richard Henkenjohann 2013
 * @author     Richard Henkenjohann
 * @package    CssSpriteGenerator
 */
class tl_theme_spritegen extends MultiColumnWizard
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
								   ->execute($row->id);

		if ($objTheme->numRows < 1)
		{
			return;
		}

		if ($objTheme->spritegen_enable != false)
		{
			// Replace the numeric folder IDs
			$objInputFolder  = FilesModel::findByPk($objTheme->spritegen_source);
			$objOutputFolder = FilesModel::findByPk($objTheme->spritegen_output_folder);

			if ($objInputFolder !== null)
			{
				// Provide settings for SpriteGen()
				$cssSprites = new \SpriteGen();
				$cssSprites->addImageFolder(TL_ROOT . '/' . $objInputFolder->path);
				$cssSprites->setOutputFolder(TL_ROOT . '/' . $objOutputFolder->path);
				$cssSprites->setCacheTime(0);
				$cssSprites->useDatabase($objTheme->spritegen_modify_selectors, unserialize($objTheme->spritegen_selectors));
				// Generate Sprite
				$cssSprites->generateSprite($objTheme->spritegen_output_file, $objTheme->id, true, $objTheme->spritegen_direction, $objTheme->spritegen_output_width);

				// Display success confirmation
				\Message::addConfirmation($GLOBALS['TL_LANG']['MSC']['spritegen_successful']);
				$this->log('Generated image and style sheet for sprite ' . $objTheme->spritegen_output_file, __METHOD__, CRON);
			}
		}
	}
}
