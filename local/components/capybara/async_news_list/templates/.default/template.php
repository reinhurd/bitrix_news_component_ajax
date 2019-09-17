<?php

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);
CJSCore::Init(array("jquery"));
CJSCore::Init(array('ajax'));
$this->addExternalJs($templateFolder . '/scripts.js');
$this->addExternalCss($templateFolder . '/styles.css');
    ?>
    <div class="news-list-custom" data-page="<?=$arResult['CURRENT_PAGE']?>">
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
        <p class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <?if($arItem["DISPLAY_ACTIVE_FROM"]):?>
                <span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
            <?endif?>
            <?if($arItem["NAME"]):?>
                    <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
            <?endif;?>
            <?if($arItem["PREVIEW_TEXT"]):?>
                <?echo $arItem["PREVIEW_TEXT"];?>
            <?endif;?>
            </p>
        <?endforeach;?>
        <br />
    </div>
    <div class="place_to_insert"></div>
    <div
            class="ajax_link"
            data-lastpage="<?=$arResult['END_NAV']?>"
            data-url="<?=$arResult['AJAX_LINKS']?>">
        <?=$arResult["NAV_STRING"]?>
    </div>

<script>
    //Подгрузка новых новостей при скролле
    var enabled = true;
    $(window).scroll(
        function() {

            if ($(window).scrollTop() + $(window).height() == $(document).height() && enabled) {
                asyncLoad();
                enabled = false;
            }
        }
    );



</script>
