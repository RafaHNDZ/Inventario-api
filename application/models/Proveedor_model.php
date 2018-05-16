<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor_model extends CI_Model{

  function __construct(){
    parent::__construct();
    $this->proveedor_table = 'proveedor';
  }

/**
 * Registra una serie de proveedores
 * @param  Array $proveedores Arreglo de proveedores
 * @return Boolean
 */
  public function insert_proveedores($proveedores){
    if($this->db->insert_batch($this->proveedor_table, $proveedores)){
      return true;
    }else{
      return false;
    }
  }
  /**
   * Regresa información sobre un proveedor
   * @param  Int $id Id del proveedor
   * @return Array
   */
  public function detalils($id){
    $this->db->where('idproveedor', $id);
    $query = $this->db->get('proveedor');
    if($query->num_rows() > 0){
      return $query->row();
    }else{
      return null;
    }
  }

  public function paginate($page, $per_page){
    $offset = $per_page * ($page - 1);
    $this->db->select('*');
    $this->db->from('proveedor');
    $this->db->limit($per_page, $offset);
    $this->db->order_by('idproveedor', 'DESC');
    $query = $this->db->get();
    if($query->num_rows() > 0){
      return $query->result();
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
