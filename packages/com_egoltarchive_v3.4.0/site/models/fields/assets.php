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

jimport('joomla.form.formfield');

class JFormFieldAssets extends JFormField {

    protected $type = 'Assets';

    protected function getInput() {		
	
		JHtml::_('behavior.framework');		
		$document	= JFactory::getDocument();		
		if (!version_compare(JVERSION, '3.0', 'ge')) {
			$needJquery = true;
			$header = $document->getHeadData();
			foreach($header['scripts'] as $scriptName => $scriptData)
			{
				if(substr_count($scriptName,'/jquery')){
					$needJquery = false;
				}
			}	
			// Add jQuery if needed
			if($needJquery) 
				$document->addScript(JUri::root().$this->element['path'].'js/jquery.min.js');
		}

		if(version_compare(JVERSION, '3.0', 'ge')) { // J3.x
			$document->addStyleSheet(JUri::root().$this->element['path'].'css/adminmenu-j3.css');   
			$document->addScript(JUri::root().$this->element['path'].'js/adminmenu-j3.js');
		}
		else {
			$document->addStyleSheet(JUri::root().$this->element['path'].'css/adminmenu.css');   
			$document->addScript(JUri::root().$this->element['path'].'js/adminmenu.js');		
		}
                
        return null;
    }
}