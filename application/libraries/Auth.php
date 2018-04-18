<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('vendor/autoload.php');

use Firebase\JWT\JWT;

/**
 * JWT Librería de encriptación en PHP
 * @subpackage      Librería
 * @category        Librería
 * @author          Rafael Hernández
 * @version         1.0.0
 */
class Auth{
  /**
   * Super Secret Key
   * @var string
   */
  private static $key;

  /**
   * Algoritmo(s) de encriptación
   * @var array
   */
  private static $encrypt;


  private static $aud = null;

  private function __construct(){
    $this->ci = get_instance();
    $key = $this->ci->config->item('jwt_key');
    $encrypt = $this->ci->config->item('jwt_algo');
  }

  /**
   * JWT encodeing function
   * @param  array $payload Array of data to be secured
   * @return string JWT String
   */
  public static function encode($payload){
    return JWT::encode($payload, self::$key);
  }

  /**
   * JWT decoding function
   * @param  String $data to JWT String
   * @return Array Decoded object
   */
  public static function decode($data){
    $time = time();
    $token = array(
      'iat' => $time,
      'exp' => $time + (60 * 60),
      'data' => $data,
      'aud' => self::aud()
    );
    return JWT::decode($token, self::$key, self::$encrypt);
  }

  /**
   * [check description]
   * @param  String $token Cadena JWT
   * @return Boolean
   */
  public static function check($token){
    if(empty($token)){
      throw new Exception('Invalid token supplied.');
    }else{
      $decode =  JWT::decode($token, self::$key, self::$encrypt);
      if($decode->aud !== self::$aud){
        //throw new Exception('Invalid user token.');
        return false;
      }else{
        return true;
      }
    }
  }

  private static function aud(){
    $aud = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $aud = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $aud = $_SERVER['REMOTE_ADDR'];
    }

    $aud .= @$_SERVER['HTTP_USER_AGENT'];
    $aud .= gethostname();

    return sha1($aud);
  }
}
