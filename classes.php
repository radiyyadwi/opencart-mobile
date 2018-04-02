<?php
class CurlRequest
{
    private $url;
    private $merchantID;
    private $postData = array();
    private $response = '';
    private $responseStatus = '';
    private $handle;
    private $isDebug = 0;

    public function __construct($merchantID, $url)
    {
        $this->merchantID = $merchantID;
        $this->url = $url;
    }

    public function makePostRequest()
    {
        $this->makeRequest("POST");
    }

    public function makeRequest($method = "GET")
    {
        $this->handle = curl_init($this->url);
        curl_setopt($this->handle, CURLOPT_HEADER, false);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);

        if($this->isDebug){
            $this->printRequestInfo($method);
        }

        if ($method != "GET") {
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($this->postData));

            if($this->isDebug){
                $this->printRequest();
            }
        }

        $headers = array(
            'X-Oc-Merchant-Id: ' . $this->merchantID,
            'Content-Type: application/json',
        );

        if (isset($_SESSION['sessionid']) && !empty($_SESSION['sessionid'])) {
            $headers[] ='X-Oc-Session: ' . $_SESSION['sessionid'];
        }

        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        $this->response = curl_exec($this->handle);
        $this->responseStatus = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

        if($this->isDebug){
            $this->printResponse();
        }

        $this->postData = array();

        curl_close($this->handle);
    }

    public function makePutRequest()
    {
        $this->makeRequest("PUT");
    }

    public function makeDeleteRequest()
    {
        $this->makeRequest("DELETE");
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setData($postData)
    {
        $this->postData = $postData;
    }

    public function getResponseStatus()
    {
        return $this->responseStatus;
    }

    public function getResponse()
    {
        return json_decode($this->response, true);
    }

    public function printResponse()
    {
        $response = json_decode($this->response, true);
        echo("<h4>Response:</h4>");
        echo("<pre".(empty($response['success']) ? ' style="background:#fb0000;color:#fff;"' : '').">");
        print_r($response);
        echo("</pre><hr>");
    }

    public function printRequest()
    {
        echo("<h4>Request:</h4>");
        echo("<pre>");
        echo json_encode($this->postData, JSON_PRETTY_PRINT);
        echo("</pre>");
    }

    public function printRequestInfo($method)
    {
        echo("<h4>Method: ".$method."</h4>");
        echo("<h4>URL: ".$this->url."</h4>");
    }

    public function getRawResponse()
    {
        return $this->response;
    }
}

class Base
{
    /**
     * @var OpenCartRestApi
     */
    public $restAPI;

    /**
     * @var CurlRequest
     */
    protected $curl;

    public function __construct($restAPI)
    {
        $this->restAPI = $restAPI;
        $this->curl = $restAPI->curl;
    }
}

class Cart extends Base
{

    public function add($product, $quantity = 1, $option = array())
    {

        $postData = array();

        if (is_numeric($product) && is_numeric($quantity)) {
            $postData['product_id'] = $product;
            $postData['quantity'] = $quantity;
            $postData['option'] = $option;
        } else {
            throw new Exception('Invalid product information for Cart->add()');
        }

        $this->curl->setUrl($this->restAPI->getUrl('rest/cart/cart'));
        $this->curl->setData($postData);
        $this->curl->makePostRequest();

        return $this->curl->getResponse();
    }

