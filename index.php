<?php
    require_once('functions.php');
    // use function \Functions\getDocument;
    // use function \Functions\findItems;
    // use function \Functions\getPages;
    $document = getDocument('http://tiesshop.ru/catalog/102');
    $findItems = findItems($document);
    $pages = getPages($findItems);
    print_r($pages);
    // $printTableTitles = array_keys($pages[0]['content']);
    // echo mb_convert_encoding("Название;","windows-1251");
    // echo mb_convert_encoding(implode(";", $printTableTitles) . PHP_EOL, "windows-1251");
    // foreach ($pages as $key => $page) {
    //     $titlePage = $pages[$key]['title'];
    //     echo mb_convert_encoding($titlePage . ';', "windows-1251");
    //     echo mb_convert_encoding(implode(";", $page['content']), "windows-1251");
    //     echo PHP_EOL;
    // }
