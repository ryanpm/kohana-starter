<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" />
	<title>Login</title>

	<link rel="stylesheet" href="${baseurl}css/main.css"/>
	<link rel="stylesheet" href="${baseurl}css/ui-lightness/jquery-ui-1.10.2.custom.min.css"/>

	<script src="${baseurl}js/jquery.js"></script>
	<script src="${baseurl}js/jquery-ui-1.10.2.custom.min.js"></script>
	<script src="${baseurl}js/jquery.browser.min.js"></script>
	<script src="${baseurl}js/main.js"></script>

	<script>
		var baseurl = '${baseurl}';
		var siteurl = '${siteurl}';
	</script>

	<link rel="shortcut icon" href="${baseurl}img/favicon.ico"/>

 	<script>

		$(function(){

			$('#form_login').submit(function(){

                var username = $('#username').val();
                var password = $('#password').val();
				if( username == '' || password == '' ){
					$('#error span').html('Please enter username and password');
					$('#error').show();
					return false;
				}


				$.ajax({
					url:'<?= URL::site('login/auth') ?>',
                    type:'post',
					data:$('#form_login').serialize(),
					success:function(response){

                        json=convertToJson(response);
                        console.log(json);
						$('#error span').html(json.message);
						$('#error').show();
                        if(json.success ){
                            window.location.href = json.redirect;
                        }

					}
				})
				return false;
			})

		})

	</script>

</head>

<body id="body">
 <div class="container-fluid">


		<div class="row-fluid">
			<div class="span12 center login-header">
				<h2>Document Keeper<!-- header --></h2>
			</div><!--/span-->
		</div><!--/row-->


			<div class="row-fluid">
				<div class="well span5 center login-box">
					<div class="alert alert-info">
						Please login with your Username and Password.
					</div>
					<form id="form_login" class="form-horizontal"  action="<?= URL::site('login/auth') ?>?reload" method="post">

						<div id="error" class="alert alert-error" style="display:none">
						 	<button type="button" class="close" data-dismiss="alert">x</button>
							<span></span>
						</div>

						<fieldset>
							<div class="input-prepend" data-rel="tooltip" data-original-title="Username">
								<span class="add-on"><i class="icon-user"></i></span><input autofocus="" class="input-large span10" name="username" id="username" type="text" value=""/>
							</div>
							<div class="clearfix"></div>

							<div class="input-prepend" data-rel="tooltip" data-original-title="Password">
								<span class="add-on"><i class="icon-lock"></i></span><input class="input-large span10" name="password" id="password" type="password" value=""/>
							</div>
							<div class="clearfix"></div>

							<div class="input-prepend">


							<label class="remember" for="remember"><input type="checkbox" id="remember"  tal:attributes="checked remembered" />	Remember me</label>
							</div>
							<div class="clearfix"></div>

							<p class="center span5">
							<button type="submit" class="btn btn-primary" data-loading-text="Loading...">Login</button>
							</p>
						</fieldset>
					</form>
				</div><!--/span-->
			</div>

	</div>

<?= View::factory('common/footer') ?>

</body>
</html>