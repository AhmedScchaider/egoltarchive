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

jimport( 'joomla.application.component.view');

class EgoltArchiveViewEgoltArchive extends JViewLegacy
{
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$doc		= JFactory::getDocument();
		$feedEmail	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'author';
		$siteEmail	= $app->getCfg('mailfrom');
			
        // Get data from the model	
        $items = $this->get( 'Data');
		$pagination = $this->get( 'Pagination' );
        $params 	= $this->get( 'Params' );
		
		// Set Feed Type
		JFactory::getApplication()->input->set('type', $params->get('feed_type'));

		// Grab each feed item
		foreach($items as $row) {
					
			// strip html from feed item title
			$title = $this->escape( $row->egtitle );
			$title = html_entity_decode( $title );

			// strip html from feed item description text
			if(isset($row->egthumb))
			{
				$thumb = '<img 
					src="'.$row->egthumb.'" 
					width="'.$row->egthumb_w.'"					
					height="'.$row->egthumb_h.'"	
					align="'.$row->egthumb_a.'"	
					alt="'.$row->egtitle.'"					
				/>';
			}
			$description = ((isset($row->egthumb)) ? $thumb . $row->egtext : $row->egtext);
			
			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $row->eglink;
			$item->description 	= $description;
			$item->date			= $row->egdate;
			$item->category   	= $row->egcat;
			if(isset($row->egauthor))
				$item->author		= $row->egauthor;
			if ($feedEmail == 'site') 
			{
				$item->authorEmail = $siteEmail;
			}
			else 
			{
				if(isset($row->egauthor_email) and !empty($row->egauthor_email))
					$item->authorEmail = $row->egauthor_email;
			}

			// loads item info into feed array
			$doc->addItem( $item );
		}

		$doc->link = '';
		
	}
}
