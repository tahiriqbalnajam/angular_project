var app =  angular.module('main-App',['ngRoute','angularUtils.directives.dirPagination']);
app.constant('site_url', 'http://localhost/angular_project/public');
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/', {
				//templateUrl: 'auth/login.blade.php',
                templateUrl: 'templates/home.html',
                //controller: 'AdminController'
            }).
            when('/items', {
                templateUrl: 'templates/items.html',
                controller: 'ItemController'
            }).
			when('/dirs', {
                templateUrl: 'templates/dirs.html',
                controller: 'DirController'
            }).
			when('/accounts', {
                templateUrl: 'templates/accounts.html',
                controller: 'AccountController'
            }).
			/////////// REPORTS /////////
			when('/ser_mall_detaill/:id', {
                templateUrl: 'templates/rep_ser_mall_detail.html',
                controller: 'AccountController'
            }).
			when('/rep_prod_detail/:id', {
                templateUrl: 'templates/rep_prod_detail.html',
                controller: 'AccountController'
            }).
			when('/ser_naqdi_detaill/:id', {
                templateUrl: 'templates/rep_ser_naqdi_detail.html',
                controller: 'AccountController'
            }).
			when('/mall_detail/:id', {
                templateUrl: 'templates/rep_mall_detail.html',
                controller: 'AccountController'
            }).
			when('/naqdi_detail/:id', {
                templateUrl: 'templates/rep_naqdi_detail.html',
                controller: 'AccountController'
            }).
			when('/rep_chatha/:id', {
                templateUrl: 'templates/rep_chatha.html',
                controller: 'AccountController'
            }).
			when('/sales_detail/:id', {
                templateUrl: 'templates/rep_sales_details.html',
                controller: 'SaleitemsController'
            }).
			when('/purchase_detail/:id', {
                templateUrl: 'templates/rep_purchase_details.html',
                controller: 'SaleitemsController'
            }).
			////////// END REPORTS //////
			when('/accountTypes', {
                templateUrl: 'templates/account_types.html',
                controller: 'AccountTypesController'
            }).
			when('/accounts_detail', {
                templateUrl: 'templates/accounts_detail.html',
                controller: 'AccountDetailController'
            }).
			when('/accounts_rokkar', {
                templateUrl: 'templates/accounts_rokkar.html',
                controller: 'AccountRokkarController'
            }).
			when('/mallrokkar', {
                templateUrl: 'templates/mallrokkar.html',
                controller: 'MallrokkarController'
            }).
			when('/mallrokkar_new', {
                templateUrl: 'templates/mallrokkar_new.html',
                controller: 'MallrokkarController'
            }).
			when('/mallrokkar_purchase', {
                templateUrl: 'templates/mallrokkar_purchase.html',
                controller: 'MallrokkarController'
            }).
			when('/mallamad', {
                templateUrl: 'templates/mall_amad.html',
                controller: 'MallamadController'
            }).
			when('/transection', {
                templateUrl: 'templates/transection.html',
                controller: 'TransectionController'
            }).
			when('/add_transection', {
                templateUrl: 'templates/add_transection.html',
                controller: 'TransectionController'
            }).
			when('/saleitems', {
                templateUrl: 'templates/saleitems.html',
                controller: 'SaleitemsController'
            }).
			when('/sales', {
                templateUrl: 'templates/sales.html',
                controller: 'SalesController'
            }).
			when('/sale_new', {
                templateUrl: 'templates/sale_new.html',
                controller: 'SalesController'
            }).
			when('/sale_direct', {
                templateUrl: 'templates/sale_direct.html',
                controller: 'SalesController'
            }).
			when('/sale_combine', {
                templateUrl: 'templates/sale_combine.html',
                controller: 'SalesController'
            }).
			when('/users', {
                templateUrl: 'templates/user.html',
                controller: 'UserController'
            }).
			when('/reports', {
                templateUrl: 'templates/reports.html',
                controller: 'ReportsController'
            });
			
}]);
