<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
* BusinessCalendar
* Класс, генерирующий производственный календарь на указанный период.
* Праздники берутся из инфоблока
* 
* @author Sword Dancer
* @version 1.0.0
*/

class BusinessCalendar
{
	private $iblock_id;
	private $porop_type_day;

	private $arAllHolidaysFromDB;
	private $arHolidayTypes;

	const SECOND_IN_DAY = 86400;

	/**
	* __construct Из параметров компонента в конструктор передаётся 
	* ИД инфоблока с праздниками и название его свойства, где указан 
	* тип дня - выходной, рабочий или короткий день
	*/
	public function __construct($iblock_id, $porop_type_day)
	{
		$this->iblock_id = $iblock_id;
		$this->porop_type_day = $porop_type_day;
		
		$this->arAllHolidaysFromDB = $this->getAllHolidaysFromDB();
		$this->arHolidayTypes = $this->getHolidayTypeCodes();
	}

	/**
	* getCalendar Возвращает календарь на указанные года в виде массива
	*/
	public function getCalendar($year)
	{
		$arCalendarBase = $this->generateCalendarBase($year);
		$arCalendarHolidays = $this->getCalendarHolidays($year);

		$arCalendar = $this->addHolidaysIntoCalendarBase($arCalendarBase, $arCalendarHolidays);
		return $arCalendar;
	}

	/**
	* generateCalendarBase Создаёт пустой календарь без праздников на указанные года
	*/
	private function generateCalendarBase($year)
	{
		$arCalendar = array();
		for($month=1; $month<=12; $month++)
		{
			$first_day_stamp = mktime(0,0,0, $month, 1, $year);
			$day_in_month = date('t', $first_day_stamp);
			$last_day_stamp = $first_day_stamp + $day_in_month*self::SECOND_IN_DAY;

			for($day_stamp = $first_day_stamp;
				$day_stamp < $last_day_stamp;
				$day_stamp = $day_stamp + self::SECOND_IN_DAY )
					$arCalendar = $this->calendarArrayBuilder($day_stamp, $arCalendar, array());
		}

		return $arCalendar;
	} 
	/**
	* getCalendarHolidays Создаёт календарь из праздников на указанные года
	*/
	private function getCalendarHolidays($year)
	{
		$arHolidays = array();
		$arHolidaysFromDB = $this->getHolidaysFromDB($year);
		foreach ($arHolidaysFromDB as $arHolidayFromDB)
		{
			$day_start_stamp =  $arHolidayFromDB["ACTIVE_FROM_TIMESTAMP"];
			$day_finish_stamp = $arHolidayFromDB["ACTIVE_TO_TIMESTAMP"];
		
			$holiday_type_id = $arHolidayFromDB['PROPERTY_'.$this->porop_type_day.'_ENUM_ID'];
			$type_code = $this->arHolidayTypes[$holiday_type_id]["XML_ID"];

			for($day_stamp =  $day_start_stamp;
				$day_stamp <= $day_finish_stamp;
				$day_stamp =  $day_stamp + self::SECOND_IN_DAY )
			{
				$arParams = array(
					"type_name" => $arHolidayFromDB["PROPERTY_".$this->porop_type_day."_VALUE"],
					"title" =>     $arHolidayFromDB['PREVIEW_TEXT'],
					"type_code" => $type_code,
				);
				$arHolidays = $this->calendarArrayBuilder($day_stamp, $arHolidays, $arParams);
			}
		}
		return $arHolidays;		
	}
	/**
	* addHolidaysIntoCalendarBase Объединяет праздники и базовый календарь
	*/
	private function addHolidaysIntoCalendarBase($arCalendarBase, $arCalendarHolidays)
	{
		foreach ($arCalendarHolidays as $year => $arYear)
			foreach ($arYear as $month => $arMonth)
				foreach ($arMonth as $num_week => $arWeek)
					foreach ($arWeek as $num_in_week => $arDay)
						$arCalendarBase[$year][$month][$num_week][$num_in_week] = 
							$arCalendarHolidays[$year][$month][$num_week][$num_in_week];

		return $arCalendarBase;
	}

