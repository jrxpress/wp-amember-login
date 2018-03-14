<?php

class AMember_API_Handler {
    /**
     * @const string of url target api endpoint and target API key
     */
    public $target_url;
    public $target_api_key;

    public function __construct() {
  		$this->directory_path = plugin_dir_path( dirname( __FILE__ ) );
  		$this->directory_url  = plugin_dir_url( dirname( __FILE__ ) );

      $this->target_url = get_option('wp_amember_login_api_url');
      $this->target_api_key = get_option('wp_amember_login_api_key');
  	}

    /**
     * curl setting options, please refer to this:
     * @link http://php.net/manual/en/function.curl-setopt.php
     **/
    static $REST_CONNECTTIMEOUT = 30;
    static $REST_TIMEOUT = 10;
    /**
     * Return aMember URL for API Endpoint of check username access by username
     *
     * @return string
     */
    public static function amember_url_check_username()
    {
        return $this->target_url . "check-access/by-login";
    }
    /**
     * Return aMember URL for API Endpoint of get username information
     *
     * @param $user_id integer of user id on aMember
     * @return string
     */
    public static function amember_url_get_user_data($user_id)
    {
        return $this->target_url . "users/" . $user_id;
    }

    /**
     * Get User Subscriptions information
     *
     * @param $username string aMember Username
     * @return mixed json
     */
    function get_user_subscription_info($username)
    {
        $fields = array(
            '_key' => $this->target_api_key,
            '_format' => 'json',
            'login' => $username,
        );
        $url = $this->amember_url_check_username() . '?' . http_build_query($fields);
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => self::$REST_CONNECTTIMEOUT,
            CONNECTION_TIMEOUT => self::$REST_TIMEOUT
        ));
        $result = curl_exec($curl);
        //dd(curl_error($curl));
        curl_close($curl);
        return $result;
    }

    function login($username, $password){
        $fields = array(
            '_key' => $this->target_api_key,
            'login'=> $username,
            'pass' => $password
        );
        $url = $this->target_url . 'check-access/by-login-pass?' . http_build_query($fields);
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => self::$REST_CONNECTTIMEOUT,
            CONNECTION_TIMEOUT => self::$REST_TIMEOUT
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    function login_without_password($username){
        $fields = array(
            '_key' => $this->target_api_key,
            'login'=> $username,
        );
        $url = $this->target_url . 'check-access/by-login?' . http_build_query($fields);
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => self::$REST_CONNECTTIMEOUT,
            CONNECTION_TIMEOUT => self::$REST_TIMEOUT
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        return $this->validate_user_subcription($result);
    }

    function get_all_products()
    {
        $fields = array(
            '_key' => $this->target_api_key,
            '_format' => 'json',
            '_count' => '100'
        );
        $url = $this->target_url . 'products?' . http_build_query($fields);
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_CONNECTTIMEOUT => self::$REST_CONNECTTIMEOUT,
            CONNECTION_TIMEOUT => self::$REST_TIMEOUT
        ));
        $result = $this->product_attr_filter(json_decode(curl_exec($curl)));
        //dd(curl_error($curl));
        curl_close($curl);
        return ($result);
    }

    function product_attr_filter($arr){
      $products = [];
      //unset($arr['_total']);

      foreach($arr as $key => $item){
        if($key == '_total')
          continue;

        $product = new stdClass();
        $product->title = $item->title;
        $product->id = $item->product_id;
        array_push($products, $product);

      }
      return $products;
    }
}
?>
