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

// Set flag that this is a parent file
define('_JEXEC', 1);

// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

define( 'DS', '/' );

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');

$app = JFactory::getApplication('site');
$app->initialise();

jimport('ego.include');

$cnd = JFactory::getApplication()->input->get('source');
if(EGOSource::_($cnd)->getExist())
{
	$filter['select.name'] = 'egdump';
	$filter['published'] = 1;
	$output = EGOSource::_($cnd)->getCatsList($filter);
}
else
{
	$output = '';
}
		
echo $output;
