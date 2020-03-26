/*app.controller('AdminController', function($scope,$http){
 
  $scope.pools = [];
   
});*/

app.controller('SaleitemsController', function(dataFactory,$scope,$http,$routeParams,site_url){
 
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
          dataFactory.httpRequest(site_url+'/saleitems?search='+$scope.searchText+'&page='+pageNumber).then(function(data) {
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
        dataFactory.httpRequest(site_url+'/saleitems?page='+pageNumber).then(function(data) {
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
  $scope.saveFirstAdd = function(){
	  $scope.addItem.$invalid = true;
    dataFactory.httpRequest('saleitemsFirst','POST',{},$scope.form).then(function(data) {
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
    //dataFactory.httpRequest('sale_item_detail/'+id).then(function(data) {
		dataFactory.httpRequest('sale_item_detail','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
    	$scope.detaildata = data.data;
		$scope.total = data.total;
    });
  }
  $scope.detail_sale = function(id){
    //dataFactory.httpRequest('sales_detail/'+id).then(function(data) {
		dataFactory.httpRequest('sales_detail','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
    	$scope.salesdetaildata = data.data;
		$scope.total = data.total;
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
$scope.getSaleTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
        	total += ((product.price * product.quantity) + ((product.price*product.quantity)*(product.intrest/100)));
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
  
 $scope.sales_detail_print = function() {
  var printContents = document.getElementById('sales_detail_p').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;} td{border:1px solid}</style><link rel="stylesheet" href="'+site_url+'/css/bootstrap.min.css"><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
}
  
  
  
   
});