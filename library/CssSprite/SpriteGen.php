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
 *	addImageFolder($folder)		– Add folder that contains images
 *	setOutputFolder($folder)	– Set output folder for image and style sheet
 *	setCacheTime($time)			– Set time until image will be regenerate (0 = no caching, < 0 = never regeneration)
 *	generateSprite([true=horizontal], [filename.png], [true=display width in style sheet])	- Provide stylesheet
 *
 * @package   Library
 * @author    Richard Henkenjohann / originally from <http://www.christophs-blog.de/2011/02/php-klasse-csssprites>
 * @copyright Richard Henkenjohann 2012
 */

class SpriteGen
{

	private $aFolderImage		= array();
	private $sFolderOutput		= '';

	private $sStylesheet 		= '';

	private $sTempCssFilename	= 'csssprite.css';

	private $iCacheTime			= 60;
	
	public function addImageFolder($folder)
	{
		if(substr($folder, -1) != '/')
		{
			$folder .= '/';
		}
		$this->sFolderImage[] = $folder;
	}

	public function setOutputFolder($folder)
	{
		if(substr($folder, -1) != '/')
		{
			$folder .= '/';
		}
		$this->sFolderOutput = $folder;
	}

	public function setCacheTime($time)
	{
		$this->iCacheTime = $time;
	}

	public function generateImage($directionX=true, $outputFileName, $outputWidth=true)
	{
		$output			= $this->sFolderOutput . $outputFileName;
		$bgImage		= TL_FILES_URL . str_replace(TL_ROOT . '/', '', $output);

		$neededWidth	= 0;
		$neededHeight	= 0;

		foreach($this->sFolderImage as $folder)
		{
			if(!is_dir($folder))
			{
				$this->sStylesheet .= "/**\n * Source Folder is not valid!!!\n */";
				$this->saveCss();
				return;
			}

			if ($handle = opendir($folder))
			{
				while (false !== ($file = readdir($handle)))
				{
					if($file == '.' || $file == '..' || is_dir($folder . $file))
					{
						continue;
					}
					try
					{
						$size = getimagesize($folder . $file);
					}
					catch(Exception $ex)
					{
						continue;
					}
					if($directionX)
					{
						$neededWidth += $size[0];
						if($size[1] > $neededHeight)
						{
							$neededHeight = $size[1];
						}
					}
					else
					{							
						$neededHeight += $size[1];
						if($size[0] > $neededWidth)
						{
							$neededWidth = $size[0];
						}
					}
				}

				closedir($handle);
			}
		}
		
		if($neededWidth <= 0)
		{
			$this->sStylesheet .= "/**\n * Error while reading images, or no image found in directory!!!\n */";
			$this->saveCss();
			return;
		}

		$image = imagecreatetruecolor($neededWidth, $neededHeight);

		imagesavealpha($image, true);

		$white	= imagecolorallocate($image, 255, 255, 255);
		$grey	= imagecolorallocate($image, 128, 128, 128);
		$black	= imagecolorallocate($image, 0, 0, 0);

		imagefilledrectangle($image, 0, 0, 150, 25, $black);
		$trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
		imagefill($image, 0, 0, $trans_colour);

		$this->sStylesheet .= '/* CSS sprite generator, generated ' . date('Y-m-d H:i') . " */\n";

		$currentX = 0;
		$currentY = 0;

		foreach($this->sFolderImage as $folder)
		{
			if ($handle = opendir($folder))
			{
				while (false !== ($file = readdir($handle)))
				{
					if($file == '.' || $file == '..' || is_dir($folder . $file))
					{
						continue;
					}
					if(strtoupper(substr($folder . $file, -3)) == 'PNG')
					{
						$source = imagecreatefrompng($folder . $file);
					}
					else if(strtoupper(substr($folder . $file, -3)) == 'JPG')
					{
						$source = imagecreatefromjpeg($folder . $file);
					}
					else if(strtoupper(substr($folder . $file, -3)) == 'GIF')
					{
						$source = imagecreatefromgif($folder . $file);
					}
					else
					{
						continue;
					}

					$size		= getimagesize($folder . $file);
					$cssClass	= str_replace(substr($file, -4), '', $file);
										
					//imageAlphaBlending($source, false);
					//imageSaveAlpha($source, true);

					//imagecopymerge($image, $source, $currentX, 0, 0,0, $size[0], $size[1], 100);
					imagecopy($image, $source, $currentX, $currentY, 0, 0, $size[0], $size[1]);

					if($outputWidth)
					{
						$this->sStylesheet .= '.' . str_replace(array('.', ' '), array('_', '_'), $cssClass) . "{\n\tbackground: url(" . $bgImage . ") no-repeat;\n\tbackground-position: -" . $currentX . "px " . $currentY . "px;\n\twidth: " . $size[0] . "px;\n\theight: " . $size[1] . "px;\n}\n";
					}
					else
					{
						$this->sStylesheet .= '.' . str_replace(array('.', ' '), array('_', '_'), $cssClass) . "{\n\tbackground: url(" . $bgImage . ") no-repeat;\n\tbackground-position: -" . $currentX . "px -" . $currentY . "px;\n}\n";
					}

					if($directionX)
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

		$this->saveCss();
	}

	public function saveCss()
	{
		$output = $this->sFolderOutput . $this->sTempCssFilename;

		$fh = fopen($output, 'w') or die('can not open file');
		fwrite($fh, $this->sStylesheet);
		fclose($fh);
	}

	public function generateSprite($directionX=true, $outputFileName, $outputWidth=true)
	{
		if(!file_exists($this->sFolderOutput . $outputFileName))
		{
			if(!is_dir($this->sFolderOutput))
			{
				mkdir($this->sFolderOutput);
			}
			$this->generateImage($directionX, $outputFileName, $outputWidth);
		}
		else
		{
			if(filemtime($this->sFolderOutput . $outputFileName) <= (time()-$this->iCacheTime))
			{
				$this->generateImage($directionX, $outputFileName);
			}
			else
			{
				$this->sStylesheet = file_get_contents($this->sFolderOutput . $this->sTempCssFilename);
			}
		}
	}
}

?>