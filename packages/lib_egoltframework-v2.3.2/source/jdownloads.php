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

// Check Egolt Framework
defined('_EGOINC') or die('Egolt Framework not installed!');

class EGOSourceJdownloads
{
	var $_cols;
	var $_db;
	
	function __construct()
	{
		$this->_db		= JFactory::getDBO();
		$this->_cols = array(
			'egid'			=> 'a.file_id',
			'egtitle'		=> 'a.file_title', 
			'egalias'		=> 'a.file_alias',
			'egintro'		=> 'a.description', 
			'egfull'		=> 'a.description_long',
			'egcat'			=> 'cat.cat_title',		
			'egcat_id'		=> 'a.cat_id',
			'egcat_alias'	=> 'cat.cat_alias',
			'egcreate'		=> 'a.date_added',
			'egdate'		=> 'a.date_added',
			'egmodify'		=> 'a.modified_date',
			'egstart'		=> 'a.publish_from', 
			'egend'			=> 'a.publish_to',
			'egimage'		=> 'a.file_pic',
			'egauthor'		=> 'ua.name',
			'egauthor_id'	=> 'a.created_id',
			'egauthor_email'=> 'a.url_author',
			'egauthor_alias'=> 'a.author',
			'egpublish'		=> 'a.published',
			'eghits'		=> 'a.downloads',
			// 'egaccess'		=> 'a.jaccess'	
		);
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'jdownloads';
		$info['title']	= 'jDownloads';
		$info['compat']	= '1.9.2.6 - Beta';
		$info['compat_start']	= '1.9.1 - B931';

		return $info;
	}
	
	public function getExist()
	{
		$db		= JFactory::getDbo();
		$tables = $db->getTableList();
		$pref	= $db->getPrefix();
		if(in_array( $pref.'jdownloads_files', $tables))
			return true;
		else
			return false;
	}
	
