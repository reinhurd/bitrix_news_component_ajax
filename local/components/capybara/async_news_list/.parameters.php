<?

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loader::includeModule("iblock");

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 */

Loc::loadMessages(__FILE__);

##Подгружаемые инфоблоки с типами
$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock = array();
$iblockFilter = (
!empty($arCurrentValues['IBLOCK_TYPE'])
    ? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
    : array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
    $arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
unset($arr, $rsIBlock, $iblockFilter);
###Параметры для сортировки
$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
    "ID"=>GetMessage("T_IBLOCK_DESC_FID"),
    "NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
    "ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
    "SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
    "TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
);

$arComponentParameters['PARAMETERS'] = array(
    "IBLOCK_TYPE" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => Loc::GetMessage("IBLOCK_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlockType,
        "REFRESH" => "Y",
    ),
    "IBLOCK_ID" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => Loc::GetMessage("IBLOCK_IBLOCK"),
        "TYPE" => "LIST",
        "ADDITIONAL_VALUES" => "Y",
        "VALUES" => $arIBlock,
        "REFRESH" => "Y",
    ),
    "ADDITIONAL_FILTER" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => Loc::GetMessage("ADDITIONAL_FILTER"),
        "TYPE" => "STRING",
        "ADDITIONAL_VALUES" => "Y",
        "REFRESH" => "Y",
        "DEFAULT" => "",
    ),
    "NEWS_NUM_ALL" => array(
        "PARENT" => "VISUAL",
        "NAME" => Loc::GetMessage("NEWS_NUM_ALL"),
        "TYPE" => "STRING",
        "ADDITIONAL_VALUES" => "Y",
        "REFRESH" => "N",
        "DEFAULT" => 10,
    ),
    "SORT_BY1" => Array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
        "TYPE" => "LIST",
        "DEFAULT" => "ACTIVE_FROM",
        "VALUES" => $arSortFields,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_ORDER1" => Array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
        "TYPE" => "LIST",
        "DEFAULT" => "DESC",
        "VALUES" => $arSorts,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_BY2" => Array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
        "TYPE" => "LIST",
        "DEFAULT" => "SORT",
        "VALUES" => $arSortFields,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_ORDER2" => Array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
        "TYPE" => "LIST",
        "DEFAULT" => "ASC",
        "VALUES" => $arSorts,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_CUSTOM" => array(
        "PARENT" => "VISUAL",
        "NAME" => Loc::GetMessage("SORT_CUSTOM"),
        "TYPE" => "STRING",
        "ADDITIONAL_VALUES" => "Y",
        "REFRESH" => "Y",
        "DEFAULT" => "",
    ),
);
