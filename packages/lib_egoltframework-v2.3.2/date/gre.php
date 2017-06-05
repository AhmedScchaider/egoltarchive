<?php
/**
 * @package   	Egolt Framewrok
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt - www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 */

// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

// Check Egolt Framework
defined('_EGOINC') or die('Egolt Framework not installed!');

class EGODateGre
{
	var $_default_date;
	var $_default_format;
	var $weekstart;
	var $langtag;
	
	function __construct()
	{
		// parent::__construct();

		$this->_default_date	= date('Y-m-d h:i:s');
		$this->_default_format	= 'Y-m-d h:i:s';
		$this->weekstart = -2; // Monday
		
		$lang	= JFactory::getLanguage();
		$this->langtag	= $lang->getTag();
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'gre';
		$info['title']	= 'Gregorian (international)';
		$info['compat']	= '2.5,3';

		return $info;
	}
	
	public function show($date = null, $format  = null)
	{
		if(!isset($format))
		{
			$format = $this->_default_format;
		}
		
		if(!isset($date))
		{
			$date = $this->_default_date;
		}
		
		$time = strtotime($date);
	
		$date2 = explode(' ' ,$date);
		list($year, $month, $day) = explode('-', $date2[0]);
		$year = (int) $year;
		$month = (int) $month;
		$day = (int) $day;
		
		$output = $format;
		
		$trans = array(
			'Y' => $year, 
			'm' => $month, 
			'd' => $day, 
			'M' => $this->getMonths($month, 'short'), 
			'F' => $this->getMonths($month), 
			'l' => $this->getWeekday(date('N',strtotime($date)))
		);

		$output = strtr($output, $trans);
		
		return $output;	
	}
	
	public function toGre($date = null, $format  = null)
	{
		return $this->show($date, $format);
	}
	