	public function getItemsQR($filter = array())
	{			
		$db			= $this->_db;
		$query		= $db->getQuery(true);
		$nullDate	= $db->getNullDate();
		// $now		= EGODate::_('gre')->toGre(null, 'Y-m-d h:i:s');
		$now		= JFactory::getDate()->toSql();
		
		if(isset($filter['customselect']))
		{
			$query->select($filter['customselect']);		
		}
		else
		{
			$query->select('a.*');
			$squery = '';
			foreach($this->_cols as $talias => $tname)
			{
				$squery .= $tname . ' as ' . $talias . ',';				
			}
			$squery = substr($squery, 0, -1);  
			$query->select($squery);
		}
		
		$query->from('#__jdownloads_files as a');

		if( (isset($filter['authorised'])) and (isset($this->_cols['egaccess'])) )
		{
			$query->where( $this->_cols['egaccess'] . ' IN(' . implode(',', $filter['authorised']) . ')');
		}
		
		if( (isset($filter['cat_inc'])) and (isset($this->_cols['egcat_id'])) ) 
		{	
			$filter['cat_inc'] = is_array($filter['cat_inc'])? implode(',', $filter['cat_inc']) : $filter['cat_inc'];
			$query->where( $this->_cols['egcat_id'] . ' IN(' . $filter['cat_inc'] . ')');			
		}	
		
		if( (isset($filter['cat_noinc'])) and (isset($this->_cols['egcat_id'])) ) 
		{
			$filter['cat_noinc'] = is_array($filter['cat_noinc'])? implode(',', $filter['cat_noinc']) : $filter['cat_noinc'];
			$query->where( $this->_cols['egcat_id'] . ' NOT IN(' . $filter['cat_noinc'] . ')');
		}
		
		if(isset($this->_cols['egstart'])) 
			$query->where('( '. $this->_cols['egstart'] .' = '.$db->Quote($nullDate).' OR '. $this->_cols['egstart'] .' <= '.$db->Quote($now).' )') ;
		if(isset($this->_cols['egend'])) 
			$query->where('( '. $this->_cols['egend'] .' = '.$db->Quote($nullDate).' OR '. $this->_cols['egend'] .' >= '.$db->Quote($now).' )');	
			
		if( (isset($filter['date_select'])) and (isset($this->_cols['egdate'])) )
		{
			if(
				($filter['date_select'] == 'exact') 
				or ($filter['date_select'] == 'date')
			) 
			{
				if(
					isset($this->_cols['egdate']) 
					and isset($filter['startdate'])
					and isset($filter['enddate'])
				) 
				{
					$query->where( $this->_cols['egdate'] .' BETWEEN \''. $filter['startdate'] .' 0:0\' AND \''. $filter['enddate'] .' 23:59\' ');
				}
			}
				
			if($filter['date_select'] == 'duration') 
			{
				if(isset($filter['duration'])) 
				{
					switch($filter['duration']) 
					{
						case 'daily':
							$dur_param = 'INTERVAL 1 DAY';
						break;
						
						case 'weekly':
							$dur_param = 'INTERVAL 7 DAY';
						break;
						
						case '15day':
							$dur_param = 'INTERVAL 15 DAY';
						break;
						
						case 'monthly':
							$dur_param = 'INTERVAL 1 MONTH';
						break;
						
						case '3month':
							$dur_param = 'INTERVAL 3 MONTH';
						break;
						
						case '6month':
							$dur_param = 'INTERVAL 6 MONTH';
						break;
							
						case '9month':
							$dur_param = 'INTERVAL 9 MONTH';
						break;
						
						case 'yearly':
							$dur_param = 'INTERVAL 1 YEAR';
						break;
					}
					$query->where( $this->_cols['egdate'] .' BETWEEN DATE_SUB(CURDATE(), '. $dur_param .') AND CURDATE()');
				}
			}
		}
		
		$query->where( $this->_cols['egdate'] .' <> '. $db->Quote($nullDate));

		if( (isset($filter['like_str']))
			and (isset($this->_cols['egtitle']))
			and (isset($this->_cols['egintro']))
			and (isset($this->_cols['egfull']))
		)
		{
			$search_str = $filter['like_str'] ;
			if(isset($filter['exact_like'])) {
				$str = "% {$search_str} %";
			}
			else {
				$str = "%{$search_str}%";
			}
			$query->where( "(". $this->_cols['egtitle'] ." LIKE '{$str}' OR ". $this->_cols['egintro'] ." LIKE '{$str}' OR ". $this->_cols['egfull'] ." LIKE '{$str}' ) ");
		}
			
		if( (isset($filter['not_like_str']))
			and (isset($this->_cols['egtitle']))
		)
		{
			$not_search_str = $filter['not_like_str'];
			if(isset($filter['exact_not_like'])) 
			{
				$str = "% {$not_search_str} %";
			}
			else 
			{
				$str = "%{$not_search_str}%";
			}
			$query->where( "(". $this->_cols['egtitle'] ." NOT LIKE '{$str}' AND ". $this->_cols['egintro'] ." NOT LIKE '{$str}' AND ". $this->_cols['egfull'] ." NOT LIKE '{$str}' ) ");
		}
		
		if( (isset($filter['created_by_alias'])) 
			and (isset($this->_cols['egauthor_alias']))
		)
		{
			$query->where( $this->_cols['egauthor_alias'] . ' = ' . $db->quote($filter['created_by_alias']));
		}
		
		if( (isset($filter['created_by']))
			and (isset($this->_cols['egauthor_id']))
		)
		{
			$query->where( $this->_cols['egauthor_id'] . ' = ' . $db->quote($filter['created_by']));
		}
		
		if(isset($filter['order']))
		{
			$query->order($db->escape($filter['order']));
		}
		
		// Filter on the published state
		if ( (isset($filter['published'])) and isset($this->_cols['egpublish']) )
		{
			if (is_numeric($filter['published']))
			{
				$query->where( $this->_cols['egpublish'] . ' = ' . (int) $filter['published']);
			}
			elseif (is_array($filter['published']))
			{
				JArrayHelper::toInteger($filter['published']);
				$query->where( $this->_cols['egpublish'] . ' IN (' . implode(',', $filter['published']) . ')');
			}
		}
		
		if( (isset($filter['items'])) and (isset($this->_cols['egid'])) )
		{
			$query->where( $this->_cols['egid'] . ' IN (' . implode(',', $filter['items']) . ')');
		}
		
		// Join over the categories table for category name
		if(isset($this->_cols['egcat_id']))
		{
			$query->join('LEFT', '#__jdownloads_cats AS cat ON cat.cat_id = ' . $this->_cols['egcat_id']);
			// $query->where('cat.extension = ' . $db->quote('com_content') );
		}
		
		// Join over the users table for authors name
		if(isset($this->_cols['egauthor_id']))
			$query->join('LEFT', '#__users AS ua ON ua.id = ' . $this->_cols['egauthor_id']);
			
		// die( $query );
		return $query;
	}
	
	public function getMinpubQR($filter = array())
	{			
		$filter['customselect'] = 'MIN('. $this->_cols['egdate'] .') as min_publish_up';
		$query = $this->getItemsQR($filter);
		
		return $query;
	}
	
