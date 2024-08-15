<?php

class PsApiProductCurl{
    public $base;
    public $key;
    
    public function __construct($base, $apiKey) {
        $this->base = $base;
        $this->key = $apiKey;
    }

    public function getProductApi($apiUrl){    
        //$url = $this->base . 'api/products/?display=[id]&filter[id_category_default]=' . $categoryId;
        $url = $this->base . 'api/products/' . $apiUrl;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        echo $httpCode . "\n";
        if($httpCode == 200){
            $xml = simplexml_load_string($response);
        }
        return $xml->products->product;
    }

    public function addEmptyProductApi($productData) {
        $url = $this->base . 'api/products/';

        $xmlData = '<prestashop><product>';
        foreach ($productData as $key => $value) {
            $xmlData .= "<$key><![CDATA[$value]]></$key>";
        }
        $xmlData .= '</product></prestashop>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        echo $httpCode . "\n";
        return $response;
    }

    public function deleteProductApi($productId) {
        $url = $this->base . 'api/products/' . $productId;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        echo $httpCode . "\n";
        return $response;
    }

}
