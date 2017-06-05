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

<div class="pg-body" >
<?php foreach ($this->items as $row): ?>
	<?php if(isset($row->egthumb)): ?>
	<a class="pg-link" href="<?php echo $row->eglink; ?>" target="_blank" >
	<div class="grid pg-row" style="width:<?php echo $row->egthumb_w+50; ?>px;height:<?php echo $row->egthumb_h+60;?>px;" >
			<img 
				class="pg-img"
				src="<?php echo $row->egthumb; ?>" 
				width="<?php echo $row->egthumb_w; ?>"					
				height="<?php echo $row->egthumb_h; ?>"	
				alt="<?php echo $row->egtitle; ?>"	
				style="width:<?php echo $row->egthumb_w; ?>px;"
			/>
			<br/>
			<?php echo $row->egtitle; ?>
	</div>
	</a>
	<?php endif; ?>
<?php endforeach; ?>
	<div class="clear" ></div>
</div>
