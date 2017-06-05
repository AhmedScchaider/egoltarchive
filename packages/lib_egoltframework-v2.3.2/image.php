<?php
/**
 * @package   	Egolt Framewrok
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt - www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 */
 
// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

// Check Egolt Framework
defined('_EGOINC') or die('Egolt Framework not installed!');

class EGOImage
{
	var $engine;
	var $path;
	
	function __construct($config = array())
	{
		$this->engine	= (isset($config['engine']) and ($config['engine'] != 'off') ) ? $config['engine'] : null;
		$this->path		= isset($config['path']) ? $config['path'] : 'cache';	
		$this->type		= isset($config['type']) ? $config['type'] : 'crop';	
		$this->defimg	= isset($config['defimg']) ? $config['defimg'] : null;
		$this->start();
	}
	
	protected function start()
	{
		if($this->engine == 'phpthumb')
		{
			require_once '_thumbs/phpthumb/ThumbLib.inc.php';	
		}
		elseif($this->engine == 'joomla')
		{
			jimport('joomla.image.image');
		}
	}
	
	public function show($width = null, $height = null, $image = null, $attr = array())
	{
		if($image)
		{
			$img = $image;
		}
		elseif(isset($this->defimg))
		{
			$img = $this->defimg;
			$isDef = true;
		}
		else
		{
			return false;
		}
		
		if(!$img)
			return false;
		
		$cropx = (isset($attr['cropx'])) ? $attr['cropx'] : 100;
		$cropy = (isset($attr['cropy'])) ? $attr['cropy'] : 100;
	
		$tmp = explode('/', $img);
		$ctmp = count($tmp);
		$imgname = $tmp[--$ctmp];
		
		if(isset($isDef) and isset($attr['forceresizedef']))
			$this->type = 'resize';
			
		$cachename	= $this->path . '/' . md5($this->type . $width . $height . $img . $imgname) . '.png';
		$cache	= JPATH_SITE .'/' . $cachename;
		$imgpath= JPATH_SITE .'/' . $img;
		
		if(isset($this->engine))
		{
			if($this->engine == 'phpthumb')
			{
				if(!is_file($cache)) 
				{
					if($this->type == 'resize')
					{
						$thumb = PhpThumbFactory::create($imgpath);
						$thumb->resize($width, $height);
						$thumb->save($cache);
					}
					elseif($this->type == 'crop')
					{
						$thumb = PhpThumbFactory::create($imgpath);
						$thumb->crop($cropx, $cropy, $width, $height);
						$thumb->save($cache);
					}
				}
				if(isset($attr['addbase']))
					return JUri::root() . $cachename;
				else
					return $cachename;
			}
			elseif($this->engine == 'joomla')
			{
				if(!is_file($cache)) 
				{
					$thumb = new JImage($imgpath);
					if($this->type == 'resize')
					{
						$thumb->resize($width, $height);
						$thumb->toFile($cache);
					}
					elseif($this->type == 'crop')
					{
						$thumb->crop($width, $height, $cropx, $cropy);
						$thumb->toFile($cache);
					}
				}
				if(isset($attr['addbase']))
					return JUri::root() . $cachename;
				else
					return $cachename;
			}
		}
		else // engine off
		{
			if(isset($attr['addbase']))
				return JUri::root() . $img;
			else
				return $img;
		}
	}
}
