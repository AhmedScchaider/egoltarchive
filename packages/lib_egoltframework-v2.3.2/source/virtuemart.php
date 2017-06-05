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

class EGOSourceVirtuemart
{
	var $_cols;
	var $_db;
	var $_tag;
	
	function __construct()
	{
		$lang 			= JFactory::getLanguage();
		$this->_tag 	= strtolower(str_replace('-', '_', $lang->getTag()));
		$this->_db		= JFactory::getDBO();
		$this->_cols = array(
			'egid'			=> 'a.virtuemart_product_id',
			'egtitle'		=> 'l.product_name',
			'egalias'		=> 'l.slug',
			'egintro'		=> 'l.product_s_desc', 
			'egfull'		=> 'l.product_desc',
			'egcat'			=> 'lcat.category_name',			
			'egcat_id'		=> 'cat.virtuemart_category_id',
			'egcat_alias'	=> 'lcat.slug',
			'egcreate'		=> 'a.created_on',
			'egdate'		=> 'a.created_on',
			'egmodify'		=> 'a.modified_on',			
			'egstart'		=> 'a.product_available_date', 
			// 'egend'			=> 'a.product_available_date',
			'egimage'		=> 'a.virtuemart_product_id',
			// 'egauthor'		=> 'ua.name',
			// 'egauthor_id'	=> 'a.created_by',
			// 'egauthor_email'=> 'ua.email',
			// 'egauthor_alias'=> 'a.created_by_alias',
			'egpublish'		=> 'a.published',
			'eghits'		=> 'a.hits',
			// 'egaccess'		=> 'a.access'		
		);
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'virtuemart';
		$info['title']	= 'VirtueMart';
		$info['compat']	= '2.0.22b';
		$info['compat_start']	= '2.0.20a';

		return $info;
	}
	
	public function getExist()
	{
		$db		= JFactory::getDbo();
		$tables = $db->getTableList();
		$pref	= $db->getPrefix();
		if(in_array( $pref.'virtuemart_products', $tables))
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
		
		$query->from('#__virtuemart_products as a');

		if( (isset($filter['authorised'])) and (isset($this->_cols['egaccess'])) )
		{
			$query->where( $this->_cols['egaccess'] . ' IN(' . implode(',', $filter['authorised']) . ')');
		}
		
		if( (isset($filter['cat_inc'])) and (isset($this->_cols['egcat_id'])) ) 
		{						
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
		
		// Product Media and Images
		// $query->join('LEFT', '#__virtuemart_product_medias AS m ON m.virtuemart_product_id = a.virtuemart_product_id' );
		// $query->join('LEFT', '#__virtuemart_medias AS ms ON ms.virtuemart_media_id = m.virtuemart_media_id' );
		// $query->where( 'ms.published = 1');
		

		// Product Language
		$query->join('LEFT', '#__virtuemart_products_'. $this->_tag .' AS l ON l.virtuemart_product_id = a.virtuemart_product_id' );
		
		// Join over the categories table for category name
		if(isset($this->_cols['egcat_id']))
		{
			$query->join('LEFT', '#__virtuemart_product_categories AS cat ON cat.virtuemart_product_id = a.virtuemart_product_id' );
	
			$query->join('LEFT', '#__virtuemart_categories_'. $this->_tag .' AS lcat ON lcat.virtuemart_category_id = cat.virtuemart_category_id' );
			
		}
		
		// Join over the users table for authors name
		if(isset($this->_cols['egauthor_id']))
			$query->join('LEFT', '#__users AS ua ON ua.id = ' . $this->_cols['egauthor_id']);
			
		// die( nl2br(str_replace('#_', 'tbaom', $query)));
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

		$query->select('cat.virtuemart_category_id as id, lcat.category_name as title, f.category_parent_id AS parent');
		$query->from('#__virtuemart_product_categories AS cat');
		// $query->where('a.parent_id > 0');
		
		$query->join('LEFT', '#__virtuemart_categories AS g ON g.virtuemart_category_id = cat.virtuemart_category_id' );
	
		$query->join('LEFT', '#__virtuemart_categories_'. $this->_tag .' AS lcat ON lcat.virtuemart_category_id = cat.virtuemart_category_id' );
		
		$query->join('LEFT', '#__virtuemart_category_categories AS f ON f.category_child_id = cat.virtuemart_category_id' );
			
		// Filter on included categories
		if(isset($filter['singlecat']))
		{
			$query->where('cat.virtuemart_category_id =' . $filter['singlecat']);
		}
		elseif(isset($filter['cats']))
		{
			$filter['cats'] = is_array($filter['cats'])? implode(',', $filter['cats']) : $filter['cats'];
			$query->where('cat.virtuemart_category_id IN (' . $filter['cats'] . ')');
		}
		
		// Filter on not included categories
		if(isset($filter['notcats']))
		{
			$filter['notcats'] = is_array($filter['notcats'])? implode(',', $filter['notcats']) : $filter['notcats'];
			$query->where('cat.virtuemart_category_id NOT IN (' . $filter['notcats'] . ')');
		}

		// Filter on the published state
		if (isset($filter['published']))
		{
			if (is_numeric($filter['published']))
			{
				$query->where('g.published = ' . (int) $filter['published']);
			}
			elseif (is_array($filter['published']))
			{
				JArrayHelper::toInteger($filter['published']);
				$query->where('g.published IN (' . implode(',', $filter['published']) . ')');
			}
		}
			
		$query->order('g.ordering');
		
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
		// require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'router.php');

		$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$id.'&virtuemart_category_id=' . $catid);
		
		return $url;
	}
	
	public function getCatlink($catid = null, $catalias = null, $filter= array()) 
	{
		// require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'router.php');
		
		$url = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $catid);
		
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
			$db		= $this->_db;
			$query	= $db->getQuery(true);
			
			$query->from('#__virtuemart_product_medias AS m');

			$query->select( 'm.*' );
			$query->where( 'm.virtuemart_product_id = ' . $image);

			$query->join('LEFT', '#__virtuemart_medias AS ms ON ms.virtuemart_media_id = m.virtuemart_media_id' );
			$query->select( 'ms.file_url' );
			$query->where( 'ms.published = 1');
			
			// die( nl2br(str_replace('#_', 'tbaom', $query)));
			
			$db->setQuery($query);
			$row = $db->loadObject();
				
			if(!empty($row)) 
			{		
				$output['url'] = $row->file_url;	
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
		if(false){
		
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
		return false;
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
