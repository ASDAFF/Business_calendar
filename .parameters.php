<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks_bd=Array();

$db_iblock = CIBlock::GetList(
	Array("SORT"=>"ASC"), 
	Array("SITE_ID"=>$_REQUEST["site"], 
	"TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-" ? $arCurrentValues["IBLOCK_TYPE"] : ""))
);
while($arRes = $db_iblock->Fetch())
{
	$arIBlocks_bd[$arRes["ID"]] = $arRes["NAME"];
}

$arComponentParameters = array(
	'PARAMETERS' => array(
		"IBLOCK_TYPE" => Array(
			"PARENT" => "ID_HOLIDAY",
			"NAME" => "Тип инфоблоков",
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_HOLIDAY_ID" => Array(
			"PARENT" => "ID_HOLIDAY",
			"NAME" => "Инфоблок праздников",
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks_bd,
			//"DEFAULT" => '={$_REQUEST["ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"IBLOCK_HOLIDAY_PROPERTY" => Array(
			"PARENT" => "ID_HOLIDAY",
			"NAME" => "Свойство типа дня",
			"TYPE" => "STRING",
		),		
		'CACHE_TIME'  =>  array('DEFAULT'=>3600),
	),
	'GROUPS' => array(
		"ID_HOLIDAY" => array(
		    "NAME" => "Инфоблок праздников",
		    "SORT" => "550",
		)
	)
);
?>
