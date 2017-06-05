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

class EGODateTurkish extends EGODateGre
{

	function __construct()
	{
		parent::__construct();	
		$this->langtag = 'tr-TR';
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'turkish';
		$info['title']	= 'Turkish';
		$info['compat']	= '2.5,3';

		return $info;
	}
}