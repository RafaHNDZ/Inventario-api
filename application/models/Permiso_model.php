<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permiso_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }

  public function get_permisos($user){
    $this->db->select('manage_usuarios, manage_almacen, manage_ventas, manage_entradas, read_entradas, read_compras, is_admin');
    $this->db->where('usuario', $user);
    $query = $this->db->get('permisos');
    if($query->num_rows() > 0){
      return $query->row();
    }else{
      return false;
    }
  }

  public function canManageUsers($user){
    //$this->db->select('manage_usuarios, manage_almacen, manage_ventas, manage_entradas, read_entradas, read_compras, is_admin');
    //$this->db->where('usuario', $user);
    //$query = $this->db->get('permisos');
    $permisos = self::get_permisos($user);
    if(!isset($permisos) OR $permisos->manage_usuarios != TRUE){
      return false;
    }else{
      return true;
    }
  }

}
