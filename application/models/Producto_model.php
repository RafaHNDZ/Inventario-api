<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }

  public function insert_productos($productos){
    return $this->db->insert_batch('producto', $productos);
  }

/**
 * Obtiene detalles de un producto
 * @param  Int $id Id de producto
 * @return Array
 */
  public function details($id){
    $this->db->where('idproducto', $id);
    $query = $this->db->get('producto');
    if($query->num_rows() > 0){
      $producto = $query->row();
      $this->db->reset_query();
      $this->db->select('idunidad_medida as id, nombre, simbolo');
      $this->db->where('idunidad_medida', $producto->medida);
      $query = $this->db->get('unidad_medida');
      if($query->num_rows() > 0){
        $producto->medida = $query->row();
      }else{
        $producto->medida = null;
      }
      return $producto;
    }else{
      return null;
    }
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
