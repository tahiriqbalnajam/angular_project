app.controller('AccountRokkarController', function(dataFactory,$scope,$http){
 //alert('asdfsadf');
//var myapp = angular.module('main-App', ['ngPrint']); 
 
  $scope.data = [];
  //$scope.ngPrint = ['ngPrint'];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};

  $scope.accountType = {};
  $scope.mall_roakker_detail = {};
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
        dataFactory.httpRequest('/angular_project/public/accounts_rokkar?page='+pageNumber).then(function(data) {
          $scope.data = data.account.data;
		  $scope.accountType = data.account_type;
          $scope.totalItems = data.account.total;
		  $scope.perpage = data.perpage;
		  $scope.config = data.config;
		  console.log(data);
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
  
  $scope.accounts_detail = function(id){
	  accounts_detail1(id);
  }
  
  function accounts_rokkar(){
	  dataFactory.httpRequest('accounts_rokkar/').then(function(data) {console.log(data);
          $scope.naam_jama_detail = data.naam_jama_detail;
		  
		  //alert('asfd');
		  //getResultsPage(1);
      });
  }
  
  $scope.saleTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
        total += (product.price * product.quantity);
    }
    return total.toFixed(2);
}
$scope.amountTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
        total += (product.amount);
    }
    return total.toFixed(2);
}
$scope.mallroakkerTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
        total += (product.seller_amount);
    }
    return total.toFixed(2);
}
$scope.parseFloat = function(value)
    {
        return parseFloat(value);
    }
	
$scope.print1 = function(){
	window.print() ;
	/*var printContents = document.getElementById("print_").innerHTML;
	var popupWin = window.open('','_blank','width = 300,height = 300');
	popupWin.document.open();
	popupWin.document.write('<html><body onload="window.print()">' + printContents + '</body></html>');*/	
}




$scope.printDiv = function() {
  var printContents = document.getElementById('detail').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;}</style><link rel="stylesheet" href="http://localhost/angular_project/public/datatables/dataTables.bootstrap.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
} 




   
});