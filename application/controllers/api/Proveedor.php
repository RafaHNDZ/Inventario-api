<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH.'libraries/REST_Controller.php');

class Proveedor extends REST_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('Proveedor_model', 'Proveedor');
    $this->load->model('Permiso_model', 'Permiso');
  }

  function pagination_get(){
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
          if(!isset($permisos) OR !$permisos->manage_proveedores){
            $response = array(
              'code' => 401,
              'message' => 'Acceso denegado'
            );
          }else{
            $page = $this->get('page');
            $per_page = $this->get('per_page');
            $proveedores = $this->Proveedor->paginate($page, $per_page);
            if ($proveedores) {
              $response = array(
                'code' => 200,
                'total' => $this->db->count_all('proveedor'),
                'sucursales' => $this->Permiso->getSucursales($usuario->data->id_user),
                'data' => $proveedores
              );
            }else{
              $response = array(
                'code' => 404,
                'message' => 'Sin resultados'
              );
            }
          }
        }
      }
    }
    $this->response($response);
  }

}
