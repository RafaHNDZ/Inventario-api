<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH.'libraries/REST_Controller.php');

class Usuario extends REST_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('Usuario_model', 'Usuario');
  }

  public function index_get(){

  }

  public function login_post(){
    $this->form_validation->set_rules('email', 'E-Mail', 'trim|required|valid_email');
    $this->form_validation->set_rules('password', 'Contraseña', 'trim|required');

    if($this->form_validation->run() === FALSE){
      $response = array(
        'status' => 201,
        'errors' => $this->validation_errors()
      );
    }else{
      $usuario = $this->Usuario->Login($this->post('email'), $this->post('password'));
      if($usuario === null){
        $response = array(
          'status' => 404,
          'message' => 'Usuario no valido'
        );
      }else{
        if($usuario === false){
          $response = array(
            'status' => 401,
            'message' => 'Contraseña incorrecta'
          );
        }else{
          $this->load->library('Auth');
          $time = time();
          $jwt = array(
            'iat' => $time,
            'eat' => $time + (60 * 60) * 12,
            'aud' => $this->auth->aud(),
            'data' => $usuario
          );
          $token = $this->auth->encode($jwt);
          $response = array(
            'status' => 200,
            'error' => null,
            'token' => $token,
            'jwt' => $jwt
          );
        }
      }
    }
    $this->response($response);
  }

}
