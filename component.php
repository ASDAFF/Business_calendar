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

$year_start  = (int)$_GET["year"];
$year_finish = (int)$_GET["year"];
if(!$year_start || !$year_finish)
{
	$year_start  = date("Y");
	$year_finish = date("Y");
}

$calendar = new CBusinessCalendar($arParams['IBLOCK_HOLIDAY_ID'], $arParams['IBLOCK_HOLIDAY_PROPERTY']);

$arResult = $calendar->getCalendar($year_start, $year_finish);

$arParams["years"] = $calendar->getYearsNav();
$arParams["year_select"] = $year_start;

//echo "</pre>";
$this->IncludeComponentTemplate();
?>
