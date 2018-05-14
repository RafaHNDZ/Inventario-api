<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }

  public function login($email, $password){
    $this->db->select('id_user, email, pssword, nombre, apellidos, foto, created_at, status');
    $this->db->where('email', $email);
    $this->db->limit('1');
    $query = $this->db->get('usuario');
    if($query->num_rows() > 0){
      //Usuario encontrado
      $usuario  = $query->row();
      if(password_verify($password, $usuario->pssword)){
        //Credenciales validas
        unset($usuario->pssword);
        //unset($usuario->status);
        $this->db->reset_query();
        $this->db->select('manage_usuarios, manage_almacen, manage_ventas, manage_entradas, read_entradas, read_compras');
        $this->db->where('usuario', $usuario->id_user);
        $this->db->limit('1');
        $query = $this->db->get('permisos');
        if($query->num_rows() > 0){
          $usuario->permisos = $query->row();
        }else{
          $usuario->permisos = null;
        }
        return $usuario;
      }else{
        return false;
      }
    }else{
      return null;
    }
  }

  public function get_all(){
    $this->db->select('*');
    $this->db->from('usuario');
    $query = $this->db->get();
    if($query->num_rows() > 0){
      $usuarios = $query->result();
      $this->db->reset_query();
      for ($i=0; $i < sizeof($usuarios); $i++) {
        unset($usuarios[$i]->pssword);
        $this->db->select('*');
        $this->db->from('sucursal');
        $this->db->where('idsucursal', $usuarios[$i]->sucursal);
        $query = $this->db->get();
        if($query->num_rows() > 0){
          $usuarios[$i]->sucursal = $query->row();
        }else{
          $usuarios[$i]->sucursal = null;
        }
      }
      return $usuarios;
    }else{
      return null;
    }
  }

  public function paginate($page, $per_page){
    $offset = $per_page * ($page - 1);
    $this->db->select('*');
    $this->db->from('usuario');
    $this->db->limit($per_page, $offset);
    $query = $this->db->get();
    if($query->num_rows() > 0){
      $usuarios = $query->result();
      $this->db->reset_query();
      for ($i=0; $i < sizeof($usuarios); $i++) {
        unset($usuarios[$i]->pssword);
        $this->db->select('*');
        $this->db->from('sucursal');
        $this->db->where('idsucursal', $usuarios[$i]->sucursal);
        $query = $this->db->get();
        if($query->num_rows() > 0){
          $usuarios[$i]->sucursal = $query->row();
        }else{
          $usuarios[$i]->sucursal = null;
        }
      }
      return $usuarios;
    }else{
      return null;
    }
  }

  public function save($usuario){
    return $this->db->insert('usuario', $usuario);
  }

  public function save_many($users){
    $this->db->trans_begin();

    $this->db->insert_batch('usuario', $users);

    if($this->db->trans_status() === FALSE){
      $$this->db->trans_rollback();
      return false;
    }else{
      $this->db->trans_complete();
      return true;
    }
  }

  public function update($id, $data){
    $this->db->where('id_user', $id);
    return $this->db->update('usuario', $data);
  }

  public function delete($id){
    $this->db->set('status', 2);
    $this->db->where('id_user', $id);
    return $this->db->update('usuario');
  }

/**
 * Obtiene la información de un usuario
 * @param  Int $id Id del usuario
 * @return Array
 */
  public function details($id){
    $this->db->where('id_user', $id);
    $query = $this->db->get('usuario');
    if($query->num_rows() > 0){
      return $query->row();
    }else{
      return null;
    }
  }

  function truncate(){
    $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
    $this->db->reset_query();
    if($this->db->truncate('usuario')){
      $this->db->reset_query();
      $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
      return true;
    }else{
      $this->db->reset_query();
      $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
      return false;
    }
  }

}
