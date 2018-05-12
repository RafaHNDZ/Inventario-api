<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH.'libraries/REST_Controller.php');

class Usuario extends REST_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('Usuario_model', 'Usuario');
    $this->load->model('Permiso_model', 'Permiso');
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

  public function create_post(){
    //Obtener Headers
    $headers = $this->input->request_headers();
    $authorization = $headers['Authorization'];
    if (!isset($authorization)) {
      $response = array(
        'code' => 400,
        'mesage' => 'Se requiere autorización'
      );
    }else{
      //Validar token
      $this->load->library('Auth');
      if(!$this->auth->check($authorization)){
        $response = array(
          'code' => 401,
          'message' => 'Token no valido'
        );
      }else{
        //Validar permisos
        $usuario = $this->auth->decode($authorization);
        $permisos = $this->Permiso->get_permisos($usuario->data->id_user);
        if(!isset($permisos) OR $permisos->manage_usuarios != TRUE){
          $response = array(
            'code' => 401,
            'message' => 'Acceso denegado'
          );
        }else{
          //Validar formulario de registro
          $this->form_validation->set_rules('nombre', 'Nombre', 'trim|required|max_length[35]');
          $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|max_length[35]');
          $this->form_validation->set_rules('email', 'Correo', 'required|valid_email|max_length[60]|is_unique[usuario.email]', array('is_unique' => 'Ese correo ya esta registrado'));
          $this->form_validation->set_rules('password', 'Contraseña', 'trim|required|max_length[35]');
          $this->form_validation->set_rules('direccion', 'Dirección', 'trim|required|max_length[100]');
          //$this->form_validation->set_rules('sucursal', 'Sucursal', 'trim|required');

          if($this->form_validation->run() === FALSE){
            //Formulario invalido
            $errors = $this->validation_errors();
            //$detalle = explode('\r\n', $errors);
            $response = array(
              'code' => 400,
              'message' => $errors,
              //'details' => $detalle,
              'files' => $_FILES['profile_pic']
            );
          }else{
            if(isset($_FILES['profile_pic'])){
              //Envio un archivo
              if($_FILES['profile_pic']['type'] != 'image/png'){
                //Tipo de archivo invalido
                $response = array(
                  'code' => 400,
                  'message' => 'Archivo no valido'
                );
              }else{
                //Cargar archivo
                $config['upload_path']          = './public/uploads/users';
                $config['allowed_types']        = 'jpg|png';
                $config['max_size']             = 2048;
                //$config['max_width']            = 1024;
                //$config['max_height']           = 768;

                $this->load->library('upload', $config);

                if(!$this->upload->do_upload('profile_pic')){
                  //Carga fallida
                  $response = array(
                    'code' => 500,
                    'message' => 'Error al cargar el archivo',
                    'detail' => $this->upload->display_errors('','')
                  );
                }else{
                  //Carga correcta, registrar el usuario
                  $file_data = $this->upload->data();
                  $usuario = array(
                    'nombre' => $this->post('nombre'),
                    'apellidos' => $this->post('apellidos'),
                    'email' => $this->post('email'),
                    'pssword' => password_hash($this->post('password'), PASSWORD_BCRYPT, array('cost' => 12)),
                    'direccion' => $this->post('direccion'),
                    'foto' => $file_data['file_name'],
                    'sucursal' => null
                  );
                  if($this->Usuario->save($usuario)){
                    //Usuario registrado
                    $response = array(
                      'code' => 200,
                      'file' => $file_data,
                      'user' => $usuario
                    );
                  }else{
                    //Error
                    $response = array(
                      'code' => 500,
                      'message' => 'No se pudo registrar el usuario',
                      'error' => $this->db->error()
                    );
                  }
                }
              }
            }else{
              //No envio archivo
              $usuario = array(
                'nombre' => $this->post('nombre'),
                'apellidos' => $this->post('apellidos'),
                'email' => $this->post('email'),
                'pssword' => password_hash($this->post('password'), PASSWORD_BCRYPT, array('cost' => 12)),
                'direccion' => $this->post('direccion'),
                'foto' => null,
                'sucursal' => null
              );
              if($this->Usuario->save($usuario)){
                //Usuario registrado
                $response = array(
                  'code' => 200,
                  'user' => $usuario
                );
              }else{
                //Error
                $response = array(
                  'code' => 500,
                  'message' => 'No se pudo registrar el usuario',
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

  public function update_put($user_id){
    //Obtener Headers
    $headers = $this->input->request_headers();
    $authorization = $headers['Authorization'];
    if (!isset($authorization)) {
      $response = array(
        'code' => 400,
        'mesage' => 'Se requiere autorización'
      );
    }else{
      //Validar token
      $this->load->library('Auth');
      if(!$this->auth->check($authorization)){
        $response = array(
          'code' => 401,
          'message' => 'Token no valido'
        );
      }else{
        //Validar permisos
        $usuario = $this->auth->decode($authorization);
        $permisos = $this->Permiso->get_permisos($usuario->data->id_user);
        if(!isset($permisos) OR $permisos->manage_usuarios != TRUE){
          $response = array(
            'code' => 401,
            'message' => 'Acceso denegado'
          );
        }else{
          //Puede administrar usuarios, coprobar que el usuario a editar sea
          //de la misma sucursal o que tenga privilegios de administrador
          $request_user = $this->auth->decode($authorization);
          $request_permisos = $this->Permiso->get_permisos($request_user->data->id_user);
          $user_to_edit = $this->Usuario->details($user_id);

          if($user_to_edit->sucursal != $request_user->data->sucursal && $request_permisos->is_admin == FALSE){
            //No es administrador, no puede manejar sucursales libremente
            $response = array(
              'code' => 403,
              'message' => 'No tienes permisos sobre esa sucursal'
            );
          }else{
            //Es administrador
            $data = array(
              'nombre' => $this->put('nombre'),
              'apellidos' => $this->put('apellidos'),
              'email' => $this->put('email'),
              'pssword' => password_hash($this->put('password'), PASSWORD_BCRYPT, array('cost' => 12)),
              'direccion' => $this->put('direccion'),
              'foto' => $file_data['file_name'],
              'sucursal' => $this->put('sucursal')
            );
            if($this->Usuario->update($user_id, $data)){
              $response = array(
                'code' => 200,
                //'requester' => $request_user,
                //'permisos' => $request_permisos,
                //'usert_to_edit' => $user_to_edit,
                'data' => $data
              );
            }else{
              $response = array(
                'code' => 500,
                'message' => 'No se pudo actualizar el registro',
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
