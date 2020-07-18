app.controller('AdminController', function(dataFactory,$scope,$http,$routeParams,site_url){
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
          dataFactory.httpRequest(site_url+'/accounts?search='+$scope.searchText+'&account_type='+$scope.account_type+'&page='+pageNumber).then(function(data) {
            $scope.data = data.account.data;
			$scope.accountType = data.account_type;
            $scope.totalItems = data.account.total;
			$scpoe.perpage = data.perpage;
			$scope.config = data.config;
			$scope.opts = [{ "value": "naam", "text": "نام" }, { "value": "jama", "text": "جمع" }];
			
          });
      }else{
        dataFactory.httpRequest(site_url+'/accounts?page='+pageNumber).then(function(data) {
          $scope.data = data.account.data;
		  $scope.accountType = data.account_type;
          $scope.totalItems = data.account.total;
		  $scope.perpage = data.perpage;
		  $scope.config = data.config;
		  $scope.total_accounts = data.total_accounts;
		  $scope.opts = [{ "value": "naam", "text": "نام" }, { "value": "jama", "text": "جمع" }];
		  //alertt();
		  console.log(data);
		  
		  //line

 //Main chart
    /*var ctxL = document.getElementById("lineChart-main").getContext('2d');
    var myLineChart = new Chart(ctxL, {
      type: 'line',
      data: {
        labels: ["January", "February", "March", "April", "May", "June", "July"],
        datasets: [{
          label: "My First dataset",
          fillColor: "#fff",
          backgroundColor: 'rgba(255, 255, 255, .3)',
          borderColor: 'rgba(255, 255, 255, .9)',
          data: [0, 10, 5, 2, 20, 30, 45],
        }]
      },
      options: {
        legend: {
          labels: {
            fontColor: "#fff",
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: true,
              color: "rgba(255,255,255,.25)"
            },
            ticks: {
              fontColor: "#fff",
            },
          }],
          yAxes: [{
            display: true,
            gridLines: {
              display: true,
              color: "rgba(255,255,255,.25)"
            },
            ticks: {
              fontColor: "#fff",
            },
          }],
        }
      }
    });*/



		  
		  
		  
		  
		  
		  
		  
		  
        });
      }
  }
  function alertt(){
	alert('asdfasfd');
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
  
  function accounts_detail1(id){
	  dataFactory.httpRequest('accounts_detail/'+id).then(function(data) {console.log(data);
          $scope.sales_items_detail = data.sales_items_detail;
		  $scope.direct_sales_items_detail = data.direct_sales_items_detail;
		  $scope.direct_sales_items_detail_jama = data.direct_sales_items_detail_jama;
		  $scope.sales_items_detail_jama = data.sales_items_detail_jama;
		  $scope.mall_roakker_detail = data.mall_roakker_detail;
		  $scope.mall_roakker_detail_jama = data.mall_roakker_detail_jama;
		  $scope.transection_detail_jama = data.transection_detail_jama;
		  $scope.transection_detail_naam = data.transection_detail_naam;
		  $scope.transection_detail_receive_jama = data.transection_detail_receive_jama;
		  $scope.transection_detail_receive_naam = data.transection_detail_receive_naam;
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.total_mall = data.total_mall;
		  $scope.total_mall_jama = data.total_mall_jama;
		  $scope.total_naqdi_naam = data.total_naqdi_naam;
		  $scope.total_naqdi_jama = data.total_naqdi_jama;
		  $scope.total_receive_naam = data.total_receive_naam;
		  $scope.total_receive_jama = data.total_receive_jama;
		  $scope.total_sale_naam = data.total_sale_naam;
		  $scope.total_sale_jama = data.total_sale_jama;
		  $scope.total_dsale_naam = data.total_dsale_naam;
		  $scope.total_dsale_jama = data.total_dsale_jama;
		  $scope.balance = data.balance;
		  console.log($scope.sales_items_detail);
		  //alert('asfd');
		  //getResultsPage(1);
      });
  }
  
  
  
  $scope.accounts_rokkarr = function(){
	   dataFactory.httpRequest('accounts_rokkar','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
	  //dataFactory.httpRequest('accounts_rokkar').then(function(data) {console.log(data);
          $scope.naam_jama_detail = data.naam_jama_detail;
		  $scope.total_jama = data.total_jama;
		  $scope.total_naam = data.total_naam;
		  $scope.total_balance = data.total_balance;
		  $scope.naam = data.naam;
		  $scope.jama = data.jama;
		  
		  //alert('asfd');
		  //getResultsPage(1);
      });
  }
 /* function accounts_rokkarr(){
	  dataFactory.httpRequest('accounts_rokkar','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
	  //dataFactory.httpRequest('accounts_rokkar').then(function(data) {console.log(data);
          $scope.naam_jama_detail = data.naam_jama_detail;
		  $scope.total_jama = data.total_jama;
		  $scope.total_naam = data.total_naam;
		  $scope.total_balance = data.total_balance;
		  $scope.naam = data.naam;
		  $scope.jama = data.jama;
		  
		  //alert('asfd');
		  //getResultsPage(1);
      });
  }*/
  
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
$scope.priceTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
        total += (product.price);
    }
    return total.toFixed(2);
}
$scope.ppriceTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
        total += (product.purchase_price);
    }
    return total.toFixed(2);
}
$scope.bachatTotal = function(detaildata){
    var total = 0;
    for(var i = 0; i < detaildata.length; i++){
        var product = detaildata[i];
        total += (product.price-product.purchase_price);
    }
    return total.toFixed(2);
}
$scope.parseFloat = function(value)
    {
        return parseFloat(value);
    }
