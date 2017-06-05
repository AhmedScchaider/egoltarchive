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
<div class="searchbox-top" ></div>

<div class="search-body">
	<form action="<?php echo JRoute::_('index.php', false); ?>" method="post" name="egoltarchivecp" id="egoltarchivecp" >
	<?php echo $this->dtinput; ?>
	<?php if($this->params->get('datesearch', 1)) : ?>
		<div class="dsall" >
			<div class="dsheader dsheader-1" id="1" >
				<div class="datelabel" >
					<?php echo JText::_( 'COM_EGOLTARCHIVE_DATE' ); ?> 
				</div>
			</div>
			<div class="dsblock dsblock-1" id="1" >
				<div class="duration">
					<div class="grid spc" style="padding-top:5px;">
						<?php echo JText::_( 'COM_EGOLTARCHIVE_SELECT_DATE' ); ?> 
					</div>
					<div class="grid spc">
						<?php echo $this->dateselect;?>
					</div>
					<div class="clear"></div>
				</div>
			</div>	
			<div class="clear" style="height:10px;"></div>
		</div>
			
		<div class="dsall" >
			<div class="dsheader dsheader-2" id="2" >
				<div class="datelabel" >
				<?php echo JText::_( 'COM_EGOLTARCHIVE_DURATION' ); ?> 
				</div>
			</div>
			<div class="dsblock dsblock-2" id="2" >
				<div class="duration">
					<div class="grid spc" style="padding-top:5px;">
						<?php echo JText::_( 'COM_EGOLTARCHIVE_SELECT_DURATION' ); ?> 
					</div>
					<div class="grid spc">
						<?php echo $this->duration;?>
					</div>
					<div class="clear"></div>
				</div>
			</div>	
			<div class="clear" style="height:10px;"></div>
		</div>
			
		<div class="dsall" >
			<div class="dsheader dsheader-3" id="3" style="margin-bottom:0px;" >
				<div class="datelabel" >
					<?php echo JText::_( 'COM_EGOLTARCHIVE_BETWEEN_DATES' ); ?> 
				</div>
			</div>	
			<div class="dsblock dsblock-3" id="3" >
				<div class="startdate">
					<div class="grid spc betdatelabel" >
						<?php echo JText::_( 'COM_EGOLTARCHIVE_START_DATE' ); ?> 
					</div>	
					<div class="grid spc dselect">
						<?php echo $this->starty;?> 
					</div>
					<div class="grid spc dselect">
						<?php echo $this->startm;?> 
					</div>
					<div class="grid spc dselect">
						<?php echo $this->startd;?>
					</div>
					<div class="clear" ></div>
				</div>
				<div class="enddate">
					<div class="grid spc betdatelabel" >
						<?php echo JText::_( 'COM_EGOLTARCHIVE_END_DATE' ); ?>
					</div>
					<div class="grid spc dselect">
						<?php echo $this->endy;?> 
					</div>
					<div class="grid spc dselect">
						<?php echo $this->endm;?> 
					</div>
					<div class="grid spc dselect">
						<?php echo $this->endd;?>
					</div>
					<div class="clear" ></div>
				</div>
				<div class="clear" ></div>
			</div>	
			<div class="clear" style="height:10px;"></div>
		</div>
	<?php endif; //end of date search ?>		
	
	<?php if($this->params->get('categorysearch', 1)) : ?>		
		<div class="categorys">
			<div  class="grid spc flabel" >
				<?php echo JText::_( 'COM_EGOLTARCHIVE_CATEGORIES' ); ?>
			</div>
			<div  class="grid finc" >
				<?php echo $this->catlist;?> 
			</div>
			<div class="clear" ></div>
		</div>	
	<?php endif; //end of category search ?>
	
	<?php if($this->params->get('display_authors', 1) and ($this->authorslist)) : ?>
		<div class="authors">
			<div  class="grid spc flabel" >
				<?php echo JText::_( 'COM_EGOLTARCHIVE_AUTHORS' ); ?>
			</div>
			<div  class="grid finc" >
				<?php echo $this->authorslist;?> 
			</div>
			<div class="clear" ></div>
		</div>	
	<?php endif; //end of author search ?>	
	
	<?php if($this->params->get('display_ordering', 1)) : ?>		
		<div class="sort">
			<div  class="grid spc flabel" >
				<?php echo JText::_( 'COM_EGOLTARCHIVE_SORT' ); ?>
			</div>
			<div  class="grid finc" >
				<?php echo $this->orderinglist;?> 
			</div>
			<div class="clear" ></div>
		</div>	
	<?php endif; //end of sort search ?>	
	
	<?php if($this->params->get('text_inc_search', 1)) : ?>		
		<div class="like" >
			<div class="grid spc flabel" >
				<?php echo JText::_( 'COM_EGOLTARCHIVE_INCLUDE_TEXT' ); ?>
			</div>
			<div class="grid finc">
				<div class="grid" >
					<input class="aainput" type="text" name="search_str" size="30" id="search_str" value="<?php echo @$this->sess['like_str'] ?>" /> 
				</div>
				<?php if($this->params->get('text_inc_exact_search', 1)) : ?>	
					<div class="grid checkexact" >
						<input <?php if(isset($this->sess['exact_like'])) echo 'checked="checked"'; ?> name="exact_search" value="on" type="checkbox">
					</div>
					<div class="grid checkexactlabel" >
						<?php echo JText::_( 'COM_EGOLTARCHIVE_EXACT_SEARCH' ); ?>
					</div>
				<?php endif;  ?>
				<div class="clear" ></div>
			</div>
			<div class="clear" ></div>
		</div>
	<?php endif; //end of text include search ?>
	
	<?php if($this->params->get('text_notinc_search', 1)) : ?>				
		<div class="notlike">
			<div  class="grid spc flabel" >
				<?php echo JText::_( 'COM_EGOLTARCHIVE_NOT_INCLUDE_TEXT' ); ?> 
			</div>
			<div  class="grid finc">
				<div class="grid" >
					<input class="aainput" type="text" name="not_search_str" size="30" id="not_search_str" value="<?php echo @$this->sess['not_like_str'] ?>" />
				</div>
				<?php if($this->params->get('text_notinc_exact_search', 1)) : ?>	
					<div class="grid checkexact" >				
						<input <?php if(isset($this->sess['exact_not_like'])) echo 'checked="checked"'; ?> name="exact_not_search" value="on" type="checkbox">
					</div>
					<div class="grid checkexactlabel" >			
						<?php echo JText::_( 'COM_EGOLTARCHIVE_EXACT_SEARCH' ); ?>
					</div>
				<?php endif; ?>
				<div class="clear" ></div>				
			</div>
			<div class="clear" ></div>
		</div>		
	<?php endif; //end of text not include search ?>	
	
		<div class="submit" >
			<div class="grid spc" style="width:90px;margin-right:5px;">
				<input class="aasubmit" type="submit" name="aasubmit" id="aasubmit" value="<?php echo JText::_( 'COM_EGOLTARCHIVE_SUBMIT' ); ?>" />
			</div>
			<div class="grid spc">
				<a class="nodecor" href="<?php echo JRoute::_('index.php?option=com_egoltarchive&searchall=on'); ?>" ><div style="text-align:center;" class="all_button" ><?php echo JText::_( 'COM_EGOLTARCHIVE_FULL_ARCHIVE' ); ?></div></a>
			</div>
			<div class="clear" ></div>
		</div>

		<input type="hidden" name="option" value="com_egoltarchive" />
		<input type="hidden" name="task" value="egoltarchive" />
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>
<div class="clear" style="height:20px;"></div>