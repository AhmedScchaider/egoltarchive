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

class modEgoltArchiveDateHelper
{
	var $_params;
	var $_cmparams;
	var $_model;

	function __construct($params)
	{		
		$config = array();
		if($params->get('auto_dateselect') != 'global')
		{
			$config['params']['auto_dateselect'] = $params->get('auto_dateselect');
		}
		$config['menu'] = $params->get('egmenu');
		
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_egoltarchive/models', 'EgoltArchiveModel');	
		$this->_params	= $params;
		$this->_model	= JModelLegacy::getInstance( 'EgoltArchive', 'EgoltArchiveModel', $config);
		$this->_cmparams= $this->_model->getParams();
	}

    public function getList()
    {
		$config = array(
			'menu' => $this->_params->get('egmenu'),
			'eghidesearch' => $this->_params->get('eghidesearch'),
		);
		$list = $this->_model->getDateLinkList($config);
		return $list;
    }
	
	public function getCMParams()
	{
		return $this->_cmparams;
	}

}
