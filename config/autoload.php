<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package CssSpriteGenerator
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'CssSprite',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Library
	'CssSprite\SpriteGen' => 'system/modules/cssspritegen/library/CssSprite/SpriteGen.php',
));
