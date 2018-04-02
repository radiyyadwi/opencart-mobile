<?php
	session_start();
	require_once 'classes.php';

	$apiSecretKey = '12345';
	$apiURL = "http://localhost/opencart";
	$OpenCartRestClient = new OpenCartRestApi($apiSecretKey, $apiURL);
	$cartContent = array();
	$subTotal = '';

	// todo: make email and password value dynamic
	$email = 'test@test.com';
    $password = '12345';

	// get session
	if(!isset($_SESSION['sessionid'])){
	    $session = $OpenCartRestClient->customer->getSession();
	    $_SESSION['sessionid'] = $session["data"]["session"];
	}

	if(isset($_SESSION['sessionid']) && !empty($_SESSION['sessionid'])){
	    //Login
	    $OpenCartRestClient->customer->login($email, $password);
		
		$response = $OpenCartRestClient->cart->cartProducts();
		$cartContent = $response['data']['products'];
		$subTotal = $response['data']['totals'][0]['text'];
	}

	function s35_writeItem(array $itemData) {
		$isFirst = true;
		echo "<div>";
			echo "<div class='item-detail-container'>";
				echo "<input class='cart-checkbox' type='checkbox'>";
				echo "<img class='cart-item-img' src='".$itemData['thumb']."'/>";
				echo "<div class='cart-item-desc'>";
					echo "<div class='item-seller'>Nama penjual</div>";
					echo "<div class='item-name'>".$itemData['name']."</div>";
					echo "<div class='item-subtitle'>subtitle</div>";
					echo "<div class='clothe-size-icon'>L</div> <div class='clothe-desc'>warna</div>";
				echo "</div>";
				echo "<i class='fa fa-times cart-del-button'></i>";
			echo "</div>";

			echo "<div class='item-secondary-container'>";
				echo "<div class='item-time-left'>sisa waktu : <span>hh:mm:ss</span></div>";
				echo "<div class='item-price'>".$itemData['price']."</div>";
			echo "</div>";
			echo "<hr>";
		echo "</div>";
	}

	function s35_writeBundle(array $itemData) {
		echo "<div class='bundle-container'>";
			echo "<div class='item-bundle-header'>";
				echo "<input class='cart-checkbox' type='checkbox'>";
				echo "<span class='item-header'>&nbsp;NAMA PENJUAL</span>";
				echo "<i class='fa fa-times cart-del-button'></i>";
			echo "</div>";
			echo "<div class='item-list'>";
				s35_writeItem($itemData);
			echo "</div>";
		echo "</div>";
	}
?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>screen_35</title>
	<link rel="stylesheet" type="text/css" href="css/screen_35.css">
	<!-- <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"> -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div>
		<form id="cart-form">
				<?php
					foreach ($cartContent as $itemData) {
						s35_writeBundle($itemData);
					}
				?>
		</form>
	</div>
	<div class="subtotal-container">
		<span class="subtotal-tag">Subtotal</span>
		<span id='subtotal-value'><?php echo $subTotal?></span>
	</div>
</body>
</html>

<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/screen_35.js"></script>