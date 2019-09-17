<?php


use \Bitrix\Main\Loader,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Type\ParameterDictionary;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

Loader::includeModule("iblock");

class AsyncNewsListComponent extends \CBitrixComponent
{
    ##Запрашиваемые сведения об элементе по дефолту
    public $requested_fields = array(
        "ID",
        "NAME",
        "DISPLAY_ACTIVE_FROM",
        "PREVIEW_TEXT",
        "DETAIL_PAGE_URL",
    );

    ##Работа со строкой пагинации
    public $nav_string = "";
    public $current_page = 1;
    public $nEndPage;

    public function prepareFilter()
    {

        $arFilter = array(
            "IBLOCK_ID"=>$this->arParams['IBLOCK_ID'],
            "ACTIVE_DATE"=>"Y",
            "ACTIVE"=>"Y",
            array(
     ##Чтобы отображались новости из корневого раздела при фильтрации по активности разделов
                "LOGIC" => "OR",
                array("SECTION_ID" => 0),
                array("!SECTION_ID" => 0, "SECTION_GLOBAL_ACTIVE"=>"Y"),
            ),
        );

        ##Работа с приходящим фильтром, если он есть
        if ($this->arParams['ADDITIONAL_FILTER'] && is_array($this->arParams['ADDITIONAL_FILTER'])) {
            $arFilter = array_merge($arFilter, $this->arParams['ADDITIONAL_FILTER']);
        }

        return $arFilter;
    }

    public function prepareSort()
    {
        $arOrder = array(
            $this->arParams['SORT_BY1'] => $this->arParams['SORT_ORDER1'],
            $this->arParams['SORT_BY2']=> $this->arParams['SORT_ORDER2']
        );
        ##У кастомной переменной с сортировкой приоритет
        if($this->arParams['SORT_CUSTOM']) {
            return $this->arParams['SORT_CUSTOM'];
        } else {
            return $arOrder;
        }
    }

    public function setPageCount()
    {
        return array(
            "nPageSize" => $this->arParams["NEWS_NUM_ALL"],
            "iNumPage"=>$this->current_page,
            "bShowAll" => false
        );
    }

    public function getResultFromGetList()
    {
        $items = array();


            $res = CIBlockElement::GetList(
                $this->prepareSort(),
                $this->prepareFilter(),
                false,
                $this->setPageCount(),
                $this->requested_fields
            );
            $this->nav_string = $res->GetPageNavStringEx($navComponentObject, '', '.default', true, $this);
            ##Получим последнюю страницу
            $x_nav = $res->NavStart("");
            $e_nav = $res->nEndPage;
            $this->nEndPage = $e_nav;

            while($ob = $res->GetNextElement())
            {
                $arFields  = $ob->GetFields();
                ##Генерация ссылок для работы эрмитажа
                $arButtons = CIBlock::GetPanelButtons(
                    $this->arParams['IBLOCK_ID'],
                    $arFields["ID"],
                    false,
                    array("SECTION_BUTTONS"=>false, "SESSID"=>false)
                );
                $arFields["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                $arFields["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
                $arFields['IBLOCK_ID'] = $this->arParams['IBLOCK_ID'];
                $items[] = $arFields;
            }
        return $items;
    }

    public function closeNoscriptForNavString($nav_string)
    {
        $nav_string_raw = explode('<a', $nav_string);
        foreach ($nav_string_raw as &$link) {
            $link = '<a'.$link;
            if (strpos($link, 'PAGEN_')!==false
                && strpos($link, 'PAGEN_1=1')===false){
                $link = "<noscript>".$link."</noscript>";
            }
            if(strpos($link, "<b>$this->current_page</b>")!==false) {
                $link = str_replace(
                    "<b>$this->current_page</b>",
                    "</noscript><b>$this->current_page</b><noscript>",
                    $link
                    );
            }
        }
        return implode('',$nav_string_raw);
    }

    ##Если страница показывается первый раз
    public function getResultFromGetListforNewPage($current_page)
    {
        $items = array();


        $res = CIBlockElement::GetList(
            $this->prepareSort(),
            $this->prepareFilter(),
            false,
            array(
                "nPageSize" => ($this->arParams["NEWS_NUM_ALL"] * ($current_page-1)),
                "iNumPage"=>1,
                "bShowAll" => false
            ),
            $this->requested_fields
        );

        while($ob = $res->GetNextElement())
        {
            $arFields  = $ob->GetFields();
            ##Генерация ссылок для работы эрмитажа
            $arButtons = CIBlock::GetPanelButtons(
                $this->arParams['IBLOCK_ID'],
                $arFields["ID"],
                false,
                array("SECTION_BUTTONS"=>false, "SESSID"=>false)
            );
            $arFields["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
            $arFields["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
            $arFields['IBLOCK_ID'] = $this->arParams['IBLOCK_ID'];
            $items[] = $arFields;
        }
        return $items;
    }

    public function generateLinkForAjax()
    {
        if($this->current_page < $this->nEndPage) {
            return parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH).'?PAGEN_1='.($this->current_page+1);
        } else {
            return '';
        }
    }

    public function workWithCPHPCache($current_page)
    {
        global $USER;

        ##Проверка, не эта ли первая страница в запросе
        if(!$_GET['NOT_NEW_PAGE'] && intval($current_page) !==1){
            $new_page = $current_page;
        } else {
            $new_page = false;
        }


        ##Учитываем группу пользователя для кеширования по умолчанию
        ##Кешируем и номер текущей страницы для возможности работы с навигацией
        $cache_id = md5(
            serialize(
                array(
                    $this->arParams,
                    $USER->GetGroups(),
                    $this->current_page,
                    $new_page
                )
            )
        );
        $cache_dir = false;

        $obCache = new CPHPCache;
        if($obCache->InitCache(3600000, $cache_id, $cache_dir))
        {
            $vars = $obCache->GetVars();
            $this->arResult = $vars['arResult'];
        }
        elseif($obCache->StartDataCache())
        {
            $items = $this->getResultFromGetList($current_page);
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_dir);

            if($new_page){
                $items_new = $this->getResultFromGetListforNewPage($new_page);
                foreach($items_new as $item)
                {
                    ##Кеш, который изменяется при изменении элементов инфоблока
                    $CACHE_MANAGER->RegisterTag("iblock_id_".$item["IBLOCK_ID"]);
                    $arResult['ITEMS'][] = $item;
                }
            }

            foreach($items as $item)
            {
                ##Кеш, который изменяется при изменении элементов инфоблока
                $CACHE_MANAGER->RegisterTag("iblock_id_".$item["IBLOCK_ID"]);
                $arResult['ITEMS'][] = $item;
            }

            $arResult['NAV_STRING'] = $this->closeNoscriptForNavString($this->nav_string);
            $arResult['CURRENT_PAGE'] = $this->current_page;
            $arResult['AJAX_LINKS'] = $this->generateLinkForAjax();
            $arResult['END_NAV'] = $this->nEndPage;


            $CACHE_MANAGER->RegisterTag("iblock_id_new");
            $CACHE_MANAGER->EndTagCache();

            ##Исключение кеширования шаблона
            $obCache->EndDataCache(array(
                'arResult' => $arResult,
            ));

            ##Если кеша пока нет вообще
            $this->arResult = $arResult;
        }
    }

    public function executeComponent()
    {
        $this->current_page = ($_GET['PAGEN_1'])? htmlentities($_GET['PAGEN_1']) : 1;

        $this->workWithCPHPCache($this->current_page);

        $this->includeComponentTemplate();
    }
}