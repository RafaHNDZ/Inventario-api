<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }

  public function insert_productos($productos){
    return $this->db->insert_batch('producto', $productos);
  }

  function truncate(){
    $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
    $this->db->reset_query();
    if($this->db->truncate('producto')){
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
