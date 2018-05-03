<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Medida_model extends CI_Model {

  public function __construct(){
    parent::__construct();
  }

  public function save_medidas($medidas){
    return $this->db->insert_batch('unidad_medida', $medidas);
  }

}
