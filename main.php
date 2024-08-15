<?php

ini_set('display_errors', true);

require_once(__DIR__ . '/db/parameters.php');
require_once(__DIR__ . "/db/EnlDb.php");
require_once(__DIR__ . "/db/EnlDbPs.php");
require_once(__DIR__ . '/PsProductMigrator.php');
require_once(__DIR__ . '/PsCategoryMigrator.php');
require_once(__DIR__ . "/psApi/images/ApiImage.php");


class PsProductMigratorService{
    public $prefix = "psov_";
    public $enlDbPsSource;
    public $enlDbPsDest;

    function __construct(){
        //__construct($prefix, $host, $username, $password, $database, $port)
        $this->enlDbPsSource = new EnlDbPs($this->prefix, MIG_SOURCE_HOST, MIG_SOURCE_USER, MIG_SOURCE_PASS, MIG_SOURCE_DB, MIG_SOURCE_PORT);
        $this->enlDbPsDest = new EnlDbPs($this->prefix, MIG_DEST_HOST, MIG_DEST_USER, MIG_DEST_PASS, MIG_DEST_DB, MIG_DEST_PORT);
    }

    function delete($idArr){
        $psCategoryMigrator = new PsCategoryMigrator($this->prefix, $this->enlDbPsSource, $this->enlDbPsDest);
        echo $psCategoryMigrator->countCompareRecords();
        echo "\n";
        echo $psCategoryMigrator->compareId();
        echo "\n";

        $psProductMigrator = new PsProductMigrator($this->enlDbPsSource, $this->enlDbPsDest);
        foreach($idArr as $id){
            $idProduct = $id;
            $psProductMigrator->deleteProduct($idProduct);
        }
    }

    function migrate($idArr){
        $psProductMigrator = new PsProductMigrator($this->enlDbPsSource, $this->enlDbPsDest);
        $apiSource = MIG_SOURCE_API;
        $apiDest = MIG_DEST_API;
        $apiSourceImage = new ApiImage(MIG_SOURCE_URL_API, $apiSource);
        $apiDestImage   = new ApiImage(MIG_DEST_URL_API, $apiDest);

        echo "\n\n";

        function sdir($folder){
            if(is_dir($folder)){
                $files = scandir($folder);
            
                $arr = array();
                foreach($files as $file){
                    if($file == '.' || $file == '..'){
                        continue;
                    }
                    array_push($arr, $file);
                }
            } else {
                echo 'Podana ścieżka nie jest folderem.';
            }
            return $arr;
        }


        function flushDir($folder){
            if(file_exists($folder)) {
                $files = glob($folder . '/*');
                foreach($files as $file) {
                    if(is_file($file)) {
                        unlink($file);
                    }
                }
                
                foreach($files as $file) {
                    if(is_dir($file)) {
                        flushDir($file);
                        rmdir($file);
                    }
                }
                echo 'Folder wyczyszczony.' . "\n";
            } else {
                echo 'Folder nie istnieje.' . "\n";
            }
        }

        $path = "./psApi/images/tmp";

        foreach($idArr as $idProduct){
            $psProductMigrator->send($idProduct);
            flushDir($path);
            $apiSourceImage->downloadAllImg($idProduct);
            //die();
            $apiDestImage->removeAllImg($idProduct);
            $folder = sdir($path);
            foreach ($folder as $el) {
                $file = $path . "/" . $el;
                echo $file . "\n";
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file);
                finfo_close($finfo);
                try{
                    $apiDestImage->addImg($idProduct, $file, $mime_type);   
                }catch(Exception $e){
                    echo $e;
                }
            }
        }
    }

    public function checkMissing() {
        $recordArrSource = $this->enlDbPsSource->execSql("SELECT * FROM " . $this->prefix . "product");
        $recordArrDest = $this->enlDbPsDest->execSql("SELECT * FROM " . $this->prefix . "product");
    
        $sourceIds = array_column($recordArrSource, 'id_product');
        $destIds = array_column($recordArrDest, 'id_product');
    
        $missingIds = array_diff($sourceIds, $destIds);
    
        if (!empty($missingIds)) {
            echo "Brakujące rekordy (id) w docelowej bazie danych: " . implode(', ', $missingIds);
        } else {
            echo "Brakujące rekordy nie zostały znalezione. Wszystkie rekordy są zsynchronizowane.";
        }
        return $missingIds;
    }
}

function localTest10(){
    $idArr = [21258, 21257, 21256, 	21255, 21254, 21253, 21252, 21251, 21250, 21249];
    $psProductMigrator = new PsProductMigratorService();
    $psProductMigrator->delete($idArr);
    $psProductMigrator->checkMissing();
    $psProductMigrator->migrate($idArr);
    $psProductMigrator->checkMissing();
}


function localTest100(){
    $psProductMigrator = new PsProductMigratorService();

    
    $psProductMigrator->enlDbPsSource->conn();

    $recordArrSource = $psProductMigrator->enlDbPsSource->execSql(
        "SELECT id_product 
        FROM " . $psProductMigrator->prefix . "product 
        ORDER BY id_product DESC 
        LIMIT 100"
    );

    $psProductMigrator->enlDbPsSource->conn->close();

    $idArr = array_column($recordArrSource, 'id_product');
    //$idArr = [, , , , ];
    //var_dump($idArr);

    //die();

    $psProductMigrator->delete($idArr);
    $psProductMigrator->checkMissing();
    //die();
    $psProductMigrator->migrate($idArr);
    $psProductMigrator->checkMissing();
}

//localTest10();
localTest100();
//$psProductMigrator = new PsProductMigratorService();
//$psProductMigrator->enlDbPsSource->conn();

//public function __construct($host, $username, $password, $database, $port){


