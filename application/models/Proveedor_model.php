<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor_model extends CI_Model{

  function __construct(){
    parent::__construct();
    $this->proveedor_table = 'proveedor';
  }

  public function insert_proveedores($proveedores){
    if($this->db->insert_batch($this->proveedor_table, $proveedores)){
      return true;
    }else{
      return false;
    }
  }

  function truncate(){
    $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
    $this->db->reset_query();
    if($this->db->truncate('proveedor')){
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
