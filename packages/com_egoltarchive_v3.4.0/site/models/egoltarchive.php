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

jimport( 'joomla.application.component.model' );

class EgoltArchiveModelEgoltArchive extends JModelLegacy
{

	var $_total;
	var $_pagination;
	var $_limit;
	var $_limitstart;
	var $_offset;
	var $_params;
	var $_sess;
	var $_user;
	var $_db;
	var $_app;
	var $_menu;
	
	function __construct($config)
	{
		parent::__construct();
		
		$app 	= JFactory::getApplication();
		$session= JFactory::getSession();
		$params = $app->getParams('com_egoltarchive');	
		$menus	= $app->getMenu('site');
		$menu	= JFactory::getApplication()->input->get('Itemid', null, 'INT');
		// $menu	= $menus->getActive()->id
		
		if(isset($config['menu']))
		{
			$component	= JComponentHelper::getComponent('com_egoltarchive');
			$params		= $menus->getParams( $config['menu'] );
			$menu		= $config['menu'];
		}
		$sess	= $session->get('egoltarchive_' . $menu);
		
		// Set Override Params
		if(isset($config['params']))
		{
			foreach($config['params'] as $key => $value)
			{
				$params->set($key, $value);
			}
		}
	
		if(!$params->get('content_service'))
		{
			// JError::raiseError(500, 'Undefined content service or without menu control.');
			throw new Exception('Undefined content service or without menu control.', 500);
			return false;
		}
		
		// Get the pagination request variables
		$limit	= $app->getUserStateFromRequest( 'global.list.limit', 'limit',$app->getCfg('list_limit'), 'int' );
		$limit	= $params->get('items_qt', '30');
		$limitstart = JFactory::getApplication()->input->get('limitstart', '0', 'INT');
		

		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		// Set class parameters
		$this->_params	= $params;
		$this->_sess	= $sess;
		$this->_app		= $app;
		$this->_user	= JFactory::getUser();
		$this->_db		= JFactory::getDBO();
		$this->_menu	= $menu;
		$this->_limit	= $limit;
		$this->_limitstart	= $limitstart;
		if($params->get('items_offset'))
		{
			$_offset = $params->get('items_offset', 0);
			$this->_limitstart	+= $_offset;
		}
	}
	
	// Returns the query
	function _buildQuery()
	{
		$user	= $this->_user;	
		$sess	= $this->_sess;		
		$db		= $this->_db;
		$app 	= $this->_app;
		$params = $this->_params;
		$query	= $db->getQuery(true);
		$cltype	= $params->get('cltype');
		$cns	= $params->get('content_service');
		
		// category filtering
		$cat_inc = $this->getCatsAccess();
		
		if(isset($sess['rbtab']))
		{
			if($sess['rbtab'] == 3)
			{
				$dateselect = 'exact';
			}
			elseif($sess['rbtab'] == 2)
			{
				$dateselect = 'duration';
			}
			elseif($sess['rbtab'] == 1)
			{
				$dateselect = 'date';		
			}
		}
		
		if(isset($sess['orderinglist']))
		{
			$default_order = $sess['orderinglist'];
		}
		else
		{
			$default_order = $params->get('default_order', 'newer_first');
		}
		$order = EGOSource::_($cns)->getSorts($default_order);
		
		// start and end date
		if(isset($sess['startdate']))
		{
			$startdate = $sess['startdate'];
		}
		if(isset($sess['enddate']))
		{
			$enddate = $sess['enddate'];
		}
		if(isset($sess['dateselect']))
		{
			$tmp = explode('-', $sess['dateselect']);
			if(isset($tmp[1]))
			{
				$tdates		= EGODate::_($cltype)->getDurs((int)$tmp[0], (int)$tmp[1]);
				$startdate	= $tdates['start'];
				$enddate	= $tdates['end'];
			}
		}
		
		if($params->get('startdate') or $params->get('enddate'))
		{
			$dateselect = 'exact';
			if($params->get('startdate'))
				$startdate = $params->get('startdate');
			if($params->get('enddate'))
				$enddate = $params->get('enddate');	
		}			
		
		if($params->get('unauth'))
		{
			$authorised = null;
		}
		else
		{
			$authorised = $user->getAuthorisedViewLevels();
		}
		
		// Set filters for select
		$filter = array();
		$filter['authorised']	= $authorised;
		$filter['cat_inc']		= isset($cat_inc) ? $cat_inc: null;
		$filter['cat_noinc']	= ($params->get('category_not_inc')) ? $params->get('category_not_inc'): null;
		$filter['cat_inc_subs']	= ($params->get('cat_inc_subs')) ? $params->get('cat_inc_subs'): null;
		$filter['date_select']	= isset($dateselect) ? $dateselect: null;
		$filter['startdate']	= isset($startdate) ? $startdate: null;
		$filter['enddate']		= isset($enddate) ? $enddate: null;
		$filter['duration']		= isset($sess['duration']) ? $sess['duration']: null;
		$filter['like_str']		= isset($sess['like_str']) ? $sess['like_str']: null;
		$filter['exact_like']	= isset($sess['exact_like']) ? $sess['exact_like']: null;
		$filter['not_like_str']	= isset($sess['not_like_str']) ? $sess['not_like_str']: null;
		$filter['exact_not_like'] = isset($sess['exact_not_like']) ? $sess['exact_not_like']: null;
		$filter['order']		= $order;
		$filter['published']	= $params->get('pub_options', 1);
		if(isset($sess['authorslist'])) 
		{
			$author = $sess['authorslist'];
			if (stripos($author, 'alias:') === 0) 
			{
				$filter['created_by_alias'] = substr($author, 6);
			}
			else
			{
				$filter['created_by'] = (int) $author;		
			}
		}
		
		$query = EGOSource::_($cns)->getItemsQR($filter);	
		// die($query);
						
		return $query;
	}
	
