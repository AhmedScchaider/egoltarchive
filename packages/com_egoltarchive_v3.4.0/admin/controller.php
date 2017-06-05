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

jimport('joomla.application.component.controller');

// Load Style-sheets
$csspath	= 'media/egoltarchive/css/';
$lang 		= JFactory::getLanguage();
$isRTL		= $lang->isRTL();
if(version_compare(JVERSION, '3.0', 'lt'))
	JHtml::_('stylesheet', $csspath . 'bootstrap.min.css');
JHtml::_('stylesheet', $csspath . 'egoltarchive_admin.css');
if($isRTL) 
{
	JHtml::_('stylesheet', $csspath . 'egoltarchive_admin.rtl.css');	
}

class EgoltArchiveController extends JControllerLegacy
{
    /**
     * Method to display the view
     *
     * @access    public
     */
	public function display($cachable = false, $urlparams = false)
    {		
		EgoltArchiveHelper::setEnv('COM_EGOLTARCHIVE_CONTROL_PANEL');
		JToolBarHelper::preferences('com_egoltarchive');
		
		parent::display();
    }
	
    function about()
    {
    	JFactory::getApplication()->input->set( 'view', 'about' );
		EgoltArchiveHelper::setEnv('COM_EGOLTARCHIVE_ABOUT_PRODUCT');
		
	    parent::display();
    }

}