    public function edit($key, $quantity)
    {
        if (empty($key) || empty($quantity)) {
            throw new Exception('Key and quantity cannot be empty for Cart->edit()');
        }

        $postData = array(
            'key' => $key,
            'quantity' => $quantity
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/cart/updatecartv2'));
        $this->curl->setData($postData);
        $this->curl->makePutRequest();
        return $this->curl->getResponse();
    }

    public function remove($key)
    {
        if (empty($key)) {
            throw new Exception('Key cannot be empty for Cart->remove()');
        }

        $postData = array(
            'key' => $key
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/cart/cart'));
        $this->curl->setData($postData);
        $this->curl->makeDeleteRequest();
        return $this->curl->getResponse();
    }

    public function cartProducts()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/cart/cart'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }
}

class Payment extends Base
{

    public function addAddress($address)
    {
        $postData = array(
            'firstname' => $address['firstname'],
            'lastname' => $address['lastname'],
            'company' => $address['company'],
            'address_1' => $address['address_1'],
            'address_2' => $address['address_2'],
            'postcode' => $address['postcode'],
            'city' => $address['city'],
            'zone_id' => $address['zone_id'],
            'country_id' => $address['country_id'],
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/payment_address/paymentaddress'));
        $this->curl->setData($postData);
        $this->curl->makePostRequest();

        return $this->curl->getResponse();
    }

    public function getAddresses()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/payment_address/paymentaddress'));
        $this->curl->makeRequest();

        return $this->curl->getResponse();
    }


    public function setExistingAddress($addressId)
    {
        if (empty($addressId)) {
            throw new Exception("Payment address cannot be empty for Payment->setExistingAddress()");
        }

        $postData = array(
            'address_id' => $addressId
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/payment_address/paymentaddress&existing=1'));

        $this->curl->setData($postData);
        $this->curl->makePostRequest();

        return $this->curl->getResponse();
    }

    public function getMethods()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/payment_method/payments'));
        $this->curl->makeRequest();

        return $this->curl->getResponse();
    }

    public function setMethod($payment_method)
    {
        if (empty($payment_method)) {
            throw new Exception("Payment method cannot be empty for Payment->method()");
        }

        $postData = array(
            'payment_method' => $payment_method,
            'agree' => 1,
            'comment' => ''
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/payment_method/payments'));
        $this->curl->setData($postData);
        $this->curl->makePostRequest();

        return $this->curl->getResponse();
    }
}

class Shipping extends Base
{
    public function addAddress($address)
    {
        $postData = array(
            'firstname' => $address['firstname'],
            'lastname' => $address['lastname'],
            'company' => $address['company'],
            'address_1' => $address['address_1'],
            'address_2' => $address['address_2'],
            'postcode' => $address['postcode'],
            'city' => $address['city'],
            'zone_id' => $address['zone_id'],
            'country_id' => $address['country_id'],
            'shipping_address' => "new"
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/shipping_address/shippingaddress'));
        $this->curl->setData($postData);
        $this->curl->makePostRequest();
        return $this->curl->getResponse();
    }

    public function getAddresses()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/shipping_address/shippingaddress'));
        $this->curl->makeRequest();

        return $this->curl->getResponse();
    }

    public function setMethod($shipping_method)
    {
        if (empty($shipping_method)) {
            throw new Exception("Shipping method cannot be empty for Shipping->method()");
        }

        $postData = array(
            'shipping_method' => $shipping_method,
            'comment' => ''
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/shipping_method/shippingmethods'));
        $this->curl->setData($postData);
        $this->curl->makePostRequest();
        return $this->curl->getResponse();
    }

    public function getMethods()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/shipping_method/shippingmethods'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }

    public function setExistingAddress($addressId)
    {
        if (empty($addressId)) {
            throw new Exception("Shipping address cannot be empty for Shipping->setExistingAddress()");
        }

        $postData = array(
            'address_id' => $addressId
        );

        $this->curl->setUrl($this->restAPI->getUrl('rest/shipping_address/shippingaddress&existing=1'));
        $this->curl->setData($postData);
        $this->curl->makePostRequest();

        return $this->curl->getResponse();
    }
}


class Product extends Base
{

    public function getProducts()
    {
        $this->curl->setUrl($this->restAPI->getUrl('feed/rest_api/products'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }

    public function getProductById($id)
    {

        if (empty($id)) {
            throw new Exception("Product ID cannot be empty");
        }

        $this->curl->setUrl($this->restAPI->getUrl('feed/rest_api/products&id=' . $id));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }
}


class Category extends Base
{

    public function getCategories()
    {
        $this->curl->setUrl($this->restAPI->getUrl('feed/rest_api/categories'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }

    public function getCategoryById($id)
    {

        if (empty($id)) {
            throw new Exception("Category ID cannot be empty");
        }

        $this->curl->setUrl($this->restAPI->getUrl('feed/rest_api/categories&id=' . $id));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }
}


class Order extends Base
{

    public function getOrders()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/order/orders'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }

    public function getOrdersbyId($orderid) {
        $this->curl->setUrl($this->restAPI->getUrl('rest/order/orders&id=' . $orderid));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }

    public function getOrderConfirmation()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/confirm/confirm'));
        $this->curl->makePostRequest();
        return $this->curl->getResponse();
    }


    public function confirm()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/confirm/confirm'));
        $this->curl->makePutRequest();
        return $this->curl->getResponse();
    }
}

class Customer extends Base
{

    public function getSession()
    {
        $this->curl->setUrl($this->restAPI->getUrl('feed/rest_api/session'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }

    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            throw new Exception("Username and password cannot be empty");
        }

        $this->curl->setUrl($this->restAPI->getUrl('rest/login/login'));
        $this->curl->setData(array(
            'email' => $username,
            'password' => $password
        ));

        $this->curl->makePostRequest();

        $response = $this->curl->getResponse();

        if (isset($response['success'])) {
            return true;
        }

        return false;
    }

    public function logout()
    {

        $this->curl->setUrl($this->restAPI->getUrl('rest/logout/logout'));
        $this->curl->makePostRequest();
        return $this->curl->getResponse();
    }


    public function forgotten($email)
    {
        if (empty($email)) {
            throw new Exception("Email cannot be empty");
        }

        $this->curl->setUrl($this->restAPI->getUrl('rest/forgotten/forgotten'));

        $this->curl->setData(array(
            'email' => $email
        ));

        $this->curl->makePostRequest();

        return $this->curl->getResponse();
    }

    public function register($firstname = '', $lastname = '', $email = '', $telephone = '', $fax = '',
                             $password = '', $password_confirmation, $company_id = '', $company = '',
                             $country_id = '', $zone_id = '', $city = '', $postcode = '', $address_1 = '',
                             $address_2 = '', $extra = array())
    {
        $postData = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'telephone' => $telephone,
                'fax' => $fax,
                'password' => $password,
                'confirm' => $password_confirmation,
                'company_id' => $company_id,
                'company' => $company,
                'country_id' => $country_id,
                'zone_id' => $zone_id,
                'city' => $city,
                'postcode' => $postcode,
                'address_1' => $address_1,
                'address_2' => $address_2,
                'tax_id' => 1,
                'agree' => 1,
            ) + $extra;

        $this->curl->setUrl($this->restAPI->getUrl('rest/register/register'));
        $this->curl->setData($postData);
        $this->curl->makePostRequest();

        return $this->curl->getResponse();
    }

    public function getAccount()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/account/account'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }

    public function getAccountAddress()
    {
        $this->curl->setUrl($this->restAPI->getUrl('rest/account/address'));
        $this->curl->makeRequest();
        return $this->curl->getResponse();
    }
}

class OpenCartRestApi
{

    /**
     * @var CurlRequest
     */
    public $curl;
    /**
     * @var Cart
     */
    public $cart;
    /**
     * @var Payment
     */
    public $payment;
    /**
     * @var Shipping
     */
    public $shipping;
    /**
     * @var Customer
     */
    public $customer;
    /**
     * @var Product
     */
    public $product;
    /**
     * @var Category
     */
    public $category;

    /**
     * @var Order
     */
    public $order;

    private $url;

    public function __construct($merchantID, $url)
    {
        $this->url = rtrim('http://' . preg_replace('/^https?\:\/\//', '', $url), '/') . '/index.php?route=';
		// $this->url = rtrim('https://' . preg_replace('/^https?\:\/\//', '', $url), '/') . '/index.php?route=';

        $this->curl = new CurlRequest($merchantID, $url);
        $this->customer = new Customer($this);
        $this->cart = new Cart($this);
        $this->payment = new Payment($this);
        $this->shipping = new Shipping($this);
        $this->product = new Product($this);
        $this->category = new Category($this);
        $this->order = new Order($this);
    }

    public function getUrl($method)
    {
        return $this->url . $method;
    }

    public function printServiceTitle($title)
    {
        echo("<h3>".$title."</h3>");
    }
}