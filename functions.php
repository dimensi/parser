<?php

namespace Functions;

require_once 'phpQuery/phpQuery.php';
function getDocument($link) // Получает файл и сохраняет его как новым дом
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $catalog = curl_exec($ch);
    curl_close($ch);

    return \phpQuery::newDocumentHTML($catalog);
}

function findItems($document) // Получает из каталога все ссылки + проверяет на наличие
{
    $links = $document->find('.good_img');
    foreach ($links as $value) {
        $pq = pq($value);
        $findNope = $pq->find('span[style=color:#a33;]')->text();
        if ($findNope != 'Нет в наличии') {
            $link = 'http://tiesshop.ru'.$pq->find('a.good_title')->attr('href');
            $linksFromCatalog[] = $link;
        }
    }

    return $linksFromCatalog;
}

function getTitlePage($document) // Получает название со страницы товара
{
    $title = $document->find('.pad > h1')->text();
    $clearTitle = preg_replace('/\b[HSMCU]{1}[0-9]*\b|\!{2,}/', '', $title); // Очищаю название товара от мусора

    return mb_convert_encoding(trim(preg_replace('/\s{2,}/', ' ', $clearTitle)), 'windows-1251'); // Очищаю от лишних пробелов и конвертирую
}
function getPricePage($document) // Получает название со страницы товара
{
    $price = $document->find('.good_text2 > b')->text();
    return mb_convert_encoding(trim(preg_replace('/\s{2,}|\D/', ' ', $price)), 'windows-1251'); // Очищаю от лишних пробелов и конвертирую
}
function getSkuPage($title) // Получает SKU из названия товара
{
    return trim(preg_replace('/\D{0,}\b/', '', $title));
}

function getPages($links) // Собирает все данные из страниц и сохраняет в массив
{
    $nameTitle = mb_convert_encoding('Название', 'windows-1251'); // заготовка
    foreach ($links as $link) {
        $pageDoc = getDocument($link); // Получил дом
        $title = getTitlePage($pageDoc); // Получил название
        $sku = getSkuPage($title); // Получил SKU
        $price = getPricePage($pageDoc); // Получил цену
        $table[$nameTitle] = $title; // Записал название в массив
        $table['SKU'] = $sku; // Записал SKU в массив
        $table['Price'] = $price; // Записал цену в Price
        foreach ($pageDoc->find('table#properties > tr') as $tr) {
            $trPq = pq($tr);
            $tdKey = mb_convert_encoding(trim($trPq->children('td')->eq(0)->text()), 'windows-1251'); // Обрезаю и конвертирую
            $tdContent = mb_convert_encoding(trim($trPq->children('td')->eq(1)->text()), 'windows-1251');
            $table[$tdKey] = $tdContent;
        } // Прошелся foreach по всем страницам и собрал нужные данные
        $pages[] = $table; // Записал в единный массив
    }

    return $pages; // Вернул единный массив
}
function createLexicon($pages) // Создает словарь со всеми названиями типов характеристик
{
    $lexicon = []; // Создал массив
    foreach ($pages as $keys) {
        foreach (array_keys($keys) as $key) {
            if (!array_key_exists($key, $lexicon)) {
                $lexicon[$key] = null; // Записываю новый ключ, если его нет в массиве
            }
        }
    }

    return $lexicon; // Возвращаю словарь
}

function updateArrays($pages, $lexicon) // Сверяю со словарем каждый массив
{
    foreach ($pages as $onePageKey => $onePage) { // Запускаю foreach для каждого массива
        foreach ($lexicon as $lexKey => $lexValue) { // Проверяю foreach словарь и если нет похожего ключа, создаю новый с пустым значением
            if (!array_key_exists($lexKey, $onePage)) {
                $pages[$onePageKey][$lexKey] = null;
            }
        }
    }

    return $pages; // Возвращаю обработанный массив
}

function saveToCSV($array, $filename) // Сохраняю в csv
{
    $fp = fopen($filename, 'w');
    fputcsv($fp, array_keys($array[0]), ';');
    foreach ($array as $value) {
        fputcsv($fp, $value, ';');
    }
    fclose($fp);
}
