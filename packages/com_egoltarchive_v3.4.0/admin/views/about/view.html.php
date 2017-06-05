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

jimport( 'joomla.application.component.view' );


class EgoltArchiveViewAbout extends JViewLegacy
{
	function display($tpl = null)
    {
		if(EgoltArchiveHelper::isBegin())
			$this->start = JText::_('COM_EGOLTARCHIVE_START_MSG');
		
		$this->version = EgoltArchiveHelper::getVersion();
		
        parent::display($tpl);
    }
}
