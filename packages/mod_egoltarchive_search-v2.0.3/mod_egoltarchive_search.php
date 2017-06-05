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

// Set Helper
require_once (dirname(__FILE__) . '/helper.php');
$helper	= new modEgoltArchiveSearchHelper($params);

// Module Params
$modsfx		= $params->get('moduleclass_sfx');
$template	= $params->get('template', 'default');
$menu		= $params->get('egmenu');
$eghide		= $params->get('eghidesearch');

// Component Params
$cmparams	= $helper->getCMParams();
$sess		= $helper->getSess();

// Load Style-sheets
$csspath	= 'media/mod_egoltarchive_search/css/';
$lang 		= JFactory::getLanguage();
$isRTL		= $lang->isRTL();
		
JHtml::_('stylesheet', $csspath . 'mod_egoltarchive_search.css');	
if($isRTL) 
{
	JHtml::_('stylesheet', $csspath . 'mod_egoltarchive_search.rtl.css');	
}

// Set Layout
require (JModuleHelper::getLayoutPath('mod_egoltarchive_search', $template . '/default'));
