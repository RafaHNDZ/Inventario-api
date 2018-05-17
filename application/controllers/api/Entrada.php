<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH.'libraries/REST_Controller.php');

class Entrada extends REST_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('Entrada_model', 'Entrada');
    $this->load->model('Permiso_model', 'Permiso');
  }
  public function index_get(){
    //Obtener Headers
    $headers = $this->input->request_headers();
    $authorization = $headers['Authorization'];
    //Comprobar si se tiene Token
    if(!$authorization){
      $response = array(
        'code' => 401,
        'message' => 'Acceso denegado',
        'error' => 'Se requiere Token'
      );
    }else{
      //Verificar Token
      $this->load->library('Auth');
      if(!$this->auth->check($authorization)){
        $response = array(
          'code' => 401,
          'message' => 'Acceso denegado',
          'error' => 'Token no valido'
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
              'message' => 'Acceso denegado',
              'error' => 'No se tienen permisos de acceso/admin'
            );
          }else{
            $sucursal = $this->get('sucursal');
            $page = $this->get('page');
            $per_page = $this->get('per_page');
            $entradas = $this->Entrada->paginate($sucursal, $page , $per_page);
            if(!$entradas){
              $response = array(
                'code' => 204,
                'message' => 'No se encontraron registros'
              );
            }else{
              $response = array(
                'code' => 200,
                'total' => $this->Entrada->count_total(array('sucursal'=>$sucursal)),
                'data' => $entradas
              );
            }
          }
        }
      }
    }
    $this->response($response);
  }

  public function detalle_get($id){
    //Obtener Headers
    $headers = $this->input->request_headers();
    $authorization = $headers['Authorization'];
    //Comprobar si se tiene Token
    if(!$authorization){
      $response = array(
        'code' => 400,
        'message' => 'Acceso denegado',
        'error' => 'Se requiere Token'
      );
    }else{
      //Verificar Token
      $this->load->library('Auth');
      if(!$this->auth->check($authorization)){
        $response = array(
          'code' => 401,
          'message' => 'Acceso denegado',
          'details' => 'Token no valido'
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
              'message' => 'Acceso denegado',
              'details' => 'No tienes los permisos necesarios'
            );
          }else{
            //Tiene permisos
            $detalle = $this->Entrada->detalle($id);
            if($detalle){
              $response = array(
                'code' => 200,
                'data' => $detalle
              );
            }else{
              $response = array(
                'code' => 404,
                'message'=> 'Sin resultados'
              );
            }
          }
        }
      }
    }
    $this->response($response);
  }

  public function update_put($id){
    //Obtener Headers
    $headers = $this->input->request_headers();
    $authorization = $headers['Authorization'];
    //Comprobar si se tiene Token
    if(!$authorization){
      $response = array(
        'code' => 400,
        'message' => 'Acceso denegado',
        'error' => 'Se requiere Token'
      );
    }else{
      //Verificar Token
      $this->load->library('Auth');
      if(!$this->auth->check($authorization)){
        $response = array(
          'code' => 401,
          'message' => 'Acceso denegado',
          'details' => 'Token no valido'
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
              'message' => 'Acceso denegado',
              'details' => 'No tienes los permisos necesarios'
            );
          }else{
            //Tiene permisos
            $data = $this->put();
            if($this->Entrada->update($id, $data)){
              $response = array(
                'code' => 200,
                'message' => 'Entrada actualizada'
              );
            }else{
              $response = array(
                'code' => 500,
                'message'=> 'Error en la base de datos',
                'error' => $this->db->error()
              );
            }
          }
        }
      }
    }
    $this->response($response);
  }
}