	public function getCatsQR($filter = array())
	{			
		$db			= $this->_db;
		$query		= $db->getQuery(true);
		
		$filter['published'] = 1;

		$query->select('a.cat_id as id, a.cat_title as title, parent_id AS parent');
		$query->from('#__jdownloads_cats AS a');
		// $query->where('a.parent_id > 0');
			
		// Filter on included categories
		if(isset($filter['singlecat']))
		{
			$query->where('a.cat_id =' . $filter['singlecat']);
		}
		elseif(isset($filter['cats']))
		{
			$filter['cats'] = is_array($filter['cats'])? implode(',', $filter['cats']) : $filter['cats'];
			$query->where('a.cat_id IN (' . $filter['cats'] . ')');
		}
		
		// Filter on not included categories
		if(isset($filter['notcats']))
		{
			$filter['notcats'] = is_array($filter['notcats'])? implode(',', $filter['notcats']) : $filter['notcats'];
			$query->where('a.cat_id NOT IN (' . $filter['notcats'] . ')');
		}

		// Filter on the published state
		if (isset($filter['published']))
		{
			if (is_numeric($filter['published']))
			{
				$query->where('a.published = ' . (int) $filter['published']);
			}
			elseif (is_array($filter['published']))
			{
				JArrayHelper::toInteger($filter['published']);
				$query->where('a.published IN (' . implode(',', $filter['published']) . ')');
			}
		}
		
		// if(isset($filter['authorised']))
		// {
			// $query->where('a.access IN(' . implode(',', $filter['authorised']) . ')');
		// }
			
		$query->order('a.ordering');	
		
		return $query;
	}
	
	public function getCats($filter = array()) 
	{
		$db		= $this->_db;
		$query	= $this->getCatsQR($filter);
		$db->setQuery($query);
		
		if(isset($filter['singlecat']))
			$result = $db->loadObject();		
		else
			$result = $db->loadObjectList();
		
		return $result;
	}
	
	public function getCatsList($filter = array()) 
	{
		$options = array();
		$attr = '';
		$items	= $this->getCats($filter);	
		
		if(!isset($filter['select.allcat.text']))
		{
			$filter['select.allcat.text'] = JText::_('EGO_ALL_CATEGORIES');
		}
		
		if(isset($filter['select.allcat'])) 
		{
			$options[] = JHtml::_('select.option', 0, $filter['select.allcat.text']);
		}
		// foreach ($items as $item)
		// {
			// $repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
			// $item->title = str_repeat('- ', $repeat) . $item->title;
			// $options[] = JHtml::_('select.option', $item->id, $item->title);
		// }
		
		if(isset($filter['select.class']))
		{
			$attr .= ' class="'. (string) $filter['select.class'];		
		}		
		if(isset($filter['select.extra']))
		{
			$attr .= (string) $filter['select.extra'];		
		}
		if(isset($filter['select.extra']))
		{
			$attr .= (string) $filter['select.extra'];		
		}
		
		// Render the HTML SELECT list.
		return EGOHtmlList::getTree( $items, 0, $options, $filter['select.name'], $attr, 'value', 'text', @$filter['select.default'] );
	}

