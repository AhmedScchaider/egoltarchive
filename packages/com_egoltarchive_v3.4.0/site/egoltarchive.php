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

// require helper file
JLoader::register('EgoltSessionHelper', JPATH_COMPONENT . '/helpers/egoltsession.php');
EgoltSessionHelper::setSess();

// Require the base controller
require_once( JPATH_COMPONENT .'/controller.php' );

// Require specific controller if requested
if ($controller = JFactory::getApplication()->input->get('controller')) 
{
    $path = JPATH_COMPONENT . '/controllers/' . $controller.'.php';
    if (file_exists($path)) 
	{
        require_once $path;
    } 
	else 
	{
        $controller = '';
    }
}
// Create the controller
$classname    = 'EgoltArchiveController'.$controller;
$controller   = new $classname();

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
