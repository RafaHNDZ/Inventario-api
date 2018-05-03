<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entrada_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }
/**
 * Registra un arreglo de entradas al sistema.
 * @param  Array $entradas arreglo de entradas con su arreglo de detalles
 * @return Boolean  True si es correcto en caso contrario False
 */
  public function insert_entrada($entradas){
    $this->db->trans_begin();

    for ($i=0; $i < sizeof($entradas); $i++) {
      $data = array(
        'sucursal' => $entradas[$i]['sucursal'],
        'usuario' => $entradas[$i]['usuario'],
        'estatus' => $entradas[$i]['estatus'],
        'proveedor' => $entradas[$i]['proveedor']
      );
      $this->db->insert('entrada', $data);
      $detalle_id = $this->db->insert_id();
      $this->db->reset_query();
      for ($x=0; $x < sizeof($entradas[$i]['detalle']); $x++) {
        $entradas[$i]['detalle'][$x]['entrada'] = $detalle_id;
      }
      $this->db->insert_batch('detalle_entrada', $entradas[$i]['detalle']);
    }

    if($this->db->trans_status() === FALSE){
      $this->db->trans_rollback();
      return false;
    }else{
      $this->db->trans_commit();
      return true;
    }
  }

  public function getAll($per_page, $offset){
    $this->db->limit($per_page, $offset);
    $query = $this->db->get('entrada');
    if($query->num_rows() > 0){
      $entradas = $query->result();
      for ($i=0; $i < sizeof($entradas); $i++) {
        $this->db->reset_query();
        $this->db->where('entrada', $entradas[$i]->identrada);
        $query = $this->db->get('detalle_entrada');
        if($query->num_rows() > 0){
          $detalle = $query->result();
          for ($x=0; $x <sizeof($detalle); $x++) {
            $this->db->where('idproducto', $detalle[$x]->producto);
            $query = $this->db->get('producto');
            if($query->num_rows() > 0){
              $detalle[$x]->producto = $query->row();
            }else{
              $producto = null;
            }
            $this->db->reset_query();
            $this->db->where('idproveedor', $detalle[$x]->proveedor);
            $query = $this->db->get('proveedor');
            if($query->num_rows() > 0){
              $detalle[$x]->proveedor = $query->row();
            }else{
              $detalle[$x]->proveedor = null;
            }
          }
        }else{
          $detalle = null;
        }
        $entradas[$i]->detalle = $detalle;
      }
      return $entradas;
    }else{
      return null;
    }
  }

  function truncate(){
    $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
    $this->db->reset_query();
    if($this->db->truncate('entrada')){
      $this->db->reset_query();
      $this->db->truncate('detalle_entrada');
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
