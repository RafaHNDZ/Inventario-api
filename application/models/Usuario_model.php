<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }

  public function login($email, $password){
    $this->db->select('id_user, email, pssword, nombre, apellidos, sucursal, foto, created_at');
    $this->db->where('email', $email);
    $this->db->limit('1');
    $query = $this->db->get('usuario');
    if($query->num_rows() > 0){
      $usuario  = $query->row();
      if(password_verify($password, $usuario->pssword)){
        unset($usuario->pssword);
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

  public function insert_usuarios($users){
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
