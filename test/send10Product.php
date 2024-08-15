<?php

require_once(__DIR__ . "/../psApi/PsApiProductCurl.php");
require_once(__DIR__ . "/../psApi/configApi.php");

$url = PS_MIGRATOR_API_URL;
$key = PS_MIGRATOR_API_KEY;

$psApiProductCurl = new PsApiProductCurl($url, $key);

function initEmptyProductByApi(){
    $productData = [
        'id' => '99999'
        'name' => 'New Product',
        'price' => '0.00',
        'id_category_default' => '2',
    ];

    $psApiProductCurl->addEmptyProductApi($productData);
    $resposneProduct = $psApiProductCurl->getProductApi("");

    echo "<pre>";
    var_dump($resposneProduct);
    echo "</pre>";
}