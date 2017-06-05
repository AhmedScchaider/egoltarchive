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

JLoader::register('EgoltArchiveHelper', JPATH_ADMINISTRATOR . '/components/com_egoltarchive/helpers/egoltarchive.php');

/**
 * Component Controller
 *
 * @package    Egolt Archive
 */
class EgoltArchiveController extends JControllerLegacy
{
	/**
     * Method to display the view
     *
     * @access    public
     */
	 
    public function display($cachable = false, $urlparams = false)
    {
		// Load Model
		jimport( 'joomla.application.component.model' );
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_egoltarchive/models', 'EgoltArchiveModel');	
		$model = JModelLegacy::getInstance( 'EgoltArchive', 'EgoltArchiveModel');
		
		$app		= JFactory::getApplication();
		$params		= $model->getParams();
		
		if($params->get('view_type') != 'fd')
		{
			EgoltArchiveHelper::setSal('A%34TO');	
		}
		else
		{
				JFactory::$document = JDocument::getInstance('feed');		
		}
        parent::display();
		if($params->get('view_type') != 'fd')
		{
			EgoltArchiveHelper::setSal('$SJdlkSD8(($#@');	
		}
    }

}
