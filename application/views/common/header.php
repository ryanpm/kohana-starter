<div class="navbar">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>

				</a>
				<a class="brand" href="${baseurl}"><span>Document Keeper</span></a>

				<!-- theme selector starts
				<div class="btn-group pull-right theme-container">
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-tint"></i><span class="hidden-phone"> Change Theme / Skin</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu" id="themes">
						<li><a data-value="classic" href="#"><i class="icon-blank"></i> Classic</a></li>
						<li><a data-value="cerulean" href="#"><i class="icon-blank icon-ok"></i> Cerulean</a></li>
						<li><a data-value="cyborg" href="#"><i class="icon-blank"></i> Cyborg</a></li>
						<li><a data-value="redy" href="#"><i class="icon-blank"></i> Redy</a></li>
						<li><a data-value="journal" href="#"><i class="icon-blank"></i> Journal</a></li>
						<li><a data-value="simplex" href="#"><i class="icon-blank"></i> Simplex</a></li>
						<li><a data-value="slate" href="#"><i class="icon-blank"></i> Slate</a></li>
						<li><a data-value="spacelab" href="#"><i class="icon-blank"></i> Spacelab</a></li>
						<li><a data-value="united" href="#"><i class="icon-blank"></i> United</a></li>
					</ul>
				</div>
			 	theme selector ends -->

				<!-- user dropdown starts -->
				<div class="btn-group pull-right">
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-user"></i><span class="hidden-phone"> ${firstname}</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<!--
						<li><a href="#">Profile</a></li>
						<li class="divider"></li> -->

						<li><a href="${siteurl}/login?logout"><span class="icon-off"></span> Logout</a></li>
					</ul>
				</div>
				<!-- user dropdown ends -->

				<div class="top-nav nav-collapse">
					<ul id="menus"  class="nav">
						<!-- put list -->
					</ul>
				</div><!--/.nav-collapse -->
			</div>
			<div id="menu_container">
				<ul id="menus">
					<li><a href="<?= URL::site('home/') ?>">Home</a></li>
					<li><a href="<?= URL::site('search/') ?>">Search</a></li>
					<li><a href="<?= URL::site('document/shelf') ?>">Documents</a></li>
					<li><a href="<?= URL::site('shelf/') ?>">Manage Shelves</a></li>
					<li><a href="<?= URL::site('user/') ?>">Users</a></li>
					<li><a href="<?= URL::site('setting/') ?>">Settings</a></li>
					<li><a href="<?= URL::site('login/?logout') ?>">Logout</a></li>
				</ul>
				<div style="clear:both"></div>
			</div>
		</div>

	</div>