	public function getMonths($select = null, $type = 'long') 
	{
		if($type == 'long')
		{
			$months = array(
				1 => EGOText::_('EGO_JAN_LG', array(
					'd' => 'January', 
					'fa-IR' => 'ژانویه', 
					'ru-RU' => 'Январь', 
					'tr-TR'=>'Ocak')
				, $this->langtag ),
				2 => EGOText::_('EGO_FEB_LG', array(
					'd' => 'February', 
					'fa-IR' => 'فوریه', 
					'ru-RU' => 'Февраль', 
					'tr-TR'=>'Şubat'
				), $this->langtag ),
				3 => EGOText::_('EGO_MAR_LG', array(
					'd' => 'March', 
					'fa-IR' => 'مارس', 
					'ru-RU' => 'Март', 
					'tr-TR'=>'Mart'
				), $this->langtag ),
				4 => EGOText::_('EGO_APR_LG', array(
					'd' => 'April', 
					'fa-IR' => 'آوریل', 
					'ru-RU' => 'Апрель',
					'tr-TR'=>'Nisan'
				), $this->langtag ),
				5 => EGOText::_('EGO_MAY_LG', array(
					'd' => 'May', 
					'fa-IR' => 'می', 
					'ru-RU' => 'Май',
					'tr-TR' => 'Mayıs'
				), $this->langtag ),
				6 => EGOText::_('EGO_JUN_LG', array(
					'd' => 'June', 
					'fa-IR' => 'جون', 
					'ru-RU' => 'Июнь',
					'tr-TR' => 'Haziran'
				), $this->langtag ),
				7 => EGOText::_('EGO_JUL_LG', array(
					'd' => 'July', 
					'fa-IR' => 'جولای', 
					'ru-RU' => 'Июль',
					'tr-TR' => 'Temmuz'
				), $this->langtag ),
				8 => EGOText::_('EGO_AUG_LG', array(
					'd' => 'August', 
					'fa-IR' => 'آگوست', 
					'ru-RU' => 'Август',
					'tr-TR' => 'Ağustos'
				), $this->langtag ),
				9 => EGOText::_('EGO_SEP_LG', array(
					'd' => 'September', 
					'fa-IR' => 'سپتامبر', 
					'ru-RU' => 'Сентябрь',
					'tr-TR' => 'Eylül'
				), $this->langtag ),
				10 => EGOText::_('EGO_OCT_LG', array(
					'd' => 'October', 
					'fa-IR' => 'اکتبر', 
					'ru-RU' => 'Октябрь',
					'tr-TR' => 'Ekim'
				), $this->langtag ),
				11 => EGOText::_('EGO_NOV_LG', array(
					'd' => 'November', 
					'fa-IR' => 'نوامبر', 
					'ru-RU' => 'Ноябрь',
					'tr-TR' => 'Kasım'
				), $this->langtag ),
				12 => EGOText::_('EGO_DEC_LG', array(
					'd' => 'December', 
					'fa-IR' => 'دسامبر', 
					'ru-RU' => 'Декабрь',
					'tr-TR' => 'Aralık'
				), $this->langtag ),
			);
		}
		elseif($type == 'short')
		{
			$months = array(
				1 => EGOText::_('EGO_JAN', array(
					'd' => 'Jan', 
					'fa-IR' => 'جن', 
					'ru-RU' => 'Янв',
					'tr-TR' => 'Oca'
				), $this->langtag ),
				2 => EGOText::_('EGO_FEB', array(
					'd' => 'Feb', 
					'fa-IR' => 'فب', 
					'ru-RU' => 'Фев',
					'tr-TR' => 'Şub'
				), $this->langtag ),
				3 => EGOText::_('EGO_MAR', array(
					'd' => 'Mar', 
					'fa-IR' => 'مار', 
					'ru-RU' => 'Март',
					'tr-TR' => 'Mar'
				), $this->langtag ),
				4 => EGOText::_('EGO_APR', array(
					'd' => 'Apr', 
					'fa-IR' => 'آپر', 
					'ru-RU' => 'Апр',
					'tr-TR' => 'Nis'
				), $this->langtag ),
				5 => EGOText::_('EGO_MAY', array(
					'd' => 'May', 
					'fa-IR' => 'می', 
					'ru-RU' => 'Май',
					'tr-TR' => 'May'
				), $this->langtag ),
				6 => EGOText::_('EGO_JUN', array(
					'd' => 'Jun', 
					'fa-IR' => 'جون', 
					'ru-RU' => 'Июнь',
					'tr-TR' => 'Haz'
				), $this->langtag ),
				7 => EGOText::_('EGO_JUL', array(
					'd' => 'Jul', 
					'fa-IR' => 'جول', 
					'ru-RU' => 'Июль',
					'tr-TR' => 'Tem'
				), $this->langtag ),
				8 => EGOText::_('EGO_AUG', array(
					'd' => 'Aug', 
					'fa-IR' => 'آگو', 
					'ru-RU' => 'Авг',
					'tr-TR' => 'Ağu'
				), $this->langtag ),
				9 => EGOText::_('EGO_SEP', array(
					'd' => 'Sep', 
					'fa-IR' => 'سپ', 
					'ru-RU' => 'Сент',
					'tr-TR' => 'Eyl'
				), $this->langtag ),
				10 => EGOText::_('EGO_OCT', array(
					'd' => 'Oct', 
					'fa-IR' => 'اکت', 
					'ru-RU' => 'Окт',
					'tr-TR' => 'Eki'
				), $this->langtag ),
				11 => EGOText::_('EGO_NOV', array(
					'd' => 'Nov', 
					'fa-IR' => 'نو', 
					'ru-RU' => 'Нояб',
					'tr-TR' => 'Kas'
				), $this->langtag ),
				12 => EGOText::_('EGO_DEC', array(
					'd' => 'Dec', 
					'fa-IR' => 'دس', 
					'ru-RU' => 'Дек',
					'tr-TR' => 'Ara'
				), $this->langtag ),
			);
		}
		
		if(!isset($select))
			return $months;
		else
			return $months[$select];			
	}
	
