<? $reqry = Lib_Helper::reQuery('shelf_id'); ?> 

		<div class="box span2">
			<div class="box-header well" data-original-title="">
				 <div><i class="icon-tasks"></i> Sub Menus</div> 
			</div>
			<div class="box-content"> 
                	<ul style="padding:5px" >
                		<li><a href="<?= Url::site('document/shelf') ?>">Shelves</a></li>
                		<li><a href="<?= Url::site('document/undocumented'.$reqry ) ?>">Undocumented</a></li>
                		<li><a href="<?= Url::site('document/bulkupload'.$reqry ) ?>">Bulk Upload</a></li>
                	</ul>  
			</div>
		</div>
<style>
.ui-menu{
	border:none !important;
}
</style>
<script>
	$(function(){
		$('#submenus ul').menu()
	})
</script>