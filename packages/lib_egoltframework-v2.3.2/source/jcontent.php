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

class EGOSourceJcontent
{
	var $_cols;
	var $_db;
	
	function __construct()
	{
		$this->_db		= JFactory::getDBO();
		$this->_cols = array(
			'egid'			=> 'a.id',
			'egtitle'		=> 'a.title', 
			'egalias'		=> 'a.alias',
			'egintro'		=> 'a.introtext', 
			'egfull'		=> 'a.fulltext',
			'egcat'			=> 'cat.title',			
			'egcat_id'		=> 'a.catid',
			'egcat_alias'	=> 'cat.alias',
			'egcreate'		=> 'a.created',
			'egdate'		=> 'a.created',
			'egmodify'		=> 'a.modified',			
			'egstart'		=> 'a.publish_up', 
			'egend'			=> 'a.publish_down',
			'egimage'		=> 'a.images',
			'egauthor'		=> 'ua.name',
			'egauthor_id'	=> 'a.created_by',
			'egauthor_email'=> 'ua.email',
			'egauthor_alias'=> 'a.created_by_alias',
			'egpublish'		=> 'a.state',
			'eghits'		=> 'a.hits',
			'egaccess'		=> 'a.access'		
		);
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'jcontent';
		$info['title']	= 'Joomla! Content';
		$info['compat']	= '3.1';
		$info['compat_start']	= '2.5';

		return $info;
	}
	
	public function getExist()
	{
		$db		= JFactory::getDbo();
		$tables = $db->getTableList();
		$pref	= $db->getPrefix();
		if(in_array( $pref.'content', $tables))
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
		
		$query->from('#__content as a');

		if( (isset($filter['authorised'])) and (isset($this->_cols['egaccess'])) )
		{
			$query->where( $this->_cols['egaccess'] . ' IN(' . implode(',', $filter['authorised']) . ')');
		}
		
		if( (isset($filter['cat_inc'])) and (isset($this->_cols['egcat_id'])) ) 
		{
			if( (isset($filter['cat_inc_subs'])) and  (@$filter['cat_inc_subs']>-1) )
			{
				$cats = $this->getCats();
				$cats_filter = is_array($filter['cat_inc'])? $filter['cat_inc'] : explode(',', $filter['cat_inc']);
				$clist = $cats_filter;
				foreach($cats_filter as $cat_filter)
				{
					$clist = array_merge($clist, EGOHtmlList::getSubcats($cats, $cat_filter, $filter['cat_inc_subs']));
				}
				$filter['cat_inc'] = implode(',', $clist);
			}
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
			$query->join('LEFT', '#__categories AS cat ON cat.id = ' . $this->_cols['egcat_id']);
			$query->where('cat.extension = ' . $db->quote('com_content') );
		}
		
		// Join over the users table for authors name
		if(isset($this->_cols['egauthor_id']))
			$query->join('LEFT', '#__users AS ua ON ua.id = ' . $this->_cols['egauthor_id']);
			
		// die( nl2br(str_replace('#_', 'monsl', $query)) );
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
		$filter['extension'] = 'com_content';
		$cats = new EGOSourceCats();
		$query = $cats->getItemsQR($filter);
		
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
		
			if( (isset($filter['cat_inc_subs'])) and  (@$filter['cat_inc_subs']>-1) )
			{
				$cats = $this->getCats();
				$cats_filter = is_array($filter['cats'])? $filter['cats']: explode(',', $filter['cats']);
				$clist = $cats_filter;
				foreach($cats_filter as $cat_filter)
				{
					$clist = array_merge($clist, EGOHtmlList::getSubcats($cats, $cat_filter, $filter['cat_inc_subs']));
				}
				$filter['cats'] = implode(',', $clist);
			}
			
		$items	= $this->getCats($filter);	
		
		if(!isset($filter['select.allcat.text']))
		{
			$filter['select.allcat.text'] = JText::_('EGO_ALL_CATEGORIES');
		}
		
		if(isset($filter['select.allcat'])) 
		{
			$options[] = JHtml::_('select.option', 0, $filter['select.allcat.text']);
		}
		foreach ($items as $item)
		{
			$repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
			$item->title = str_repeat('- ', $repeat) . $item->title;
			$options[] = JHtml::_('select.option', $item->id, $item->title);
		}
		
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
		return JHtml::_('select.genericlist', $options, $filter['select.name'], $attr, 'value', 'text', @$filter['select.default'] );
	}

	public function getLink($id, $catid = null, $config= array()) 
	{
		$db	= $this->_db;
		require_once(JPATH_SITE . '/components/com_content/helpers/route.php');
			
		if(!isset($catid))
		{				
			$filter = array();
			$filter['items'] = array($id);
			$query = $this->getItemsQR($filter);
				
			$db->setQuery($query);
			$row = $db->loadObject();
			if(!empty($row))
			{
				$catid = $row->egcat_id ;
			}
		}

		$url = JRoute::_(ContentHelperRoute::getArticleRoute($id, $catid));
		
		return $url;
	}
	
	public function getCatlink($catid = null, $catalias = null, $filter= array()) 
	{
		require_once(JPATH_SITE . '/components/com_content/helpers/route.php');

		$url = JRoute::_(ContentHelperRoute::getCategoryRoute($catid));
		return $url;
	}
	
	public function getImages($image = null, $text = null , $multi = false)
	{
		$output = array();
		
		if(isset($text))
		{
			$regex   = "/<img[^>]+src\s*=\s*[\"']\/?([^\"']+)[\"'][^>]*\>/";
			// $regex = '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i';
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
			$images = json_decode($image);	
			if(isset($images->image_intro) and !empty($images->image_intro))
			{
					$output['url'] = $images->image_intro;			
			}
			elseif (isset($images->image_fulltext) and !empty($images->image_fulltext))
			{
					$output['url'] = $images->image_fulltext;			
			}	
		}
		
		if(isset($output['url']))
		{
			// remote image
			if(substr($output['url'], 0, 4 ) === "http" || substr( $output['url'], 0, 4 ) === "www." )
			{
				if(substr($output['url'], 0, 4 ) === "http")
				{
					$imgpath = $output['url'];
				}
				else if(substr( $output['url'], 0, 4 ) === "www.")
				{
					$imgpath = 'http://' . $imgpath;
				}
				$url = $imgpath;
				$path = parse_url($url, PHP_URL_PATH);
				$filename = basename($path);
				// $partOne = strtok($filename, '.');
				$imgpath = JPATH_SITE .'/cache/egoltarchive/' . $filename;
				if(!file_exists($imgpath)) {
					try{
						file_put_contents($imgpath, file_get_contents($url));
					}catch(Exception $ex){
						return false;
					}
				}

				$info		= @getimagesize($imgpath);	
				if($info)
				{
					$output['url'] = 'cache/egoltarchive/'  . $filename;

					$output['pathinc']	= $imgpath;
					$output['width']	= $info[0];
					$output['height']	= $info[1];
					$output['type']		= $info[2];
					$output['attributes'] = $info[3];
					$output['mime']		= $info['mime'];
				}
				else
				{
					return false;
				}


			}
			// local image
			else {
				$output['url'] = urldecode(parse_url($output['url'], PHP_URL_PATH));
				$imgpath	= JPATH_SITE .'/' . $output['url'];
				$info		= @getimagesize($imgpath);	
				if($info)
				{
					$output['pathinc']	= $imgpath;
					$output['width']	= $info[0];
					$output['height']	= $info[1];
					$output['type']		= $info[2];
					$output['attributes'] = $info[3];
					$output['mime']		= $info['mime'];
				}
				else
				{
					return false;
				}
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
