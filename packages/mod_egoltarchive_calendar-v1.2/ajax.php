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

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');

$app = JFactory::getApplication('site');
$app->initialise();

jimport('ego.include');

$data = explode('-', JFactory::getApplication()->input->get('ddt'));
$langtag = str_replace('.', '-', $data[4]);

// Load languages
$lang = JFactory::getLanguage();
$lang->load('mod_egoltarchive_calendar', JPATH_SITE, $langtag, true);

$user = JFactory::getUser();


require_once (JPATH_SITE . '/modules/mod_egoltarchive_calendar/helper.php');
$helper	= new modEgoltArchiveCalendarHelper($data[0]);
$helper->getDisplay(1, $data[1], $data[2], $data[3], $langtag);
