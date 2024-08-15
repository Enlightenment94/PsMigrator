    <?php

    require_once(__DIR__ . "/../db/EnlDb.php");
    
    try{
        $enlDb = new EnlDb("127.0.0.1", "ultrasshop", "prestashop1234!", "ultrasshop", "3387");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }

    $result = $enlDb->execSql("SHOW TABLES");

    var_dump($result);