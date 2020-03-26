<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png" />
	<link rel="icon" type="image/png" href="{{ asset('/logo.png') }}" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Accounts Management</title>

	<!-- Fonts -->
	<!--<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>-->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
    <link rel="stylesheet" href="{{ asset('/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/material-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
     <link rel="stylesheet" href="{{ asset('/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/robotofonts/stylesheet.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/JameelNooriNastaleeq/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('/bootstrap-daterangepicker-master/daterangepicker.css') }}">
    <!--<link rel="stylesheet" href="{{ asset('/css/ngPrint.min.css') }}">-->
	<!--<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,700,300|Material+Icons">-->
	<script src="{{ asset('/js/jquery-3.1.0.min.js') }}"></script>
	<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/select2/select2.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/js/printElement.min.js') }}"></script>
   <!-- <script src="{{ asset('/js/ngPrint.min.js') }}"></script>-->
	<script src="{{ asset('/bootstrap-daterangepicker-master/moment.min.js') }}"></script>
    <script src="{{ asset('/bootstrap-daterangepicker-master/daterangepicker.js') }}"></script>
    
	<!-- Angular JS -->
	<script src="{{ asset('/js/angular.min.js') }}"></script>  
	<script src="{{ asset('/js/angular-route.min.js') }}"></script>
    

	<!-- MY App -->
	<script src="{{ asset('/app/packages/dirPagination.js') }}"></script>
	<script src="{{ asset('/app/routes.js') }}"></script>
	<script src="{{ asset('/app/services/myServices.js') }}"></script>
	<script src="{{ asset('/app/helper/myHelper.js') }}"></script>

	<!-- App Controller -->
	<script src="{{ asset('/app/controllers/ItemController.js') }}"></script>
    <script src="{{ asset('/app/controllers/AccountController.js') }}"></script>
    <script src="{{ asset('/app/controllers/AccountTypesController.js') }}"></script>
    <script src="{{ asset('/app/controllers/MallrokkarController.js') }}"></script>
    <script src="{{ asset('/app/controllers/MallamadController.js') }}"></script>
    <script src="{{ asset('/app/controllers/TransectionController.js') }}"></script>
    <script src="{{ asset('/app/controllers/SaleitemsController.js') }}"></script>
    <script src="{{ asset('/app/controllers/SalesController.js') }}"></script>
    <script src="{{ asset('/app/controllers/UserController.js') }}"></script>
    <script src="{{ asset('/app/controllers/DirController.js') }}"></script>
    <script src="{{ asset('/app/controllers/AccountDetailController.js') }}"></script>
    
