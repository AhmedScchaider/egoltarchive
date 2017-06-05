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

<div class="egoltarchive <?php echo $this->psfx?>">

	<?php if ($this->pparams->get('show_page_heading')) : ?>
	<h1 class="egheading">
		<?php echo $this->escape($this->pparams->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	
	<?php if($this->params->get('display_searchbox', 1)) : ?>
		<?php echo $this->loadTemplate('searchbox'); ?>
	<?php endif; ?>
	
	<?php if(count($this->items)): ?>	
	
		<?php if($this->params->get('paging_position') != 'bottom') : ?>
			<?php echo $this->loadTemplate('paging'); ?>
		<?php endif; ?>

		<?php echo $this->loadTemplate('v.' . $this->viewtype); ?>

		<?php if($this->params->get('paging_position') != 'top') : ?>
			<?php echo $this->loadTemplate('paging'); ?>
		<?php endif; ?>	
	
	<?php else: ?>
		<div class="noresult"><?php echo JText::_('COM_EGOLTARCHIVE_NO_RESULT'); ?></div>
	<?php endif; ?>
	
</div>
