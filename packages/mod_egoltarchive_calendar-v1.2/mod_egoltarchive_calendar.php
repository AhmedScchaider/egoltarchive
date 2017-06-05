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

// Set Lanuage
$language = JFactory::getLanguage();
$language->load('com_egoltarchive', JPATH_SITE);

// Load Style-sheets
$csspath	= 'media/mod_egoltarchive_calendar/css/';
$lang 		= JFactory::getLanguage();
$isRTL		= $lang->isRTL();
		
JHtml::_('stylesheet', $csspath . 'style.css');	
JHtml::_('stylesheet', 'media/egoltarchive/css/ttp.css');	
if($isRTL) 
{
	JHtml::_('stylesheet', $csspath . 'style.rtl.css');	
	JHtml::_('stylesheet', 'media/egoltarchive/css/ttp.rtl.css');	
}

// Load Javascript
if(EGOHtmlJs::needJquery())
	JHtml::_('script', 'media/egoltarchive/js/jquery.min.js');	
JHtml::_('script', 'media/egoltarchive/js/ttp.js');	
JHtml::_('script', 'media/mod_egoltarchive_calendar/js/app.js');
JFactory::getDocument()->addScriptDeclaration('var egjroot = "' . JUri::base() . '";');

// Set Helper
require_once (JPATH_SITE . '/modules/mod_egoltarchive_calendar/helper.php');
$helper	= new modEgoltArchiveCalendarHelper($module->id);

if(!$params->get('defmonth'))
	$defmonth = null;
else
	$defmonth = $params->get('defmonth');
if(!$params->get('defyear'))
	$defyear = null;
else
	$defyear = $params->get('defyear');
$helper->getDisplay(null, null, $defyear, $defmonth);

// Set Layout
require (JModuleHelper::getLayoutPath('mod_egoltarchive_calendar', 'default/default'));
