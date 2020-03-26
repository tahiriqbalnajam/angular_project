app.controller('AdminController', function($scope,$http,site_url){
 
  $scope.pools = [];
   
});

app.controller('SalesController', function(dataFactory,$scope,$http,site_url){
 
  $scope.data = [];
  $scope.detaildata  = [];
  $scope.accountdetaildata = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  

  $scope.totalItems = 0;
  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
		  	if($.isEmptyObject($scope.date)){
			  $scope.date = '';
			}
			if($.isEmptyObject($scope.selectAccount)){
			  $scope.selectAccount = '';
			}
          dataFactory.httpRequest(site_url+'/sales?date='+$scope.date+'&selectAccount='+$scope.selectAccount+'&page='+pageNumber).then(function(data) {
            $scope.data = data.items;
            $scope.totalItems = data.items.total;
			$scope.formers = data.formers;
			$scope.ginners = data.ginners;
			$scope.all_items = data.all_items;
			$scope.running_items = data.running_items;
			$scope.todayDate = data.todayDate;
			$scope.opts = [{"value":"sale","text":"سیلز"},{"value":"return","text":"واپسی"}];
			$scope.optionss = [{"value":"cash","text":"نقد"},{"value":"credit","text":"ادھار"}];
			//$scope.optionss = ["cash", "credit"];
          });
      }else{
        dataFactory.httpRequest(site_url+'/sales?page='+pageNumber).then(function(data) {
          $scope.data = data.items;
          $scope.totalItems = data.items.total;
		  $scope.formers = data.formers;
		  $scope.ginners = data.ginners;
		  $scope.all_items = data.all_items;
		  $scope.running_items = data.running_items;
		  $scope.perpage = data.perpage;
		  $scope.todayDate = data.todayDate;
		  $scope.opts = [{"value":"sale","text":"سیلز"},{"value":"return","text":"واپسی"}];
		  $scope.rabih_kharif_opt = [{"value":"rabih","text":"گندم"},{"value":"kharif","text":"نرما"}];
		  $scope.optionss = [{"value":"cash","text":"نقد"},{"value":"credit","text":"ادھار"}];
		  //$scope.optionss = ["cash", "credit"];
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
	
	//filter start 
  $scope.filter_form = function(){
	  if($.isEmptyObject($scope.libraryTemp)){//alert($scope);
              $scope.libraryTemp = $scope;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
	getResultsPage(1);
  }
  // filter end
  
  $scope.saveAdd = function(){
	  $scope.addItem.$invalid = true;
    dataFactory.httpRequest('sales','POST',{},{former:$scope.form,product:$scope.choices}).then(function(data) {
		if(data.check_no_error){
			$scope.addItem.$invalid = false;
		alert(data.check_no_error);	  
	 }
	 else if(parseInt(data.id)){
		 $scope.all_items = data.all_items;
	  alert("Data inserted Successfully");
	  alert(data.res);
		$scope.form = null;
		$scope.choices = [{
		  id: 1,
		  saleitem:'',
		  price:0,
		  qty:0,
		  total:0,
		  payment_type:'',
		  intrest:0
		  }];
	 }
		
    });
  }
  $scope.saveDirectSale = function(){
	  $scope.addItem.$invalid = true;
    dataFactory.httpRequest('direct_sales','POST',{},{former:$scope.form,product:$scope.choices1}).then(function(data) {
		if(data.check_no_error){
			$scope.addItem.$invalid = false;
		alert(data.check_no_error);	  
	 }
	 else if(parseInt(data.id)){
		 $scope.all_items = data.all_items;
	  alert("Data inserted Successfully");
	  alert(data.res);
		$scope.form = null;
		$scope.choices1 = [{id: 1,saleitem:'',purchase_price:0,saler:0,sale_price:0,qty:0,total:0,bachat:0,purchaser:0,purchaser_total:0,saler_total:0,bachat_total:0}];
	 }
		
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
    dataFactory.httpRequest('sales/'+id+'/edit').then(function(data) {
    	$scope.data = apiModifyTable($scope.data,data.id,data);
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
      dataFactory.httpRequest('sales/'+item.id,'DELETE').then(function(data) {
		  $scope.data = apiModifyTable($scope.data,data.id,data);
          //$scope.data.splice(index,1);
      });
    }
  }
  $scope.detail = function(id,running_sale){
    dataFactory.httpRequest('sale_detail/'+id+'/' + running_sale).then(function(data) {
    	$scope.detaildata = data.data;
    });
  }  
  
  $scope.choices = [{id: 1,saleitem:'',price:0,qty:0,total:0,payment_type:'',intrest:0}];
  $scope.addNewChoice = function() {
    var newItemNo = $scope.choices.length+1;
    $scope.choices.push({'id':newItemNo,saleitem:'',price:0,qty:0,total:0,payment_type:'',intrest:0});
	$(".select2").select2();
  };
    
  $scope.removeChoice = function() {
    var lastItem = $scope.choices.length-1;
    $scope.choices.splice(lastItem);
  };
  
  $scope.choices1 = [{id: 1,saleitem:'',purchase_price:0,saler:0,sale_price:0,qty:0,total:0,bachat:0,purchaser:0,purchaser_total:0,saler_total:0,bachat_total:0}];
  $scope.addNewChoice1 = function() {
	  
    var newItemNo = $scope.choices1.length+1;
    $scope.choices1.push({id: newItemNo,saleitem:'',purchase_price:0,saler:0,sale_price:0,qty:0,total:0,bachat:0,purchaser:0,purchaser_total:0,saler_total:0,bachat_total:0});
	$(".select2").select2();
  };
    
  $scope.removeChoice1 = function() {
    var lastItem = $scope.choices1.length-1;
    $scope.choices1.splice(lastItem);
  };
  
  $scope.getRunningTotal = function(index) {
    //console.log(index);
    var runningTotal = 0;
	var percentage = 0;
    var selectedTransactions = $scope.choices.slice(0, index);
    angular.forEach($scope.choices, function(transaction, index){
		if(transaction.intrest >0){
			percentage = (transaction.price*transaction.qty)*(transaction.intrest/100);
		}
		else percentage = 0;
      runningTotal += (transaction.price*transaction.qty)+percentage;
    });
	//$scope.form.total = runningTotal
	if(isNaN(runningTotal))
		return 0;
	else
    	return runningTotal;
};

$scope.getTotal = function(detaildata){
    var total = 0;
	var percentage = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
		if(product.intrest > 0){
			//percentage = (product.price * product.quantity)*product.intrest / 100;
		}
        total += (product.price * product.quantity)*( product.intrest/100)+(product.price * product.quantity);
    }
    return total;
}
$scope.saleTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
	if(product.deleted == 'no'){
        total += (product.amount);
}
    }
    return total;
}

$scope.getRunningTotal1 = function(index) {
    //console.log(index);
    var runningTotal = 0;
    var selectedTransactions = $scope.choices1.slice(0, index);
    angular.forEach($scope.choices1, function(transaction, index){
      runningTotal += transaction.sale_price*transaction.qty;
    });
	//$scope.form.total = runningTotal
	if(isNaN(runningTotal))
		return 0;
	else
    	return runningTotal;
};
  
$scope.account_detail = function(id,running_sale){
    dataFactory.httpRequest('account_detail/'+id).then(function(data) {
    	$scope.accountdetaildata = data.total_balance;
		//console.log(accountdetaildata);
		//$scope.itemList.push(accountdetaildata.name);
    });
  }
$scope.item_detail = function(id,cid){
	//console.log(cid);
    dataFactory.httpRequest('item_detail/'+id).then(function(data) {
		//alert(data.data.price);
		angular.forEach($scope.choices1, function(transaction, index){
			//console.log(cid);
			console.log(index);
			if(index == cid-1){
				transaction.purchase_price = (data.data.price).toFixed(2);
			}
		 // runningTotal += transaction.sale_price*transaction.qty;
		});
    	//choice.purchase_price = data.data.price;
		
		//console.log(accountdetaildata);
		//$scope.itemList.push(accountdetaildata.name);
    });
  }
  
  
  
  $scope.integer = function(val){
    return parseFloat(val);
    }
	
	$scope.bachat = function(val1,val2){
		var value = val1-val2;
    return value.toFixed(2);
    }
	$scope.running_seller_total = function(val1,val2){
		var value = parseFloat(val1)+parseFloat(val2);
    return value.toFixed(2);
    }
	
	$scope.qty_price_total = function(price,qty){
    return parseFloat(price)*parseFloat(qty);
    }

////////total
$scope.getpurchaser_total = function(index) {
    //console.log(index);
    var runningTotal = 0;
    var selectedTransactions = $scope.choices1.slice(0, index);
    angular.forEach($scope.choices1, function(transaction, index){
      runningTotal += parseFloat(transaction.purchaser_total);
    });
	//$scope.form.total = runningTotal
	if(isNaN(runningTotal)){
		return 0;}
	else{
		return runningTotal.toFixed(2);
		}
};
$scope.getsaler_total = function(index) {
   // console.log(index);
    var runningTotal = 0;
    var selectedTransactions = $scope.choices1.slice(0, index);
    angular.forEach($scope.choices1, function(transaction, index){
      runningTotal += parseFloat(transaction.saler_total);
    });
	//$scope.form.total = runningTotal
	if(isNaN(runningTotal)){
		return 0;}
	else{
		return runningTotal.toFixed(2);}
};
$scope.num_formate = function(value) {
	return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};
//////////////end total





  
   
});