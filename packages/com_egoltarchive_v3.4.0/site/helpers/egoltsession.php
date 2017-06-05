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
 
abstract class EgoltSessionHelper
{
	public static function setSess() 
	{
		$input = JFactory::getApplication()->input;
		jimport('joomla.user.helper');
		// Load Model
		jimport( 'joomla.application.component.model' );
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_egoltarchive/models', 'EgoltArchiveModel');	
		$model = JModelLegacy::getInstance( 'EgoltArchive', 'EgoltArchiveModel');
		
		$session 	= JFactory::getSession(); 
		$app 		= JFactory::getApplication();
		$params 	= $model->getParams();
		$menu	 	= $model->getMenu();
		$cltype		= $params->get('cltype');
		
		if($rbtab = JFactory::getApplication()->input->get('rbtab')) {
			$egoltarchivesess['rbtab'] = $rbtab;		
		}	

		if($rbtab == 1)
		{
			if($dateselect = JFactory::getApplication()->input->get('dateselect')) 
			{
				$egoltarchivesess['dateselect'] = $dateselect;
				$egoltarchivesess['duration'] = null;
				$egoltarchivesess['startdate'] = null;
				$egoltarchivesess['enddate'] = null;
			}
		}
		elseif($rbtab == 2)
		{
			if($duration = JFactory::getApplication()->input->get('duration')) 
			{
				$egoltarchivesess['duration'] = $duration;
				$egoltarchivesess['dateselect'] = null;
				$egoltarchivesess['startdate'] = null;
				$egoltarchivesess['enddate'] = null;	
			}
		}
		elseif($rbtab == 3)
		{
			if(JFactory::getApplication()->input->get('startd') or JFactory::getApplication()->input->get('onedate')) 
			{
				$lang = JFactory::getLanguage();
				$lang_tag = $lang->getTag();
			
				if(JFactory::getApplication()->input->get('onedate')) 
				{
					$startdate = $enddate = explode('_' ,JFactory::getApplication()->input->get('onedate'));	
				}
				else
				{
					$input = JFactory::getApplication()->input;
					$startdate = array ($input->get('starty'), $input->get('startm'), $input->get('startd'));
					$enddate = array ($input->get('endy'), $input->get('endm'), $input->get('endd'));
				}

				//start date
				$startdate = implode('-', $startdate);
				$startdate = EGODate::_($cltype)->toGre($startdate, 'Y-m-d');
				$egoltarchivesess['startdate'] = $startdate;
				
				//end date
				$enddate = implode('-', $enddate);
				$enddate = EGODate::_($cltype)->toGre($enddate, 'Y-m-d');
				$egoltarchivesess['enddate'] = $enddate;	
				
				$egoltarchivesess['dateselect'] = null;
				$egoltarchivesess['duration'] = null;
			}
		}
		
		if($author = $input->get('authorslist', null, 'STRING')) {
			$egoltarchivesess['authorslist'] = $author;
		}
		if($author2 = $input->get('author', null, 'STRING')) {
			if(strpos($author2, 'a--') === 0)
			{
				$atsesslike = 'alias:' . substr($author2, 3);
			}
			else
			{
				$atsesslike = JUserHelper::getUserId($author2);
			}			
			$egoltarchivesess['authorslist'] = $atsesslike;
		}
		if($ordering = $input->get('orderinglist')) {
			$egoltarchivesess['orderinglist'] = $ordering;
		}
		if($cat = $input->get('catlist')) {
			$egoltarchivesess['catlist'] = $cat;
		}
		if($search_str = $input->get('search_str', null, 'STRING')) {
			$egoltarchivesess['like_str'] = $search_str;
			if($input->get('exact_search')) {
				$egoltarchivesess['exact_like'] = 1;
			}
		}
		if($search_str = $input->get('not_search_str', null, 'STRING')) {
			$egoltarchivesess['not_like_str'] = $search_str;
			if($input->get('exact_not_search')) {
				$egoltarchivesess['exact_not_like'] = 1;
			}
		}

		if(isset($egoltarchivesess))
		{
			//set sessions
			$session->set('egoltarchive_' . $menu, $egoltarchivesess);
			JFactory::getApplication()->input->set('limitstart', 0);
		}
		
		if($input->get('searchall'))
		{
			//clear search session
			$session->clear('egoltarchive_' . $menu);
			$app->redirect(JRoute::_('index.php?option=com_egoltarchive&Itemid=' . $menu));
		}
		
	}
}
