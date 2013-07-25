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

namespace CssSprite;


/**
 * Creates a single file that contains all the images as well as the related css.
 *
 * Usage:
 *
 *     $sprite = new SpriteGen();
 *     $sprite->addImageFolder($folder);
 *     $sprite->setOutputFolder($folder);
 *     $sprite->generateSprite($outputFileName, 1, true, false);
 *
 * @package   Library
 * @author    Richard Henkenjohann / originally from <http://www.christophs-blog.de/2011/02/php-klasse-csssprites>
 * @copyright Richard Henkenjohann 2012
 */

class SpriteGen extends \System
{

	/**
	 * Source folder
	 * @var array
	 */
	protected $arrFolders = array();

	/**
	 * Output folder
	 * @var string
	 */
	protected $outputFolder = '';

	/**
	 * Output stylesheet
	 * @var string
	 */
	protected $strStylesheet = '';

	/**
	 * Possibility to use existing css selectors
	 * @var bool
	 */
	protected $updateDatabase = false;

	/**
	 * Output css filename
	 * @var string
	 */
	protected $cssFilename = 'csssprite.css';

	/**
	 * Selector index in assoc array
	 * @var string
	 */
	protected $blobSelectorIndex = 'sg_css_selector';

	/**
	 * Image index in assoc array
	 * @var string
	 */
	protected $blobImageIndex = 'sg_css_image';

	/**
	 * Cache time in seconds
	 * @var int
	 */
	protected $iCacheTime = 60;


	/**
	 * Add folder with source images
	 * @param string $folder
	 */
	public function addImageFolder($folder)
	{
		if (substr($folder, -1) != '/')
		{
			$folder .= '/';
		}

		$this->arrFolders[] = $folder;
	}


	/**
	 * Set folder for image and stylesheet output
	 * @param string $folder
	 */
	public function setOutputFolder($folder)
	{
		if (substr($folder, -1) != '/')
		{
			$folder .= '/';
		}
		$this->outputFolder = $folder;
	}


	/**
	 * Set cache time in seconds (0 = no caching; <0 = never regeneration)
	 * @param int $time
	 */
	public function setCacheTime($time)
	{
		$this->iCacheTime = $time;
	}


	/**
	 * Set selectors if already existing and updates the database if desired
	 * @param bool $blnUseDatabase
	 * @param array $arrBlob
	 * @param bool $blnUpdateDatabase
	 */
	public function useDatabase($blnUseDatabase, $arrBlob, $blnUpdateDatabase = true)
	{
		if ($blnUseDatabase)
		{
			if ($arrBlob[0][$this->blobSelectorIndex])
			{
				$this->databaseBlob = $arrBlob;
			}

			$this->updateDatabase = $blnUpdateDatabase;
		}
	}


