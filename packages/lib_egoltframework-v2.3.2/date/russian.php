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

class EGODateRussian extends EGODateGre
{
	
	function __construct()
	{
		parent::__construct();	
		$this->langtag = 'ru-RU';
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'russian';
		$info['title']	= 'Russian';
		$info['compat']	= '2.5,3';

		return $info;
	}
}