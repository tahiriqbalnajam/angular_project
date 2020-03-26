app.controller('AccountDetailController', function(dataFactory,$scope,$http){
 //alert('asdfsadf');
 
 
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};

  $scope.accountType = {};
  $scope.mall_roakker_detail = {};
  $scope.totalItems = 0;
  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

	//getResultsPage(1);
 /* accounts_detail(id);
  function accounts_detail(id) {
	  dataFactory.httpRequest('accounts_detail/'+id).then(function(data) {console.log(data);
          $scope.sales_items_detail = data.sales_items_detail;
		  $scope.mall_roakker_detail = data.mall_roakker_detail;
		  $scope.transection_detail = data.transection_detail;
		  console.log($scope.sales_items_detail);
		  $scope.ddd = 'asdfasdf';
		  //getResultsPage(1);
      });
  }*/
  /*function getResultsPage(pageNumber) {
	  
      if(! $.isEmptyObject($scope.libraryTemp)){
		  if($.isEmptyObject($scope.searchText)){
		  	$scope.searchText = '';
			}
			if($.isEmptyObject($scope.account_type)){
		  	$scope.account_type = '';
			}
          dataFactory.httpRequest('/angular_project/public/accounts?search='+$scope.searchText+'&account_type='+$scope.account_type+'&page='+pageNumber).then(function(data) {
            $scope.data = data.account.data;
			$scope.accountType = data.account_type;
            $scope.totalItems = data.account.total;
			$scpoe.perpage = data.perpage;
			$scope.config = data.config;
          });
      }else{
        dataFactory.httpRequest('/angular_project/public/accounts?page='+pageNumber).then(function(data) {
          $scope.data = data.account.data;
		  $scope.accountType = data.account_type;
          $scope.totalItems = data.account.total;
		  $scope.perpage = data.perpage;
		  $scope.config = data.config;
		  console.log(data);
        });
      }
  }*/
  
  ////// product detail /////////
$scope.product_detail = function(id){
	  product_detaill(id);
  }
function product_detaill(id){
	  dataFactory.httpRequest('product_detail/'+id).then(function(data) {console.log(data);
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.sales_items_detail = data.sales_items_detail;
		  $scope.sales_items_detail_jama = data.sales_items_detail_jama;
		  $scope.transection_detail_receive_naam = data.transection_detail_receive_naam;
		  $scope.transection_detail_receive_jama = data.transection_detail_receive_jama;
		  $scope.total_sale_naam = data.total_sale_naam;
		  $scope.total_sale_jama = data.total_sale_jama;
		  $scope.total_receive_naam = data.total_receive_naam;
		  $scope.total_receive_jama = data.total_receive_naam;
		  $scope.pro_transection_detail_jama = data.pro_transection_detail_jama;
		  $scope.pro_transection_detail_naam = data.pro_transection_detail_naam;
		  $scope.pro_total_naqdi_jama = data.pro_total_naqdi_jama;
		  $scope.pro_total_naqdi_naam = data.pro_total_naqdi_naam;
		  $scope.balance = data.balance;
      });
  }
///// end product detail //////


  $scope.searchDB = function(){
      if($scope.searchText.length >= 3){
          if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
          getResultsPage(1);
      }else{
          if(! $.isEmptyObject($scope.libraryTemp)){
              $scope.data = $scope.libraryTemp ;
              $scope.totalItems = $scope.totalItemsTemp;
              $scope.libraryTemp = {};
          }
      }
  }

  $scope.saveAdd = function(){
    dataFactory.httpRequest('accounts','POST',{},$scope.form).then(function(data) {
      $scope.data.unshift(data);
	  $scope.form=null;
      $(".modal").modal("hide");
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('accounts/'+id+'/edit').then(function(data) {
		$scope.myindex = id;
      	$scope.form = data;
    });
  }

  $scope.saveEdit = function(){
    dataFactory.httpRequest('accounts/'+$scope.form.id,'PUT',{},$scope.form).then(function(data) { console.log($scope.data);
        $scope.data = apiModifyTable($scope.data,data.id,data);
		$scope.form=null;
		$(".modal").modal("hide");
    });
  }

  $scope.remove = function(item,index){
    var result = confirm("Are you sure delete this item?");
   	if (result) {
      dataFactory.httpRequest('accounts/'+item.id,'DELETE').then(function(data) {//alert(index);
          $scope.data.splice(index,1);
      });
    }
  }
  //filter start 
  $scope.filter_form = function(){
	  if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
	getResultsPage(1);
	  /*dataFactory.httpRequest('/angular_project/public/accounts?account_type='+$scope.account_type+'&page='+pageNumber).then(function(data) {
            $scope.data = data.account.data;
			$scope.accountType = data.account_type;
            $scope.totalItems = data.account.total;
          });*/
  }
  // filter end
  
  $scope.closingToday = function(){
	  $scope.closing.$invalid = true;
    dataFactory.httpRequest('closing','POST',{},$scope.form).then(function(data) {
	  $scope.form=null;
	  //if(parseInt(data.id))
	  //aert("Data inserted Successfully");
      $(".modal").modal("hide");	 
    });
  }
  
  $scope.config_data = function(){
	  $scope.closing.$invalid = true;
    dataFactory.httpRequest('config','POST',{},$scope.config).then(function(data) {
	  //$scope.form=null;
      $(".modal").modal("hide");	 
    });
  }
  
  
  
   
});