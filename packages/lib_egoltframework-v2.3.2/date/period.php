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

class EGODatePeriod
{
	var $_default_date;
	var $_default_format;
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'period';
		$info['title']	= 'Period Date';
		$info['compat']	= '2.5,3';

		return $info;
	}
	
	public function show($date = null)
	{
		$start = strtotime($date);
		$end = time();

		$seconds	= $end - $start;
		$ago		= true;
		$years		= floor($seconds/60/60/24/365);
		$months		= floor($seconds/60/60/24/30);
		$days		= floor($seconds/60/60/24);
		$hours		= $seconds/60/60%24;
		$mins		= $seconds/60%60;
		$secs		= $seconds%60;
 
		$duration = '';
		if($years>0)
		{
			if($years == 1)
				$duration .= $years . ' ' . JText::_('EGO_YEAR');
			else
				$duration .= $years . ' ' . JText::_('EGO_YEARS');
		}
		elseif($months>0)
		{
			if($months == 1)
				$duration .= $months . ' ' . JText::_('EGO_MONTH');
			else
				$duration .= $months . ' ' . JText::_('EGO_MONTHS');
		}
		elseif($days>0)
		{
			if($days == 1)
				$duration .= $days . ' ' . JText::_('EGO_DAY');			
			else
				$duration .= $days . ' ' . JText::_('EGO_DAYS');
		}
		elseif($hours>0)
		{
			if($hours == 1)
				$duration .= $hours . ' ' . JText::_('EGO_HOUR');
			else
				$duration .= $hours . ' ' . JText::_('EGO_HOURS');
		}
		elseif($mins>0) 
		{
			if($mins == 1)
				$duration .= $mins . ' ' . JText::_('EGO_MINUTE');
			else
				$duration .= $mins . ' ' . JText::_('EGO_MINUTES');
		}
		else
		{
			$duration .= 1 . ' ' . JText::_('EGO_MINUTE');		
		}
		//elseif($secs>0) $duration .= "{$secs} seconds ";
 
		$duration = trim($duration);
			
		if($ago)
			$duration .= ' ' . JText::_('EGO_AGO');
		
		if($duration != null)		
			return $duration;
	}
}