<!--<style type="text/css">
@font-face {
    font-family: Jameel Noori Nastaleeq;
    src: url('{{ public_path('css/noori/nafees_web_naskhshipped.eot') }}');
	src: url('{{ public_path('css/noori/nafees_web_naskhshipped.eot?#iefix') }}') format('embedded-opentype'),
	 url('{{ public_path('css/noori/nafees_web_naskhshipped.woff') }}')format('woff'),
	 url('{{ public_path('css/noori/nafees_web_naskhshipped.ttf') }}')format('truetype'),
	 url('{{ public_path('css/noori/nafees_web_naskhshipped.svg#pacificoregular') }}') format('svg');
}
</style>-->
</head>

<body ng-app="main-App">
	<div class="wrapper">
    	<div class="sidebar" data-color="purple" data-image="../assets/img/sidebar-1.jpg">
			<!--
		        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

		        Tip 2: you can also add an image using data-image tag
		    -->

			<div class="logo">
				<a href="#" class="simple-text urr">
					اکائونٹس مینجمیٹ
				</a>
			</div>

	    	<div class="sidebar-wrapper">
	            <ul class="nav big-font" >
	                <li class="active">
	                    <a href="#">
	                        <i class="fa fa-dashboard"></i>
	                        <p>ڈیش بورڈ</p>
	                    </a>
	                </li>
                    
	                <li>
	                    <a href="#/accounts">
	                        <i class="fa fa-user"></i> <p>کھاتہ جات</p>
	                    </a>
	                </li>
                    <li>
	                    <a href="#/dirs">
	                        <i class="fa fa-phone"></i> <p> فون ڈائریکٹری</p>
	                    </a>
	                </li>
	                <li>
	                    <a href="#/items">
	                        <i class="fa fa-gift"></i> <p>اجناس</p>
                         </a>
	                </li>
                    <li>
	                    <a href="#/saleitems">
	                        <i class="fa fa-leaf"></i> <p>سٹاک</p>
                         </a>
	                </li>
                    <li>
	                    <a href="#/sales">
	                        <i class="fa fa-shopping-cart"></i> <p>سیلز</p>
                         </a>
	                </li>
                    <li>
	                    <a href="#/mallrokkar">
	                        <i class="fa fa-file "></i> <p>مال روکڑ</p>
	                    </a>
	                </li>
                    <li>
	                    <a href="#/mallamad">
	                        <i class="fa fa-file "></i> <p>مال آمد</p>
	                    </a>
	                </li>
                    <li>
	                    <a href="#/transection">
	                        <i class="fa fa-money "></i> <p>نقدی روکڑ</p>
	                    </a>
	                </li>
                    <li>
	                    <a href="#/users">
	                        <i class="fa fa-users "></i> <p>سسٹم یوزر</p>
	                    </a>
	                </li>
                    <li>
	                    <a href="../public/logout">
	                        <i class="fa fa-sign-out "></i> <p>لاگ آئوٹ</p>
	                    </a>
	                </li>
	                <!--<li>
	                    <a href="typography.html">
	                        <i class="material-icons">library_books</i>
	                        <p>Typography</p>
	                    </a>
	                </li>
	                <li>
	                    <a href="icons.html">
	                        <i class="material-icons">bubble_chart</i>
	                        <p>Icons</p>
	                    </a>
	                </li>
	                <li>
	                    <a href="maps.html">
	                        <i class="material-icons">location_on</i>
	                        <p>Maps</p>
	                    </a>
	                </li>
	                <li>
	                    <a href="notifications.html">
	                        <i class="material-icons text-gray">notifications</i>
	                        <p>Notifications</p>
	                    </a>
	                </li>
					<li class="active-pro">
	                    <a href="upgrade.html">
	                        <i class="material-icons">unarchive</i>
	                        <p>Upgrade to PRO</p>
	                    </a>
	                </li>-->
	            </ul>
	    	</div><!-----end sidebar-wrapper div-------->
            </div>
            <div class="main-panel" >
            	<nav class="navbar navbar-transparent navbar-absolute">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<!--<a class="navbar-brand" href="#">Accounts Management Dashboard</a>-->
                        <?php 
						$getDeaths = DB::table('config')->take(10)->get();
						//print_r($getDeaths);
						//echo $getDeaths[0]->name
						?>
                        <p class="navbar-brand urdu"><?php echo $getDeaths[0]->name?></p>                       
					</div>
                    <h5 align="right" style="padding-right:20px"> Today Date : <?php echo date('d/m/Y',strtotime(Session::get('todayDate')))?></h5>
					<!--<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav navbar-right">
							<li>
								<a href="#pablo" class="dropdown-toggle" data-toggle="dropdown">
									<i class="material-icons">dashboard</i>
									<p class="hidden-lg hidden-md">Dashboard</p>
								</a>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="material-icons">notifications</i>
									<span class="notification">5</span>
									<p class="hidden-lg hidden-md">Notifications</p>
								</a>
								<ul class="dropdown-menu">
									<li><a href="#">Mike John responded to your email</a></li>
									<li><a href="#">You have 5 new tasks</a></li>
									<li><a href="#">You're now friend with Andrew</a></li>
									<li><a href="#">Another Notification</a></li>
									<li><a href="#">Another One</a></li>
								</ul>
							</li>
							<li>
								<a href="#pablo" class="dropdown-toggle" data-toggle="dropdown">
	 							   <i class="material-icons">person</i>
	 							   <p class="hidden-lg hidden-md">Profile</p>
		 						</a>
							</li>
						</ul>

						<form class="navbar-form navbar-right" role="search">
							<div class="form-group  is-empty">
								<input type="text" class="form-control" placeholder="Search">
								<span class="material-input"></span>
							</div>
							<button type="submit" class="btn btn-white btn-round btn-just-icon">
								<i class="material-icons">search</i><div class="ripple-container"></div>
							</button>
						</form>
					</div>-->
				</div>
			</nav><!----end nav--->
            
            <div class="content">
				<div class="container-fluid">	
                	<ng-view></ng-view>
				</div><!--end container-fluid---->
     		</div><!----end content"------>
            
    	</div><!-------end main panel------>
	</div><!-----end wrapper div---->
	<!-- Scripts -->
    
    <script src="{{ asset('/js/material.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap-notify.js') }}"></script>
	<script src="{{ asset('/js/material-dashboard.js') }}"></script>
    <script src="{{ asset('/js/demo.js') }}"></script>
    
    <script>
	$(function () {
			
			$(".select2").select2();
	});
	$(function() {
		$('.date_range').daterangepicker({
			autoUpdateInput: false,
			locale: {
				format: 'DD/MM/YYYY - DD/MM/YYYY'
			}
		});
	});
	
	
	</script>
    <footer style="text-align:center">&copy; 2018-19 <a href="http://idlbridge.com" target="_blank"> &nbsp;&nbsp;IDLBridge </a> 0345-705-0405 &nbsp;&nbsp;&nbsp; version 1.2 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Developer: Muhammad Nadeem Ijaz</footer>
</body>
</html>


