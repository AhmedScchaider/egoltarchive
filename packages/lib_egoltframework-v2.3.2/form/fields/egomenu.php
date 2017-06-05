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

class JFormFieldEGOMenu extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'EGOMenu';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get some field values from the form.
		$lang		= JFactory::getLanguage();
		$lang_tag	= $lang->getTag();

		// Build the query for the list.
		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('a.id as mid, t.title as mttitle, a.link, a.title as mtitle');
		$query->from('#__menu as a');
		$query->join('LEFT', '#__menu_types AS t ON t.menutype = a.menutype');
		if($this->element['like'])
			$query->where('a.link LIKE \''.$this->element['like'].'%\'');
		$query->where('a.client_id = 0');
		$query->where('a.published = 1');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$options = array ();
		foreach ( $rows as $row )
		{
			$options[] = JHtml::_('select.option', $row->mid, '['.$row->mttitle.'] -> ' . $row->mtitle);		
		}

		// Render the HTML SELECT list.
		return JHtml::_('select.genericlist', $options, $this->name, $attr, 'value', 'text', $this->value );

	}
}