	public function getLink($id, $catid = null, $config= array()) 
	{
		$db		= $this->_db;
		// $Itemid = JRequest::getInt('Itemid');
		$cat_itemid = '';
		
		// Check Download Menus
		$db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=viewdownload%' AND published = 1");
		$cat_link_itemids = $db->loadAssocList();			
		if ($cat_link_itemids)
		{  
			for ($i=0; $i < count($cat_link_itemids); $i++)
			{
				$segments = explode('&', $cat_link_itemids[$i]['link']);
				$menu_catid = '';
				foreach($segments as $segment)
				{
					$segment = trim($segment);
					if(substr($segment, 0, 5) == 'catid')
					{
						$menu_catid = substr($segment, 6);
					}
					if(substr($segment, 0, 3) == 'cid')
					{
						$menu_cid = substr($segment, 4);
					}
				}
				
				if ( ($menu_catid == $catid) and ($menu_cid == $id) )
				{
					$cat_itemid = $cat_link_itemids[$i]['id'];
				}     
			}
		}
				
		// Check Category Menus
		if (!$cat_itemid)
		{
			$db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=viewcategory&catid%' AND published = 1");
			$cat_link_itemids = $db->loadAssocList();
			
			if ($cat_link_itemids)
			{  
				for ($i=0; $i < count($cat_link_itemids); $i++)
				{
					$segments = explode('&', $cat_link_itemids[$i]['link']);
					$menu_catid = '';
					foreach($segments as $segment)
					{
						$segment = trim($segment);
						if(substr($segment, 0, 5) == 'catid')
						{
							$menu_catid = substr($segment, 6);
							break;
						}
					}
					
					if ($menu_catid == $catid)
					{
						$cat_itemid = $cat_link_itemids[$i]['id'];
					}     
				}
			} 
		}
		
		// Check Categories List Menus
		if (!$cat_itemid)
		{
			$db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=viewcategories%' AND published = 1");
			$cat_link_itemids = $db->loadAssocList();
			
			if ($cat_link_itemids)
			{  
				for ($i=0; $i < count($cat_link_itemids); $i++)
				{
					$cat_itemid = $cat_link_itemids[$i]['id'];
					break;
				}
			}
		}
		
		// Check Other jDownload Menus
		if (!$cat_itemid)
		{
			$db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads%' AND published = 1");
			$cat_link_itemids = $db->loadAssocList();
			
			if ($cat_link_itemids)
			{  
				for ($i=0; $i < count($cat_link_itemids); $i++)
				{
					$cat_itemid = $cat_link_itemids[$i][id];
					break;
				}
			}
		}

		// use itemid when no single link exists
		// if (!$cat_itemid)
		// {
			// $cat_itemid = $Itemid;
		// }
		
		if(isset($config['alias']) and !empty($config['alias']))
		{
			$id .= '-' . $config['alias'];
		}
		
		if(isset($config['cat_alias']) and !empty($config['cat_alias']))
		{
			$catid .= '-' . $config['cat_alias'];
		}
 		
		$url = JRoute::_('index.php?option=com_jdownloads&amp;Itemid='. $cat_itemid .'&amp;view=viewdownload&amp;catid=' . $catid . '&amp;cid=' . $id );
		
		return $url;

	}
	
	public function getCatlink($catid = null, $catalias = null, $filter= array()) 
	{
		$db		= $this->_db;
		// $Itemid = JRequest::getInt('Itemid');
		$cat_itemid = '';

		$db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=viewcategory&catid%' AND published = 1");
		$cat_link_itemids = $db->loadAssocList();
				
		// Check Category Menus
		if ($cat_link_itemids)
		{  
			for ($i=0; $i < count($cat_link_itemids); $i++)
			{
				$segments = explode('&', $cat_link_itemids[$i]['link']);
				$menu_catid = '';
				foreach($segments as $segment)
				{
					$segment = trim($segment);
					if(substr($segment, 0, 5) == 'catid')
					{
						$menu_catid = substr($segment, 6);
						break;
					}
				}
				
				if ($menu_catid == $catid)
				{
					$cat_itemid = $cat_link_itemids[$i]['id'];
				}     
			}
		} 
		
		// Check Categories List Menus
		if (!$cat_itemid)
		{
			$db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads&view=viewcategories%' AND published = 1");
			$cat_link_itemids = $db->loadAssocList();
			
			if ($cat_link_itemids)
			{  
				for ($i=0; $i < count($cat_link_itemids); $i++)
				{
					$cat_itemid = $cat_link_itemids[$i][id];
					break;
				}
			}
		}
		
		// Check Other jDownload Menus
		if (!$cat_itemid)
		{
			$db->setQuery("SELECT id, link from #__menu WHERE link LIKE 'index.php?option=com_jdownloads%' AND published = 1");
			$cat_link_itemids = $db->loadAssocList();
			
			if ($cat_link_itemids)
			{  
				for ($i=0; $i < count($cat_link_itemids); $i++)
				{
					$cat_itemid = $cat_link_itemids[$i][id];
					break;
				}
			}
		}

		// use itemid when no single link exists
		// if (!$cat_itemid)
		// {
			// $cat_itemid = $Itemid;
		// }
		
		if(isset($catalias) and !empty($catalias))
		{
			$catid .= '-'. $catalias;
		}
 		
		$url = JRoute::_('index.php?option=com_jdownloads&amp;Itemid='. $cat_itemid .'&amp;view=viewcategory&amp;catid=' . $catid);
		
		return $url;
	}
	
