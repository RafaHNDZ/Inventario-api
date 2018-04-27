<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model{

  public function __construct(){
    parent::__construct();
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
