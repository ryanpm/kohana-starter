<?php

class Rest_User extends Lib_Rest{

	public $model='user';

	function getId($row){
		return $row->user_id;
	}
	function getCell($row){
		return array($row->user_id,$row->firstname,$row->lastname,$row->username);
	}

	function updateField(&$orm,$POST){
		$orm->firstname = $POST['firstname'];
	}

	function addField(&$orm,$POST){
		$this->updateField($orm,$POST);
	}

}

?>