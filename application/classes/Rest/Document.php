<?php

class Rest_Document extends Lib_Rest{

	public $model='document';

	function getId($row){
		return $row->document_id;
	}

	function getCell($row){

		$GET = Request::current()->query();

		$array[] = $row->document_id;
		$array[] = $row->shelf_id;

		$dval = ORM::factory('documentvalues')->where('document_id','=',$row->document_id)->find_all();
		foreach($dval as $f){
			$l[$f->field_id] = $f->varchar_value;
		}

		$field_order = Model_Field::getOrderedFieldName($row->shelf_id);
		foreach($field_order as $order_id => $field_id){
			if(isset($l[$field_id])){
				$array[] = $l[$field_id];
			}else{
				$array[] = '';
			}

		}

		return $array;

	}

	function updateField(&$orm,$POST){


		foreach( $POST['fields'] as $field_id => $val ){

		 	$dval = ORM::factory('documentvalues')->
		 			where('document_id','=',$POST['document_id'])->
		 			where('field_id','=',$field_id)->find();

			$dval->document_id = $POST['document_id'];
			$dval->field_id = $field_id;
			$dval->varchar_value = $val;
			$dval->save();

		}

	//	$orm->document_id =
	//	$orm->save();


		$values = ORM::factory('DocumentValues');

	}

	function addField(&$orm,$POST){
		$orm->shelf_id 	= $POST['shelf_id'];
		$this->updateField($orm,$POST);
	}

	public function setFilter(&$orm){

	}

}

?>