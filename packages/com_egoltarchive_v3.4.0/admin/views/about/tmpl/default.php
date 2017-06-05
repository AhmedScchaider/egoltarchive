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
?>
<?php if(isset($this->start)): ?>
<div class="startmsg" >
	<a href="index.php?option=com_menus&view=item&layout=edit&menutype=mainmenu" >
		<?php echo $this->start; ?>
	</a>	
</div>
<?php endif; ?>

<div class="egoltarchive-admin">

<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
<div class="row-fluid">
<?php endif; ?>

	<div class="span3">
		<center>
			<br/>
			<img src="../media/egoltarchive/images/egoltarchive_big.png">
				<div class="jpane-slider content">
			<div class="inner-about" >
				<div class="aversion"><?php echo JText::_('COM_EGOLTARCHIVE_VERSION') ?> <?php echo $this->version; ?></div>
				
				<div class="abuypro"><a href="http://www.egolt.com/go-pro/subscribe/levels?prd=egoltarchive" target="_blank"><?php echo JText::_('COM_EGOLTARCHIVE_BUYPRO') ?></a></div>
				
				<div class="adownload"><a href="http://www.egolt.com/products/egoltarchive#download" target="_blank"><?php echo JText::_('COM_EGOLTARCHIVE_DOWNLOADS') ?></a></div>
				
				<div class="asupport"><a href="http://www.egolt.com/go-pro/subscribe/levels?prd=egoltarchive" target="_blank"><?php echo JText::_('COM_EGOLTARCHIVE_SUPPORT') ?></a></div>
				
				<div class="adocumentation"><a href="http://www.egolt.com/userguide/egoltarchive" target="_blank"><?php echo JText::_('COM_EGOLTARCHIVE_USERGUIDE') ?></a></div>
				
				<div class="ademo"><a href="http://www.egolt.com/products/egoltarchive#demo" target="_blank"><?php echo JText::_('COM_EGOLTARCHIVE_DEMO') ?></a></div>
				
				<div class="alicense">
					<?php echo JText::_('COM_EGOLTARCHIVE_LIC_UNDER') ?> <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank"><?php echo JText::_('COM_EGOLTARCHIVE_LIC_GPL') ?></a>
				</div>
				
			</div>
		</div>
		</center>
	</div>
	<div class="span6">
		<div class="panel" style="padding: 5px;">
			<h2 class="jpane-toggler title" id="cpanel-panel-popular"><span><?php echo JText::_('COM_EGOLTARCHIVE_TITLE'); ?></span></h2>
			<h4><?php echo JText::_('COM_EGOLTARCHIVE_SLOGAN_MSG'); ?></h4>
			<p class="aboutp" >
				Egolt Archive provides the ability to present content and any other content source in different intervals of time that you need to archive and search. This ability is not embedded in the Joomla! core ,however is very practical and necessary.
			</p>
			<p class="aboutp" >
				There are many options that you could use it in this product. You can archive not only Joomla! Content, but other sources like content systems, shopping systems, download services and etc. Smart minimum and maximum time interval are selected from contents.
			</p>
			<p class="aboutp" >
				Egolt Archive is support Gregorian(international) and other calendars (like local calendars).
			</p>
			<p class="aboutp" >
				There are many modules which provided to help users to use the Egolt Archive in flexible and different situations.
			</p>
			<p class="aboutp" >
				Egolt Archive is based on Egolt Framework; flexible and powerful extension framework that is built by Egolt to increase the power, compatibility and reduce the bugs of the Egolt products.
			</p>
		</div>
	</div>
	<div class="span3 aboutcl">
		<center>
		<img src="../media/egoltarchive/images/egolt_big.png"><br/><br/>
		&copy; <?php echo JText::_('COM_egoltarchive_POWERED_BY') ?> <a href="http://www.egolt.com" target="_blank">
			Egolt		
		</a>
		<br/><br/>
		<a href="http://www.egolt.com" target="_blank">www.egolt.com</a><br/><br/>
		2012 - 2013<br/>
		</center>
	</div>

<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
</div>
<?php endif; ?>

</div>
