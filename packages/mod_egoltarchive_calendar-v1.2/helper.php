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

class modEgoltArchiveCalendarHelper
{
	var $_params;
	var $_cmparams;
	var $_model;
	var $_modid;

	public function __construct($modid = null)
	{		
	
		if(isset($modid))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('m.*');
			$query->from('#__modules AS m');
			$query->where('id = '. (int) $modid);
			$db->setQuery($query);
			$module = $db->loadObject();
			$params = new JRegistry($module->params);
		}
		$this->_modid = $modid;
	
		$config = array();
		$config['menu'] = $params->get('egmenu');
		
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_egoltarchive/models', 'EgoltArchiveModel');	
		$this->_params	= $params;
		$this->_model	= JModelLegacy::getInstance( 'EgoltArchive', 'EgoltArchiveModel', $config);
		$this->_cmparams= $this->_model->getParams();
	}
	
	public function getCMParams()
	{
		return $this->_cmparams;
	}
	
	public function getElements($fl = null)
	{	
		$output = array();

		$output['cmparams'] = $this->_cmparams;
		
		// Module Params
		$output['modsfx'] = $this->_params->get('moduleclass_sfx');
		$output['template']	= $this->_params->get('template', 'default');
		
		// Link Builder
		$output['trailer'] = '&rbtab=3';
		if($this->_params->get('egmenu'))
		{
			$output['trailer'] .= '&Itemid=' . $this->_params->get('egmenu');
			$output['trailer2'] = '&eghidesearch=1&rbtab=1&Itemid=' . $this->_params->get('egmenu');
		}
		else
		{
			return false;
		}
		
		// Get duration
		$cltype = $this->_cmparams->get('cltype');
		$clt = EGODate::_($cltype);
		if(isset($fl['langtag']))
			$clt->langtag = $fl['langtag'];
		$cyear = $clt->show(null, 'Y');
		$cmonth = $clt->show(null, 'm');
		if(isset($fl['cltype']))
			$cltype = $fl['cltype'];
		if(isset($fl['cyear']))
			$cyear = $fl['cyear'];
		if(isset($fl['cmonth']))
			$cmonth = $fl['cmonth'];
			
		$dur = $clt->getDurs($cyear, $cmonth);
		$startday = date('w', strtotime($dur['start']));
		$mcount = $clt->getMonthsdur($cyear, $cmonth);
		$cltitle = $clt->show($dur['start'],'F Y');
		if($startday==6)
			$addspan = 0;
		else
			$addspan = ($startday+7)-6;
		$weekarr = $clt->getWeekday(null, 'short');
		$weekarrlong = $clt->getWeekday();
		$weekplus = $clt->weekstart;
		if($this->_params->get('weekstart'))
			$weekplus = $this->_params->get('weekstart')-7;
		
		$output['cltype'] = $cltype;
		$output['cyear'] = $cyear;
		$output['cmonth'] = $cmonth;
		$output['weekplus'] = $weekplus;
		$output['weekarr'] = $weekarr;
		$output['weekarrlong'] = $weekarrlong;
		$output['addspan'] = $addspan;
		$output['cltitle'] = $cltitle;
		$output['mcount'] = $mcount;
		
		
		// Each day article number
		$config = array();
		$config['menu'] = $this->_params->get('egmenu');
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_egoltarchive/models', 'EgoltArchiveModel');	
		$dayartnum = array();
		for($i=1;$i<=$mcount;$i++)
		{
			$dayartnum[$i] = 0;
			$config['params']['startdate'] = $clt->toGre($cyear.'-'.$cmonth.'-'.$i, 'Y-m-d');
			$config['params']['enddate'] = $clt->toGre($cyear.'-'.$cmonth.'-'.$i, 'Y-m-d');
			$model	= JModelLegacy::getInstance( 'EgoltArchive', 'EgoltArchiveModel', $config);
			$dayartnum[$i] = $model->getTotal();
		}
		$output['dayartnum'] = $dayartnum;
		
		$output['monthartnum'] = 0;
		$config['params']['startdate'] = $clt->toGre($cyear.'-'.$cmonth.'-1', 'Y-m-d');
		$config['params']['enddate'] = $clt->toGre($cyear.'-'.$cmonth.'-'.$mcount, 'Y-m-d');
		$modelt	= JModelLegacy::getInstance( 'EgoltArchive', 'EgoltArchiveModel', $config);
		$output['monthartnum'] = $modelt->getTotal();

		
		return $output;
	}
	
	public function getDisplay($ajax=0, $cltype=null, $cyear=null, $cmonth=null, $ltag=null)
	{
		$fl = array();
		if(isset($cltype))
			$fl['cltype'] = $cltype;			
		if(isset($cyear))
			$fl['cyear'] = $cyear;
		if(isset($cmonth))
			$fl['cmonth'] = $cmonth;	
		if(isset($ltag))
			$fl['langtag'] = $ltag;	
			
		$e = $this->getElements($fl);
		
		if($e == false)
		{
			echo JText::_('COM_EGOLTARCHIVE_ERROR_RELATEDMENU');
			return;
		}
		
		if(isset($ltag))
			$langtag = str_replace('-', '.', $ltag);
		else
			$langtag = str_replace('-', '.', JFactory::getLanguage()->getTag());
		
		// Next and Previous month data
		$pasty = $e['cyear'];
		$pastm = $e['cmonth']-1;
		if($pastm == 0){
			$pastm = 12;
			$pasty = $pasty-1;
		}
		$pastddt = $this->_modid .'-'. $e['cltype'] .'-'. $pasty .'-'. $pastm . '-' . $langtag;
		$pastyddt = $this->_modid .'-'. $e['cltype'] .'-'. ($e['cyear']-1) .'-1' . '-' . $langtag;
		
		$nexty = $e['cyear'];
		$nextm = $e['cmonth']+1;
		if($nextm == 13){
			$nextm = 1;
			$nexty = $nexty+1;
		}
		$nextddt = $this->_modid .'-'. $e['cltype'] .'-'. $nexty .'-'. $nextm . '-' . $langtag;
		$nextyddt = $this->_modid .'-'. $e['cltype'] .'-'. ($e['cyear']+1) .'-1' . '-' . $langtag;
		
		// Link Target
		if($this->_params->get('cal_item_target', 'same') == 'new')
			$item_target = 'target="_blank"';
		else
			$item_target = '';
			
		if($e['monthartnum'])
		{
			$monthlink = str_replace('/modules/mod_egoltarchive_calendar', '', JRoute::_('index.php?dateselect='.$e['cyear'].'-'.$e['cmonth'] .$e['trailer2']) );
			$monthtext = '<span class="monthycover" ><a '.$item_target.' class="monthy egarclmod-ttp" title="'.$e['monthartnum'].' '.JText::_('COM_EGOLTARCHIVE_ARTICLE').'" href="'.$monthlink.'" >'.$e['cltitle'].'</a></span>';
		}
		else
		{
			$monthtext = '<span class="monthycover" >'.$e['cltitle'].'</span>';
		}
		?>
		<?php if(!$ajax): ?>
		<div id="egclmodcal" class="egclmodcal<?php echo $e['modsfx']; ?>">
			<div class="egcal">
		<?php endif; ?>
			  <table class="cal-table">
				<caption class="cal-caption">
				  <span class="prev pnx pnxy egarclmod-ttp" id="<?php echo $pastyddt; ?>" title="<?php echo JText::_('COM_EGOLTARCHIVE_PREV_YEAR'); ?>"><</span>
				  <span class="prev pnx egarclmod-ttp" id="<?php echo $pastddt; ?>" title="<?php echo JText::_('COM_EGOLTARCHIVE_PREV_MONTH'); ?>">&laquo;</span>
				  <span class="next pnx pnxy egarclmod-ttp" id="<?php echo $nextyddt; ?>" title="<?php echo JText::_('COM_EGOLTARCHIVE_NEXT_YEAR'); ?>">></span>
				  <span class="next pnx egarclmod-ttp" id="<?php echo $nextddt; ?>" title="<?php echo JText::_('COM_EGOLTARCHIVE_NEXT_MONTH'); ?>">&raquo;</span>
				  <?php echo $monthtext; ?>
				</caption>
				<tbody class="cal-body">
					<tr>
						<?php for($l=1;$l<=7;$l++): ?>
						<?php 
						$l2 = $l-$e['weekplus']-2; 
						if($l2<=0) $l2 = $l2+7;
						if($l2>7) $l2 = $l2-7;
						?>
						<td class="weekhead egarclmod-ttp" title="<?php echo $e['weekarrlong'][$l2]; ?>"><small><?php echo $e['weekarr'][$l2]; ?></small></td>
						<?php endfor; ?>
					</tr>
				<?php for($i=1;$i<=$e['mcount'];$i++): ?>
					<?php $i2 = $i+$e['addspan']+$e['weekplus']; ?>
					<?php if($i2%7==1) echo '<tr>'; ?>	

					<?php if($i == $e['mcount']):?>
						
						<?php if($artnum = $e['dayartnum'][$i]): ?>
						<td class="cal-check"><a class="egarclmod-ttp" <?php echo $item_target; ?> href="<?php echo str_replace('/modules/mod_egoltarchive_calendar', '', JRoute::_('index.php?onedate='.$e['cyear'].'_'.$e['cmonth'].'_'.$i .$e['trailer']) ); ?>" title="<?php echo $artnum; ?> <?php echo JText::_('COM_EGOLTARCHIVE_ARTICLE'); ?>" ><?php echo $i; ?></a></td>
						<?php else: ?>
						<td class="hasday"><?php echo $i; ?></td>				
						<?php endif; ?>				
						
						<?php
						$rmd=7-($i2%7);
						if($rmd<=0) $rmd = $rmd+7;
						if($rmd>=7) $rmd = $rmd-7;
						if(($rmd<7) and ($rmd>0))
						{
							for($k=1;$k<=$rmd;$k++)
							{
								echo '<td class="cal-off"></td>';
							}
						}
						?>	
					<?php elseif($i==1): ?>
						<?php
							$addfirstsp = $e['addspan']+$e['weekplus'];
							if($addfirstsp<=0) $addfirstsp = $addfirstsp+7;
							if($addfirstsp>=7) $addfirstsp = $addfirstsp-7;
							for($k=1;$k<=$addfirstsp;$k++)
							{
								echo '<td class="cal-off"></td>';
							}
						?>
						
						<?php if($artnum = $e['dayartnum'][$i]): ?>
						<td class="cal-check"><a class="egarclmod-ttp" <?php echo $item_target; ?> href="<?php echo str_replace('/modules/mod_egoltarchive_calendar', '', JRoute::_('index.php?onedate='.$e['cyear'].'_'.$e['cmonth'].'_'.$i .$e['trailer']) ); ?>" title="<?php echo $artnum; ?> <?php echo JText::_('COM_EGOLTARCHIVE_ARTICLE'); ?>" ><?php echo $i; ?></a></td>
						<?php else: ?>
						<td class="hasday"><?php echo $i; ?></td>				
						<?php endif; ?>
						
					<?php else: ?>
					
						<?php if($artnum = $e['dayartnum'][$i]): ?>
						<td class="cal-check"><a class="egarclmod-ttp" <?php echo $item_target; ?> href="<?php echo str_replace('/modules/mod_egoltarchive_calendar', '', JRoute::_('index.php?onedate='.$e['cyear'].'_'.$e['cmonth'].'_'.$i .$e['trailer']) ); ?>" title="<?php echo $artnum; ?> <?php echo JText::_('COM_EGOLTARCHIVE_ARTICLE'); ?>" ><?php echo $i; ?></a></td>
						<?php else: ?>
						<td class="hasday"><?php echo $i; ?></td>				
						<?php endif; ?>
						
					<?php endif; ?>
					
					<?php if(($i2%7==0) or ($i == $e['mcount'])) echo '</tr>'; ?>
				<?php endfor; ?>
				</tbody>
			  </table>
		<?php if(!$ajax): ?>
			</div>
		</div>	
		<?php endif; ?>
		<?php
	}

}
