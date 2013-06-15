<?php

class Lib_JqGrid{

	public $caption='';
	public $url='';
	public $editurl='';
	public $width=1015;
	public $height=300;
	public $datatype= "json";
	public $colNames=array();
	public $colModel=array();
	public $rowNum=10;
	public $rowList=array(10,20,30);
	public $pager= '';
	public $sortname= '';
	public $sortorder= '';
	public $viewrecords= true;
	public $ondblClickRow= '';
	public $gridComplete= '';
	public $toolbar= array(false,"top");
	public $pgbuttons=true;
	public $recordtext= '';
	public $shrinkToFit= false;
	public $forceFit = false;


	private $controlCol;

	function __construct($grid_id=''){

		$this->pager = '#pglist_'.$grid_id;
 		$this->addControlColumn();

	}

	function addControlColumn(){

		$this->controlCol = $this->addColModel();
			$this->controlCol->setName('controls');
			$this->controlCol->width = 80;
			$this->controlCol->align = 'center';
			$this->controlCol->frozen = true;
			$this->controlCol->sortable = false;

	}

	function getControlCol(){
		return $this->controlCol;
	}

	function setColNames($colNames){
		return $this->colNames = array_merge(array(''), $colNames );
	}

	function getJqGridNavGrid(){
		return new JqGridNavGrid();
	}

	function &addColModel(){
		$col = new JqGridColModel();
		$this->colModel[]	=	$col;
		return $col;
	}


	function __toString(){
		return json_encode($this);
	}

}

class JqGridColModel{
	public  $name;
	public  $index;
	public  $editable=false;
	public  $hidden=false;
	public 	$width;
	public 	$height;
	public 	$align = 'left';
	public 	$edittype;
	public 	$editoptions;
	public 	$editrules;
	public 	$frozen=false;

	function &addEditRules(){
		$o =  new JqGridEditRules();
		$this->edittype = $o;
		return $o;
	}

	function &addEditoptions(){
		$o =  new JqGridEditOptions();
		$this->editoptions = $o;
		return $o;
	}

	function  getJqGridEditOptions(){

	}

	function setName($name){
		$this->name = $name;
		$this->index = $name;
	}

}

class JqGridEditRules{
	public $edithidden;
	public $required;
	public $number;
	public $integer;
	public $minValue;
	public $maxValue;
	public $email;
	public $url;
	public $date;
	public $time;
	public $custom;

}

class JqGridEditOptions{
	public $value;
	//public $dataUrl='';
	public $defaultValue;
}

class JqGridNavGrid{

	public $add;
	public $edit;
	public $search;
	public $del;
	public $view;
	public $refresh;

	function __construct(){
		$this->add(false);
		$this->edit(false);
		$this->search(false);
		$this->del(false);
		$this->view(false);
		$this->refresh();
	}

	function all(){
		$this->add();
		$this->edit();
		$this->search();
		$this->del();
		$this->view();
		$this->refresh();
		return $this;
	}

	function add($bool=true){
		$this->add = $bool;
		return $this;
	}
	function edit($bool=true){
		$this->edit = $bool;
		return $this;
	}
	function search($bool=true){
		$this->search = $bool;
		return $this;
	}
	function del($bool=true){
		$this->del = $bool;
		return $this;
	}
	function view($bool=true){
		$this->view = $bool;
		return $this;
	}
	function refresh($bool=true){
		$this->refresh = $bool;
		return $this;
	}

	function __toString(){
		return json_encode($this);
	}

}


