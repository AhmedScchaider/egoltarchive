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
<div class="pagingblock" >
	<div class="grid pagediv" >
		<?php if(!$this->pagination->getPagesLinks()): ?>
			<ul>
				<li class="pagination-start">
					<span class="pagenav">
						 <?php echo JText::_('COM_EGOLTARCHIVE_TOTAL_RESULTS'); ?> 
					</span>
				</li>
			</ul>
		<?php else: ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		<?php endif; ?>
	</div>
	
	<?php if($this->params->get('display_pcounters', 1)) : ?>
	<div class="grid pagecounter">
		<?php echo $this->total .' '. JText::_('COM_EGOLTARCHIVE_RESULT') ?>
		&nbsp; | &nbsp;
		<?php if(!$this->pagination->getPagesLinks()): ?>
			<?php echo JText::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL', 1, 1); ?>		
		<?php else: ?>
			<?php echo $this->pagination->getPagesCounter(); ?>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	
	<div class="clear" ></div>
	
</div>
