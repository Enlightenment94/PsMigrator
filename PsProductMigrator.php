<?php

define("STR_PS_PREFIX", "psov_");

class PsProductMigrator{
	public $source;
	public $dest;

	public function __construct($sourceDb, $destDb){
		$this->source = $sourceDb;
		$this->dest   = $destDb;
		$this->source->conn();
		$this->dest->conn();
	}

	public function exportBaseProductInfo($idProduct){
		$product          = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "product WHERE id_product = " . $idProduct);
		$productShop      = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "product_shop WHERE id_product = " . $idProduct);
		$productLang      = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "product_lang WHERE id_product = " . $idProduct);
		$productAttribute = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "product_attribute WHERE id_product = " . $idProduct);
		$stockAvailable   = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "stock_available WHERE id_product = " . $idProduct);
			
		$sourceCurrency = $this->source->execSql("SELECT iso_code, conversion_rate FROM " . STR_PS_PREFIX . "currency WHERE conversion_rate = 1");
		$destCurrency = $this->dest->execSql("SELECT iso_code, conversion_rate FROM " . STR_PS_PREFIX . "currency WHERE conversion_rate = 1");

		$ratioSource = array('conversion_rate' => 1);

		//Correct
		ob_start();
		foreach($product as $p){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product", $p);
			echo "<pre>";
			echo "proudct\n";
			var_dump($data);
			echo "</pre>";

			if($data["values"][40]  == "0000-00-00"){
				echo $data["values"][40] . "\n";
				$data["values"][40] = null;
			}

			$data["values"][55] = "standard";
			$this->dest->pdoExecuteParam($data);
		}

		//Correct
		foreach($productShop as $ps){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_shop", $ps);
			echo "<pre>";
			echo "proudct_shop\n";
			var_dump($data);
			echo "</pre>";

			if($data["values"][23]  == "0000-00-00"){
				echo $data["values"][23] . "\n";
				$data["values"][23] = null;
			}

			$this->dest->pdoExecuteParam($data);
		}

		//Correct
		foreach($productLang as $p){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_lang", $p);
			echo "<pre>";
			var_dump($ps);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		//Correct
		foreach($productAttribute as $p){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_attribute", $p);
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		$pacArr = $this->source->execSql("SELECT * FROM `" . STR_PS_PREFIX . "product_attribute_combination` 
			WHERE id_product_attribute 
 			IN (SELECT id_product_attribute FROM `".STR_PS_PREFIX ."product_attribute` WHERE id_product = '" . $idProduct . "')");
	

		foreach ($pacArr as $pac) {
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_attribute_combination", $pac);
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		//???? czy to też
		$productAttributeLang = $this->source->execSql("SELECT * FROM `" . STR_PS_PREFIX . "product_attribute_lang` WHERE id_product_attribute 
			IN (SELECT id_product_attribute FROM `" . STR_PS_PREFIX ."product_attribute` WHERE id_product = '" . $idProduct . "')");
		 
		foreach($productAttributeLang as $pal){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_attribute_lang", $pal);
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		$productAttributeShop = $this->source->execSql("SELECT * FROM `" . STR_PS_PREFIX . "product_attribute_shop` WHERE id_product='" . $idProduct . "'"); 

		if($sourceCurrency[0]['iso_code'] != $destCurrency[0]['iso_code']){
			$i = 0;
			$n = count($productAttributeShop);
			for ($i = 0; $i < $n ; $i++) {
				if($productAttributeShop[$i]['price'] > 0 ){
					$productAttributeShop[$i]['price'] = "" . ($productAttributeShop[$i]['price'] * $ratioSource[0]['conversion_rate']);
					echo $productAttributeShop[$i]['price'];
				}
			}
		}
		 
		foreach($productAttributeShop as $pal){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_attribute_shop", $pal);
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		$productAttributeImage = $this->source->execSql("SELECT * FROM `" . STR_PS_PREFIX . "product_attribute_image` WHERE id_product_attribute 
			IN (SELECT id_product_attribute FROM `" . STR_PS_PREFIX . "product_attribute` WHERE id_product = '" . $idProduct . "')");

		//Correct
		foreach($productAttributeImage as $pai){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_attribute_image", $pai);
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		//Correct
		foreach($stockAvailable as $p){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "stock_available", $p);
			$this->dest->pdoExecuteParam($data);
		}

		$categoryProduct = $this->source->execSql("SELECT * FROM `" . STR_PS_PREFIX ."category_product` WHERE id_product=" . $idProduct);
		foreach ($categoryProduct as $cp) {
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "category_product", $cp);
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		$productTag = $this->source->execSql("SELECT * FROM `". STR_PS_PREFIX . "product_tag` WHERE id_product=" . $idProduct);
		foreach ($productTag as $pt) {
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "product_tag", $pt);
			echo "<pre>";
			var_dump($data);
			echo "</pre>";
			$this->dest->pdoExecuteParam($data);
		}

		$buff = ob_get_contents();
		ob_end_clean();

		return $buff;
	}

	public function exportProductFeatures($idProduct){
		$feature = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "feature_product WHERE id_product = " . $idProduct);

		foreach($feature as $f){
			$data = $this->dest->insertMakerPDO( STR_PS_PREFIX . "feature_product", $f);
			$this->dest->pdoExecuteParam($data);
		}

		$featureSource = $this->source->execSql("SELECT id_feature_value FROM " . STR_PS_PREFIX . "feature_value_lang pfvl 
											WHERE id_feature_value IN (SELECT id_feature_value 
								   			FROM " . STR_PS_PREFIX . "feature_product pfp 
								   			WHERE id_product = " . $idProduct . ");");

											
		$featureDest = $this->dest->execSql("SELECT id_feature_value FROM " . STR_PS_PREFIX . "feature_value_lang pfvl 
											   WHERE id_feature_value IN (SELECT id_feature_value 
												  FROM " . STR_PS_PREFIX . "feature_product pfp 
												  WHERE id_product = " . $idProduct . ");");

		echo "<pre>";
		var_dump($featureSource);
		echo "</pre>";

		$i = 0;
		$n = count($featureSource);
		$delIndexArr = array();
		for ($i = 0; $i < $n; $i++) {
			foreach ( $featureDest as $d ) {
				if( $featureSource[$i]['id_feature_value'] == $d['id_feature_value'] ){
					//echo $featureSource[$i]['id_feature_value'] . "</br>";
					array_push($delIndexArr, $i);
				} 
			}				
		}

		foreach ($delIndexArr as $d) {
			unset($featureSource[$d]);
		}

		//echo "str-feature";
		//echo "<pre>";
		//var_dump($featureSource);
		//echo "</pre>";

		foreach ($featureSource as $f) {
			$data = $this->dest->insertMakerPDO( STR_PS_PREFIX . "feature_value_lang", $f);
		}
	}

	public function exportImageDb($idProduct){
		$psImage = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "image WHERE id_product = " . $idProduct);
		$psImageShop = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "image_shop WHERE id_product = " . $idProduct);
		$psImageLang = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "image_lang WHERE id_image IN (SELECT id_image FROM " 
		. STR_PS_PREFIX . "image WHERE id_product = " . $idProduct . ")");

		foreach($psImage as $i){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "image", $i);
			$this->dest->pdoExecuteParam($data);
		}

		foreach($psImageShop as $is){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "image_shop", $is);
			$this->dest->pdoExecuteParam($data);
		}

		foreach($psImageLang as $il){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "image_lang", $il);
			$this->dest->pdoExecuteParam($data);
		}
	}

	public function exportSpecificPrice($idProduct){
		$specificPricePriority = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "specific_price_priority WHERE id_product = " . $idProduct);
		$specificPrice = $this->source->execSql("SELECT * FROM " . STR_PS_PREFIX . "specific_price WHERE id_product = " . $idProduct);

		foreach($specificPrice as $sp){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "specific_price", $sp);
			$this->dest->pdoExecuteParam($data);
		}

		foreach($specificPricePriority as $spp){
			$data = $this->dest->insertMakerPDO(STR_PS_PREFIX . "specific_price_priority", $spp);
			$this->dest->pdoExecuteParam($data);
		}
	}

	public function checkProductExist($idProduct){
		$query = "SELECT COUNT(*) as count FROM " . STR_PS_PREFIX . "product WHERE id_product = '{$idProduct}'";

		$result = $this->dest->execSql($query);

		if ($result && $result[0]['count'] > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	public function checkProductStatus($idProduct){
		$query = "SELECT active FROM " . STR_PS_PREFIX . "product_shop WHERE id_product = '{$idProduct}'";

		$result = $this->dest->execSql($query);

		if ($result[0]['active'] == '1') {
			return 1;
		} else {
			return 0;
		}
	}

	public function setStatus($idProduct, $state){
		$query = "UPDATE " . STR_PS_PREFIX . "product_shop SET active='" . $state . "' WHERE id_product = '{$idProduct}'";
		$result = $this->dest->execSql($query);
		return $result;
	}

	public function deleteProduct($idProduct){
		$deleteQueries = [
			"DELETE FROM ". STR_PS_PREFIX ."product WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX ."product_shop WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX ."product_lang WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX ."product_attribute WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX ."product_attribute_shop WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX ."stock_available WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX ."feature_product WHERE id_product = '{$idProduct}'",
			"DELETE FROM `". STR_PS_PREFIX ."product_attribute_combination` WHERE id_product_attribute IN (SELECT id_product_attribute FROM `" . STR_PS_PREFIX . "product_attribute` WHERE id_product = '{$idProduct}')",
			"DELETE FROM `". STR_PS_PREFIX ."product_attribute_lang` WHERE id_product_attribute IN (SELECT id_product_attribute FROM `" . STR_PS_PREFIX. "product_attribute` WHERE id_product = '{$idProduct}')",
			"DELETE FROM `". STR_PS_PREFIX . "product_attribute_image` WHERE id_product_attribute IN (SELECT id_product_attribute FROM `" . STR_PS_PREFIX ."product_attribute` WHERE id_product = '{$idProduct}')",
			"DELETE FROM ". STR_PS_PREFIX . "image_lang WHERE id_image IN (SELECT id_image FROM " . STR_PS_PREFIX . "image WHERE id_product = {$idProduct})",
			"DELETE FROM ". STR_PS_PREFIX . "image WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX . "image_shop WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX . "category_product WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX . "product_tag WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX . "specific_price WHERE id_product = '{$idProduct}'",
			"DELETE FROM ". STR_PS_PREFIX . "specific_price_priority WHERE id_product = '{$idProduct}'",
		];
		
		// Wykonanie każdego polecenia DELETE
		foreach ($deleteQueries as $query) {
			$this->dest->execSql($query);
		}
	}

	public function send($idProduct){
		$this->exportBaseProductInfo($idProduct);
		$this->exportProductFeatures($idProduct);
		$this->exportImageDb($idProduct);
		$this->exportSpecificPrice($idProduct);
	}
}