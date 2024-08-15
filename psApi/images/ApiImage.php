<?php

require_once __DIR__ . '/../vendor/autoload.php';

class ApiImage{
    public $webService;
    public $base;
    public $key;
    
    public function __construct($base, $apiKey) {
        $this->webService = new PrestaShopWebservice($base , $apiKey, false);
        $this->base = $base;
        $this->key = $apiKey;
    }

    public function hello(){
        $product = $this->webService->get(['url' => $this->base . '/api/products/12']);

        if (!$product || isset($product['errors'])) {
            echo 'Błąd podczas pobierania danych produktu.';
        } else {
            echo var_dump($product);
        }
    }

    public function getProduct($idProduct){
        try{
            $params = ['resource' => 'products', 
                             'id' => $idProduct,
            ];

            $xml = $this->webService->get($params);        
            $product = $xml;

            if ($product) {
                var_dump($product);
            } else {
                echo 'Błąd pobierania danych';
            }
        } catch (Exception $e){

        }
    }

    public function getIndexProduct($idProduct){
        try{
            $params = [
                'resource' => 'products',
                'id' => $idProduct,
                'fields' => 'reference',
            ];

            $xml = $this->webService->get($params);        
            $product = $xml->product;

            if ($product) {
                $reference = (string) $product->reference;
                
                //var_dump($product);
                //echo 'Kod referencyjny: ' . $reference;
                return $reference;
            } else {
                echo 'Błąd pobierania danych';
            }
        } catch (Exception $e){

        }
    }

    public function getProductsIds($categoryId){    
        $url = $this->base . 'api/products/?display=[id]&filter[id_category_default]=' . $categoryId;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        echo $httpCode . "\n";
        if($httpCode == 200){
            $xml = simplexml_load_string($response);
            echo "Products in category : " . count($xml->products->product) . "\n";
        }
        return $xml->products->product;
    }

