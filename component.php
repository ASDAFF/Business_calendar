<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
//echo "<pre>";

$arParams["month"] = array(
	1 => "Январь",
	2 => "Февраль",
	3 => "Март",
	4 => "Апрель",
	5 => "Май",
	6 => "Июнь",
	7 => "Июль",
	8 => "Август",
	9 => "Сентябрь",
	10 =>"Октябрь",
	11 =>"Ноябрь",
	12 =>"Декабрь",
);

$arParams["num_in_week"] = array(
	1 => "пн",
	2 => "вт",
	3 => "ср",
	4 => "чт",
	5 => "пт",
	6 => "сб",
	7 => "вс",
);

$arParams["today"] = array(
	"year" =>  date("Y"),
	"month" => date("n"),
	"day" =>   date("j"),
);

$calendar = new BusinessCalendar($arParams['IBLOCK_HOLIDAY_ID'], $arParams['IBLOCK_HOLIDAY_PROPERTY']);

$year = (int)$_GET["year"] ? (int)$_GET["year"] : date("Y");
$arResult = $calendar->getCalendar($year);

$arParams["years"] = $calendar->getYearsNav();
$arParams["year_select"] = $year;

//echo "</pre>";
$this->IncludeComponentTemplate();
?>
