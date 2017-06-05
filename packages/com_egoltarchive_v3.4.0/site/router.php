<?php
/**
 * @package   	Egolt Archive
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt - www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 *
 * Name:		Egolt Archive
 * License:		GNU/GPL 2
 * Product:		http://www.egolt.com/products/egoltarchive
 */

// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

// Check Egolt Framework
defined('_EGOINC') or die('Egolt Framework not installed!');

// Convert query to URI (in JRoute)
function EgoltArchiveBuildRoute( &$query )
{
	$segments = array();
	   
	// We need to find out if the menu item link has a view param
	if(array_key_exists('Itemid', $query)) {
		$menu = JFactory::getApplication()->getMenu()->getItem($query['Itemid']);
		if(!is_object($menu)) {
			$menuquery = array();
		} else {
			// Remove "index.php?" and parse
			parse_str(str_replace('index.php?',  '',$menu->link), $menuquery);
		}
	} 
	else 
	{
		$menuquery = array();
	}
	
	unset( $query['view'] );	
	unset( $query['rbtab'] );	
	unset( $query['limitstart'] );	
	
	if(isset($query['start']))
	{
		$segments[] = 'page';	
		
		$app 	= JFactory::getApplication();
		$session= JFactory::getSession();
		$params = $app->getParams('com_egoltarchive');	
		$menus	= $app->getMenu('site');
		$menu	= JFactory::getApplication()->input->get('Itemid', null, 'INT');
		// $menu	= $menus->getActive()->id
		
		if(isset($config['menu']))
		{
			$component	= JComponentHelper::getComponent('com_egoltarchive');
			$params		= $menus->getParams( $config['menu'] );
			$menu		= $config['menu'];
		}
		$sess	= $session->get('egoltarchive_' . $menu);
		
		// Set Override Params
		if(isset($config['params']))
		{
			foreach($config['params'] as $key => $value)
			{
				$params->set($key, $value);
			}
		}

		if(!$params->get('content_service'))
		{
			// JError::raiseError(500, 'Undefined content service or without menu control.');
			throw new Exception('Undefined content service or without menu control.', 500);
			return false;
		}		


		
		$mainframe = JFactory::getApplication();
		$config = JFactory::getConfig();

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limit	= $params->get('items_qt', '30');
		$limitstart = $query['start'];

		$page = ($limit != 0 ? (floor($limitstart / $limit) ) : 0);
		++$page;

		$segments[] = $page;
	}

	if(isset($query['dateselect']))
	{		
		if(!isset($query['start']))
		{
			$dates = explode('-', $query['dateselect']);
			if(count($dates) == 2)
			{
				$segments[] = 'year';
				$segments[] = (int) $dates[0];
				if(!($dates[1] == 'all'))
				{
					$segments[] = (int) $dates[1];
				}
			}
		}
	}
	
	if(isset($query['onedate']))
	{		
		$segments[] = 'date';
		$segments[] = $query['onedate'];
	}
	
	unset( $query['onedate'] );
	unset( $query['start'] );
	unset( $query['dateselect'] );		
	return $segments;
}

// Convert URI to query
function EgoltArchiveParseRoute($segments)
{
		$app 	= JFactory::getApplication();
		$session= JFactory::getSession();		
		$params = $app->getParams('com_egoltarchive');	
		$menus	= $app->getMenu('site');
		$menu	= JFactory::getApplication()->input->get('Itemid', null, 'INT');
		// $menu	= $menus->getActive()->id
		
		if(isset($config['menu']))
		{
			$component	= JComponentHelper::getComponent('com_egoltarchive');
			$params		= $menus->getParams( $config['menu'] );
			$menu		= $config['menu'];
		}
		$sess	= $session->get('egoltarchive_' . $menu);
		
		// Set Override Params
		if(isset($config['params']))
		{
			foreach($config['params'] as $key => $value)
			{
				$params->set($key, $value);
			}
		}

		if(!$params->get('content_service'))
		{
			// JError::raiseError(500, 'Undefined content service or without menu control.');
			throw new Exception('Undefined content service or without menu control.', 500);
			return false;
		}
		
		
		
	$vars = array();
	
	// Get active menu
	$mObject = JFactory::getApplication()->getMenu()->getActive();
	$menu = is_object($mObject) ? $mObject->query : array();
	
	// Count segments
	$count = count( $segments );
	
	$vars['view'] = 'egoltarchive';
		
	switch( $count )
	{	
		case 2:	
			if($segments[0] == 'page')
			{
				$mainframe	= JFactory::getApplication();
				$config 	= JFactory::getConfig();

				$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
				$limit	= $params->get('items_qt', '30');
				$page 	= $segments[1];
				$start	= ($page != 0 ? (--$page * $limit) : 0);
				
				$vars['limitstart'] = $start;
			}
			elseif($segments[0] == 'year')
			{
				$vars['rbtab']		= 1;
				$vars['dateselect']	= $segments[1] . '-all';
			}
			elseif($segments[0] == 'date')
			{
				$vars['rbtab']		= 3;
				$vars['onedate']	= $segments[1];
				$vars['eghidesearch']	= 1;
			}
		break;
	
		case 3:	
			if($segments[0] == 'year')
			{
				$vars['rbtab']		= 1;
				$vars['dateselect']	= $segments[1].'-'.$segments[2];
			}
		break;

	}

	return $vars;
}