	public function getImages($image = null, $text = null , $multi = false)
	{
		$output = array();
		
		if(isset($text))
		{
			$regex   = "/<img[^>]+src\s*=\s*[\"']\/?([^\"']+)[\"'][^>]*\>/";
			preg_match ($regex, $text, $matches);
			$images = (count($matches)) ? $matches : array();
			if ( count($images) )
			{
				if(!$multi)
				{
					$output['url'] = $images[1];
				}
			}
		}
		if(isset($image) and !empty($image))
		{
			$jpath = 'images/jdownloads/fileimages';
			$images = json_decode($image);	
			if(is_file(JPATH_SITE .'/' . $jpath . '/' . $image))
			{
					$output['url'] = $jpath . '/' . $image;			
			}
		}
		
		if(isset($output['url']))
		{
			$imgpath	= JPATH_SITE .'/' . $output['url'];
			$info		= getimagesize($imgpath);	
			if($info)
			{
				$output['pathinc']	= $imgpath;
				$output['width']	= $info[0];
				$output['height']	= $info[1];
				$output['type']		= $info[2];
				$output['attributes'] = $info[3];
				$output['mime']		= $info['mime'];
			}
		}
		else
		{
			return false;
		}
		
		return $output;
	}
	
	public function getAuthors($filter = array()) 
	{
			$db	= $this->_db;
			// $filter['customselect'] = 'DISTINCT a.created_by';
			$query = $this->getItemsQR($filter);
			
			// Join over the users table for authors name
			// $query->select('ua.id, ua.name AS author_name');
			// $query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
			
			$query->where($this->_cols['egauthor_id'] . ' <> \'\'');
			$query->group($this->_cols['egauthor_id']);
			$db->setQuery($query);
			
			return $db->loadObjectList();			
	}
	
	public function getAuthorsAlias($filter = array()) 
	{
			$db	= $this->_db;
			// $filter['customselect'] = 'DISTINCT a.created_by_alias';
			$query = $this->getItemsQR($filter);
			
			$query->where($this->_cols['egauthor_alias'] . ' <> \'\'');
			$query->group($this->_cols['egauthor_alias']);
			$db->setQuery($query);
			
			return $db->loadObjectList();			
	}
	
	public function getAuthorsList($filter = array()) 
	{
		$options = array();
		$attr = '';
		
		if(!isset($filter['select.allauthors.text']))
		{
			$filter['select.allauthors.text'] = JText::_('EGO_ALL_AUTHORS');
		}
		
		if(isset($filter['select.allauthors'])) 
		{
			$options[] = JHtml::_('select.option', 0, $filter['select.allauthors.text']);
		}	
		
		if(!isset($filter['authorsource'])) 
		{
			$filter['authorsource'] = 'both';
		}			

		if($filter['authorsource'] != 'onlyoriginal') {
			$items	= $this->getAuthorsAlias($filter);	
			foreach ($items as $item)
			{
				$options[] = JHtml::_('select.option', 'alias:' . $item->egauthor_alias, $item->egauthor_alias);
			}
		}
		
		if($filter['authorsource'] != 'onlyalias') {
			$items	= $this->getAuthors($filter);			
			foreach ($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->egauthor_id, $item->egauthor);
			}
		}
		
		if(isset($filter['select.class']))
		{
			$attr .= ' class="'. (string) $filter['select.class'];		
		}
		if(isset($filter['select.extra']))
		{
			$attr .= (string) $filter['select.extra'];		
		}
		
		
		// Render the HTML SELECT list.
		return JHtml::_('select.genericlist', $options, $filter['select.name'], $attr, 'value', 'text', @$filter['select.default'] );
	}
	
	public function getSorts($select = null, $filter = array()) 
	{
		$sort = array();
		$sort['newer_first']= $this->_cols['egid'] . ' DESC';
		$sort['older_first']= $this->_cols['egid'] . ' ASC';
		$sort['date_desc']	= $this->_cols['egdate'] . ' DESC';
		$sort['date_asc']	= $this->_cols['egdate'] . ' ASC';
		$sort['alpha_desc']	= $this->_cols['egtitle'] . ' DESC';
		$sort['alpha_asc']	= $this->_cols['egtitle'] . ' ASC';
		
		if(!isset($select))
			return $sort;
		else
			return $sort[$select];			
	}
	
	public function getSortsList($filter = array()) 
	{
		$options = array();
		$attr = '';
		$items = $this->getSorts();
		
		if(!isset($filter['select.text.prefix']))
		{
			$filter['select.text.prefix'] = 'EGO_';
		}
		
		foreach($items as $key => $val)
		{
			$opt_title = JText::_($filter['select.text.prefix'] . strtoupper($key));
			$options[] = JHtml::_('select.option', $key, $opt_title);			
		}	
				
		if(isset($filter['select.class']))
		{
			$attr .= ' class="'. (string) $filter['select.class'];		
		}
		if(isset($filter['select.extra']))
		{
			$attr .= (string) $filter['select.extra'];		
		}
		
		// Render the HTML SELECT list.
		return JHtml::_('select.genericlist', $options, $filter['select.name'], $attr, 'value', 'text', @$filter['select.default'] );
	}
}
