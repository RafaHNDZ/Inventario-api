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
  private static $key = 'S0p3r_S3c3t_k3y';

  /**
   * Algoritmo(s) de encriptación
   * @var array
   */
  private static $encrypt = array('HS256');


  private static $aud = null;

  public function __construct(){

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
  public static function decode($token){
    try {
      return JWT::decode($token, self::$key, self::$encrypt);
    } catch (Exception $e) {
      return null;
    }

  }

  /**
   * [check description]
   * @param  String $token Cadena JWT
   * @return Boolean
   */
  public static function check($token){
    if(empty($token)){
      //throw new Exception('Invalid token supplied.');
      return false;
    }else{
      $decode =  JWT::decode($token, self::$key, self::$encrypt);
      if($decode->aud !== self::aud()){
        //throw new Exception('Invalid user token.');
        return false;
      }else{
        return true;
      }
    }
  }

  public static function aud(){
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