$scope.running_seller_total = function(val1,val2){
		var value = parseFloat(val1)+parseFloat(val2);
    return value.toFixed(2);
    }
	
$scope.print1 = function(){
	window.print() ;
	/*var printContents = document.getElementById("print_").innerHTML;
	var popupWin = window.open('','_blank','width = 300,height = 300');
	popupWin.document.open();
	popupWin.document.write('<html><body onload="window.print()">' + printContents + '</body></html>');*/	
}


///////////////  account detail mall rokkar /////////////
$scope.mall_detaill = function(){
			dataFactory.httpRequest('mall_detail','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
	 // dataFactory.httpRequest('mall_detail/'+id).then(function(data) {console.log(data);          
		  $scope.mall_roakker_detail = data.mall_roakker_detail;
		  $scope.mall_roakker_detail_jama = data.mall_roakker_detail_jama;		  
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.total_mall = data.total_mall;
		  $scope.total_mall_jama = data.total_mall_jama;
		  
		  $scope.balance = data.balance;
      });
  }
/*function mall_detaill_(id){
	  dataFactory.httpRequest('mall_detail/'+id).then(function(data) {console.log(data);          
		  $scope.mall_roakker_detail = data.mall_roakker_detail;
		  $scope.mall_roakker_detail_jama = data.mall_roakker_detail_jama;		  
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.total_mall = data.total_mall;
		  $scope.total_mall_jama = data.total_mall_jama;
		  
		  $scope.balance = data.balance;
      });
  }*/
///////// end account detail mall rokkar///////////
/////// accounts detail naqdi roakker ////////////
$scope.naqdi_detaill = function(id){
	  ///dataFactory.httpRequest('naqdi_detail/'+id).then(function(data) {console.log(data);          
	  dataFactory.httpRequest('naqdi_detail','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
		  $scope.transection_detail_jama = data.transection_detail_jama;
		  $scope.transection_detail_naam = data.transection_detail_naam;
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.sales_items_detail = data.sales_items_detail;
		  $scope.sales_items_detail_jama = data.sales_items_detail_jama;
		  $scope.total_naqdi_naam = data.total_naqdi_naam;
		  $scope.total_naqdi_jama = data.total_naqdi_jama;
		  $scope.total_sale_naam = data.total_sale_naam;
		  $scope.total_sale_jama = data.total_sale_jama;
		  $scope.transection_detail_receive_naam = data.transection_detail_receive_naam;
		  $scope.transection_detail_receive_jama = data.transection_detail_receive_jama;
		  $scope.total_receive_naam = data.total_receive_naam;
		  $scope.total_receive_jama = data.total_receive_naam;
		  $scope.balance = data.balance;
      });
  }
