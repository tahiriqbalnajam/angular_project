app.controller('AdminController', function($scope,$http){
 
  $scope.pools = [];
   
});

app.directive('contactType', function($rootScope) {
  return {
    restrict: "E",
    scope: {},
    templateUrl:'templates/SaleItems_Direc.html',
    controller: function($rootScope, $scope, $element) {
      $scope.all_items = $rootScope.all_items;
      $scope.Delete = function(e) {
        //remove element and also destoy the scope that element
        $element.remove();
        $scope.$destroy();
      }
    }
  }
});




app.controller('SalesController', function(dataFactory,$scope,$compile,$rootScope,$http){
 
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};

  $scope.totalItems = 0;
  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest('/angular_project/public/sales?search='+$scope.searchText+'&page='+pageNumber).then(function(data) {
            $scope.data = data.items.data;
            $scope.totalItems = data.items.total;
			$scope.supplier = data.supplier;
			$scope.all_items = data.all_items;
          });
      }else{
        dataFactory.httpRequest('/angular_project/public/sales?page='+pageNumber).then(function(data) {
          $scope.data = data.items.data;
          $scope.totalItems = data.items.total;
		  $scope.formers = data.formers;
		  $rootScope.all_items = data.all_items;
		  $scope.perpage = data.perpage;
        });
      }
  }
	
 $scope.AddContactTypeControl = function() {
    var divElement = angular.element(document.querySelector('#contactTypeDiv'));
    var appendHtml = $compile('<contact-Type></contact-Type>')($scope);
    divElement.append(appendHtml);
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
	dataFactory.httpRequest('sales','POST',{},{former:$scope.form,product:$scope.choices}).then(function(data) {
		//$scope.form=null;
      //$scope.data.push(data);
      //$(".modal").modal("hide");
    });
  }
  $scope.saveReceiveItem = function(){
	  $scope.receiveItem.$invalid = true;
    dataFactory.httpRequest('receive_saleitems','POST',{},$scope.form).then(function(data) {
		$scope.form=null;
      $scope.data.push(data);
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
  
  
  $scope.choices = [{
	  id: 'choice1',
	  saleitem:'',
	  price:'',
	  qty:''
	  }];
  $scope.addNewChoice = function() {
    var newItemNo = $scope.choices.length+1;
    $scope.choices.push({
		'id':'choice'+newItemNo,
		saleitem:'',
		price:'',
		qty:''
		});
  };
    
  $scope.removeChoice = function() {
    var lastItem = $scope.choices.length-1;
    $scope.choices.splice(lastItem);
  };
  
  
   
});