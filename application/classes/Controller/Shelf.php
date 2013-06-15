<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Shelf extends Lib_Controller{

	function action_index(){
		$this->action_list();
	}

	function action_list(){

		$this->add_navigation_path(array('Manage Shelves','shelf/'));

		$grid_id = 'shelf_list';
		$data['grid_id'] = $grid_id;

		$jqgrid 	= new Lib_JqGrid($grid_id);
		$jqgrid->url 		= Url::site('rest/shelf/get');
		$jqgrid->editurl = Url::site('rest/shelf/update');

		$jqgrid->sortname = 'shelf_id';
		$jqgrid->sortorder = 'desc';

		$jqgrid->setColNames(array('ID','NAME'));

		$col = $jqgrid->addColModel();
			$col->setName('shelf_id');
			$col->width = 45;
			$col->align = 'center';

		$col= $jqgrid->addColModel();
			$col->setName('shelf_name');
			$col->editable = true;
			$col->width = 200; 
        
		$grid['caption'] = 'Manage Shelves';
		$grid['grid_id'] = $grid_id;
		$grid['jqGrid'] = json_encode($jqgrid); 
		$grid['navGrid'] = json_encode($jqgrid->getJqGridNavGrid()->all());
 
		$data['grid'] = View::factory('common/list',$grid);

		$this->view('./list',$data);

	}



}

?>