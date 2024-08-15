<?php

class PsCategoryMigrator{
	public $source;
	public $dest;
	public $prefix;

	public function __construct($prefix, $sourceDb, $destDb){
		$this->prefix = $prefix;
		$this->source = $sourceDb;
		$this->dest   = $destDb;
		$this->source->conn();
		$this->dest->conn();
	}

	public function countCompareRecords(){
		$sourceRec = $this->source->execSql("SELECT * FROM " . $this->prefix . "category" );
		$destRec = $this->dest->execSql("SELECT * FROM " . $this->prefix . "category" );
		$nSource = count($sourceRec);
		$nDest = count($destRec);
		return  "Source: " . $nSource . " Dest: " . $nDest;
	}

	public function compareId(){
		$sourceRec = $this->source->execSql("SELECT * FROM " . $this->prefix . "category" );
		$destRec = $this->dest->execSql("SELECT * FROM " . $this->prefix . "category" );

		$miss = array();
		$flag = 0;
		foreach($sourceRec as $sr){
			$flag = 0;
			foreach($destRec as $dr){
				if($sr['id_category'] == $sr['id_category']){
					$flag = 1;
					break;
				}
			}

			if($flag == 0){
				array_push($miss, $sr['id_category']);
			}
		}
		
		return count($miss);
	}

}