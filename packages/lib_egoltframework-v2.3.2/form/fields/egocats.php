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

class JFormFieldEGOCats extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'EGOCats';

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
		$attr .= $this->element['multiple'] ? ' multiple="'.(string) $this->element['multiple'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get some field values from the form.
		$lang		= JFactory::getLanguage();
		$lang_tag	= $lang->getTag();
		
		if(!is_array($this->value))
		{
			if(strpos($this->value, ','))
				$this->value = explode(',', $this->value);
			elseif($this->value)
				$this->value = array($this->value);
			else
				$this->value = array();
		}
		$this->value = array_unique($this->value);
		
		// preg_match('/[^\x00-\x7F]/', $this->name, $matches);
		$name2 = $this->name;
		$name2 = str_replace ("]", "", $name2);
		$name2 = str_replace ("[", "", $name2 );
		$name2 = str_replace (" ", "", $name2 );
		JFactory::getDocument()->addScriptDeclaration('var egjroot = "' . JUri::base() . '";');
		JFactory::getDocument()->addScriptDeclaration('var egnval_'. $name2 .' = new Array(\''.implode('\',\'', $this->value).'\');');
			
		$cnd = $this->element['source'];
		if(EGOSource::_($cnd)->getExist())
		{
			$filter['select.default'] = $this->value;
			$filter['select.name'] = $this->name;
			$filter['select.extra'] = $attr;
			$filter['published'] = 1;
			$output = EGOSource::_($cnd)->getCatsList($filter);
		}
		else
		{
			$options = array();
			$output = JHtml::_('select.genericlist', $options, $this->name, $attr, 'value', 'text', $this->value );
		}
		
		return $output;
		
		// $options = array ();
		// foreach ( $rows as $row )
		// {
			// $options[] = JHtml::_('select.option', $row->mid, '['.$row->mttitle.'] -> ' . $row->mtitle);		
		// }

	}
}
