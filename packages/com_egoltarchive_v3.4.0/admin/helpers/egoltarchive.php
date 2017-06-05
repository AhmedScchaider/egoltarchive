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
 
/**
 * EgoltArchive component helper.
 */
abstract class EgoltArchiveHelper
{
	/**
	 * Configure the environment.
	 */
	public static function setEnv($active) 
	{
		// set title bar
		$egolt_title = JText::_( 'COM_EGOLTARCHIVE' );
		if ($active != 'COM_EGOLTARCHIVE_CONTROL_PANEL') {
			$egolt_title .=' <small>- [ '.JText::_($active).' ]</small>';
		}
        JToolBarHelper::title($egolt_title ,'egoltarchive');

		// set sub-menus with active menu decleration
		JSubMenuHelper::addEntry(JText::_('COM_EGOLTARCHIVE_CONTROL_PANEL'),'index.php?option=com_egoltarchive', $active == 'COM_EGOLTARCHIVE_CONTROL_PANEL');
		JSubMenuHelper::addEntry(JText::_('COM_EGOLTARCHIVE_ABOUT_PRODUCT'),'index.php?option=com_egoltarchive&task=about', $active == 'COM_EGOLTARCHIVE_ABOUT_PRODUCT');
		
		// set some property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-egoltarchive 
		{background-image: url(../media/egoltarchive/images/egoltarchive.png);}');
		$document->setTitle(JText::_('COM_EGOLTARCHIVE').' - '.JText::_($active));	
	}
	
	public static function isBegin()
	{
		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('a.id as mid, t.title as mttitle, a.link, a.title as mtitle');
		$query->from('#__menu as a');
		$query->join('LEFT', '#__menu_types AS t ON t.menutype = a.menutype');
		$query->where('a.link LIKE \'index.php?option=com_egoltarchive%\'');
		$query->where('a.client_id = 0');
		// $query->where('a.published = 1');
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows())
			return false;
		else
			return true;
	}
	
	public static function getVersion()
	{
		$option = 'pkg_egoltarchive';
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('manifest_cache');
		$query->from('#__extensions');
		$query->where($query->qn('type') . ' = ' . $db->quote('package'));
		$query->where($query->qn('element') . ' = ' . $db->quote($option));
		$db->setQuery($query);
		$res	= $db->loadResult();
		$info	= json_decode($res);
		
		return $info->version;
	}
	
	public static function setSal($tp = null)
	{
		if (sha1('egolt') === '2e3340a60b208dd180664060422e299f1c42a2e6') 
		{
			if($tp == 'A%34TO')
			{
				$argu = '';
			}
		}
	}
	
}
