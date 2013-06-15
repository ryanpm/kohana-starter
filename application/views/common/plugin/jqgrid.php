<link rel="stylesheet" href="${baseurl}plugin/jqgrid/css/ui.jqgrid.css"/>
<script src="${baseurl}plugin/jqgrid/js/i18n/grid.locale-en.js"></script>
<script src="${baseurl}plugin/jqgrid/js/jquery.jqGrid.min.js"></script>
<script src="${baseurl}plugin/jqgrid/src/jqModal.js"></script> 
<style>

.ui-pager-control input{
	width:20px;
	text-align:center;
}
.ui-pager-control select{
	width:55px;
	height:25px !important;
}
.ui-jqgrid .ui-jqgrid-pager,.ui-jqgrid .ui-pager-control{
	height: 40px !important;
}

.ui-jqgrid-hdiv,.ui-jqgrid-hbox,.ui-jqgrid-htable thead tr{
    height: 40px !important; 
}
.ui-jqgrid-htable thead div{
    font-weight: bold !important;
} 


</style>
<script>

/*<![CDATA[*/
/*
  Options:
    base - a valid jQuery selector for the DOM element the grid will use to get a new base size
    offset - the number of pixels added to the base size, giving the grid its new final size 

  Usage:
  
  // make #theGrid 10 pixles smaller than #grid_wrapper
  $("#theGrid").fluidGrid({ parent:"#grid_wrapper", offset:-10 });
  
*/
jQuery.jgrid.fluid =
{
  fluidGrid: function(options)
  {
    var grid = $(this);
    var settings = $.extend(
                      {
                        base: grid.parent(),
                        offset: 0
                      }, options || {});
 
    
    
    function getWidth(){
        var w = $(settings.parent).innerWidth() + settings.offset;
        grid.setGridWidth(w); 
    }
    getWidth();
    $(window).resize(function(){
        getWidth()
    })

  }
} 

$.fn.extend({ fluidGrid : jQuery.jgrid.fluid.fluidGrid });

function jqgridInsertControl(control,control_obj){

	var _controls=control_obj.html();
	console.log(_controls);
	if( $.trim(_controls) != '' ){
		_controls += '&nbsp;&nbsp;';
	}
	_controls += control;

	_controls = _controls.replace(/^(&nbsp;)+/, '');
	_controls = _controls.replace(/(&nbsp;)+$/, '');

	control_obj.html($.trim(_controls));
}

function jqgridGetId(_this){
	return  $(_this).parent().parent().attr('id');
}

/*]]>*/
</script>