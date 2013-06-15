<?= VIew::factory('common/plugin/jqgrid') ?>

<style>

	.ui-jqgrid-view tbody td{
		padding:10px !important;
		cursor:pointer;
	}

	.ui-jqgrid-view thead th{
		padding-left:10px !important;
		padding-right:10px !important;
		font-size:14px;
	}

	.ui-jqgrid-view tbody .jqgfirstrow td{
		padding-top:0px !important;
		padding-bottom:0px !important;
	}

	.jqui-control{
		display:inline-block;
		padding:8px !important;
	}

</style>

<script type="text/javascript">

/*<![CDATA[*/

var grid_${grid_id};
var  navGrid = ${navGrid};
var  jqGrid = ${jqGrid};


$(function(){

	jqGrid = $.extend({},jqGrid,{
		onclickSubmit:function(){ return false }
	})

	grid_${grid_id} = $("#list_${grid_id}").jqGrid(jqGrid); 



	grid_${grid_id}[0].p.ondblClickRow 	= ondblClickRow_${grid_id};
	grid_${grid_id}[0].p.gridComplete 	= gridComplete_${grid_id};
	
 
	grid_${grid_id}.jqGrid(
		'navGrid','#pglist_${grid_id}',
		navGrid , //parameter
		jqGridFormProperties_${grid_id}, //edit settings
		jqGridFormProperties_${grid_id},  // add settings
		{multipleSearch : true}, // enable the advanced searching
		{closeOnEscape:true} /* allow the view dialog to be closed when user press ESC key*/
	);

	grid_${grid_id}.jqGrid('setFrozenColumns');
    $("#list_${grid_id}").fluidGrid({parent:'#list_container_${grid_id}',offset:-27})
    


});

// we can add post data
var getEditData = { 
	//group1:2,
	//group2:function(){ return 1 }
} 

var jqGridFormProperties_${grid_id}  =  { 
        modal:true,
        top:10,
	    width:500, 
		addCaption: "Add Record",
		editCaption: "Edit Record",
		bSubmit: "Submit",
		bCancel: "Cancel",
		bClose: "Close",
		saveData: "Data has been changed! Save changes?",
		bYes : "Yes",
		bNo : "No",
		bExit : "Cancel",
        beforeShowForm: defaultbeforeShowForm_${grid_id}, 
        closeAfterEdit :true,
        closeAfterAdd :true ,
        closeOnEscape:true,
        editData:getEditData,
        beforeSubmit:beforeSubmit_${grid_id},
        beforeCheckValues:beforeCheckValues_${grid_id},
        recreateForm:true

} 


function defaultbeforeShowForm_${grid_id}(form){
        $(form).height(350);
        
	   if( beforeShowForm_${grid_id} != null ){
	   		beforeShowForm_${grid_id}(form);
	   }
       $('.ui-jqdialog').center();
	   return true;
}

function gridComplete_${grid_id}(){
	addDefaultControls_${grid_id}();
}

function row_edit_${grid_id}(id){
	grid_${grid_id}.jqGrid('editGridRow', id, jqGridFormProperties_${grid_id} );
}

function row_delete_${grid_id}(id){
	grid_${grid_id}.jqGrid('delGridRow', id);
}

function addDefaultControls_${grid_id}(){

	var control_obj =$('.ui-jqgrid [aria-describedby=list_${grid_id}_controls]');

	if( navGrid.edit ){
		jqgridInsertControl('<button class="btn btn-mini btn-primary" title="Edit" onclick="row_edit_${grid_id}(jqgridGetId(this))"><i class="icon-edit icon-white"></i></button>',control_obj); 
	}

	if( navGrid.del ){
		jqgridInsertControl('<button class="btn btn-mini btn-primary" title="Delete" onclick="row_delete_${grid_id}(jqgridGetId(this))"><i class="icon-trash icon-white"></i></button>',control_obj);  
	}

	addControl_${grid_id}(control_obj);
 

}


function ondblClickRow_${grid_id}(rowid,iRow,iCol, e){
	row_edit_${grid_id}(rowid);
	// override
}

function beforeShowForm_${grid_id}(form){
	// override
}

function addControl_${grid_id}(control_obj){
	// override
}
function beforeSubmit_${grid_id}(postdata, formid){ 
	return[true,"_message__"];
}
 
 /**
	mode - add/edit
 */
function beforeCheckValues_${grid_id}(posdata ,formid , mode ){
	return $(formid).serializeObject();
}
/*]]>*/
</script>

<div class="box span12" style="margin-left: 0px;">
	<div class="box-header well" data-original-title="">
		<h2><i class="icon-th-list"></i> <span tal:condition="exists:caption" >${caption}</span></h2>
	</div>
	<div class="box-content" id="list_container_${grid_id}" >  
        <table id="list_${grid_id}"></table>
        <div id="pglist_${grid_id}"></div>  
	</div>
</div> 