<?php

class AMember_API_Handler {
    /**
     * @const string of url target api endpoint and target API key
     */
    static $target_url;
    static $target_api_key;

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
     * Get current sub-domain
     *
     * @param null|string $url
     * @return null|string
     */
    function get_url_subdomain($url = null)
    {
        if ($url === null)
            $url = $_SERVER['HTTP_HOST'];
        $urlSegments = parse_url($url, PHP_URL_PATH);
        if ($urlSegments != null) {
            $urlHostSegments = explode('.', $urlSegments);
        } else {
            $urlSegments = parse_url($url, PHP_URL_HOST);
            $urlHostSegments = explode('.', $urlSegments);
        }
        if (count($urlHostSegments) > 2) {
            return $urlHostSegments[0];
        } else {
            return null;
        }
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

    /**
     * Validate if user has valid subscriptions or not.
     *
     * @param $curlJsonData string
     * @return false|array
     */
     function validate_user_subcription($curlJsonData)
     {
         $data = json_decode($curlJsonData);
         try{
             if ($data->ok === false) {
                 return false;
             } else {
                 if (count($data->subscriptions) > 0) {
                     // valid subscription id(s), please edit accordingly
                     $paid_subs_id = array(1,2,3,4,7,8,9,42,43,44,45,46,47);
                     $paid_subs_agent = array(1,2,3,4);
                     $paid_subs_reseller = array(7,8,9,42,43,44,45,46,47);
                     //$paid_subs_id = array(32, 33);
                     // valid subscription counter
                     $valid_subs_counter = 0;
                     $isAgent = false;
                     $isReseller = false;
                     foreach ($data->subscriptions as $id_subscriptions => $valid_date_subscription) {
                         $res = in_array(intval($id_subscriptions), $paid_subs_id);
                         if ($res && (time() <= strtotime($valid_date_subscription)) === true) {
                             // has valid paid subs id and still active
                             if(!$isAgent)
                                $isAgent = in_array(intval($id_subscriptions), $paid_subs_agent);
                             if(!$isReseller)
                                $isReseller = in_array(intval($id_subscriptions), $paid_subs_reseller);

                             $valid_subs_counter++;
                         }
                     }
                     // check how many paid subs have
                     if ($valid_subs_counter > 0) {
                         if($isAgent){
                             $data->status = 'agen';
                         } else if($isReseller){
                             $data->status  = 'reseller';
                         } else{
                             $data->status = 'unknown';
                         }
                         return $data;
                     } else {
                         return false;
                     }
                 } else {
                     return false;
                 }
             }
         }catch(\Exception $e){
             return false;
         }
     }




    /**
     * Get User information by domain
     *
     * @param $domain integer of user id
     * @return mixed json
     */
     function get_user_info_by_domain($domain){
       $fields = array(
           '_key' => $this->target_api_key,
           '_format' => 'json',
           '_filter[domain]' => $domain
       );
       $url = $this->target_url . 'users?' . http_build_query($fields);
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

     /**
      * Get User information by city
      *
      * @param $domain integer of user id
      * @return mixed json
      */
        function get_user_info_by_city($idCity){
        $fields = array(
            '_key' => $this->target_api_key,
            '_format' => 'json',
            '_filter[kota]' => $idCity,
            '_count' => 100,
            '_filter[status]' => 1
        );
        $url = $this->target_url . 'users?' . http_build_query($fields);
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

      /**
       * Get User information by province
       *
       * @param $domain integer of user id
       * @return mixed json
       */
         function get_user_info_by_province($idProvince){
         $fields = array(
             '_key' => $this->target_api_key,
             '_format' => 'json',
             '_filter[provinsi]' => $idProvince,
             '_count' => 100,
            '_filter[status]' => 1
         );
         $url = $this->target_url . 'users?' . http_build_query($fields);
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

    /**
     * Get User information
     *
     * @param $user_id integer of user id
     * @return mixed json
     */
    function get_user_info($user_id)
    {
        $fields = array(
            '_key' => $this->target_api_key,
            '_format' => 'json'
        );
        $url = $this->amember_url_get_user_data($user_id) . '?' . http_build_query($fields);
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

    /**
     * Get User information
     *
     * @param $email integer of user id
     * @return mixed json
     */
    function get_user_by_email($email)
    {
        $fields = array(
            '_key' => $this->target_api_key,
            '_format' => 'json',
            '_filter[email]' => $email
        );
        $url = $this->target_url  . 'users?' . http_build_query($fields);
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

    /**
     * Validate user information
     *
     * @param $curlJsonData string
     * @return false|array
     */
    function validate_user_information($curlJsonData)
    {
        /**
         * @var array $user_data
         */
        $data = json_decode($curlJsonData);
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $user_data = array(
                    'id' => $value->user_id,
                    'login' => $value->login,
                    'name' => $value->name_f . ' ' . $value->name_l,
                    'email' => $value->email,
                    'mobile' => (isset($value->hp)) ? ((strlen($value->hp) > 0) ? $value->hp : false) : false,
                    'bb' => (isset($value->bb)) ? ((strlen($value->bb) > 0) ? $value->bb : false) : false,
                    'nobanklain' => (isset($value->nobanklain)) ? ((strlen($value->nobanklain) > 0) ? $value->nobanklain : false) : false,
                    'nomandiri' => (isset($value->nomandiri)) ? ((strlen($value->nomandiri) > 0) ? $value->nomandiri : false) : false,
                    'nobca' => (isset($value->nobca)) ? ((strlen($value->nobca) > 0) ? $value->nobca : false) : false,
                    'nobni' => (isset($value->nobni)) ? ((strlen($value->nobni) > 0) ? $value->nobni : false) : false,
                    'nobri' => (isset($value->nobri)) ? ((strlen($value->nobri) > 0) ? $value->nobri : false) : false,
                    'atasnamabanklain' => (isset($value->atasnamabanklain)) ? ((strlen($value->atasnamabanklain) > 0) ? $value->atasnamabanklain : false) : false,
                    'namabanklain' => (isset($value->namabanklain)) ? ((strlen($value->namabanklain) > 0) ? $value->namabanklain : false) : false,
                    'namamandiri' => (isset($value->namamandiri)) ? ((strlen($value->namamandiri) > 0) ? $value->namamandiri : false) : false,
                    'namabca' => (isset($value->namabca)) ? ((strlen($value->namabca) > 0) ? $value->namabca : false) : false,
                    'namabni' => (isset($value->namabni)) ? ((strlen($value->namabni) > 0) ? $value->namabni : false) : false,
                    'namabri' => (isset($value->namabri)) ? ((strlen($value->namabri) > 0) ? $value->namabri : false) : false,
                    'subdistrict' => (isset($value->kecamatan)) ? ((strlen($value->kecamatan) > 0) ? $value->kecamatan :  false) : false,
                    'city' => (isset($value->kota)) ? ((strlen($value->kota) > 0) ? $value->kota : false) : false,
                    'province' => (isset($value->provinsi)) ? ((strlen($value->provinsi) > 0) ? $value->provinsi : false) : false,
                    'address' => (isset($value->alamat)) ? ((strlen($value->alamat) > 0) ? $value->alamat : false) : false,
                    'zipcode' => (isset($value->kodepos)) ? ((strlen($value->kodepos) > 0) ? $value->kodepos : false) : false,
                    'videomd' => (isset($value->videomd)) ? ((strlen($value->videomd) > 0) ? $value->videomd : false) : false,
                    'avatar' => (isset($value->avatar)) ? ((strlen($value->avatar) > 0) ? $value->avatar : false) : false,
                    'kodepos' => (isset($value->kodepos)) ? ((strlen($value->kodepos) > 0) ? $value->kodepos : false) : false,
                );
            }
            return $user_data;
        } else {
            return false;
        }
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

    function isAgent($data){
      $paid_sub_agent = array(1,2,3,4);
      foreach ($data->subscriptions as $id_subscriptions => $valid_date_subscription) {
          $res = in_array(intval($id_subscriptions), $paid_sub_agent);
          if ($res && (time() <= strtotime($valid_date_subscription)) === true) {
              // has valid paid subs id and still active
              return true;
          }
      }
      return false;
    }

    function isReseller($data){
      $paid_sub_reseller = array(7,8,9);
      foreach ($data->subscriptions as $id_subscriptions => $valid_date_subscription) {
          $res = in_array(intval($id_subscriptions), $paid_sub_reseller);
          if ($res && (time() <= strtotime($valid_date_subscription)) === true) {
              // has valid paid subs id and still active
              return true;
          }
      }
      return false;
    }
}
?>
