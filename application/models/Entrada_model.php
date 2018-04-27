<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entrada_model extends CI_Model{

  public function __construct(){
    parent::__construct();
  }

  public function insert_entrada($entradas){
    $this->db->trans_begin();

    for ($i=0; $i < sizeof($entradas); $i++) {
      $data = array(
        'sucursal' => $entradas[$i]['sucursal'],
        'usuario' => $entradas[$i]['usuario'],
        'estatus' => $entradas[$i]['estatus']
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

}
