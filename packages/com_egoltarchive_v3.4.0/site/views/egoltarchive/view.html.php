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

jimport( 'joomla.application.component.view' );

class EgoltArchiveViewEgoltArchive extends JViewLegacy
{

	function display($tpl = null)
    {
		$app 			= JFactory::getApplication();
		$session 		= JFactory::getSession(); 
		$user			= JFactory::getUser();
		$db				= JFactory::getDBO();
		$lang 			= JFactory::getLanguage();
		$doc			= JFactory::getDocument();
		$this->pparams	= $app->getParams();
		$menus			= $app->getMenu();
		
        // Get data from the model
        $params 	= $this->get( 'Params' );
        $menu2	 	= $this->get( 'Menu' );
        $items 		= $this->get( 'Data' );
		$pagination = $this->get( 'Pagination' );
		$mindateup 	= $this->get( 'MinDate' );
		$total	 	= $this->get( 'Total' );
		$catslist 	= $this->get( 'Catslist' );
		$datepoints = $this->get( 'Datepoints' );
		$authorslist	= $this->get( 'Authorslist' );
		$orderinglist	= $this->get( 'Orderinglist' );
		$dateselect		= $this->get( 'Datelist' );
		$duration		= $this->get( 'Durlist' );
		$between		= $this->get( 'BetweenList' );
				
		$sess 			= $session->get('egoltarchive_' . $menu2);
		$cltype			= $params->get('cltype');
		$pageclass_sfx	= htmlspecialchars($this->pparams->get('pageclass_sfx'));
		$viewtype		= $params->get('view_type', 'ti');
		
		// Link Target
		if($params->get('archive_item_target', 'same') == 'new')
			$this->item_target = 'target="_blank"';
		else
			$this->item_target = '';
		if($params->get('archive_cat_target', 'new') == 'new')
			$this->cat_target = 'target="_blank"';
		else
			$this->cat_target = '';
		
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->pparams->def('page_heading', $this->pparams->get('page_title', $menu->title));
		}
		else
		{
			$this->pparams->def('page_heading', JText::_( 'COM_EGOLTARCHIVE_ADVANCED_SEARCH_AND_ARCHIVE' ));
		}
					
		if(isset($sess['rbtab']))
		{	
			$dtinput = '<input type="hidden" name="rbtab" class="egrbtab" value="' . $sess['rbtab'] .'" />';
		}
		else
		{
			$dtinput = '<input type="hidden" name="rbtab" class="egrbtab" value="1" />';
		}
		
		// Date Points
		$startyrec		= $datepoints['starty'];
		$startyrec_set	= $datepoints['starty_set'];
		$startmrec		= $datepoints['startm'];
		$startmrec_set	= $datepoints['startm_set'];
		$startdrec		= $datepoints['startd'];
		$startdrec_set	= $datepoints['startd_set'];
		$endyrec		= $datepoints['endy'];
		$endyrec_set	= $datepoints['endy_set'];
		$endmrec		= $datepoints['endm'];
		$endmrec_set	= $datepoints['endm_set'];
		$enddrec		= $datepoints['endd'];
		$enddrec_set	= $datepoints['endd_set'];
	
		// Between List
		$starty	= $between['starty'];
		$endy	= $between['endy'];
		$startm = $between['startm'];
		$endm	= $between['endm'];
		$startd = $between['startd'];
		$endd	= $between['endd'];
		
		// Layout Parameters
		$this->dtinput		= $dtinput;
		$this->dateselect	= $dateselect;
		$this->duration		= $duration;
		$this->catlist		= $catslist;
		$this->authorslist	= $authorslist;
		$this->orderinglist	= $orderinglist;
        $this->items		= $items;
        $this->total		= $total;
        $this->psfx			= $pageclass_sfx;
        $this->doc			= $doc;
        $this->lang			= $lang;
		$this->pagination	= $pagination;
		$this->params		= $params;
		$this->sess			= $sess;	
		$this->viewtype		= $viewtype;	
		$this->starty		= $starty;
		$this->endy			= $endy;
		$this->startm		= $startm;
		$this->endm			= $endm;
		$this->startd		= $startd;
		$this->endd			= $endd;
		
		// Load Style-Sheets
		$this->loadCSS();
		
		// Load Javascript
		$this->loadJS();
		
		parent::display($tpl);
    }
	
	function loadJS()
	{
		$params 	= $this->params;
		$jspath		= 'media/egoltarchive/js';
		
		if(isset($this->sess['rbtab']))
			$datetype = $this->sess['rbtab'];
		else
			$datetype = 1;
			
		// Add jQuery if needed
		if(EGOHtmlJs::needJquery())
			JHtml::_('script', $jspath . '/jquery.min.js');
			
		JHtml::_('script', $jspath . '/egoltarchive.js');
		
		if($params->get('responsive', 1))
			JHtml::_('script', $jspath . '/egoltarchive-respo.js');

		$this->doc->addScriptDeclaration('var egdatetype = ' . $datetype . ';');
		$this->doc->addScriptDeclaration('var egchangesearchtxt = "' . JText::_( 'COM_EGOLTARCHIVE_CHANGE_SEARCH_PARAMS' ) . '";');
		
		$this->start = JFactory::getApplication()->input->get('limitstart','0','INT');
		if($params->get('autohidesearch', 1))
		{
			$hidesearch = 0;
			if(JFactory::getApplication()->input->get('limitstart','0','INT') or JFactory::getApplication()->input->get('eghidesearch') or $params->get('eghidesearch', 0))
			{
				$hidesearch = 1;
			}
			$this->doc->addScriptDeclaration('var eghidesearch = ' . $hidesearch . ';');
		}	
	}

	function loadCSS()
	{
		$params 	= $this->params;
		$csspath	= 'media/egoltarchive/css/';
		$isRTL		= $this->lang->isRTL();
		$tcolor		= $params->get('theme_color', -1);
		$cssfiles	= array(
					'egoltarchive' ,
					'egoltarchive_paging' ,
					'egoltarchive_searchbox' ,
					'egoltarchive_v-'.$this->viewtype ,			
					);
		
		// load stylesheet files
		foreach($cssfiles as $fname)
		{
			JHtml::_('stylesheet', $csspath . $fname . '.css');	
			
			// RTL languages styles
			if($isRTL) 
			{
				JHtml::_('stylesheet', $csspath . $fname . '.rtl.css');	
			}
			
			// Theme color styles
			if((int)$tcolor != -1)
			{
				JHtml::_('stylesheet', $csspath . $fname . '.c-'. $tcolor .'.css');	
			}	
		}
		
		if(version_compare(JVERSION, '3.0', 'lt'))
			JHtml::_('stylesheet', $csspath . 'egoltarchive-j25.css');

		// $style = '/* ++++++++++ Egolt Archive Override Style +++++++++++++ */
		// ';		
		// $this->doc->addStyleDeclaration( $style );		
	}
}
