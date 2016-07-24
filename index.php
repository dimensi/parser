<?php
    require_once 'functions.php';
    use function \Functions\getDocument;
    use function \Functions\findItems;
    use function \Functions\getPages;
    use function \Functions\createLexicon;
    use function \Functions\updateArrays;
    use function \Functions\saveToCSV;

    $document = getDocument('http://tiesshop.ru/catalog/102');
    $findItems = findItems($document);
    $pages = getPages($findItems);
    $lexicon = createLexicon($pages);
    $result = updateArrays($pages, $lexicon);
    saveToCSV($result, 'file1.csv');