	public function getWeekday($select = null, $type = 'long') 
	{
		if(isset($select))
		{
			$select = (int) $select;
		}

		if($type == 'long')
		{
			$week = array(
				1 => EGOText::_('EGO_MON_LG', array(
					'd' => 'Monday', 
					'fa-IR' => 'دوشنبه',
					'ru-RU' => 'понедельник',
					'tr-TR' => 'Pazartesi'
				), $this->langtag ),
				2 => EGOText::_('EGO_TUE_LG', array(
					'd' => 'Tuesday', 
					'fa-IR' => 'سه شنبه',
					'ru-RU' => 'вторник',
					'tr-TR' => 'Salı'
				), $this->langtag ),
				3 => EGOText::_('EGO_WED_LG', array(
					'd' => 'Wednesday', 
					'fa-IR' => 'چهارشنبه',
					'ru-RU' => 'среда',
					'tr-TR' => 'Çarsamba'
				), $this->langtag ),
				4 => EGOText::_('EGO_THU_LG', array(
					'd' => 'Thursday', 
					'fa-IR' => 'پنجشنبه',
					'ru-RU' => 'четверг',
					'tr-TR' => 'Persembe'
				), $this->langtag ),
				5 => EGOText::_('EGO_FRI_LG', array(
					'd' => 'Friday', 
					'fa-IR' => 'جمعه',
					'ru-RU' => 'пятница',
					'tr-TR' => 'Cuma'
				), $this->langtag ),
				6 => EGOText::_('EGO_SAT_LG', array(
					'd' => 'Saturday', 
					'fa-IR' => 'شنبه',
					'ru-RU' => 'суббота',
					'tr-TR' => 'Cumartesi'
				), $this->langtag ),
				7 => EGOText::_('EGO_SUN_LG', array(
					'd' => 'Sunday', 
					'fa-IR' => 'یکشنبه',
					'ru-RU' => 'воскресенье',
					'tr-TR' => 'Pazar'
				), $this->langtag ),
			);
		}
		elseif($type == 'short')
		{
			$week = array(
				1 => EGOText::_('EGO_MON', array(
					'd' => 'Mon', 
					'fa-IR' => 'د',
					'ru-RU' => 'Пн',
					'tr-TR' => 'Pzt'
				), $this->langtag ),
				2 => EGOText::_('EGO_TUE', array(
					'd' => 'Tue', 
					'fa-IR' => 'س',
					'ru-RU' => 'вт',
					'tr-TR' => 'Sa'
				), $this->langtag ),
				3 => EGOText::_('EGO_WED', array(
					'd' => 'Wed', 
					'fa-IR' => 'چ',
					'ru-RU' => 'ср',
					'tr-TR' => 'Çrs'
				), $this->langtag ),
				4 => EGOText::_('EGO_THU', array(
					'd' => 'Thu', 
					'fa-IR' => 'پ',
					'ru-RU' => 'чт',
					'tr-TR' => 'Prs'
				), $this->langtag ),
				5 => EGOText::_('EGO_FRI', array(
					'd' => 'Fri', 
					'fa-IR' => 'ج',
					'ru-RU' => 'пт',
					'tr-TR' => 'Cum'
				), $this->langtag ),
				6 => EGOText::_('EGO_SAT', array(
					'd' => 'Sat', 
					'fa-IR' => 'ش',
					'ru-RU' => 'сб',
					'tr-TR' => 'Cmt'
				), $this->langtag ),
				7 => EGOText::_('EGO_SUN', array(
					'd' => 'Sun', 
					'fa-IR' => 'ی',
					'ru-RU' => 'вс',
					'tr-TR' => 'Paz'
				), $this->langtag ),
			);
		}
		
		if(!isset($select))
			return $week;
		else
			return $week[$select];
	}
	
	public function getMonthsdur($year, $month = null) 
	{
		$year = (int) $year;
		if(isset($month))
			$month = (int) $month;
			
		$months = array(
			1 => 31,
			3 => 31,
			4 => 30,
			5 => 31,
			6 => 30,
			7 => 31,
			8 => 31,
			9 => 30,
			10 => 31,
			11 => 30,
			12 => 31,
		);
		if($this->isKbs($year))
			$months[2] = 29;
		else
			$months[2] = 28;
		
		if(!isset($month))
			return $months;
		else
			return $months[$month];			
	}
	
	public function isKbs($year) 
	{
		if($year%4 == 0)
			return true;
		else
			return false;
	}
	
	public function getDurs($year, $month = null)
	{
		if($month == '')
			$month = null;
		if(!isset($month))
		{
			$lastday		= $this->getMonthsdur($year, 12);
			$output['start']= $this->toGre($year.'-01-01', 'Y-m-d');
			$output['end']	= $this->toGre($year.'-12-'.$lastday, 'Y-m-d');
		}
		else
		{
			$lastday		= $this->getMonthsdur($year, $month);
			$output['start']= $this->toGre($year.'-'.$month.'-01', 'Y-m-d');
			$output['end']	= $this->toGre($year.'-'.$month.'-'.$lastday, 'Y-m-d');
		}	
		return $output;
	}
}
