<?php
	session_start();

	require_once 'classes.php';

	$apiSecretKey = '12345';
	$apiURL = "http://localhost/opencart";
	$OpenCartRestClient = new OpenCartRestApi($apiSecretKey, $apiURL);
	$Orderitem = array();


	$email = 'coba@coba.coms';
    $password = '12345';

    if(!isset($_SESSION['sessionid'])){
    	$session = $OpenCartRestClient->customer->getSession();
	    $_SESSION['sessionid'] = $session["data"]["session"];
    }

    if(isset($_SESSION['sessionid']) && !empty($_SESSION['sessionid'])){
	    $OpenCartRestClient->customer->login($email, $password);
		
		$response = $OpenCartRestClient->order->getOrders();
		$response2 = $OpenCartRestClient->product->getProducts();
		$orderContent = $response['data'];
		$itemdetails = $response2['data'];
	}

	//list all item with 
	function s38_listitem (array $listitem, array $itemdetails, OpenCartRestApi $API) {
		echo "<div class='item-container'>";
        echo "<div class='date'>".$listitem['date_added']."</div>";
        echo "<div class='orderid'> Order No : ".$listitem['order_id']."</div>";


        //get order details by id
        $responseorder = $API->order->getOrdersbyId($listitem['order_id']);

        $orderproduct = $responseorder['data']['products'];

        $i=0;
        foreach ($itemdetails as $a) {
          $count = count($orderproduct);
          foreach ($orderproduct as $x) {
            if ($a['product_id'] == $x['product_id']) {
               echo "<img class='itemimage' src='".$a['image']."'>";
               echo "<div class='brand'>".$a['manufacturer']."</div>";
               echo "<div class='itemname'>".$a['name']."</div>";
               echo "<div class='itemsource'>".$a['meta_title']."</div>";
               echo "<div class='size'> L </div>";
               echo "<div class='model'>".$a['model']."</div>";
               if ($i != $count-1) {
                echo "<br>";
               }
               $i+=1;
            } 
          }
        }
        echo "<div class='price'>".$listitem['total']."</div>";
        echo "<div class='currency'>".$listitem['currency_code']."</div>";
        echo "</div>";
	}

?>


<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
<title>Screen_38</title>
<body>
<div id= "top-taskbar">
	<i class="fa fa-chevron-left" style="color: grey"></i> Daftar Transaksi
</div>

<style>

        #file_content {width: 100%;}
	     ul#tabs { list-style-type: none; margin: 0; padding: 0 0 0.3em 0; width: 100%;}
        ul#tabs li.tab1 { display: table-cell; float: left; width: 15%}
        ul#tabs li.tab2 { display: table-cell; float: left; width: 25%}
        ul#tabs li.tab3 { display: table-cell; float: left; width: 15%}
        ul#tabs li.tab4 { display: table-cell; float: left; width: 25%}
        ul#tabs li { display: table-cell; float: left; width: 20%}
        ul#tabs li a { display: block; color: #42454a; background-color: #dedbde; border: 1px solid #c9c3ba; border-bottom: none; padding: 0.3em; text-decoration: none; margin:0; font-size: 14px; text-align: center}
        ul#tabs li a:hover { background-color: #f1f0ee;}
        ul#tabs li a.selected {
          color: #ffa500;
          background-color: #fff;
          font-weight: bold;
          border-bottom: 2px solid white;
        }

        div.tabContent { border: 1px solid #c9c3ba; padding: 0.5em; background-color: #fff; margin:0; height: 100%}
        div.tabContent.hide { display: none; }

        ul#tabs li a {
        	margin-right: 0;
        }

        .search-container input[name="search"]{border: none; text-align: center;width: 90%}

        .search-container {
			top: 0px;
			margin-top: 20px;  
			padding: 0px 2px 2px 3px;    
			border-width: 2px;
			border-bottom: 1px grey solid;
			width: 100%;
        }


	    #file_content td {
	      padding: 5px;
	    }

	    .itemimage {
	    	width : 60px; 
	    	height : 60px;
	    	float: left;
	    	margin-bottom: 10px;
	    	margin-top: 10px;
	    	margin-right: 15px;
	    }

      .date {
        font-size: 10px;
        color: grey;
      }

      .itemsource {
        font-size: 12px;
        color: grey;
      }

	    .size {
	    	float:left;
	    }

	    .model {
	    	float:left;
        margin-left: 10px;
	    }

      .size{
        margin-right: 10px;
        background-color: black;
        color:white;  
      }

	    .currency {
	    	text-align:right;
	    }

	    .price {
	    	float:right;
	    }

	    .item-container {
	    	border-bottom: 2px grey solid;
	    }

</style>

        <table id="file_content" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                      <ul id="tabs">
                          <li class="tab1"><a href="#semua">Semua</a></li>
                          <li class="tab2"><a href="#pembayaran">Pembayaran</a></li>
                          <li class="tab3"><a href="#lunas">Lunas</a></li>
                          <li class="tab4"><a href="#komplain">Komplain</a></li>
                          <li class="tab5"><a href="#selesai">Selesai</a></li>

                      </ul>

                      <div class="tabContent" id="semua">
                      	<div class="search-container">
                      		<i class="fa fa-search"> </i>
	      					        <input type="text" placeholder="Cari Transaksi.." name="search">
	      				        </div>

                        <div class="content-semua">
      						<?php
								foreach ($orderContent as $listitem) {
									s38_listitem($listitem, $itemdetails, $OpenCartRestClient);
								}
							?>
                        </div>
                      </div>

                      <div class="tabContent" id="pembayaran">
                        <h2>Advantages of tabs</h2>
                        <div>
                          <p>JavaScript tabs are great if your Web page contains a large amount of content.</p>
                          <p>They're also good for things like multi-step Web forms.</p>
                        </div>
                      </div>

                      <div class="tabContent" id="lunas">
                        <h2>Using tabs</h2>
                        <div>
                          <p>Click a tab to view the tab's content. Using tabs couldn't be easier!</p>
                        </div>
                      </div>

                      <div class="tabContent" id="komplain">
                        <h2>Using tabs</h2>
                        <div>
                          <p>Click a tab to view the tab's content. Using tabs couldn't be easier!</p>
                        </div>
                      </div>

                      <div class="tabContent" id="selesai">
                        <h2>Using tabs</h2>
                        <div>
                          <p>Click a tab to view the tab's content. Using tabs couldn't be easier!</p>
                        </div>
                      </div>

                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
        </table>
</body>
</html>
<script type="text/javascript" src="script.js"></script>
