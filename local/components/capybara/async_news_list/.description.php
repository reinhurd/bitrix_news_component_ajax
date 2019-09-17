<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    'NAME'        => Loc::GetMessage('NAME'),
    'DESCRIPTION' => Loc::GetMessage('DESCRIPTION'),
    'ICON'        => '',
    'COMPLEX'     => 'N',
    'CACHE_PATH'  => 'Y',
    'PATH'        => array(
        'ID'    => 'capybara',
        'NAME'  => 'Capybara',
        'CHILD' => array(
            'ID'   => 'async_news_list',
            'NAME' => Loc::GetMessage('PATH_NAME'),
        ),
    ),
);
