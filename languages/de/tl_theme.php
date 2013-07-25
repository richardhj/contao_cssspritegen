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
$GLOBALS['TL_LANG']['tl_theme']['spritegen_enable'] = array('Den CSS-Sprite-Generator aktivieren', 'Wenn Sie den Sprite-Generator aktivieren, haben Sie die Möglichkeit, einen Ordner mit Dateien auszuwählen, die zu einem CSS-Sprite zusammengefasst werden sollen.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_source'] = array('Ordner mit einzelnen Bildern auswählen', 'Wählen Sie den Ordner aus, in dem sich die Bilder befinden, die zusammengefasst werden sollen.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_folder'] = array('Pfad für Ausgabe des Bildes', 'Geben Sie an, wo das zusammengefasste Bild gespeichert werden soll.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_file'] = array('Dateiname für das Bild', 'Benennen Sie das zusammengefasste Bild. <strong>Bsp.: sprite.png</strong>');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_direction'] = array('Anordnung der Bilder', 'Legen Sie fest, ob die Bilder nebeneinander oder untereinander platziert werden sollen.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_output_width'] = array('Breite im CSS ausgeben', 'Bestimmen Sie, ob im generierten CSS die Breite der einzelnen Bilder ausgegeben werden sollen.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_modify_selectors'] = array('CSS-Selektoren selbst bestimmen', 'Legen Sie die CSS-Selektoren bei Bedarf selbst fest, damit Sie das Stylsesheet direkt ins Layout einbinden können.');
$GLOBALS['TL_LANG']['tl_theme']['spritegen_selectors'] = array('CSS-Selektoren', 'Hier legen Sie die CSS-Selektoren nach Bedarf fest. Ein "Reset" ist durch das Leeren des ersten Feldes möglich.');
// Options
$GLOBALS['TL_LANG']['tl_theme']['spritegen_direction']['options'] = array(0 => 'vertikal', 1 => 'horizontal');
// MultiColumnWizard
$GLOBALS['TL_LANG']['tl_theme']['spritegen_mcw_image'] = 'Bild';
$GLOBALS['TL_LANG']['tl_theme']['spritegen_mcw_selector'] = 'Der CSS-Selektor';
