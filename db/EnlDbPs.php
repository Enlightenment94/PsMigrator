<?php

require_once( __DIR__ . '/EnlDb.php');

class EnlDbPs extends EnlDb{
	public $perfix;

	public function __construct($prefix, $host, $username, $password, $database, $port){
		parent::__construct($host, $username, $password, $database, $port);
		$this->perfix = $prefix;
	}

	public function arrToStr($arr, $key){
		$str = "";
		foreach ($arr as $el) {
			$str .= $el[$key] . ", ";
		}
		$str = rtrim($str, ", ");
		return $str;
	}



	//PRODUCT
	public function getProductName($id_lang, $id_product){
		return $this->execSql("SELECT name FROM " . $this->perfix . "product_lang WHERE id_product=" . $id_product . " AND id_lang = " . $id_lang );
	}

	public function updateProductName($id_lang, $id_product, $name){
		$query = "UPDATE " . $this->perfix . "product_lang SET name = :name WHERE id_lang = :id_lang AND id_product = :id_product";
	    
		$values = array(
	        ':name' => $name,
	        ':id_lang' => $id_lang,
	        ':id_product' => $id_product
	    );

	    $this->pdoExecuteParam(array('query' => $query, 'values' => $values));
	}

	public function getProductFeatures($id_lang, $id_product){
		return $this->execSql("SELECT fvl.value FROM " . $this->perfix . "feature_value_lang fvl, " . $this->perfix . "feature_product fp WHERE fvl.id_feature_value = fp.id_feature_value AND id_product=" . $id_product . " AND id_lang = " . $id_lang );
	}

