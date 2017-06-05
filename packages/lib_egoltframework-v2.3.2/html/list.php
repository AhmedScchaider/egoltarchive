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

class EGOHtmlList
{
	public static function getTree( $src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected )
    {
		// establish the hierarchy of the menu
		$children = array();
		
		// first pass - collect children
		foreach ($src_list as $v )
		{
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		
		// second pass - get an indent list of the items
		jimport( 'joomla.html.html.menu' );

		$ilist = EGOHtmlList::treerecurse( 0, '', array(), $children );
   
		// assemble menu items to the array
		$this_treename = '';
		foreach ($ilist as $item) 
		{
			if ($this_treename) 
			{
				if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) 
				{
					$tgt_list[] = JHtml::_('select.option', $item->id, $item->treename );
				}
			} 
			else
			{
				if ($item->id != $src_id) 
				{
					$tgt_list[] = JHtml::_('select.option', $item->id, $item->treename );
				} 
				else 
				{
					$this_treename = "$item->treename/";
				}
			}
		}
		
		if(!isset($key))
			$key = 'value';

		if(!isset($text))
			$text = 'text';			
		
		// build the html select list
		return JHtml::_('select.genericlist', $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
      }
	  
	public static function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;
				$pre = '';
				$spacer = '- ';
					
				if (@$v->parent_id == 0)
				{
					$txt = $v->title;
				}
				else
				{
					$txt = $pre . $v->title;
				}
				@$pt = $v->parent_id;
				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list = EGOHtmlList::treerecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1);
			}
		}
		return $list;
	}
	
	public static function getSubcats($cats, $target, $level = 20)
	{
		$arr = array();
		foreach ($cats as $cat)
		{
			$cid	= $cat->id;
			$pid	= $cat->parent_id;
			if ( ($cat->parent_id > 1) and ($cat->parent_id == $target))
			{
				$arr = array_unique(array_merge($arr, array($cat->id)));
				if(--$level>0)
					$arr = array_unique(array_merge($arr, EGOHtmlList::getSubcats($cats, $cat->id, $level)));
			}
		}
		// die(var_dump($arr));
		return $arr;
	}
	  
}