	/**
	 * Generate the image and stylesheet
	 * @param string $outputFileName
	 * @param int $themeId
	 * @param bool $useInAssetsFolderToo
	 * @param bool $directionX
	 * @param bool $outputWidth
	 */
	public function generateImage($outputFileName, $themeId = 0, $useInAssetsFolderToo = false, $directionX = true, $outputWidth = true)
	{
		$output = $this->outputFolder . $outputFileName;
		$bgImage = TL_FILES_URL . str_replace(TL_ROOT . '/', '', $output);

		$neededWidth = 0;
		$neededHeight = 0;

		// Walk each line and set size of new image
		foreach ($this->arrFolders as $folder)
		{
			if (!is_dir($folder))
			{
				$this->strStylesheet .= "/**\n * Source Folder is not valid!!!\n */";
				$this->saveCss();
				return;
			}

			if ($handle = opendir($folder))
			{
				while (false !== ($file = readdir($handle)))
				{
					if ($file == '.' || $file == '..' || is_dir($folder . $file))
					{
						continue;
					}
					try
					{
						$size = getimagesize($folder . $file);
					}
					catch (Exception $ex)
					{
						continue;
					}
					if ($directionX)
					{
						$neededWidth += $size[0];
						if ($size[1] > $neededHeight)
						{
							$neededHeight = $size[1];
						}
					}
					else
					{
						$neededHeight += $size[1];
						if ($size[0] > $neededWidth)
						{
							$neededWidth = $size[0];
						}
					}
				}

				closedir($handle);
			}
		}

		if ($neededWidth <= 0)
		{
			$this->strStylesheet .= "/**\n * Error while reading images, or no image found in directory!!!\n */";
			$this->saveCss();
			return;
		}

		// Create image
		$image = imagecreatetruecolor($neededWidth, $neededHeight);

		imagesavealpha($image, true);

		$white = imagecolorallocate($image, 255, 255, 255);
		$grey = imagecolorallocate($image, 128, 128, 128);
		$black = imagecolorallocate($image, 0, 0, 0);

		imagefilledrectangle($image, 0, 0, 150, 25, $black);
		$trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
		imagefill($image, 0, 0, $trans_colour);

		$this->strStylesheet .= '/* CSS sprite generator, generated ' . date('Y-m-d H:i') . " */\n";

		$currentX = 0;
		$currentY = 0;

		// Walk each line and create image as well as css chunks
		foreach ($this->arrFolders as $folder)
		{
			if ($handle = opendir($folder))
			{
				for ($i = 0; false !== ($file = readdir($handle)); $i++)
				{
					$filePath = $folder . $file;

					if ($file == '.' || $file == '..' || is_dir($filePath))
					{
						$i--;
						continue;
					}
					if (strtoupper(substr($filePath, -3)) == 'PNG')
					{
						$source = imagecreatefrompng($filePath);
					}
					elseif (strtoupper(substr($filePath, -3)) == 'JPG')
					{
						$source = imagecreatefromjpeg($filePath);
					}
					elseif (strtoupper(substr($filePath, -3)) == 'GIF')
					{
						$source = imagecreatefromgif($filePath);
					}
					else
					{
						$i--;
						continue;
					}

					$size = getimagesize($filePath);

					// Import CSS selecotrs or write new
					if ($this->databaseBlob[$i] !== null)
					{
						$cssSelector = utf8_decode_entities($this->databaseBlob[$i][$this->blobSelectorIndex]);
					}
					else
					{
						$cssSelector = '.' . str_replace(array('.', ' '), array('_', '_'), substr($file, 0, -4));
						$databaseBlob[$i][$this->blobSelectorIndex] = $cssSelector;
					}

					//imageAlphaBlending($source, false);
					//imageSaveAlpha($source, true);

					//imagecopymerge($image, $source, $currentX, 0, 0,0, $size[0], $size[1], 100);
					imagecopy($image, $source, $currentX, $currentY, 0, 0, $size[0], $size[1]);

					// Write CSS
					if ($outputWidth)
					{
						$this->strStylesheet .= $cssSelector . "\n{\n\tbackground: url(" . $bgImage . ") no-repeat;\n\tbackground-position: -" . $currentX . "px " . $currentY . "px;\n\twidth: " . $size[0] . "px;\n\theight: " . $size[1] . "px;\n}\n";
						$databaseBlob[$i][$this->blobSelectorIndex] = $cssSelector;
						$databaseBlob[$i][$this->blobImageIndex] = str_replace(TL_ROOT . '/', '', $filePath);
					}
					else
					{
						$this->strStylesheet .= $cssSelector . "\n{\n\tbackground: url(" . $bgImage . ") no-repeat;\n\tbackground-position: -" . $currentX . "px -" . $currentY . "px;\n}\n";
						$databaseBlob[$i][$this->blobSelectorIndex] = $cssSelector;
						$databaseBlob[$i][$this->blobImageIndex] = str_replace(TL_ROOT . '/', '', $filePath);
					}

					// Update current background position
					if ($directionX)
					{
						$currentX += $size[0];
					}
					else
					{
						$currentY += $size[1];
					}
				}

				closedir($handle);
			}
		}
		imagepng($image, $output);
		imagedestroy($image);

		if ($this->updateDatabase && $themeId > 0)
		{
			$this->updateDatabaseBlob($themeId, $databaseBlob);
		}

		$this->saveCss();

		if ($useInAssetsFolderToo)
		{
			$this->strStylesheet = str_replace(array('files/', "\n\t", "\n", ': ', ' */'), array('../../files/', '', '', ':', " */\n"), $this->strStylesheet);
			$this->cssFilename = str_replace('.css', '.assets.css', $this->cssFilename);
			$this->saveCss();
		}
	}


	/**
	 * Save the CSS file
	 */
	protected function saveCss()
	{
		$stylesheet = new \File(str_replace(TL_ROOT . '/', '', $this->outputFolder . $this->cssFilename));
		$stylesheet->write($this->strStylesheet);
		$stylesheet->close();
	}


	/**
	 * Set the image->selector relation in database
	 * @param $themeId
	 * @param $databaseBlob
	 */
	protected function updateDatabaseBlob($themeId, $databaseBlob)
	{
		\Database::getInstance()->prepare("UPDATE tl_theme SET spritegen_selectors=? WHERE id=?")
								->execute(serialize($databaseBlob), $themeId);
	}


	/**
	 * Generate sprite while handling the cache time
	 * @param string $outputFileName
	 * @param int $themeId
	 * @param bool $useInAssetsFolderToo
	 * @param bool $directionX
	 * @param bool $outputWidth
	 */
	public function generateSprite($outputFileName, $themeId = 0, $useInAssetsFolderToo = false, $directionX = true, $outputWidth = true)
	{
		if (!file_exists($this->outputFolder . $outputFileName))
		{
			if (!is_dir($this->outputFolder))
			{
				mkdir($this->outputFolder);
			}
			$this->generateImage($outputFileName, $themeId, $useInAssetsFolderToo, $directionX, $outputWidth);
		}
		else
		{
			if (filemtime($this->outputFolder . $outputFileName) <= (time() - $this->iCacheTime))
			{
				$this->generateImage($outputFileName, $themeId, $useInAssetsFolderToo, $directionX, $outputWidth);
			}
			else
			{
				$this->strStylesheet = file_get_contents($this->outputFolder . $this->cssFilename);
			}
		}
	}
}
