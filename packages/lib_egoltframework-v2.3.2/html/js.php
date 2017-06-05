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

class EGOHtmlJs
{
	public static function needJquery()
	{
		$doc = JFactory::getDocument();
		$needJquery = true;
		JHtml::_('behavior.framework');		
		// if (!version_compare(JVERSION, '3.0', 'ge')) 
		// {
			$header = $doc->getHeadData();
			foreach($header['scripts'] as $scriptName => $scriptData)
			{
				if(substr_count($scriptName,'/jquery'))
				{
					$needJquery = false;
				}
			}			
		// }	
		
		return $needJquery;	
	}
}