function naqdi_detail_(id){
	  dataFactory.httpRequest('naqdi_detail/'+id).then(function(data) {console.log(data);          
		  $scope.transection_detail_jama = data.transection_detail_jama;
		  $scope.transection_detail_naam = data.transection_detail_naam;
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.sales_items_detail = data.sales_items_detail;
		  $scope.sales_items_detail_jama = data.sales_items_detail_jama;
		  $scope.total_naqdi_naam = data.total_naqdi_naam;
		  $scope.total_naqdi_jama = data.total_naqdi_jama;
		  $scope.total_sale_naam = data.total_sale_naam;
		  $scope.total_sale_jama = data.total_sale_jama;
		  $scope.transection_detail_receive_naam = data.transection_detail_receive_naam;
		  $scope.transection_detail_receive_jama = data.transection_detail_receive_jama;
		  $scope.total_receive_naam = data.total_receive_naam;
		  $scope.total_receive_jama = data.total_receive_naam;
		  $scope.balance = data.balance;
      });
  }
////// end accounts detail naqdi roakker  /////////
////// product detail /////////
$scope.product_detail = function(){	
	  //product_detaill($routeParams.id);
	 // dataFactory.httpRequest('product_detail/'+id).then(function(data) {console.log(data);
	 dataFactory.httpRequest('product_detail','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
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
 

/*$scope.product_detail_form = function()
{
	dataFactory.httpRequest('product_detail','POST',{},$scope.form).then(function(data) {
		//dataFactory.httpRequest('closing','POST',{},$scope.form).then(function(data) {
		
	});
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
*////// end product detail //////



$scope.num_formate = function(value) {
	return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

///////// services detail /////////////
///////////////  account detail mall rokkar /////////////
$scope.ser_mall_detaill = function(){
		dataFactory.httpRequest('ser_mall_detail','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
	  //dataFactory.httpRequest('ser_mall_detail/'+id).then(function(data) {console.log(data);          
		  $scope.mall_roakker_detail = data.mall_roakker_detail;
		  $scope.mall_roakker_detail_jama = data.mall_roakker_detail_jama;
		  
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.total_mall = data.total_mall;
		  $scope.total_mall_jama = data.total_mall_jama;
		  
		  $scope.balance = data.balance;
      });
  }
/*function ser_mall_detaill_(id){
	  dataFactory.httpRequest('ser_mall_detail/'+id).then(function(data) {console.log(data);          
		  $scope.mall_roakker_detail = data.mall_roakker_detail;
		  $scope.mall_roakker_detail_jama = data.mall_roakker_detail_jama;
		  
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.total_mall = data.total_mall;
		  $scope.total_mall_jama = data.total_mall_jama;
		  
		  $scope.balance = data.balance;
      });
  }*/
///////// end account detail mall rokkar///////////
/////// accounts detail naqdi roakker ////////////
$scope.ser_naqdi_detaill = function(id){
		dataFactory.httpRequest('ser_naqdi_detail','POST',{"id":$routeParams.id},$scope.form).then(function(data) {
	  //dataFactory.httpRequest('ser_naqdi_detail/'+id).then(function(data) {console.log(data);          
		  $scope.transection_detail_jama = data.transection_detail_jama;
		  $scope.transection_detail_naam = data.transection_detail_naam;
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.sales_items_detail = data.sales_items_detail;
		  $scope.sales_items_detail_jama = data.sales_items_detail_jama;
		  $scope.total_naqdi_naam = data.total_naqdi_naam;
		  $scope.total_naqdi_jama = data.total_naqdi_jama;
		  $scope.total_sale_naam = data.total_sale_naam;
		  $scope.total_sale_jama = data.total_sale_jama;
		  $scope.transection_detail_receive_naam = data.transection_detail_receive_naam;
		  $scope.transection_detail_receive_jama = data.transection_detail_receive_jama;
		  $scope.total_receive_naam = data.total_receive_naam;
		  $scope.total_receive_jama = data.total_receive_naam;
		  $scope.balance = data.balance;
      });
  }
/*function ser_naqdi_detail_(id){
	  dataFactory.httpRequest('ser_naqdi_detail/'+id).then(function(data) {console.log(data);          
		  $scope.transection_detail_jama = data.transection_detail_jama;
		  $scope.transection_detail_naam = data.transection_detail_naam;
		  $scope.account = data.account;		  
		  $scope.nnn = data.nnn;
		  $scope.sales_items_detail = data.sales_items_detail;
		  $scope.sales_items_detail_jama = data.sales_items_detail_jama;
		  $scope.total_naqdi_naam = data.total_naqdi_naam;
		  $scope.total_naqdi_jama = data.total_naqdi_jama;
		  $scope.total_sale_naam = data.total_sale_naam;
		  $scope.total_sale_jama = data.total_sale_jama;
		  $scope.transection_detail_receive_naam = data.transection_detail_receive_naam;
		  $scope.transection_detail_receive_jama = data.transection_detail_receive_jama;
		  $scope.total_receive_naam = data.total_receive_naam;
		  $scope.total_receive_jama = data.total_receive_naam;
		  $scope.balance = data.balance;
      });
  }*/
////// end accounts detail naqdi roakker  /////////
//////// end services detail /////////

$scope.printDiv = function() {
  var printContents = document.getElementById('detail').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;}</style><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
} 

$scope.printRokkar = function() {
  var printContents = document.getElementById('rokkar').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;} td{border:1px solid}</style><link rel="stylesheet" href="'+site_url+'/css/bootstrap.min.css"><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
} 

