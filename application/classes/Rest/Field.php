<?php

class Rest_Field extends Lib_Rest{

	public $model='field';

	function getId($row){
		return $row->field_id;
	}

	function getCell($row){
		$is_required = 'No';
		if($row->is_required==1){
			$is_required = 'Yes';
		}
		return array(
					$row->shelf_id,
					ucfirst($row->field_name),
					$row->default_value,
					Lib_DataType::getTypeName($row->datatype),
					$row->enum_values,
					$is_required,
				);
	}

	function updateField(&$orm,$POST){
		$orm->field_name 		= $POST['field_name'];
		$orm->default_value 	= $POST['default_value'];
		$orm->datatype 			= $POST['datatype'];
		if( $POST['datatype'] == Lib_DataType::ENUM ){
			$orm->enum_values 	= $POST['enum_values']; 
		}else{
			$orm->enum_values 	= ''; 
		} 
		$orm->is_required 		= (strtolower($POST['is_required'])=='yes' or strtolower($POST['is_required'])=='on')?1:0;

	}

	function addField(&$orm,$POST){
		$orm->shelf_id 		= $POST['shelf_id'];
		$orm->order_position 	= $this->getTotal();
		$this->updateField($orm,$POST);
	}

	public function setFilter(&$orm){
		$POST = Request::current()->query('shelf_id');
		$orm->where('shelf_id','=',$POST['shelf_id']);
	} 

}

?>