<?php

if(count(get_included_files()) ==1) exit("Direct access not permitted.");

/**
 *
 * Class for sequence Generator.
 *
 */
 
 class sequence {
 	
 	private $currentNumber;
 	private $sql; 
 	private $pk = 1;
 	
 	function __construct () {
 		$this->sql = new sqlControl();
 		$this->currentNumber = $this->CallDatabaseNumber();
 	}
 	
 	function __destruct () {
 		$this->sql = '';
 	}	
 	
 	public function GetNextNumbers($count){
 		//Get the numbers from the DB, return to field. 
 		$return = array();
 		for($i = 1; $i <= $count; $i++){
 			$r = $i + $this->currentNumber;
			$return[] = str_pad($r, 7, '0', STR_PAD_LEFT);
 		}
 		
 		$this->UpdateNumber($count);
 		return $return;
 	}
 	
 	private function CallDatabaseNumber(){
 		$this->sql->sqlCommand('SELECT Number FROM count WHERE PK = :PK', array(':PK' => $this->pk));
 		$return = $this->sql->returnResults();
 		return $return['Number'];
 	}
 	
 	private function UpdateNumber( $count ){
 		$this->currentNumber += $count;
 		$this->sql->sqlCommand('UPDATE count SET Number = :I WHERE PK = :PK', array(':I' => $this->currentNumber, ':PK' => $this->pk));
 	}
 	
 	public function UndoLastAdd( $numero ){ 
 		$this->currentNumber -= $numero;
 		$this->sql->sqlCommand('UPDATE count SET Number = :I WHERE PK = :PK', array(':I' => $this->currentNumber, ':PK' => $this->pk));
 	}
 	
 	public function OutputCurrentNumber(){
 		return $this->currentNumber;
 	}
 
 }