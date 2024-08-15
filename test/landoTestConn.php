<?php

require_once(__DIR__ . "/../db/EnlDb.php");

function landoTestConn(){
    try{
        $enlDb = new EnlDb("database", "ultrasshop", "prestashop1234!", "ultrasshop", "3306");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }


    try{
        $enlDb = new EnlDb("localhost", "ultrasshop", "prestashop1234!", "ultrasshop", "3387");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }
    
    echo "</br></br>";

    try{
        $enlDb = new EnlDb("127.0.0.1", "ultrasshop", "prestashop1234!", "ultrasshop", "3387");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }

    echo "</br>";

    try{
        $enlDb = new EnlDb("127.0.0.1", "ultrasshop", "prestashop1234!", "ultrasshop", "3391");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }

    echo "</br></br>";


    //docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' bee64a58e7f5

    try{
        $enlDb = new EnlDb("172.28.0.3", "ultrasshop", "prestashop1234!", "ultrasshop", "3387");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }

    echo "</br>";

    try{
        $enlDb = new EnlDb("172.28.0.3", "ultrasshop", "prestashop1234!", "ultrasshop", "3391");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }

    echo "</br>";

    try{
        $enlDb = new EnlDb("172.19.0.4", "ultrasshop", "prestashop1234!", "ultrasshop", "3387");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }

    echo "</br>";

    try{
        $enlDb = new EnlDb("172.19.0.4", "ultrasshop", "prestashop1234!", "ultrasshop", "3391");
        $enlDb->conn();
    }catch(Exception $e){
        echo $e;
    }

    echo "</br></br>";

    try {
        $enlDb = new EnlDb("172.19.0.4", "ultrasshop", "prestashop1234!", "ultrasshop", "3306");
        $enlDb->conn();
    } catch (Exception $e) {
        echo $e;
    }

    echo "</br>";

    try {
        $enlDb = new EnlDb("database", "ultrasshop", "prestashop1234!", "ultrasshop", "3387");
        $enlDb->conn();
    } catch (Exception $e) {
        echo $e;
    }
}

landoTestConn();