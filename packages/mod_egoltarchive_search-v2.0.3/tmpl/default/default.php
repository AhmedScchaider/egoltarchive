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
?>
<div id="egarmodsearch-<?php echo $module->id; ?>" class="egarmodsearch<?php echo $modsfx; ?>">
	<form action="<?php echo JRoute::_('index.php?Itemid='.$menu, false); ?>" method="post"  >
		<input class="aainput" type="text" name="search_str" size="30" id="search_str" value="keyword ..." onblur="if (this.value=='') this.value='keyword ...';" onfocus="if (this.value=='keyword ...') this.value='';">
		<br/>
		<input class="aasubmit" type="submit" name="aasubmit" id="aasubmit" value="<?php echo JText::_( 'MOD_EGOLTARCHIVE_SEARCH_SEARCH' ); ?>" />
				
		<input type="hidden" name="eghidesearch" value="<?php echo $eghide; ?>" />
		<input type="hidden" name="layout" value="default" />
		<input type="hidden" name="view" value="egoltarchive" />
		<input type="hidden" name="task" value="egoltarchive" />
		<input type="hidden" name="option" value="com_egoltarchive" />
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>
