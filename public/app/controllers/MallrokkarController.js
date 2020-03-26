app.controller('MallrokkarController', function(dataFactory,$scope,$http,site_url){

 //alert('asdfsadf');
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  //$scope.purchaserAccount = {"id":8,"name":"ginner1","address":"0","phone":"0","cnic":"0","status":"enable","account_type":2,"updated_at":"2017-05-31 06:35:45","created_at":"2017-05-31 06:35:45"},{"id":9,"name":"ginner 2","address":"1","phone":"1","cnic":"1","status":"enable","account_type":2,"updated_at":"2017-05-31 06:36:01","created_at":"2017-05-31 06:36:01"},{"id":10,"name":"marketer","address":"1","phone":"1","cnic":"1","status":"enable","account_type":3,"updated_at":"2017-05-31 06:40:29","created_at":"2017-05-31 06:40:29"};                 
  $scope.accountType = {};
  $scope.totalItems = 0;
  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };
  
  getResultsPage(1);
  function getResultsPage(pageNumber) {
	  
      if(! $.isEmptyObject($scope.libraryTemp)){
		  		
          dataFactory.httpRequest(site_url+'/mallrokkar?date='+$scope.date+'&percha_no='+$scope.percha_no+'&account='+$scope.account+'&page='+pageNumber).then(function(data) {
            $scope.saler_rokkar = data.saler_rokkar;
		  	$scope.totalItems = data.saler_rokkar.total;
			$scope.purchaser_rokkar = data.purchaser_rokkar;
		  	$scope.totalItems1 = data.purchaser_rokkar.total;
		  	$scope.SellerAccount = data.seller_account;
			$scope.items = data.items;
			$scope.purchaserAccount = data.purchaser_account;
			$scope.all_account = data.all_account;
//			$scope.receive_jama = data.receive_jama;
//			$scope.receive_naam = data.receive_naam;
			$scope.todayDate = data.todayDate;
          });
      }else{
        dataFactory.httpRequest(site_url+'/mallrokkar?page='+pageNumber).then(function(data) {
          	$scope.saler_rokkar = data.saler_rokkar;
		  	$scope.totalItems = data.saler_rokkar.total;
			$scope.purchaser_rokkar = data.purchaser_rokkar;
		  	$scope.totalItems1 = data.purchaser_rokkar.total;
		  	$scope.SellerAccount = data.seller_account;
			$scope.items = data.items;
			$scope.purchaserAccount = data.purchaser_account;
			$scope.all_account = data.all_account;
//			$scope.receive_jama = data.receive_jama;
//			$scope.receive_naam = data.receive_naam;
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

  $scope.save123Add = function(){
	  var purchaser_total = ($scope.getRunningTotal()).toFixed(2);
	  var seller_total = $scope.form.ttl;
	  if(purchaser_total < seller_total){
		  alert('Please Enter Purchaser');
	  }
	  else{
			$scope.addItem.$invalid = true;
			dataFactory.httpRequest('mallrokkar','POST',{},{purchaser:$scope.choices,seller:$scope.form}).then(function(data) {
			$scope.form=null;
			$scope.choices = [{id: 1,purchaser_account:'',price:0,weight:0}];
			if(parseInt(data.id))
			alert("Data inserted Successfully");
			if(data.res !='undefined')
			alert(data.res);
    		});
	  	}
  }
  
  $scope.saveAddPurchase = function(){
	  $scope.addItem.$invalid = true;
    dataFactory.httpRequest('mallrokkar_post','POST',{},$scope.form).then(function(data) {
	  $scope.form=null;
	  if(parseInt(data.id))
	  alert("Data inserted Successfully");
      //$(".modal").modal("hide");	 
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('mallrokkar/'+id+'/edit').then(function(data) {		
		$scope.myindex = id;
      	$scope.form1 = data;
		//alert(data.id);
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
    var result = confirm("Are you sure delete this ?");
   	if (result) {
	  dataFactory.httpRequest('mallrokkar_destroy/'+item.sale_purchase_id).then(function(data) {//alert(index);
          $scope.data.splice(index,1);
		  //getResultsPage(1);
      });
    }
  }
  $scope.correct = function(item,index){
    var result = confirm("Are you sure Correct this ?");
   	if (result) {
	  dataFactory.httpRequest('mallrokkar_corroect/'+item.sale_purchase_id).then(function(data) {//alert(index);
          $scope.data.splice(index,1);
		  getResultsPage(1);
      });
    }
  }
  
  //filter start 
  $scope.filter_form = function(){
	  if($.isEmptyObject($scope.libraryTemp)){//alert($scope);
              $scope.libraryTemp = $scope;
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
   $scope.getAccounts = function(){
	   dataFactory.httpRequest('mallrokkar_get').then(function(data) {
		   
		$scope.purchaserAccount = data.seller_account;
		console.log($scope.purchaserAccount);
    });	       
   	}
	$scope.calculate = function(){
		$scope.total = $scope.form.rate*($scope.form.weight/40);
		$scope.gtotal;
		$scope.palydari;
		$scope.brokri;
		$scope.anjuman_fund;
		$scope.petty_cash ;
		//$scope.petty_cash11 = .45;
		//$scope.form.petty_cash11 = $scope.petty_cash11;
		$scope.form.total = parseFloat($scope.total).toFixed(2);
		$scope.form.palydari = $scope.palydari = parseFloat($scope.total*0.01).toFixed(2);
		$scope.form.brokri  = $scope.brokri = parseFloat($scope.total*0.0012).toFixed(2);
		$scope.form.anjuman_fund = $scope.anjuman_fund = parseFloat($scope.total*0.0003).toFixed(2);
		//$scope.form.petty_cash = $scope.petty_cash = parseFloat($scope.total*($scope.form.petty_cash/100)).toFixed(2);
		if($scope.form.petty_cash11 < 1.2){
			alert('1.2 یا اس سے زیادہ درج کریں');
			$scope.form.petty_cash11 = 1.6;
		}
		
		$scope.petty_cash = parseFloat($scope.total*(($scope.form.petty_cash11-1.15)/100)).toFixed(2);
		$scope.form.petty_cash = parseFloat($scope.total*(($scope.form.petty_cash11-1.15)/100)).toFixed(2);
		$scope.gtotal = parseFloat($scope.total) -(parseFloat($scope.palydari) + parseFloat($scope.brokri) + parseFloat($scope.anjuman_fund) +parseFloat($scope.petty_cash));
		$scope.form.s_grand_total = ($scope.gtotal).toFixed(2);
		$scope.form.p_grand_total = (parseFloat($scope.total+($scope.total*1.6/100))).toFixed(2);
		$scope.form.chong = ($scope.total*$scope.form.petty_cash11/100).toFixed(2);
		$scope.form.arhat = ($scope.total*1.6/100).toFixed(2);
		var ttll = (parseFloat($scope.form.total) + parseFloat($scope.form.arhat));
		$scope.form.ttl = ttll.toFixed(2);
	}
	
	//transection....
   $scope.saveTransection = function(){
	   $scope.addItem.$invalid = true;
	  //console.log($scope.addItem.$invalid);
	  //$scope.form.account=null;
    dataFactory.httpRequest('add_transection','POST',{},$scope.form).then(function(data) {
      //$scope.data.unshift(data);
	  //$scope.form=null;
      //$(".modal").modal("hide");	 
    });
  }
  
  $scope.saleTotal1 = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
		if(product.deleted == 'no'){
        	total += (product.amount);
		}
    }
    return total.toFixed(2);
}
$scope.saleTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
		if(product.deleted == 'no'){
        	total += (product.amount);
		}
    }
    return total.toFixed(2);
}
$scope.parseFloat = function(value)
    {
        return parseFloat(value);
    }
  //end Transection 
  $scope.choices = [{id: 1,purchaser_account:'',price:0,weight:0}];
  $scope.addNewChoice = function() {
    var newItemNo = $scope.choices.length+1;
    $scope.choices.push({'id':newItemNo,purchaser_account:'',price:0,weight:0});
	$(".select2").select2();
  };
    
  $scope.removeChoice = function() {
    var lastItem = $scope.choices.length-1;
    $scope.choices.splice(lastItem);
  };
  
  $scope.getRunningTotal = function(index) {
    console.log(index);
    var runningTotal = 0;
    var selectedTransactions = $scope.choices.slice(0, index);
    angular.forEach($scope.choices, function(transaction, index){
      runningTotal += ((transaction.price*(transaction.weight/40))+((transaction.price*(transaction.weight/40))*.016));
    });
	//$scope.form.total = runningTotal;
	if(isNaN(runningTotal))
		return 0;
	else
    	return runningTotal;
};
  
  
  
  
  
});