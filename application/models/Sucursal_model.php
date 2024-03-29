<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sucursal_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }

  public function insert_sucursales($sucursales){
    if($this->db->insert_batch('sucursal', $sucursales)){
      return true;
    }else{
      return false;
    }
  }

  public function details($id) {
    $this->db->where('idsucursal', $id);
    $query = $this->db->get('sucursal');
    if($query->num_rows() > 0){
      return $query->row();
    }else{
      return null;
    }
  }

  function truncate(){
    $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
    $this->db->reset_query();
    if($this->db->truncate('sucursal')){
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