	public function checkCategoryProduct($id_product, $categoryName){
		return $this->execSql("SELECT * FROM " . $this->perfix . "category_product pcp,  " . $this->perfix . "category_lang cl WHERE pcp.id_category  = cl.id_category AND cl.name = '" . $categoryName . "' AND pcp.id_product = " . $id_product);
	}
	
	
	///CATEGORY
	public function getCategoryByIdLang($idLang){
		return $this->execSql('SELECT cp.id_category, pl.name, pl.description, pl.meta_description, pl.meta_title
								FROM ' . $this->perfix . 'category_lang pl 
                        		JOIN ' . $this->perfix . "category cp ON pl.id_category = cp.id_category
                        		WHERE pl.id_lang = '" . $idLang . "'");
	}

	public function getIdsCategory(){
		return $this->execSql('SELECT id_category FROM ' . $this->perfix . 'category');
	}

	public function getProductsCategoryByIdCategory($idCategory, $idLang){
		return $this->execSql('SELECT p.id_product, pl.name as product_name
								FROM ' . $this->perfix . 'product p
								JOIN ' . $this->perfix . 'product_lang pl ON p.id_product = pl.id_product
								JOIN ' . $this->perfix . 'category_product cp ON p.id_product = cp.id_product
								WHERE cp.id_category = ' . $idCategory . " AND pl.id_lang = '" . $idLang . "'");
	}

	public function updateCategoryName($id_lang, $id_category, $new_name) {
	    $filtered_name = str_replace("\n", "", $new_name);

	    $query = "UPDATE " . $this->perfix . "category_lang SET name = :filtered_name WHERE id_lang = :id_lang AND id_category = :id_category";

	    $values = array(
	        ':filtered_name' => $filtered_name,
	        ':id_lang' => $id_lang,
	        ':id_category' => $id_category
	    );

	    $this->pdoExecuteParam(array('query' => $query, 'values' => $values));
	}

	public function updateCategoryDesc($id_lang, $id_category, $new_desc) {
	    $query = "UPDATE " . $this->perfix . "category_lang SET description = :new_desc WHERE id_lang = :id_lang AND id_category = :id_category";

	    $values = array(
	        ':new_desc' => $new_desc,
	        ':id_lang' => $id_lang,
	        ':id_category' => $id_category
	    );

	    $this->pdoExecuteParam(array('query' => $query, 'values' => $values));
	}

	public function getCategoryLang($id_lang, $name){
		return $this->execSql("SELECT * FROM " . $this->perfix . "category_product pcp, " . $this->perfix . "product_lang ppl WHERE ppl.id_product = pcp.id_product AND ppl.name ='" . $name . "' AND ppl.id_lang = " . $id_lang);
	}

	public function getCategoryProductsLangByIdLang($id_lang, $name){
		return $this->execSql("SELECT * FROM " . $this->perfix . "category_lang cl, "  . $this->perfix . "category_product pcp, " . $this->perfix . "product_lang ppl WHERE cl.id_category = pcp.id_category AND ppl.id_product = pcp.id_product AND ppl.id_lang = " . $id_lang . " AND cl.name='" . $name . "' GROUP BY (ppl.id_product)");
	}

	public function getCategoryProductsLang($name){
		return $this->execSql("SELECT * FROM " . $this->perfix . "category_lang cl, "  . $this->perfix . "category_product pcp, " . $this->perfix . "product_lang ppl WHERE cl.id_category = pcp.id_category AND ppl.id_product = pcp.id_product AND cl.name='" . $name . "'");
	}

	public function getCategoryProducts($name, $idLang){
		$rec = $this->execSql("SELECT * FROM ". $this->perfix . "category_lang WHERE name='" . $name . "'");
		$rec = $this->getProductsCategoryByIdCategory($rec[0]['id_category'], $idLang);
		return $rec;
	}



	//ATTRIBUTES
	public function getAttributeLang($id_lang){
		return $this->execSql("SELECT * FROM " . $this->perfix . "attribute_lang al, " . $this->perfix . "product_attribute_shop pas, " . $this->perfix . "product_attribute_combination pac 
								WHERE al.id_attribute = pac.id_attribute AND 
								pas.id_product_attribute = pac.id_product_attribute 
								AND al.id_lang = " . $id_lang);
	}

	public function getAttributeLangByIdProduct($id_lang, $id_product){
		return $this->execSql("SELECT * FROM " . $this->perfix . "attribute_lang al, " . $this->perfix . "product_attribute_shop pas, " . $this->perfix . "product_attribute_combination pac 
								WHERE al.id_attribute = pac.id_attribute AND 
								pas.id_product_attribute = pac.id_product_attribute 
								AND al.id_lang = " . $id_lang 
								. " AND pas.id_product = " . $id_product);
	}

	//jeszcze powinnwa byc product_attribute bo w shop może nie mieć kazdy produkt
	public function getAttributeNameArrLangByIdProduct($id_lang, $id_product, $key){
		$arr = $this->execSql("SELECT name FROM " . $this->perfix . "attribute_lang al, " . $this->perfix . "product_attribute_shop pas, " . $this->perfix . "product_attribute_combination pac 
								WHERE al.id_attribute = pac.id_attribute AND 
								pas.id_product_attribute = pac.id_product_attribute 
								AND al.id_lang = " . $id_lang 
								. " AND pas.id_product = " . $id_product);
								
		return $this->arrToStr($arr, $key);
	}

	//jeszcze powinnwa byc product_attribute bo w shop może nie mieć kazdy produkt
	public function getAttributeNameLangByIdProduct($id_lang, $id_product, $name){
		$arr = $this->execSql("SELECT al.id_attribute, id_lang, name FROM " . $this->perfix . "attribute_lang al, " . $this->perfix . "product_attribute_shop pas, " . $this->perfix . "product_attribute_combination pac 
								WHERE al.id_attribute = pac.id_attribute AND 
								pas.id_product_attribute = pac.id_product_attribute " 
								. " AND pas.id_product = " . $id_product . " GROUP By(name)");
		return $arr;
	}

	public function getIdAttributeByName($name){
		return $this->execSql("SELECT id_attribute FROM " . $this->perfix . "attribute_lang WHERE name='" . $name . "'");
	}

	public function getAttributeNameByIdAttribute($idAttribute, $idLang){
		return $this->execSql("SELECT name FROM " . $this->perfix . "attribute_lang WHERE id_lang = '" . $idLang . "' AND id_attribute='" . $idAttribute . "'");
	}

	public function getAttributeById($idAttribute){
		return $this->execSql("SELECT * FROM " . $this->perfix . "attribute_lang al WHERE id_attribute='" . $idAttribute . "'");
	}

	public function setIdAttributeProductAttrComb($idAttribute, $newIdAttribute, $idProductAttribute){
		return $this->execSql("UPDATE " . $this->perfix . "product_attribute_combination SET id_attribute = " . $newIdAttribute . " WHERE id_attribute = '" . $idAttribute . "' AND id_product_attribute = " . $idProductAttribute);
	}



	public function deleteAttributeByName($idLang, $name, $idProduct){
		$idAttr = $this->getIdAttributeByName($name);
		$idAttr = $idAttr[$idLang - 1]['id_attribute'];

		$prodAttr = $this->execSql("SELECT * FROM " . $this->perfix . "product_attribute WHERE id_product='" . $idProduct . "'");
		$prodAttrShop = $this->execSql("SELECT * FROM " . $this->perfix . "product_attribute_shop WHERE id_product='" . $idProduct . "'");

		$idAllAttr = array_merge($prodAttr, $prodAttrShop);

		$in = "(";
		foreach($idAllAttr as $rec){
			$in .= $rec['id_product_attribute'] . ",";
		}
		$in = substr($in, 0, -1);
		$in .= ")";

		if( count($idAllAttr) != 0 ){
			echo "SELECT * FROM " . $this->perfix . "product_attribute_combination WHERE id_attribute = " . $idAttr. " AND id_product_attribute IN " . $in;

			$idAttrArrToRemove = $this->execSql("SELECT id_attribute, id_product_attribute FROM " . $this->perfix . "product_attribute_combination WHERE id_attribute = " . $idAttr. " AND id_product_attribute IN " . $in);

			echo "<pre>";
			var_dump($idAttrArrToRemove);
			echo "</pre>";

			foreach ($idAttrArrToRemove as $id) {
				//$this->execSql("DELETE FROM " . $this->perfix . "product_attribute WHERE id_product_attribute = " . $id['id_product_attribute'] . "");
				//$this->execSql("DELETE FROM " . $this->perfix . "product_attribute_shop WHERE id_product_attribute = " . $id['id_product_attribute'] . "");				
				$this->execSql("DELETE FROM " . $this->perfix . "product_attribute_combination WHERE id_attribute = " . $idAttr. " AND id_product_attribute = " . $id['id_product_attribute'] . "");
			}
		}
		//die();
	}

	public function checkAttributeExist($idLang, $name, $idProduct){
		$idAttr = $this->getIdAttributeByName($name);
		$idAttr = $idAttr[$idLang - 1]['id_attribute'];

		$prodAttr = $this->execSql("SELECT * FROM " . $this->perfix . "product_attribute WHERE id_product='" . $idProduct . "'");
		$prodAttrShop = $this->execSql("SELECT * FROM " . $this->perfix . "product_attribute_shop WHERE id_product='" . $idProduct . "'");

		$idAllAttr = array_merge($prodAttr, $prodAttrShop);

		$in = "(";
		foreach($idAllAttr as $rec){
			$in .= $rec['id_product_attribute'] . ",";
		}
		$in = substr($in, 0, -1);
		$in .= ")";

		if( count($idAllAttr) != 0 ){
			//echo "SELECT * FROM " . $this->perfix . "product_attribute_combination WHERE id_attribute = " . $idAttr. " AND id_product_attribute IN " . $in;

			$idAttrArrToRemove = $this->execSql("SELECT id_attribute, id_product_attribute FROM " . $this->perfix . "product_attribute_combination WHERE id_attribute = " . $idAttr. " AND id_product_attribute IN " . $in);

			if(count($idAttrArrToRemove)){
				//return $idAttrArrToRemove[0]['id_product_attribute'];
				return $idAttrArrToRemove;
			}else{
				return 0;
			}
		}
		return 0;
	}

	public function insertAttrComb($idLang, $idAttribute, $idProductAttribute) {
	    $filtered_name = str_replace("\n", "", $new_name);

	    $query = "INSERT INTO " . $this->perfix . "product_attribute_combination (id_attribute, id_product_attribute) VALUES(:id_attribute, :id_product_attribute)";

	    $values = array(
	        ':id_attribute' => $idAttribute,
	        ':id_product_attribute' => $idProductAttribute,
	    );

	    $this->pdoExecuteParam(array('query' => $query, 'values' => $values));
	}

	//PRICESSS
	public function priceBridgeToUp($enlDbDest){
		$psRec = $this->execSql("SELECT * FROM " . $this->perfix . "product_shop" );

		echo "Product shop</br>\n";
		foreach($psRec as $el){
			$rec = $enlDbDest->execSql("SELECT id_product, price FROM " . $this->perfix . "product_shop WHERE id_product =" . $el['id_product']);
			if( $rec[0]['price'] < $el['price'] ){
				if($el['id_product'] == $rec[0]['id_product']){
					echo $el['id_product'] . " DEST " . $rec[0]['price'] . " < " . "SOURCE:" . $el['price'] . "</br>\n";
					$enlDbDest->execSql("UPDATE " . $this->perfix . "product_shop SET price= " . $el['price'] . " WHERE id_product ='" . $el['id_product'] . "'");
				}
			}
		}

		echo "Product attribute</br>\n";
		$psARec = $this->execSql("SELECT * FROM " . $this->perfix . "product_attribute" );
		foreach($psARec as $el){
			$rec = $enlDbDest->execSql("SELECT id_product, price FROM " . $this->perfix . "product_attribute WHERE id_product ='" . $el['id_product'] . "' AND id_product_attribute= '". $el['id_product_attribute'] ."'"  );			
			if( $rec[0]['price'] < $el['price'] ){
				if( $el['id_product'] == $rec[0]['id_product'] ){
					echo $el['id_product'] . " DEST " . $rec[0]['price'] . " < " . "SOURCE:" . $el['price'] . "</br>\n";
					$enlDbDest->execSql("UPDATE " . $this->perfix . "product_attribute SET price= " . $el['price'] . " WHERE id_product ='" . $el['id_product'] . "' AND id_product_attribute='" . $el['id_product_attribute'] . "'");
				}
			}
		}

		echo "Product attribute shop</br>\n";
		$psASRec = $this->execSql("SELECT * FROM " . $this->perfix . "product_attribute_shop" );
		foreach($psARec as $el){
			$rec = $enlDbDest->execSql("SELECT id_product, price FROM " . $this->perfix . "product_attribute_shop WHERE id_product ='" . $el['id_product'] . "' AND id_product_attribute= '". $el['id_product_attribute'] ."'"  );	

			if( $rec[0]['price'] < $el['price'] ){
				if($el['id_product'] == $rec[0]['id_product']){
					echo $el['id_product'] . " DEST " . $rec[0]['price'] . " < " . "SOURCE:" . $el['price'] . "</br>\n";
					$enlDbDest->execSql("UPDATE " . $this->perfix . "product_attribute_shop SET price= " . $el['price'] . " WHERE id_product ='" . $el['id_product'] . "' AND id_product_attribute='" . $el['id_product_attribute'] . "'");
				}
			}
		}

	}

	//Features
	public function getFeatureFieldByIdProduct($idProduct, $featureName){
		$idFeature = $this->execSql("SELECT * FROM " . $this->perfix . "feature_lang WHERE name='" . $featureName . "'");

		$value = $this->execSql("SELECT * FROM ". $this->perfix ."feature_value_lang pfvl 
						WHERE id_feature_value IN (SELECT id_feature_value 
									FROM ". $this->perfix ."feature_product pfp 
									WHERE id_product = " . $idProduct . " AND id_feature="  . $idFeature[0]['id_feature'] . ");");
		
		return $value;
	}
}