    //?display=[combinations]&filter[id]=4708
    public function getCombinationsProductByIdProudct($idProduct){
        $url = $this->base . 'api/products/' . $idProduct;
        //$url = $this->base . 'api/products/' . $idProduct . '/combinations';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if($httpCode == 200){
            $xml = simplexml_load_string($response);
            $combinations = $xml->combinations;
            foreach($combinations as $combination) {
                echo $httpCode;
            }
            var_dump($response);
        }
    }


    /*
    public function addImg($idProduct, $imagePath, $imageMime){
        $urlImage = $this->base . 'api/images/products/' . $idProduct; 

        $image_path = $imagePath;
        $image_mime = $imageMime; 

        $args['image'] = new CurlFile($image_path, $image_mime);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_URL, $urlImage);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //var_dump($result);
        echo $httpCode;

        if (200 == $httpCode) {
            echo 'Product image was successfully created.';
        }
    }*/

    public function addImg($idProduct, $imagePath, $imageMime) {
        $urlImage = $this->base . 'api/images/products/' . $idProduct; 
    
        $args['image'] = new CurlFile($imagePath, $imageMime);
    
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $urlImage);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0); // Header output disabled
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':');
    
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        curl_close($ch);
    
        echo $httpCode;
    
        if ($httpCode == 200) {
            echo 'Product image was successfully created.';
        } else {
            echo 'Failed to upload image. HTTP Status Code: ' . $httpCode;
            // Optionally, you can also output the result for debugging
            // echo 'Response: ' . $result;
        }
    }
    

    /*
    public function getImgIds($idProduct){
        $url = $this->base . 'api/images/products/' . $idProduct;

        echo $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo "\n";
        var_dump($response);
        //echo "<pre>" . print_r($response). "<pre>";

        curl_close($ch);

        $arr = array();
        if ($httpCode == 200) {
            $xmlStart = strpos($response, '<?xml');
            $xml = substr($response, $xmlStart);

            $xml = simplexml_load_string($xml);
            $declinations = $xml->image->declination;

            foreach($xml->image->declination as $dec){
                array_push($arr, $dec);
            }
        } else {
            echo 'Wystąpił błąd pobierania ids img: ' . $httpCode . "\n";
        }

        return $arr;
    }*/

    public function getImgIds($idProduct){
        $url = $this->base . 'api/images/products/' . $idProduct;
    
        echo $url . "\n";
    
        // Inicjalizacja cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');
    
        // Wyłączenie weryfikacji certyfikatu SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
        // Wykonanie zapytania
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
            curl_close($ch);
            return [];
        }
    
        // Pobranie kodu HTTP
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // Oddzielenie nagłówków od treści
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
    
        curl_close($ch);
    
        // Obsługa odpowiedzi
        $arr = array();
        if ($httpCode == 200) {
            // Przetwarzanie XML
            $xml = simplexml_load_string($body);

            //var_dump($xml);

            if ($xml === false) {
                echo 'Error parsing XML response.' . "\n";
                return [];
            }
    
            foreach ($xml->image->declination as $image) {
                echo $image['id'][0] . "\n";
                array_push($arr, (string)$image['id'][0]);
            }
        } else {
            echo 'Wystąpił błąd pobierania ids img: ' . $httpCode . "\n";
            echo 'Nagłówki odpowiedzi: ' . $headers . "\n";
        };
        return $arr;
    }  

    public function removeImg($idProduct, $id_image){
        $url = $this->base . 'api/images/products/' . $idProduct . '/' . $id_image; 

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Sprawdzenie odpowiedzi
        if ($response === false) {
            echo 'Błąd: ' . curl_error($ch);
        } else {
            echo 'Obrazek został usunięty' . "\n";
        }
        
        echo $this->webService->delete(array('resource' => 'images/products/'. $idProduct, 'id' => $id_image));
    }

    public function removeAllImg($idProduct){
        $declinations = $this->getImgIds($idProduct);

        //var_dump($declinations);

        foreach($declinations as $el){
            //var_dump($el);
            //die();
            //$url = $this->base . 'api/images/products/' . $idProduct . '/' . $el->attributes()['id'];
            $url = $this->base . 'api/images/products/' . $idProduct . '/' . $el;

            //var_dump($el);
            echo "url ". $url . "\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200) {
                echo 'Wszystkie obrazy zostały usunięte dla produktu o ID: ' . $idProduct . "\n";
            } else {
                echo 'Wystąpił błąd usuwania: ' . $httpCode . "\n";
            }
        }

    }

    public function removeAllImg2($idProduct){
        $imgIdArr = $this->getImgIds($idProduct);
        var_dump($imgIdArr);
        foreach($imgIdArr as $el){
            $this->removeImg($idProduct, $el['id']);
        }
    }

    public function updateImg($idProduct, $id_image){
        $urlImage = $this->base . 'api/images/products/' . $idProduct . '/' . $id_image . "/?ps_method=PUT";

        $image_path = 'enl.jpeg';
        $image_mime = 'image/jpg';

        $args['image'] = new CurlFile($image_path, $image_mime);
        $args['image'] = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_URL, $urlImage);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (200 == $httpCode) {
            echo 'Category image was successfully updated.';
        }        
    }

    public function imageExtensionFromMime($mime) {
        switch($mime) {
            case 'image/jpeg':
                return '.jpg';
            case 'image/png':
                return '.png';
            case 'image/gif':
                return '.gif';
            case 'image/webp';
                return '.webp';
            default:
                return '';
        }
    }

    /*
    public function downloadImg($idProduct, $id_image, $downloadedPath){
        $urlImage = $this->base . 'api/images/products/' . $idProduct . '/' . $id_image;

        echo $urlImage . "\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $urlImage);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':');
        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        //var_dump($result);

        if (200 == $httpCode) {
            echo 'Pobrano obraz.';

            // Odczytanie rozszerzenia pliku na podstawie odpowiedzi
            echo $contentType . "</br>";

            $extension = $this->imageExtensionFromMime($contentType);

            // Zapisanie pliku z odpowiednim rozszerzeniem
            $newDownloaded = $downloadedPath . $extension;

            $file = fopen($newDownloaded, "w");
            fputs($file, $result);
            fclose($file);
        }else{
            echo $httpCode;
        }
    }*/

    public function downloadImg($idProduct, $id_image, $downloadedPath){
        $urlImage = $this->base . 'api/images/products/' . $idProduct . '/' . $id_image;
    
        echo "Pobieranie obrazu z: " . $urlImage . "\n";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $urlImage);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':');
        
        // Wyłączanie weryfikacji SSL w przypadku problemów z certyfikatem
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
        $result = curl_exec($ch);
    
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch) . "\n";
        }
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
    
        if ($httpCode == 200) {
            echo "Obraz pobrany pomyślnie.\n";
            echo "Typ zawartości: " . $contentType . "\n";
    
            // Uzyskanie rozszerzenia pliku na podstawie typu MIME
            $extension = $this->imageExtensionFromMime($contentType);
    
            if ($extension) {
                $newDownloaded = $downloadedPath . $id_image . $extension;
                echo "Zapisanie obrazu do: " . $newDownloaded . "\n";
    
                // Zapisanie pliku na dysku
                $file = fopen($newDownloaded, "w");
                if ($file) {
                    fwrite($file, $result);
                    fclose($file);
                    echo "Obraz zapisany pomyślnie.\n";
                } else {
                    echo "Błąd przy otwieraniu pliku do zapisu.\n";
                }
            } else {
                echo "Nieznany typ zawartości: " . $contentType . "\n";
            }
        } else {
            echo "Błąd pobierania obrazu: HTTP kod " . $httpCode . "\n";
        }
    }


    public function downloadAllImg($idProduct){
        $imgIdArr = $this->getImgIds($idProduct);
        var_dump($imgIdArr);
        foreach($imgIdArr as $el){
            $this->downloadImg($idProduct, $el,  __DIR__ . "/tmp/" . $el);
        }
    }

    /*
    public function getCombinationsIdProductImage($combinationId){
        $url = $this->base . '/combinations/' . $combinationId . '/images';
    }
    */

}
