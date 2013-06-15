<?php

class Lib_Rest{

	public $model;
	public $pk_id;

	public function index(){
		$this->get();
	}

	public function get(){

		$GET = Request::current()->query();

		$page = 	$GET['page']; // get the requested page
		$limit = 	$GET['rows']; // get how many rows we want to have into the grid

		$count = $this->getTotal();

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;

		$start = $limit*$page - $limit; // do not put $limit*($page - 1)

		$responce['page'] = $page;
		$responce['total'] = $total_pages;
		$responce['records'] = $count;

		$list = $this->getDataAsArray();

		foreach($list as $row){
			$responce['rows'][] = $this->getRow($row);
		}

		echo json_encode($responce);

	}

	public function update(){
		$POST = Request::current()->post();
		if( $POST['oper'] == 'edit' ){
			$this->edit($POST);
		}elseif( $POST['oper'] == 'add' ){
			$this->add($POST);
		}elseif( $POST['oper'] == 'del' ){
			$this->delete($POST['id']);
		}
	}


	public function getTotal(){

		$orm = ORM::factory($this->model);
		$this->setFilter($orm);
		return ORM::factory($this->model)->count_all();

	}

	private function getDataAsArray(){

		$orm = ORM::factory($this->model);
		$this->setFilter($orm);
		$this->setOrder($orm);
		$row = $orm->find_all();
		return 	$row->as_array();


	}

	private function getRow($row){
		$l['id']	=	$this->getId($row);
		$l['cell']	=	array_merge(array(''),$this->getCell($row) );
		return $l;
	}

	private function edit($POST){
		$orm = ORM::factory($this->model,$POST['id']);
		if( $orm->loaded() ){
			$this->updateField($orm,$POST);
			$orm->save();
		}
	}

	private function add($POST){
		$orm = ORM::factory($this->model);
		$this->addField($orm,$POST);
		if( !$orm->loaded() ){
			$orm->save();
		}

	}

	private function delete($id){
		$orm = ORM::factory($this->model,$id);
		$orm->delete();
	}

	public function setFilter(&$orm){

	}

	public function setOrder(&$orm){
		$GET = Request::current()->query();
		$sidx = 	$GET['sidx']; // get index row - i.e. user click to sort
		$sord = 	$GET['sord']; // get the direction
		if(!$sidx) $sidx =1;
		$orm->order_by($sidx,$sord);
	}

	public function getId($row){
		//return $row->fieldname;
	}

	public function getCell($row){
		//return array($row->fieldname);
	}

	public function updateField(&$orm,$POST){
		//$orm->fieldname = $POST['fieldname'];
	}

	public function addField(&$orm,$POST){
		//$orm->fieldname = $POST['fieldname'];
	}

}

?>