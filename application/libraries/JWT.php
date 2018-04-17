<?php
use Firebase\JWT\JWT;

class JWT{
  private static $key = $this->config->item('encriptyon_key');
  private static $encrypt =  ['HS256'];
  private static $aud = null;

  public static function encode($payload){
    return JWT::encode($payload, slef::$key);
  }

  public static function decode($token){
    return JWT::decode($token, self::$key, self::$encrypt);
  }

  public static function check($token){
    if(empty($token)){
      throw new Exception('Invalid token supplied.');
    }else{
      $decode =  JWT::decode($token, self::$key, self::$encrypt);
      if($decode->aud !== self::$aud){
        throw new Exception('Invalid user token.');
      }else{
        return true;
      }
    }
  }

  private static function Aud(){
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
