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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_theme']['spritegen_legend']	= 'Bilder in CSS-Sprite zusammenfassen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_theme']['spritegen_enable'] = array('Enable the css sprite generator', 'The css sprite generator allows you to choose one folder with images that will combined.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_source'] = array('Source Folder with images', 'Choose the folder with the source images as its content.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_folder'] = array('Output path', 'Select the folder for the generated style sheet and image.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_file'] = array('Filename', 'Name the combined image. <strong>e.g.: sprite.png</strong>');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_direction'] = array('Image direction', 'Define whether the images should be placed among each other or side by side.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_width'] = array('Output width in CSS', 'Define whether the image size of each image should be written.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_modify_selectors'] = array('Define css selectors by own', 'Check if you want to define the css selectors by your own. So you can include the stylesheet in the layouts directly.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_selectors'] = array('CSS selectors', 'Define the css selectors as requested. "Reset" all fields by clearing the first row.');
// Options
$GLOBALS['TL_LANG']['tl_theme']['spritegen_direction']['options'] = array(0 => 'vertical', 1 => 'horizontal');
// MultiColumnWizard
$GLOBALS['TL_LANG']['tl_theme']['spritegen_mcw_image'] = 'Image';
$GLOBALS['TL_LANG']['tl_theme']['spritegen_mcw_selector'] = 'The CSS selector';
