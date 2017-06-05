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

if (!defined('_EGOINC'))
{
    define('_EGOINC', '1.0.0');
	
	// Load Library language
	$lang = JFactory::getLanguage();
	$lang->load('lib_egoltframework', JPATH_SITE);

	// Load Form Fields
	jimport('joomla.form.helper');
	JFormHelper::addFieldPath(JPATH_SITE . '/libraries/ego/form/fields');
	
    function _ego_autoloader($class_name)
    {
        static $egoPath = null;

        // Make sure the class has a EGO prefix
        if (substr($class_name, 0, 3) != 'EGO')
            return;

        if (is_null($egoPath))
        {
            $egoPath = dirname(__FILE__);
        }

        // Remove the prefix
        $class = substr($class_name, 3);

        // Change from camel cased (e.g. SourceJcontent) into a lowercase array (e.g. 'source','jcontent')
        $class = preg_replace('/(\s)+/', '_', $class);
        $class = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $class));
        $class = explode('_', $class);

        // First try finding in structured directory format (preferred)
        $path = $egoPath . '/' . implode('/', $class) . '.php';
        if (@file_exists($path))
        {
            include_once $path;
        }
    }

	// check for spl extension compatibility
    if (function_exists('spl_autoload_register'))
    {
        if (function_exists('__autoload'))
		{
            spl_autoload_register('__autoload');
		}
        spl_autoload_register('_ego_autoloader');
    } 
	else
    {
        throw new Exception('Egolt Framework requires the SPL extension to be loaded and activated', 500);
    }

    // Not supporting JLoader
    function egoRegisterClasses()
    {
        throw new Exception('egoRegisterClasses not supported', 500);
    }

}
