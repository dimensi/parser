<?php
require_once('phpQuery/phpQuery.php');
function getDocument($link) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $catalog = curl_exec($ch);
    curl_close($ch);
    return phpQuery::newDocumentHTML($catalog);
}

function findItems($document) {
    $links = $document->find('.good_img');
    foreach ($links as $value) {
        $pq = pq($value);
        $findNope = $pq->find('span[style=color:#a33;]')->text();
        if ($findNope != "Нет в наличии") {
            $link = 'http://tiesshop.ru' . $pq->find('a.good_title')->attr('href');
            $linksFromCatalog[] = $link;
        }
    }
    return $linksFromCatalog;
}

function getTitlePage($document) {
    return $document->find('.pad > h1')->text();
}

function getTablePage($document) {
    foreach ($document->find('table#properties > tr') as $value) {
        $trPq = pq($value);
        $tdKey = $trPq->children('td')->eq(0)->text();
        $tdContent = $trPq->children('td')->eq(1)->text();
        $table[$tdKey] = $tdContent;
    }
    return $table;
}

function getPages($pagesLinks) {
    foreach ($pagesLinks as $itemLink)  {
        $itemDocument = getDocument($itemLink);
        $title = getTitlePage($itemDocument);
        $table = getTablePage($itemDocument);
        $pageContent['title'] = $title;
        $pageContent['content'] = $table;
        $pages[] = $pageContent;
    }
    return $pages;
}