	// Return Parameters
	public function getParams()
	{
		return $this->_params;
	}	
	
	// Return Parameters
	public function getSess()
	{
		return $this->_sess;
	}
	
	// Return Menu ID
	public function getMenu()
	{
		return $this->_menu;
	}
	
	// Retrieves the entrys
	function getData()
	{
		$params 		= $this->_params;
		$sess			= $this->_sess;
		$user			= $this->_user;
		$db				= $this->_db;
		$menus			= $this->_app->getMenu();
		$cltype			= $params->get('cltype');
		$cns			= $params->get('content_service');
		
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query		= $this->_buildQuery();
			$this->_data= $this->_getList( $query, $this->_limitstart, $this->_limit);
		}
		$items = $this->_data;
		
		foreach($items as $item) {
			$date_format = $params->get('date_format', 'd F Y');
			
			if($params->get('view_type') != 'fd')
			{
				if($params->get('date_showtype', 'exactdate') == 'period')
				{
					$item->egdate = EGODate::_('period')->show($item->egdate);
				}
				else
				{
					$item->egdate = EGODate::_($cltype)->show($item->egdate, $date_format);
				}
			}
			else
				$item->egdate = EGODate::_('gre')->show($item->egdate, $date_format);
									
			$config = array(
				'alias' => $item->egalias, 
				'cat_alias'=> $item->egcat_alias
			);
			$item->eglink = EGOSource::_($cns)->getLink( $item->egid, $item->egcat_id, $config);
							
			$item->egcat_link = EGOSource::_($cns)->getCatlink($item->egcat_id, $item->egcat_alias);
			
			if(isset($item->egauthor_alias))
			{
				if($item->egauthor_alias)
					$item->egauthor	=	$item->egauthor_alias;
			}
			
			if($cmsys = $params->get('info_comments', 0))
			{
				if($cmsys == 'jcomments')
				{
					$jlive = JPATH_SITE . '/components/com_jcomments/jcomments.php';
					if (file_exists($jlive)) {
						require_once($jlive);
						$item->egcomment = JComments::getCommentsCount($item->egid, 'com_content');
					}
				}
			}
			
			if($params->get('display_thumb', 1) or ($params->get('view_type') == 'pg'))
			{	
				$item->egimg	= $item->egthumb = null;
				$tmptxt	= $item->egintro . $item->egfull;
				$tmpimg	= EGOSource::_($cns)->getImages($item->egimage, $tmptxt);
				
				if ( ($tmpimg) or ($params->get('default_thumb', 1)) ) 
				{
					// Image Parameters
					$item->egimg 	= $tmpimg['url'];
					$item->egimg_a	= $params->get('thumb_align', 'right');
					$item->egimg_w	= $tmpimg['width'];
					$item->egimg_h	= $tmpimg['height'];
				
					// Thumnail Parameters
					$item->egthumb_w= $params->get('thumb_width', 100);
					$item->egthumb_h= $params->get('thumb_height', 80);
					$item->egthumb_a= $params->get('thumb_align', 'right');
					
					// general thumbnail configuration
					$config = array(
						'engine'	=>  $params->get('thumb_engine'),
						'path'		=> 	'cache/egoltarchive',
						'type'		=>	$params->get('thumb_type'),
						'defimg'	=>	'media/egoltarchive/images/default.png'
					);
					$egthumb = new EGOImage($config);
					
					// Single thumbnail configuration
					$thumbconfig = array();
					$thumbconfig['addbase'] = true;
					if($params->get('cropx'))
						$thumbconfig['cropx']	= $params->get('cropx');
					if($params->get('cropy'))
						$thumbconfig['cropy']	= $params->get('cropy');				
					if($params->get('forceresizedef', 1))
						$thumbconfig['forceresizedef'] = $params->get('forceresizedef' ,1);
					
					// Create thumbnail
					$item->egthumb = $egthumb->show(
						$item->egthumb_w,
						$item->egthumb_h,
						$item->egimg,
						$thumbconfig
					);
				}
			}
			
			$item->egtext = trim(strip_tags($item->egintro, $params->get('archive_tags', '')));
			$pattern = array("/[\n\t\r]+/",'/{(\w+)[^}]*}.*{\/\1}|{(\w+)[^}]*}/Us', '/\$/');
			$replace = array(' ', '', '$-');
			$item->egtext = preg_replace( $pattern, $replace, $item->egtext );
            if($params->get('archive_charlimit', 200)) 
			{
				$archive_limited = EGOHtmlString::sub($item->egtext ,$params->get('archive_charlimit', 200));
				if($item->egtext != $archive_limited)
				{
					$item->egtext = $archive_limited . ' ' . $params->get('archive_trailer', '...');
					if($params->get('readmore', 1))
					{
						$item->egtext .= ' <a href="'.$item->eglink.'" >'
						.JText::_( 'COM_EGOLTARCHIVE_READMORE' )
						.'</a>';
					}
				}
			}
		}
		return $this->_data;
	}
	
	function getMindate()
	{
		$user	= $this->_user;
		$params = $this->_params;
		$db		= $this->_db;
		
		$filter = array();
		
		if($params->get('unauth'))
		{
			$authorised = null;
		}
		else
		{
			$authorised = $user->getAuthorisedViewLevels();
		}
		$filter['authorised']	= $authorised;
		// $filter['published']	= 1;
		$filter['published']	= $params->get('pub_options', 1);

		$query = EGOSource::_($params->get('content_service'))->getMinpubQR($filter);
		$db->setQuery($query);
		
		return $db->loadResult();
	}

	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->_limitstart, $this->_limit );
		}
		return $this->_pagination;
	}
	
	// Retrieves the count of entrys
	function getTotal()
	{
		$params = $this->_params;
		$db		= $this->_db;
		$cns	= $params->get('content_service');
		if (empty($this->_total)) 
		{
			$query = $this->_buildQuery();
			$db->setQuery( (string)$query );	
			$items = $db->loadObjectList();
			if(($params->get('view_type') == 'pg') and !($params->get('default_thumb', 1)))
			{
				$i = 0;
				foreach($items as $item)
				{
					$tmptxt	= $item->egintro . $item->egfull;
					$tmpimg	= EGOSource::_($cns)->getImages($item->egimage, $tmptxt);
						
					if ($tmpimg) 
					{
						++$i;
					}
				}
				$this->_total = $i;
			}
			else
			{
				// $this->_total = $this->_getListCount($query);
				// $items = $this->getData();
				$this->_total = count($items);
			}
		}
		return $this->_total;
	}
	
	// Get Categories List
	function getCatslist()
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		
		$filter = array();
		if($params->get('category_use_allcat', 1)) {
			$filter['select.allcat'] = 1;
		}
		if($cat_inc = $params->get('category_inc')) {			
			$filter['cats'] = $cat_inc ;
		}				
		if($cat_not_inc = $params->get('category_not_inc')) {			
			$filter['notcats'] = $cat_not_inc;
		}	
		if($cat_inc_subs = $params->get('cat_inc_subs')) {			
			$filter['cat_inc_subs'] = $cat_inc_subs;
		}	
		if(isset($sess['catlist']))
		{
			$filter['select.default'] = $sess['catlist'];
		}
		$filter['select.name'] = 'catlist';		
		$filter['select.extra'] = ' onchange="form.submit()" ';
		$filter['published'] = 1;
		// $filter['published']	= $params->get('pub_options', 1);
		$catlist = EGOSource::_($params->get('content_service'))->getCatsList($filter);
		
		return $catlist;
	}
	
	// Get Authors List
	function getAuthorslist()
	{
		$params = $this->_params;
		$sess	= $this->_sess;

		$filter = array();
		if($params->get('authors_use_all', 1)) {
			$filter['select.allauthors'] = 1;
		}
		if($authors_inc = $params->get('authors_inc')) {			
			$filter['authors'] = $authors_inc ;
		}				
		if($authors_not_inc = $params->get('authors_not_inc')) {			
			$filter['notauthors'] = $authors_not_inc;
		}	
		if(isset($sess['authorslist']))
		{
			$filter['select.default'] = $sess['authorslist'];
		}
		$filter['authorsource'] = $params->get('authorsource', 'both');
		$filter['select.name'] = 'authorslist';	
		$filter['select.extra'] = ' onchange="form.submit()" ';		
		// $filter['published'] = 1;
		$filter['published'] = $params->get('pub_options', 1);;
		$authorslist = EGOSource::_($params->get('content_service'))
								->getAuthorsList($filter);
		
		return $authorslist;
	}
	
	// Get authors link list
	public function getAuthorsLinkList($filter = array())
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		$db		= $this->_db;
		$user	= $this->_user;
		$config = $this->getDatepoints();
		$cltype	= $params->get('cltype');
		jimport('joomla.user.helper');
		$filter2 = $filter;
		
		$trailer = '';
		if(isset($filter['eghidesearch']) and ($filter['eghidesearch']))
		{
			$trailer .= '&eghidesearch=1';
		}
		if(isset($filter['menu']) and ($filter['menu']))
		{
			$trailer .= '&Itemid=' . $filter['menu'];
		}
		
		// Get Authors
		$filter = array();
		if($authors_inc = $params->get('authors_inc')) {			
			$filter['authors'] = $authors_inc ;
		}				
		if($authors_not_inc = $params->get('authors_not_inc')) {			
			$filter['notauthors'] = $authors_not_inc;
		}	
		if(isset($sess['authorslist']))
		{
			$filter['select.default'] = $sess['authorslist'];
		}
		$filter['authorsource'] = $params->get('authorsource', 'both');
		// $filter['published'] = 1;
		$filter['published']	= $params->get('pub_options', 1);
		
		$authorslist = array();
		if($filter['authorsource'] != 'onlyoriginal') {
			$items	= EGOSource::_($params->get('content_service'))
								->getAuthorsAlias($filter);	
			foreach ($items as $item)
			{
				$authorslist['a--' . $item->egauthor_alias] = $item->egauthor_alias;
			}
		}
		
		if($filter['authorsource'] != 'onlyalias') {
			$items	= EGOSource::_($params->get('content_service'))
								->getAuthors($filter);			
			foreach ($items as $item)
			{
				$authorslist[JFactory::getUser($item->egauthor_id)->username] = $item->egauthor;
			}
		}

		$arr = array();	
		// $arr[] = JHTML::_('select.option', '0', JText::_( 'COM_EGOLTARCHIVE_ALL' ));
		$output = '<ul class="egmenu" >';
		
		foreach($authorslist as $atlink => $atname)
		{
			$filter = array();
			if(strpos($atlink, 'a--') === 0)
			{
				$sesslike = 'alias:' . substr($atlink, 3);
				$filter['created_by_alias'] = substr($atlink, 3);
			}
			else
			{
				$sesslike = JUserHelper::getUserId($atlink);
				$filter['created_by'] = $sesslike;
			}
			$selected = (@$sess['authorslist'] == $sesslike) ? 'selected' : '';
			
			// Get authors articles count
			if($params->get('unauth'))
			{
				$authorised = null;
			}
			else
			{
				$authorised = $user->getAuthorisedViewLevels();
			}
			$filter['authorised']	= $authorised;
			$filter['cat_inc']		= isset($cat_inc) ? $cat_inc: null;
			$filter['cat_noinc']	= ($params->get('category_not_inc')) ? $params->get('category_not_inc'): null;
			// $filter['published']	= 1;
			$filter['published']	= $params->get('pub_options', 1);
			$query = EGOSource::_($params->get('content_service'))->getItemsQR($filter);
			$db->setQuery( (string)$query );	
			if (!$db->execute()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
			$anumrows = $db->getNumRows();	

			// die($sesslike);
			// die($sess['authorslist']);
			$trailer2 = $trailer . '&author=' . $atlink ;
			$output .= '<li class="egauthor '.$selected.'"><a href='. JRoute::_('index.php?' . $trailer2) .' >'. $atname .' <small>('. $anumrows .')</small></a></li>';
			
			if(@!$filter2['disyears'])
				continue;
			
			for($i=$config['starty']; $i<=$config['endy']; $i++)
			{					
				$cat_inc = $this->getCatsAccess();
					
				$tdates	= EGODate::_($cltype)->getDurs($i);
					
				$filter = array();
				
				if(strpos($atlink, 'a--') === 0)
				{
					$filter['created_by_alias'] = substr($atlink, 3);
				}
				else
				{
					$filter['created_by'] = JUserHelper::getUserId($atlink);
				}
				
				if($params->get('unauth'))
				{
					$authorised = null;
				}
				else
				{
					$authorised = $user->getAuthorisedViewLevels();
				}
				
				$filter['startdate']	= $tdates['start'];
				$filter['enddate']		= $tdates['end'];
				$filter['authorised']	= $authorised;
				$filter['cat_inc']		= isset($cat_inc) ? $cat_inc: null;
				$filter['cat_noinc']	= ($params->get('category_not_inc')) ? $params->get('category_not_inc'): null;
				$filter['date_select']	= 'date';
				// $filter['published']	= 1;
				$filter['published']	= $params->get('pub_options', 1);
				$query = EGOSource::_($params->get('content_service'))->getItemsQR($filter);
				$db->setQuery( (string)$query );	
				if (!$db->execute()) {
					$this->setError($db->getErrorMsg());
					return false;
				}
				$numrows = $db->getNumRows();
				if(!$numrows and $params->get('auto_dateselect', 1)) {	
					continue;
				}
				$selected = (@$sess['dateselect'] == $i .'-all') ? 'selected' : '';
				$output .= '<li class="egyear2 '.$selected.'"><a href='. JRoute::_('index.php?dateselect='. $i .'-all&rbtab=1'.$trailer2) .' >'. $i .' <small>('.$numrows.')</small></a></li>';	
				// $arr[] = JHTML::_('select.option', $i.'-all', $i);	
				
				if(@$filter2['dismonths']) {
				
					$output .= '<li><ul>';
					foreach(EGODate::_($cltype)->getMonths() as $key => $month)
					{
						if($i == $config['starty']) {
							if($key < $config['startm'])
							{
								continue;
							}
						}
						elseif($i == $config['endy']) {
							if($key > $config['endm'])
							{
								continue;
							}
						}

						$tdates	= EGODate::_($cltype)->getDurs($i, $key);
						$filter['startdate']	= $tdates['start'];
						$filter['enddate']		= $tdates['end'];
						$query = EGOSource::_($params->get('content_service'))->getItemsQR($filter);
						$db->setQuery( (string)$query );	
						if (!$db->execute()) 
						{
							$this->setError($db->getErrorMsg());
							return false;
						}
						$numrows = $db->getNumRows();
						if(!$numrows and $params->get('auto_dateselect', 1)) {	
							continue;
						}
						$selected = (@$sess['dateselect'] == $i.'-'.$key) ? 'selected' : '';
							
						$output .= '<li class="egmonth2 '.$selected .'"><a href='. JRoute::_('index.php?dateselect='. $i.'-'.$key .'&rbtab=1'.$trailer2) .' > - '. $month .' <small>('. $numrows .')</small></a></li>';	
						// $arr[] = JHTML::_('select.option', $i.'-'.$key , $i.' - '.$month);
					}
					$output .= '</ul></li>';
				
				}
			}
		}
		$output .= '</ul>';
		
		return $output;
	}
	
	// Get Ordering List
	public function getOrderinglist()
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		
		$filter = array();
		if(isset($sess['orderinglist']))
		{
			$filter['select.default'] = $sess['orderinglist'];
		}
		else
		{
			$filter['select.default'] = $params->get('default_order', 'newer_first');
		}
		$filter['select.extra'] = ' onchange="form.submit()" ';
		$filter['select.name'] = 'orderinglist';
		
		$orderinglist = EGOSource::_($params->get('content_service'))
								->getSortsList($filter);

		return $orderinglist;
	}
	
	// Get Date select List
	public function getDatelist()
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		$db		= $this->_db;
		$user	= $this->_user;
		$config = $this->getDatepoints();
		$cltype	= $params->get('cltype');

		$arr = array();	
		$arr[] = JHtml::_('select.option', '0', JText::_( 'COM_EGOLTARCHIVE_ALL' ));
		for($i=$config['starty']; $i<=$config['endy']; $i++)
		{
			if($params->get('auto_dateselect', 1))
			{					
				$cat_inc = $this->getCatsAccess();
				
				if($params->get('unauth'))
				{
					$authorised = null;
				}
				else
				{
					$authorised = $user->getAuthorisedViewLevels();
				}
					
				$tdates	= EGODate::_($cltype)->getDurs($i);
				$filter = array();
				$filter['startdate']	= $tdates['start'];
				$filter['enddate']		= $tdates['end'];
				$filter['authorised']	= $authorised;
				$filter['cat_inc']		= isset($cat_inc) ? $cat_inc: null;
				$filter['cat_noinc']	= ($params->get('category_not_inc')) ? $params->get('category_not_inc'): null;
				$filter['date_select']	= 'date';
				// $filter['published']	= 1;
				$filter['published']	= $params->get('pub_options', 1);
				$query = EGOSource::_($params->get('content_service'))->getItemsQR($filter);
				$db->setQuery( (string)$query );	
				if (!$db->execute()) {
					$this->setError($db->getErrorMsg());
					return false;
				}
				if(!$db->getNumRows()) {	
					continue;
				}
			}			
			$arr[] = JHtml::_('select.option', $i.'-all', $i);	
			
			foreach(EGODate::_($cltype)->getMonths() as $key => $month)
			{
				if($i == $config['starty']) {
					if($key < $config['startm'])
					{
						continue;
					}
				}
				elseif($i == $config['endy']) {
					if($key > $config['endm'])
					{
						continue;
					}
				}

				if($params->get('auto_dateselect', 1))
				{
					$tdates	= EGODate::_($cltype)->getDurs($i, $key);
					$filter['startdate']	= $tdates['start'];
					$filter['enddate']		= $tdates['end'];
					$query = EGOSource::_($params->get('content_service'))->getItemsQR($filter);
					$db->setQuery( (string)$query );	
					if (!$db->execute()) {
						$this->setError($db->getErrorMsg());
						return false;
					}
					if(!$db->getNumRows()) {	
						continue;
					}
				}
				
				$arr[] = JHtml::_('select.option', $i.'-'.$key , $i.' - '.$month);
			}
		}
		$dateselect  =  JHtml::_('select.genericlist', $arr, 'dateselect', ' onchange="form.submit()" ', 'value', 'text', @$sess['dateselect']);
		
		return $dateselect;
	}
	
	// Get Date Link List
	public function getDateLinkList($filter = array())
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		$db		= $this->_db;
		$user	= $this->_user;
		$config = $this->getDatepoints();
		$cltype	= $params->get('cltype');
		
		$trailer = '';
		if(isset($filter['eghidesearch']) and ($filter['eghidesearch']))
		{
			$trailer .= '&eghidesearch=1';
		}
		if(isset($filter['menu']) and ($filter['menu']))
		{
			$trailer .= '&Itemid=' . $filter['menu'];
		}

		$arr = array();	
		// $arr[] = JHTML::_('select.option', '0', JText::_( 'COM_EGOLTARCHIVE_ALL' ));
		$output = '<ul class="egmenu" >';
		for($i=$config['starty']; $i<=$config['endy']; $i++)
		{					
			$cat_inc = $this->getCatsAccess();
				
			$tdates	= EGODate::_($cltype)->getDurs($i);
			
			if($params->get('unauth'))
			{
				$authorised = null;
			}
			else
			{
				$authorised = $user->getAuthorisedViewLevels();
			}
				
			$filter = array();
			$filter['startdate']	= $tdates['start'];
			$filter['enddate']		= $tdates['end'];
			$filter['authorised']	= $authorised;
			$filter['cat_inc']		= isset($cat_inc) ? $cat_inc: null;
			$filter['cat_noinc']	= ($params->get('category_not_inc')) ? $params->get('category_not_inc'): null;
			$filter['date_select']	= 'date';
			// $filter['published']	= 1;
			$filter['published']	= $params->get('pub_options', 1);
			$query = EGOSource::_($params->get('content_service'))->getItemsQR($filter);
			$db->setQuery( (string)$query );	
			if (!$db->execute()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
			$numrows = $db->getNumRows();
			if(!$numrows and $params->get('auto_dateselect', 1)) {	
				continue;
			}
			$selected = (@$sess['dateselect'] == $i .'-all') ? 'selected' : '';
			$output .= '<li class="egyear '.$selected.'"><a href='. JRoute::_('index.php?dateselect='. $i .'-all&rbtab=1'.$trailer) .' >'. $i .' <small>('.$numrows.')</small></a></li>';	
			// $arr[] = JHTML::_('select.option', $i.'-all', $i);	
			
			$output .= '<li><ul>';
			foreach(EGODate::_($cltype)->getMonths() as $key => $month)
			{
				if($i == $config['starty']) {
					if($key < $config['startm'])
					{
						continue;
					}
				}
				elseif($i == $config['endy']) {
					if($key > $config['endm'])
					{
						continue;
					}
				}

				$tdates	= EGODate::_($cltype)->getDurs($i, $key);
				$filter['startdate']	= $tdates['start'];
				$filter['enddate']		= $tdates['end'];
				$query = EGOSource::_($params->get('content_service'))->getItemsQR($filter);
				$db->setQuery( (string)$query );	
				if (!$db->execute()) 
				{
					$this->setError($db->getErrorMsg());
					return false;
				}
				$numrows = $db->getNumRows();
				if(!$numrows and $params->get('auto_dateselect', 1)) {	
					continue;
				}
				$selected = (@$sess['dateselect'] == $i.'-'.$key) ? 'selected' : '';
					
				$output .= '<li class="egmonth '.$selected .'"><a href='. JRoute::_('index.php?dateselect='. $i.'-'.$key .'&rbtab=1'.$trailer) .' > - '. $month .' <small>('. $numrows .')</small></a></li>';	
				// $arr[] = JHTML::_('select.option', $i.'-'.$key , $i.' - '.$month);
			}
			$output .= '</ul></li>';
		}
		$output .= '</ul>';
		
		return $output;
	}
	
	// Get Date Points
	public function getDatepoints()
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		$cltype		= $params->get('cltype');
		$mindateup	= $this->getMinDate();

		$config['starty_set'] = $config['starty'] = EGODate::_($cltype)->show($mindateup, 'Y');
		$config['startm_set'] = $config['startm'] = EGODate::_($cltype)->show($mindateup, 'm');
		$config['startd_set'] = $config['startd'] = EGODate::_($cltype)->show($mindateup, 'd');
		$config['endy_set'] = $config['endy'] = EGODate::_($cltype)->show(null, 'Y');
		$config['endm_set'] = $config['endm'] = EGODate::_($cltype)->show(null, 'm');
		$config['endd_set'] = $config['endd']	= EGODate::_($cltype)->show(null, 'd');	
		
		if(isset($sess['rbtab']))
		{
			if($sess['rbtab'] == 3) 
			{
				if(isset($sess['startdate']))
				{
					$config['date_set']	= true;
					$config['starty_set'] = EGODate::_($cltype)->show($sess['startdate'], 'Y');
					$config['startm_set'] = EGODate::_($cltype)->show($sess['startdate'], 'm');
					$config['startd_set'] = EGODate::_($cltype)->show($sess['startdate'], 'd');
					
					$config['endy_set'] = EGODate::_($cltype)->show($sess['enddate'], 'Y');
					$config['endm_set'] = EGODate::_($cltype)->show($sess['enddate'], 'm');	
					$config['endd_set'] = EGODate::_($cltype)->show($sess['enddate'], 'd');
				}	
			}
		}

		return $config;
	}
	
	// Get Date Points
	public function getDurList()
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		$cltype	= $params->get('cltype');
		
		$arr = array (
			JHtml::_('select.option', '0', JText::_( 'COM_EGOLTARCHIVE_ALL' )) ,
			JHtml::_('select.option', 'daily', JText::_( 'COM_EGOLTARCHIVE_DAILY' )) ,
			JHtml::_('select.option', 'weekly', JText::_( 'COM_EGOLTARCHIVE_WEEKLY' )) ,
			JHtml::_('select.option', '15day', JText::_( 'COM_EGOLTARCHIVE_15_DAY' )) ,
			JHtml::_('select.option', 'monthly', JText::_( 'COM_EGOLTARCHIVE_MONTHLY' )),
			JHtml::_('select.option', '3month', JText::_( 'COM_EGOLTARCHIVE_3_MONTH' )) ,
			JHtml::_('select.option', '6month', JText::_( 'COM_EGOLTARCHIVE_6_MONTH' )) ,
			JHtml::_('select.option', '9month', JText::_( 'COM_EGOLTARCHIVE_9_MONTH' )) ,
			JHtml::_('select.option', 'yearly', JText::_( 'COM_EGOLTARCHIVE_YEARLY' )) 
		);	
		if(isset($sess['rbtab']))
		{				
			if($sess['rbtab'] == 2) {
					$durrec = @$sess['duration'];
			}
		}
		$duration  =  JHTML::_('select.genericlist', $arr, 'duration', ' onchange="form.submit()" ', 'value', 'text',@$durrec);	
		
		return $duration;
	}
	
	// Return Date Points
	public function getBetweenList()
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		$cltype	= $params->get('cltype');
		$points = $this->getDatepoints();
		
		$arr = array();
		foreach(EGODate::_($cltype)->getMonths() as $key => $month)
		{
			$arr[] = JHtml::_('select.option', $key, $month) ;		
		}

		$output['starty'] = JHtml::_('select.integerlist', $points['starty'], $points['endy'], 1, 'starty', null, $points['starty_set'] );
		
		$output['endy'] = JHtml::_('select.integerlist', $points['starty'], $points['endy'], 1, 'endy', null, $points['endy_set'] );
		
		$output['startm'] = JHtml::_('select.genericlist', $arr, 'startm', null, 'value', 'text', $points['startm_set'] );
		
		$output['endm'] = JHtml::_('select.genericlist', $arr, 'endm', null, 'value', 'text', $points['endm_set'] );
		
		$output['startd'] = JHtml::_('select.integerlist', 1, 31, 1, 'startd', null, $points['startd_set'] );
		
		$output['endd'] = JHtml::_('select.integerlist', 1, 31, 1, 'endd', null, $points['endd_set'] );		
		
		return $output;
	}
	
	// Return Category could be accessed
	public function getCatsAccess()
	{
		$params = $this->_params;
		$sess	= $this->_sess;
		
		$output = array();
		if($tmp = $params->get('category_inc'))
			$catinc		= is_array($tmp) ? $tmp : array($tmp);
		
		if(isset($sess['catlist']))
			$catsess	= is_array($sess['catlist']) ? $sess['catlist'] : array($sess['catlist']);
			
		if( isset($catsess) and (!isset($catinc)) )
		{
			$output = $catsess;	
		}
		elseif( (!isset($catsess)) and isset($catinc) )
		{
			$output = $catinc;
		}
		elseif( isset($catsess) and isset($catinc) )
		{
			$output = $catsess;	
		}

			
		if(!empty($output))
			return $output;
		else
			return;
	}
}