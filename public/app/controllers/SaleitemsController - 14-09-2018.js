app.controller('AdminController', function($scope,$http){
 
  $scope.pools = [];
   
});

app.controller('SaleitemsController', function(dataFactory,$scope,$http){
 
  $scope.data = [];
  $scope.detaildata = {};
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  $scope.detaildata = {};

  $scope.totalItems = 0;
  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest('/angular_project/public/saleitems?search='+$scope.searchText+'&page='+pageNumber).then(function(data) {
            $scope.data = data.items.data;
            $scope.totalItems = data.items.total;
			$scope.supplier = data.supplier;
			$scope.all_items = data.all_items;
			$scope.category = data.category;
			
			//$scope.opts = ["purchase", "return"];
			$scope.opts = [{ "value": "purchase", "text": "خرید" }, { "value": "return", "text": "واپسی" }];
			$scope.todayDate = data.todayDate;
          });
      }else{
        dataFactory.httpRequest('/angular_project/public/saleitems?page='+pageNumber).then(function(data) {
          $scope.data = data.items.data;
          $scope.totalItems = data.items.total;
		  $scope.supplier = data.supplier;
		  $scope.all_items = data.all_items;
		  $scope.perpage = data.perpage;
		  $scope.category = data.category;
		  $scope.opts = [{ "value": "purchase", "text": "خرید" }, { "value": "return", "text": "واپسی" }];
		  //$scope.opts = ["purchase", "return"];
		  $scope.todayDate = data.todayDate;
        });
      }
  }

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
	  $scope.addItem.$invalid = true;
    dataFactory.httpRequest('saleitems','POST',{},$scope.form).then(function(data) {
		$scope.all_items = data.all_items;
		$scope.form=null;
      $scope.data.push(data.create);
      $(".modal").modal("hide");
    });
  }
  $scope.saveReceiveItem = function(){
	  $scope.receiveItem.$invalid = true;
    dataFactory.httpRequest('receive_saleitems','POST',{},$scope.form).then(function(data) {
		$scope.all_items = data.all_items;
		$scope.form=null;
		$scope.data = apiModifyTable($scope.data,data.items.id,data.items);
      //$scope.data.push(data.id);
      $(".modal").modal("hide");
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('saleitems/'+id+'/edit').then(function(data) {
    	console.log(data);
      	$scope.form = data;
    });
  }

  $scope.saveEdit = function(){
    dataFactory.httpRequest('saleitems/'+$scope.form.id,'PUT',{},$scope.form).then(function(data) {
		$scope.form=null;
      	$(".modal").modal("hide");
        $scope.data = apiModifyTable($scope.data,data.id,data);
    });
  }

  $scope.remove = function(item,index){
    var result = confirm("Are you sure delete this item?");
   	if (result) {
      dataFactory.httpRequest('saleitems/'+item.id,'DELETE').then(function(data) {
          $scope.data.splice(index,1);
      });
    }
  }
  
  $scope.detail = function(id){
    dataFactory.httpRequest('sale_item_detail/'+id).then(function(data) {
    	$scope.detaildata = data.data;
    });
  }
  
  $scope.getTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
		if(product.type == 'purchase'){
        	total += ((product.price * product.quantity) + (product.off_loading*product.quantity));
		}
    }
    return total;
}
$scope.remove_receive_item = function(item,index){
    var result = confirm("Are you sure delete this ?");
   	if (result) {
      //dataFactory.httpRequest('mallrokkar/'+item.id,'DELETE').then(function(data) {//alert(index);
	  dataFactory.httpRequest('remove_receive_item/'+item.id).then(function(data) {//alert(index);
          $scope.data.splice(index,1);
		  getResultsPage(1);
      });
    }
  }
   
});