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

<div class="to-body" >
<?php foreach ($this->items as $i => $row): ?>
	<div class="to-row" >
		
		<div id="to-top" >
			<span id="to-title">
			
				<?php if($this->params->get('index_number', 1)): ?>
					<small><?php echo $this->start+$i+1; ?>.</small> 
				<?php endif; ?>
				
			<a href="<?php echo $row->eglink; ?>" <?php echo $this->item_target; ?> >
				<?php echo $row->egtitle; ?>
			</a>				
			</span>
		</div>
			
		<?php if($this->params->get('showinfo', 1)): ?>
		<ul id="to-info" >
		
			<?php if($this->params->get('info_date', 1)) : ?>
			<?php if(isset($row->egdate)) : ?>	
			<li class="grid" id="to-created">
				<?php echo $row->egdate; ?>
			</li>
			<?php endif; ?>
			<?php endif; ?>
					
			<?php if($this->params->get('info_cat', 1)) : ?>	
			<?php if(isset($row->egcat)) : ?>	
			<li class="grid" id="to-cat">
				<?php if($this->params->get('info_catlink', 1)) : ?>
					<a class="nodecor" href="<?php echo $row->egcat_link; ?>" <?php echo $this->cat_target; ?> >
				<?php endif; ?>
					<?php echo $row->egcat; ?>	
				<?php if($this->params->get('info_catlink', 1)) : ?>
					</a>
				<?php endif; ?>
					
			</li>
			<?php endif; ?>
			<?php endif; ?>
				
			<?php if($this->params->get('info_author', 1)) : ?>				
			<?php if(isset($row->egauthor)) : ?>				
			<li class="grid" id="to-author">
				<?php echo $row->egauthor; ?>
			</li>
			<?php endif; ?>
			<?php endif; ?>
					
			<?php if($this->params->get('info_hits', 1)) : ?>
			<?php if(isset($row->eghits)) : ?>							
			<li class="grid" id="to-hits">
				<?php echo $row->eghits; ?>
			</li>
			<?php endif;?>				
			<?php endif;?>

			<?php if($this->params->get('info_comments', 1)): ?>
			<?php if(isset($row->egcomment)) : ?>							
			<li class="grid" id="to-comments">
				<a class="nodecor" href="<?php echo $row->eglink; ?>#comments" <?php echo $this->item_target; ?> >
					<?php echo $row->egcomment; ?>
				</a>
			</li>
			<?php endif; ?>
			<?php endif; ?>
			
		</ul>
		<?php endif; ?>
			
		<div class="clear" ></div>
	</div>
<?php endforeach; ?>
</div>
