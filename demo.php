<?php
session_start();

require_once 'classes.php';

$apiSecretKey = '12345';
//$apiURL = "http://api.opencart-api.com";
// $apiURL = "http://demo.ecentrix.net/openchart";
$apiURL = "http://localhost/opencart";


$OpenCartRestClient = new OpenCartRestApi($apiSecretKey, $apiURL);

//1.STEP Get session id
if(!isset($_SESSION['sessionid'])){
    $OpenCartRestClient->printServiceTitle("1.STEP - Get session id");
    $session = $OpenCartRestClient->customer->getSession();
    $_SESSION['sessionid'] = $session["data"]["session"];
} else {
    $OpenCartRestClient->printServiceTitle("1.STEP - User already has session ID");
    $OpenCartRestClient->printServiceTitle("Your current session ID: ".$_SESSION['sessionid']);
}
echo "<h1>session id: ".$_SESSION['sessionid']."</h1>";
if(isset($_SESSION['sessionid']) && !empty($_SESSION['sessionid'])){
    //2. STEP
    //Login
    $OpenCartRestClient->printServiceTitle("2.STEP - Login");
    $email = 'coba@coba.com';
    $password = '12345';
    $OpenCartRestClient->customer->login($email, $password);

    //3.STEP
    //Add item to cart
    $OpenCartRestClient->printServiceTitle("3.STEP - Add item to cart");
    $productId = 34;
    $OpenCartRestClient->cart->add($productId);

    //Get cart (OPTIONAL)
    $OpenCartRestClient->printServiceTitle("Get cart (OPTIONAL)");
    $OpenCartRestClient->cart->cartProducts();

    //4.STEP
    //Get customer payment address
    $OpenCartRestClient->printServiceTitle("4.STEP - Get customer payment address");
    $OpenCartRestClient->payment->getAddresses();
    $defaultPaymentAddress = $OpenCartRestClient->curl->getResponse();

    //5.STEP
    //Select an existing payment address
    $OpenCartRestClient->printServiceTitle("5.STEP - Select an existing payment address");
    $addressId = $defaultPaymentAddress['data']['addresses'][0]['address_id'];
    $OpenCartRestClient->payment->setExistingAddress($addressId);

    //6.STEP
    //Get customer shipping address
    $OpenCartRestClient->printServiceTitle("6.STEP - Get customer shipping address");
    $OpenCartRestClient->shipping->getAddresses();
    $defaultShippingAddress = $OpenCartRestClient->curl->getResponse();

    //7.STEP
    //Select an existing shipping address
    $OpenCartRestClient->printServiceTitle("7.STEP - Select an existing shipping address");
    $addressId = $defaultShippingAddress['data']['addresses'][0]['address_id'];
    $OpenCartRestClient->shipping->setExistingAddress($addressId);

    //8.STEP
    //Get available payment methods
    $OpenCartRestClient->printServiceTitle("8.STEP - Get available payment methods");
    $OpenCartRestClient->payment->getMethods();
    $defaultPaymentAddress = $OpenCartRestClient->curl->getResponse();

    //9.STEP
    //Set payment method
    $OpenCartRestClient->printServiceTitle("9.STEP - Set payment method: COD");
    $methodCode = "cod";
    $OpenCartRestClient->payment->setMethod($methodCode);

    //10.STEP
    //Get available shipping methods
    $OpenCartRestClient->printServiceTitle("10.STEP - Get available shipping methods");
    $OpenCartRestClient->shipping->getMethods();
    $defaultPaymentAddress = $OpenCartRestClient->curl->getResponse();

    //11.STEP
    //Set shipping method
    $OpenCartRestClient->printServiceTitle("11.STEP - Set shipping method: FLAT");
    $methodCode = "flat.flat";
    $OpenCartRestClient->shipping->setMethod($methodCode);

    //12.STEP
    //Get Order details to confirm the order details
    $OpenCartRestClient->printServiceTitle("12.STEP - Get Order details to confirm the order details");
    $OpenCartRestClient->order->getOrderConfirmation();

    //13.STEP
    //Save and confirm order
    $OpenCartRestClient->printServiceTitle("13.STEP - Save and confirm order");
    $OpenCartRestClient->order->confirm();

    //Get product detail
    $OpenCartRestClient->product->getProducts();

    //14.STEP (OPTIONAL)
    //Get customer orders
    $OpenCartRestClient->printServiceTitle("14.STEP (OPTIONAL) - Get customer orders");
    $OpenCartRestClient->order->getOrders();

    $OpenCartRestClient->printServiceTitle("14.STEP (OPTIONAL) - Get customer orders by id");
    $OpenCartRestClient->order->getOrdersbyId(35);

} else {
    echo "lol";
}

//Some other sample API demo

//Register
/*
$firstname = 'Test';
$lastname = 'User';
$email = 'test@test.com';
$telephone = '5555566';
$fax = '';
$password = 12345;
$password_confirmation = 12345;
$company_id = '';
$company = '';
$country_id = '97';
$zone_id = '1433';
$city = 'Budapest';
$postcode = '8888';
$address_1 = 'Demo street';
$address_2 = '28';

$OpenCartRestClient->customer->register($firstname, $lastname, $email, $telephone, $fax,
                        $password, $password_confirmation, $company_id, $company,
                         $country_id, $zone_id, $city, $postcode, $address_1,
                         $address_2, $extra = array());
*/


//Logout
//$OpenCartRestClient->customer->logout();

//Get account details
//$OpenCartRestClient->customer->getAccount();

//Get account address
//$OpenCartRestClient->customer->getAccountAddress();

//Edit cart, update quantity
//$productKey = 24;
//$quantity = 5;
//$OpenCartRestClient->cart->edit($productKey, $quantity);

//remove item from cart
//$productKey = 24;
//$OpenCartRestClient->cart->remove($productKey);

//Get all products
//$OpenCartRestClient->product->getProducts();
//$OpenCartRestClient->curl->printResponse();

//Get product by ID
//$productId = 40;
//$OpenCartRestClient->product->getProductById($productId);
//$OpenCartRestClient->curl->printResponse();

//Get categories
//$OpenCartRestClient->category->getCategories();
//$OpenCartRestClient->curl->printResponse();

//Get category by ID
//$categoryId = 20;
//$OpenCartRestClient->category->getCategoryById($categoryId);
//$OpenCartRestClient->curl->printResponse();


//Add payment address
/*
$address = array(
    'firstname' => 'Test',
    'lastname' => 'Payment',
    'company' => '',
    'address_1' => 'Demo payment address',
    'address_2' => '444',
    'postcode' => 8888,
    'city' => 'Budapest',
    'zone_id' => '1433',
    'country_id' => '97'
);

$OpenCartRestClient->payment->addAddress($address);
*/

//Add shipping address
/*
$address = array(
    'firstname' => 'Test',
    'lastname' => 'Shipping',
    'company' => '',
    'address_1' => 'Demo Shipping address',
    'address_2' => '444',
    'postcode' => 8888,
    'city' => 'Budapest',
    'zone_id' => '1433',
    'country_id' => '97'
);

$OpenCartRestClient->shipping->addAddress($address);
*/