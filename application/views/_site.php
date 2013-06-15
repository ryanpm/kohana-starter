<!DOCTYPE HTML>
<html>
<head>
	<title>${title}</title>

	<link rel="stylesheet" href="${baseurl}css/main.css"/>
	<link rel="stylesheet" href="${baseurl}css/smoothness/jquery-ui-1.10.2.custom.min.css"/>

	<script src="${baseurl}js/jquery.js"></script>
	<script src="${baseurl}js/jquery-ui-1.10.2.custom.min.js"></script>
	<script src="${baseurl}/js/jquery.browser.min.js"></script>

	<script>
		var baseurl = '${baseurl}';
		var siteurl = '${siteurl}';
	</script>

	<link rel="shortcut icon" href="${baseurl}img/favicon.ico"/>

	<script src="${baseurl}/js/main.js"></script>


</head>

<body id="body">


<?= View::factory('common/header') ?>
 <div id="content-wrapper" class="container-fluid ">

    <div id="navigation_path" class="breadcrumb">
    	<a tal:repeat="path navigation_path" tal:omit-tag="php:!isset(path[1])" href="<?= Url::site() ?>${path/1}">${path/0}</a>
    </div>

     <div  class="row-fluid">

    	<div id="submenus" tal:condition="exists:submenus" tal:content="structure submenus" ></div>
        <div id="content"  class="span10" tal:content="structure body" ></div>

    </div>

</div>

<?= View::factory('common/footer') ?>


</body>
</html>