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

class EGOSourceCats
{
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'cats';
		$info['title']	= 'Joomla! Categories';
		$info['compat']	= '2.5';

		return $info;
	}
	
	public function getItemsQR($filter = array())
	{			
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$extension = 'com_content';	
		
		$filter['published'] = 1;
		
		$query->select('a.id, a.title, a.level, a.parent_id');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');

		// Filter on extension
		if(isset($filter['extension']))
		{
			$query->where('extension = ' . $db->quote($filter['extension']));
		}
			
		// Filter on included categories
		if(isset($filter['singlecat']))
		{
			$query->where('a.id =' . $filter['singlecat']);
		}
		elseif(isset($filter['cats']))
		{
			if($filter['cats'])
			{
				$filter['cats'] = is_array($filter['cats'])? implode(',', $filter['cats']) : $filter['cats'];
				$query->where('a.id IN (' . $filter['cats'] . ')');
			}
		}
		
		// Filter on not included categories
		if(isset($filter['notcats']))
		{
			$filter['notcats'] = is_array($filter['notcats'])? implode(',', $filter['notcats']) : $filter['notcats'];
			$query->where('a.id NOT IN (' . $filter['notcats'] . ')');
		}

		// Filter on the published state
		if (isset($filter['published']))
		{
			if (is_numeric($filter['published']))
			{
				$query->where('a.published = ' . (int) $filter['published']);
			}
			elseif (is_array($filter['published']))
			{
				JArrayHelper::toInteger($filter['published']);
				$query->where('a.published IN (' . implode(',', $filter['published']) . ')');
			}
		}
		
		if(isset($filter['authorised']))
		{
			$query->where('a.access IN(' . implode(',', $filter['authorised']) . ')');
		}
			
		$query->order('a.lft');	
						
		return $query;
	}
}