$scope.printmall_detail = function() {
  var printContents = document.getElementById('mall_detail').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;} td{border:1px solid}</style><link rel="stylesheet" href="'+site_url+'/css/bootstrap.min.css"><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
} 

//////////naqdi detail print ////////////
$scope.printnaqdi_detail = function() {
  var printContents = document.getElementById('naqdi_detail').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;} td{border:1px solid}</style><link rel="stylesheet" href="'+site_url+'/css/bootstrap.min.css"><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
} 
///////// end naqdi detail print ///////
//////// product detail  /////////
$scope.printprod_detail = function() {
  var printContents = document.getElementById('prod_detail').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;} td{border:1px solid}</style><link rel="stylesheet" href="'+site_url+'/css/bootstrap.min.css"><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
} 
/////// end product detail //////
////// services naqdi detail /////
$scope.printser_naqdi = function() {
  var printContents = document.getElementById('ser_naqdi_detail').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;} td{border:1px solid}</style><link rel="stylesheet" href="'+site_url+'/css/bootstrap.min.css"><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
} 
////// end services naqdi detail /////
/////// services mall detail  /////////
$scope.printser_detail = function() {
  var printContents = document.getElementById('ser_mall_detail').innerHTML;
  var popupWin = window.open('', '_blank');
  popupWin.document.open();
  popupWin.document.write('<html><head><style>button {display: none;}table {width: 100%;} td{border:1px solid}</style><link rel="stylesheet" href="'+site_url+'/css/bootstrap.min.css"><link rel="stylesheet" href="'+site_url+'/datatables/dataTables.bootstrap.css"><link rel="stylesheet" href="'+site_url+'/css/custom.css"></head><body onload="window.print()">' + printContents + '</body></html>');
  popupWin.document.close();
}
 /////// end services mall detail //////


   
});