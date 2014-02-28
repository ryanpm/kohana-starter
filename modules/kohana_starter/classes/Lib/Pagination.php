<?php defined('SYSPATH') or die('No direct script access.');

class Lib_Pagination{

	public $totalrows;
	public $rowsperpage;
	public $currentpage;
	public $totapages;
	public $view;

	public $data;

	function __construct($totalrows, $currentpage,  $rowsperpage = 10 ){
		$this->totalrows 	= $totalrows;
		$this->currentpage 	= ( (int)$currentpage === 0 )?1:(int)$currentpage;
		$this->rowsperpage 	= $rowsperpage;
		$this->totalpages 	= 1;
		if( (int)$this->rowsperpage > 0 ){
			$this->totalpages 	= ceil($this->totalrows/$this->rowsperpage);
		}
		$this->view = '_common/pagination_nav';
	}

	function start(){
		return $this->rowsperpage*($this->currentpage-1);
	}

	function offset(){
		return $this->rowsperpage;
	}

	function limit(){
		return $this->start().','.$this->rowsperpage;
	}

	function isNext(){
		return   $this->totalpages > 1 and   $this->totalpages !=  $this->currentpage;
	}

	function isPrev(){
		return  $this->totalpages > 1 and  $this->currentpage > 1;
	}

	function __toString(){
		$data['current'] = $this->currentpage;
		$data['totalpages'] = $this->totalpages;
		$data['page'] = $this;

		$data['append_data'] = '';
		if( isset($this->data) ){
			$parts = array();
			foreach ($this->data as $key => $value) {
				if( $key == 'page' )continue;
				$parts[] = $key .'='.$value;
			}
			if( count($parts)>0 ){
				$data['append_data'] = '&'. implode('&',  $parts );
			}
		}

		$render = Lib_View::factory($this->view,$data)->render();
		return $render;
	}

}