	private function getHolidaysFromDB($year)
	{
		$arHolidaysFromDB = array();
		foreach ($this->arAllHolidaysFromDB as $arHoliday)
		{
			$year_inherit = $arHoliday["ACTIVE_FROM_YEAR"];

			if ($year_inherit==$year)
				$arHolidaysFromDB[] = $arHoliday;

			if ($year_inherit > $year)
				break;
		}	
		return $arHolidaysFromDB;
	}

	private function calendarArrayBuilder($day_stamp, $arCalendar, $arParams)
	{
		$year =        date("Y", $day_stamp);
		$month =       date("n", $day_stamp);
		$day =         date("j", $day_stamp);
		$num_week =    date("W", $day_stamp);
		$num_in_week = date("N", $day_stamp);

		$type_code = ($num_in_week==6 || $num_in_week==7) ? "week_end" : "" ;

		$arCalendar[$year][$month][$num_week][$num_in_week] = array(
			"value" =>     $day,
			"type_name" => $arParams["type_name"],
			"title" =>     $arParams['title'],
			"type_code" => ($arParams['type_code'] ? $arParams['type_code'] : $type_code),
		);
		return $arCalendar;
	}


	/**
	* getYearsNav Возвращает навигацию по годам в виде массива
	*/
	public function getYearsNav()
	{
		$arMinAndMaxYear = $this->getMinAndMaxYear();
		$year_min = $arMinAndMaxYear["year_min"];
		$year_max = $arMinAndMaxYear["year_max"];

		$arYears = range($year_min, $year_max);
		return $arYears;
	}
	private function getMinAndMaxYear()
	{
		$arAllHolidaysFromDB = $this->arAllHolidaysFromDB;
		reset($arAllHolidaysFromDB);
		$first_holiday = current($arAllHolidaysFromDB);
		$last_holiday = end($arAllHolidaysFromDB);

		return array(
			"year_min" => $first_holiday["ACTIVE_FROM_YEAR"],
			"year_max" => $last_holiday['ACTIVE_TO_YEAR']
		);
	}


	private function getHolidayTypeCodes()
	{
		$arSort = Array("DEF"=>"DESC", "SORT"=>"ASC");
		$arFilter = Array("IBLOCK_ID"=>$this->iblock_id, "CODE"=>$this->porop_type_day);
		$dbHolidayTypes = CIBlockPropertyEnum::GetList($arSort, $arFilter);

		$arHolidayTypes = array();
		while($arHolidayType = $dbHolidayTypes->GetNext())
		{
			$arHolidayTypes[$arHolidayType["ID"]] = array(
				"ID" =>   $arHolidayType["ID"],
				"XML_ID"=>$arHolidayType["XML_ID"]
			);
		}		
		return $arHolidayTypes;
	}
	private function getAllHolidaysFromDB()
	{
		$arSelect = Array("ACTIVE_FROM", "ACTIVE_TO", "PREVIEW_TEXT", "PROPERTY_".$this->porop_type_day);
		$arFilter = Array("IBLOCK_ID"=>$this->iblock_id, "ACTIVE"=>"Y");
		$arSort = Array("ACTIVE_FROM"=>"ASC");
		$dbDays = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);

		$arAllHolidaysFromDB = array();
		while($arDay = $dbDays->Fetch())
		{
			$arDay["ACTIVE_FROM_TIMESTAMP"] = MakeTimeStamp($arDay["ACTIVE_FROM"]);
			$arDay["ACTIVE_TO_TIMESTAMP"] =   MakeTimeStamp($arDay["ACTIVE_TO"]);
			$arDay["ACTIVE_FROM_YEAR"] = date("Y", $arDay["ACTIVE_FROM_TIMESTAMP"]);
			$arDay["ACTIVE_TO_YEAR"] =   date("Y", $arDay["ACTIVE_TO_TIMESTAMP"]);

			$arAllHolidaysFromDB[] = $arDay;
		}
		return $arAllHolidaysFromDB;
	}

}