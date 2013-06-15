<?php

class Rest_Undocumented extends Lib_Rest{

	public $model='undocumented';

	function getId($row){
		return $row->document_id;
	}

	function getCell($row){

		$array[] = '<a>a</div>';
		$array[] = date('F d, Y h:ia, l',strtotime($row->dateuploaded));
		$array[] = Lib_Document::removePrefix($row->filename);
		return $array;

	}

	function updateField(&$orm,$POST){

	}

	function addField(&$orm,$POST){
		$this->updateField($orm,$POST);
	}


	public function setFilter(&$orm){

	}

}

?>