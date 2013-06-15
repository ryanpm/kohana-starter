<?php

class Rest_shelf extends Lib_Rest{

	public $model='shelf';

	function getId($row){
		return $row->shelf_id;
	}

	function getCell($row){
		$cells_id = (int) Request::current()->query('cells_id');
		$cells[0] = array($row->shelf_id,ucfirst($row->shelf_name));
		$cells[1] = array(ucfirst($row->shelf_name));
		return $cells[$cells_id];
	}


	function updateField(&$orm,$POST){
		$orm->shelf_name = $POST['shelf_name'];
	}

	function addField(&$orm,$POST){

		$this->updateField($orm,$POST);

		//$db =
		//CREATE TABLE example (
        // id INT,
        // data VARCHAR(100)
       	//)

	}

}

?>