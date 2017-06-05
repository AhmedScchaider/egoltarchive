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

class EGOText
{
	public static function _($string, $meaning = array(), $tagset = null)
	{
		$lang	= JFactory::getLanguage();
		$tag	= $lang->getTag();
			
		if(isset($tagset))
		{
			// if($tagset != $tag)
			// {
				// $lang->load('lib_egoltframework', JPATH_SITE, $tagset);
			// }
			$tag = $tagset ;
		}
		
		$tmp	= explode('-', $tag);
		$subtag	= $tmp[0];

		if( (JText::_($string) != $string) and (!isset($tagset)) )
		{
			return JText::_($string);
		}
		elseif(array_key_exists($tag, $meaning))
		{
			return $meaning[$tag];
		}
		elseif(array_key_exists($subtag, $meaning))
		{
			return $meaning[$subtag];
		}
		elseif(array_key_exists('d', $meaning))
		{
			return $meaning['d'];
		}
		else
		{
			return $string;
		}
	}
}
