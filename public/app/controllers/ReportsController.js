app.controller('ReportsController', function(dataFactory,$scope,$http,site_url){
 //alert('asdfsadf');
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};

  $scope.selectAccount = {};
  $scope.totalItems = 0;
  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  getResultsPage(1);
  function getResultsPage(pageNumber) {
	  
      if(! $.isEmptyObject($scope.libraryTemp)){
		  if($.isEmptyObject($scope.searchText)){
		  	$scope.searchText = '';
			}
			if($.isEmptyObject($scope.selectAccount)){
		  	$scope.selectAccount = '';
			}
          dataFactory.httpRequest(site_url+'/reports?date='+$scope.date+'&item_id='+$scope.item_id+'&page='+pageNumber).then(function(data) {
			  $scope.items = data.items;
			  $scope.sale_items = data.sale_items;
			  $scope.items_detail = data.items_detail;
          });
      }else{
        dataFactory.httpRequest(site_url+'/reports?page='+pageNumber).then(function(data) {
          $scope.items = data.items;
		  $scope.sale_items = data.sale_items;
          //$scope.totalItems = data.user.total;
        });
      }
  }
  /////////////// direct sale totals
  $scope.qty_total = function(data){
    	var total = 0;
   	 	for(var i = 0; i < data.length; i++){
        	var product = data[i];
        	total += (product.quantity);
    	}
    	return total.toFixed(2);
	}
	$scope.sale_price_total = function(data){
    	var total = 0;
   	 	for(var i = 0; i < data.length; i++){
        	var product = data[i];
        	total += (product.price);
    	}
    	return total.toFixed(2);
	}
	$scope.purchase_pirce_total = function(data){
    	var total = 0;
   	 	for(var i = 0; i < data.length; i++){
        	var product = data[i];
        	total += (product.purchaser_percentage);
    	}
    	return total.toFixed(2);
	}
	$scope.bachat_total = function(data){
    	var total = 0;
   	 	for(var i = 0; i < data.length; i++){
        	var product = data[i];
        	total += (product.purchaser_percentage-product.price);
    	}
    	return total.toFixed(2);
	}
  /////////////// end of direct sale totals

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
	  angular.forEach($scope.data, function(item){
		   console.log($scope.form.email);
		   if(item.email == $scope.form.email)
				$scope.validd = true;
	   })
	   if($scope.validd){
	   		alert('Email Address Should Be Unique');
			$scope.validd = false;
		}
		else{
			dataFactory.httpRequest('users','POST',{},$scope.form).then(function(data) {
			  $scope.data.unshift(data);
			  $scope.form=null;
			  $(".modal").modal("hide");
			});
		}			  	 
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('users/'+id+'/edit').then(function(data) {
		$scope.myindex = id;
      	$scope.form = data;
    });
  }

  $scope.saveEdit = function(){	  
			dataFactory.httpRequest('users/'+$scope.form.id,'PUT',{},$scope.form).then(function(data) { console.log($scope.data);
			$scope.data = apiModifyTable($scope.data,data.id,data);
			$scope.form=null;
			$(".modal").modal("hide");
		});
  }

  $scope.remove = function(item,index){
    var result = confirm("Are you sure delete this item?");
   	if (result) {
      dataFactory.httpRequest('users/'+item.id,'DELETE').then(function(data) {//alert(index);
          $scope.data.splice(index,1);
      });
    }
  }
  //filter start 
  $scope.filter_form = function(){
	  if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope;
			  console.log($scope.libraryTemp);
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
  
	$scope.password_check = function(){
		if($scope.form.password == $scope.form.c_password)
			$scope.addItem.$invalid = true;
		//else
			//alert ('not');
	}

   
});