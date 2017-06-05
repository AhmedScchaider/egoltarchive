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

$lang = JFactory::getLanguage();
$lang_tag = $lang->getTag();
if($lang->isRTL())
{
	$icon_float = 'right';
	$logo_float = 'left';
}
else
{
	$icon_float = 'left';
	$logo_float = 'right';
}
?>
<?php if(isset($this->start)): ?>
<div class="startmsg" >
	<a href="index.php?option=com_menus&view=item&layout=edit&menutype=mainmenu" >
		<?php echo $this->start; ?>
	</a>	
</div>
<?php endif; ?>

<div class="adminform egoltarchive-admin" id="adminForm"  name="adminForm">

<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
<div class="row-fluid">
<?php endif; ?>

	<div class="span7">
		<div class="egicon" >
			<a href="index.php?option=com_egoltarchive&amp;task=about">
				<img src="../media/egoltarchive/images/about.png" alt="<?php echo JText::_('COM_EGOLTARCHIVE_ABOUT_PRODUCT') ?>"  />
				<div class="clearfix" style="height:10px;" ></div>
				<span><?php echo JText::_('COM_EGOLTARCHIVE_ABOUT_PRODUCT') ?></span>
			</a>
		</div>
		<div class="clearfix" ></div>
	</div>
	<div class="span5 egpanelcover">
		<div class="egpanel">
			<h3>
				<?php echo JText::_('COM_EGOLTARCHIVE_PRODUCT_BY') ?> Egolt
			</h3>
			<div class="egpanelinfo">
				<div class="inner-content" style="background:#FFF;padding:7px;line-height:20px;" >
					<div style="float: <?php echo $logo_float ?>; margin: 10px;">
						<img src="../media/egoltarchive/images/egolt.png" >
					</div>
					<div class="egpanelinfo-inner" >
						<?php echo JText::_('COM_EGOLTARCHIVE_TITLE') ?><br/>
						<?php echo JText::_('COM_EGOLTARCHIVE_POWERED_BY') ?> <a href="http://www.egolt.com" target="_blank">
						Egolt
						- www.egolt.com</a><br/>
						&copy; 2012 - 2013	
					</div>
					<div class="clearfix" > </div>
				</div>
			</div>
		</div>
	</div>
	
<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
</div>
<?php endif; ?>

</div>