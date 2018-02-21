<?php
namespace App\Exchanges;

use Auth;

class HitBTC {


  CONST HITBTC_API_URL = 'http://demo-api.hitbtc.com';
  CONST HITBTC_TRADING_API_URL_SEGMENT = '/api/1/trading/';

  CONST HITBTC_PAYMENTS_API_URL_SEGMENT = '/api/1/payment/transactions';

  private $_key, $_secret;



  private $_availableMethods = array(
      'balance',
      'trades',
      'transactions'
  );

  private $_postMethods = array(
      'new_order',
      'cancel_order'
  );

  public function __construct($key, $secret)
  {
      $this->_key = $key;
      $this->_secret = $secret;
      $this->_nonce =  time()*1E3;
  }



  public function __call($name, $arguments) {
      $methodPathParts = preg_split('/(?=[A-Z])/', $name);
      $methodPathParts = array_map(
          function($pathSegment) { return strtolower($pathSegment); },
          $methodPathParts
      );
      $method = implode('/', $methodPathParts);
      if(!in_array($method, $this->_availableMethods)){
          throw new \Exception( 'Method that you try to call doesn\'t exists!' );
      }
      return $this->_request($method, $arguments, in_array($method, $this->_postMethods));
  }



  private function _request($method, $arguments, $isPost = FALSE)
  {
      $requestUri = self::HITBTC_TRADING_API_URL_SEGMENT
          . $method
          . '?nonce=' . $this->_getNonce()
          . '&apikey=' . $this->_key;
      $arguments = sizeof($arguments) > 0 ? $arguments[0] : array();
      $params = http_build_query($arguments);
      if (strlen($params) && $isPost === FALSE) {
          $requestUri .= '&' . $params;
      }
      $ch = curl_init();
      curl_setopt_array($ch, array(
              CURLOPT_URL => self::HITBTC_API_URL . $requestUri,
              CURLOPT_CONNECTTIMEOUT => 10,
              CURLOPT_RETURNTRANSFER => 1
          ));
      if($isPost) {
          curl_setopt($ch, CURLOPT_POST, TRUE);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      }
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Signature: ' . $this->_signature($requestUri, $isPost ? $params : '')));
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
  }


  private function _signature($uri, $postData)
  {
      return strtolower(hash_hmac('sha512', $uri . $postData, $this->_secret));
  }


  private function _getNonce()
  {
      return $this->_nonce++;
  }



}



 ?>
