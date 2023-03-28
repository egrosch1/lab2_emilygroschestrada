<?php
require_once '../model/Database.php';
require_once '../model/ProductTable.php';
require_once '../model/CustomerTable.php';
require_once '../model/RegistrationTable.php';
require_once '../util/Util.php';

class CustomerController {
    private $action;
    
    public function __construct() {
        
         $this->startSession();

        $https = filter_input(INPUT_SERVER, 'HTTPS');
        if (!$https) {
            $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $url = 'https://' . $host . $uri;
            header("Location: " . $url);
            exit();
        }
        $this->action = '';
        $this->db = new Database();
        if (!$this->db->isConnected()) {
            $error_message = $this->db->getErrorMessage();
            include '../view/errors/database_error.php';
            exit();
        }
    }
    
    public function invoke() {
        // get the action to be processed
        $this->action = Util::getAction($this->action);
        
        switch ($this->action) {
            case 'customer_login':
                $this->processCustomerLogin();
                break;
            case 'get_customer':
                $this->processGetCustomer();
                break;
            case 'register_product':
                $this->processRegisterProduct();
                break;
            case 'Logout':
                $this->processLogout();
                break;
            default:
                $this->processCustomerLogin();
                break;
        }
    }
    
    /****************************************************************
     * Process Request
     ***************************************************************/
    private function processCustomerLogin() {
        if (!isset($_SESSION['is_valid_customer'])) {
            $email = '';
            $password = '';
            $message = '';
            include '../view/customer/customer_login.php';
        } else {
            $customer_table = new CustomerTable($this->db);
            $customer = $customer_table->get_customer_by_email($_SESSION['email']);
            $product_table = new ProductTable($this->db);
            $products = $product_table->get_products();
            include '../view/customer/product_register.php';
        }
    }
    
    private function processGetCustomer() {
        $email = filter_input(INPUT_POST, 'email');
        $customer_table = new CustomerTable($this->db);
        $customer = $customer_table->get_customer_by_email($email);
        if ($customer == false) {
            $error = "Invalid email address";
            include('../view/errors/error.php');
        } else {
            $product_table = new ProductTable($this->db);
            $products = $product_table->get_products();
            include '../view/customer/product_register.php';
        }
    }
    
    private function processRegisterProduct() {
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $product_code = filter_input(INPUT_POST, 'product_code');
        $registration_table = new RegistrationTable($this->db);
        $registration_table->add_registration($customer_id, $product_code);
        $message = "Product ($product_code) was registered successfully.";
        include '../view/customer/product_register.php';
    }
    
     private function processLogout() {
        unset($_SESSION['is_valid_customer']);
        $this->deleteCookie();
        if (!isset($_SESSION['is_valid_admin'])) {
            $_SESSION = array();
            session_destroy();
        }
        $message = 'You have successfully logged out.';
        $email = '';
        $password = '';
        include '../view/customer/customer_login.php';
    }

    private function deleteCookie() {
        $name = 'customer';
        $expire = strtotime('-1 year');
        $params = session_get_cookie_params();
        $path = $params['path'];
        $domain = $params['domain'];
        $secure = $params['secure'];
        $httponly = $params['httponly'];
        setcookie($name, '', $expire, $path, $domain, $secure, $httponly);
    }

    private function startSession() {
        session_start();
    }

}

?>