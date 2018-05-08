<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH.'libraries/REST_Controller.php');

class Entrada extends REST_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('Entrada_model', 'Entrada');
    $this->load->model('Permiso_model', 'Permiso');
  }
  public function index_get($page = 1, $per_page = 10){
    //Obtener Headers
    $headers = $this->input->request_headers();
    $authorization = $headers['Authorization'];
    //Comprobar si se tiene Token
    if(!$authorization){
      $response = array(
        'code' => 401,
        'message' => 'Se requiere Token'
      );
    }else{
      //Verificar Token
      $this->load->library('Auth');
      if(!$this->auth->check($authorization)){
        $response = array(
          'code' => 401,
          'message' => 'Token no valido'
        );
      }else{
        //Decodificar Token
        $usuario = $this->auth->decode($authorization);
        if(!$usuario){
          $response = array(
            'code' => 500,
            'message' => 'No se puede acceder al Token'
          );
        }else{
          //Procesar solicitud
          //Verificar los permisos del usuario
          $permisos = $this->Permiso->get_permisos($usuario->data->id_user);
          if(!isset($permisos) OR !$permisos->manage_entradas){
            $response = array(
              'code' => 401,
              'message' => 'Acceso denegado'
            );
          }else{
            $entradas = $this->Entrada->getAll($page , $per_page);
            if(!$entradas){
              $response = array(
                'code' => 204,
                'message' => 'No se encontraron registros'
              );
            }else{
              $response = array(
                'code' => 200,
                'total' => $this->Entrada->total(),
                'entradas' => $entradas
              );
            }
          }
        }
      }
    }
    $this->response($response);
  }
}
