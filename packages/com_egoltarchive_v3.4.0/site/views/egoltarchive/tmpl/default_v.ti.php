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

<div class="ti-body" >
<?php foreach($this->items as $i => $row): ?>
	<div class="ti-row" >
	
		<div id="ti-top" >
			<span id="ti-title">
			
				<?php if($this->params->get('index_number', 1)): ?>
					<small><?php echo $this->start+$i+1; ?>.</small> 
				<?php endif; ?>
				
			<a href="<?php echo $row->eglink; ?>" <?php echo $this->item_target; ?> >
				<?php echo $row->egtitle; ?>
			</a>
			</span>
		</div>
				
		<?php if($this->params->get('showinfo', 1)): ?>
		<div id="ti-middle" >
		
			<?php if($this->params->get('info_date', 1)) : ?>
			<?php if(isset($row->egdate)) : ?>
			<div class="grid" id="ti-created">
				<span class="ti-head" >
					<?php echo JText::_( 'COM_EGOLTARCHIVE_PUBLISHED_DATE' ); ?>
				</span>
				<?php echo $row->egdate; ?>
			</div>
			<?php endif; ?>
			<?php endif; ?>
					
			<?php if($this->params->get('info_cat', 1)) : ?>
			<?php if(isset($row->egcat)) : ?>			
			<div class="grid" id="ti-cat">
				<span class="ti-head" >						
					<?php echo JText::_( 'COM_EGOLTARCHIVE_CATEGORY' ); ?>
				</span>
					
				<?php if($this->params->get('info_catlink', 1)) : ?>
					<a class="nodecor" href="<?php echo $row->egcat_link; ?>" <?php echo $this->cat_target; ?> >
				<?php endif; ?>
					<?php echo $row->egcat; ?>	
				<?php if($this->params->get('info_catlink', 1)) : ?>
					</a>
				<?php endif; ?>
					
			</div>
			<?php endif; ?>
			<?php endif; ?>
				
			<?php if($this->params->get('info_author', 1)) : ?>				
			<?php if(isset($row->egauthor)): ?>				
			<div class="grid" id="ti-author">
				<span class="ti-head" >
					<?php echo JText::_( 'COM_EGOLTARCHIVE_AUTHOR' ); ?>
				</span>
				<?php echo $row->egauthor; ?>
			</div>
			<?php endif; ?>
			<?php endif; ?>

			<?php if($this->params->get('info_hits', 1)): ?>
			<?php if(isset($row->eghits)) : ?>							
			<div class="grid" id="ti-hits">
				<span class="ti-head" >
					<?php echo JText::_( 'COM_EGOLTARCHIVE_HITS' ); ?>
				</span>
				<?php echo $row->eghits; ?>
			</div>
			<?php endif; ?>
			<?php endif; ?>
			
			<?php if($this->params->get('info_comments', 1)): ?>
			<?php if(isset($row->egcomment)) : ?>							
			<div class="grid" id="ti-comments">
				<span class="ti-head" >
					<?php echo JText::_( 'COM_EGOLTARCHIVE_COMMENTS' ); ?>
				</span>
				<a class="nodecor" href="<?php echo $row->eglink; ?>#comments" <?php echo $this->item_target; ?> >
					<?php echo $row->egcomment; ?>
				</a>
			</div>
			<?php endif; ?>
			<?php endif; ?>
				
			<div class="clear" ></div>
		</div>
		<?php endif; ?>

		
		<div id="ti-bottom" >
			<p>
			<?php if(isset($row->egthumb)): ?>
				<img 
					id="ti-img"
					src="<?php echo $row->egthumb; ?>" 
					width="<?php echo $row->egthumb_w; ?>"					
					height="<?php echo $row->egthumb_h; ?>"	
					align="<?php echo $row->egthumb_a; ?>"	
					alt="<?php echo $row->egtitle; ?>"
					style="width:<?php echo $row->egthumb_w; ?>px;"
				/>
			<?php endif; ?>
			<?php echo $row->egtext; ?>
			</p>
			<div class="clear"> </div>
		</div>
			
	</div>
<?php endforeach; ?>
</div>
