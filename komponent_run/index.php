<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("NEWS.LIST с асинхронной подгрузкой страниц");
?><?$APPLICATION->IncludeComponent(
	"capybara:async_news_list",
	".default", 
	array(
		"ADDITIONAL_FILTER" => "",
		"IBLOCK_ID" => "1",
		"IBLOCK_TYPE" => "news",
		"NEWS_NUM_ALL" => "10",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_CUSTOM" => "",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>