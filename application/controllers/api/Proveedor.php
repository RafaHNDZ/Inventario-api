<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH.'libraries/REST_Controller.php');

class Proveedor extends REST_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('Proveedor_model', 'Proveedor');
    $this->load->model('Permiso_model', 'Permiso');
  }

  //Paginar proveedores
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
          $page = $this->get('page');
          $per_page = $this->get('per_page');
          $proveedores = $this->Proveedor->paginate($page, $per_page);
          if ($proveedores) {
            $response = array(
              'code' => 200,
              'total' => $this->db->count_all('proveedor'),
              //'sucursales' => $this->Permiso->getSucursales($usuario->data->id_user),
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
    $this->response($response);
  }

  //Registrar proveedor
  public function create_post(){
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
          'code' => 403,
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
          //Verificar permisos
          $permisos = $this->Permiso->get_permisos($usuario->data->id_user);
          if(!isset($permisos) OR $permisos->manage_proveedores != TRUE){
            //No tiene permisos en general
            $response = array(
              'code' => 403,
              'message' => 'Acceso denegado',
              'error' => 'Sin permisos para administrar usuarios'
            );
          }else{
            $this->form_validation->set_rules('nombre', 'Nombre', 'trim|required|max_length[120]|is_unique[proveedor.nombre]',array(
              'is_unique' => 'Empresa ya registrada.'
            ));
            $this->form_validation->set_rules('tipo', 'Tipo', 'trim|required');
            $this->form_validation->set_rules('descripcion', 'Descripción', 'trim|max_length[200]');
            $this->form_validation->set_rules('web', 'Dirección Web', 'trim|required|max_length[50]|is_unique[proveedor.web]',array(
              'is_unique' => 'Sitio web ya registrado.'
            ));
            $this->form_validation->set_rules('direccion', 'Dirección', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('calles', 'Calles', 'max_length[50]');
            $this->form_validation->set_rules('ciudad', 'Ciudad', 'required|max_length[30]');
            $this->form_validation->set_rules('region', 'Region', 'required|max_length[30]');
            $this->form_validation->set_rules('codigo_postal', 'Codigó Postal', 'required|max_length[5]');
            $this->form_validation->set_rules('rep_nombre', 'Nombre Representante', 'required|max_length[15]');
            $this->form_validation->set_rules('rep_apellido', 'Apellidos Representante', 'required|max_length[15]');
            $this->form_validation->set_rules('telefono', 'Teléfono', 'required');
            $this->form_validation->set_rules('email', 'Correo', 'required|max_length[30]|valid_email|is_unique[proveedor.email]',array(
              'is_unique' => 'Correo ya registrado.'
            ));

            if($this->form_validation->run() === FALSE){
              //$detalle = explode(".", $this->validation_errors());
              $response = array(
                'code' => 400,
                'message' => 'Formulario incompleto',
                //'error' => $detalle,
                'detalle' => $this->validation_errors('<li>','</li>')
              );
            }else{
              if($this->Proveedor->insert_proveedores(array($this->input->post()))){
                $response = array(
                  'code' => 200,
                  'message' => 'Registro creado'
                );
              }else{
                $response = array(
                  'code' => 500,
                  'message' => 'Error en la DB',
                  'error' => $this->db->error()
                );
              }
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
          'code' => 403,
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
          //Verificar permisos
          $permisos = $this->Permiso->get_permisos($usuario->data->id_user);
          if(!isset($permisos) OR $permisos->manage_proveedores != TRUE){
            //No tiene permisos en general
            $response = array(
              'code' => 403,
              'message' => 'Acceso denegado',
              'error' => 'Sin permisos para administrar usuarios'
            );
          }else{
            $data = $this->put();
            if($this->Proveedor->update($id, $data)){
              $response = array(
                'code' => 200,
                'data' => $this->put(),
                'id' => $id
              );
            }else{
              $response = array(
                'code' => 500,
                'message' => 'Error el la DB',
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
