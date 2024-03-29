<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entrada_model extends CI_Model{

  public function __construct(){
    parent::__construct();
    $this->load->model('Proveedor_model', 'Proveedor');
    $this->load->model('Usuario_model', 'Usuario');
    $this->load->model('Sucursal_model', 'Sucursal');
    $this->load->model('Producto_model', 'Producto');
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

  /**
   * Regresa una serie de registros de la tabla entrada
   * @param  Int $per_page Número de registros solicitados
   * @param  Int $offset   Posición desde la cual iniciar
   * @return Array         Arreglo de datos
   */
  public function paginate($sucursal, $page, $per_page){
    //Paginar
    $offset = $per_page * ($page - 1);
    //Traer los registros base(entradas)
    $this->db->select('*');
    $this->db->from('entrada');
    $this->db->where('sucursal', $sucursal);
    $this->db->limit($per_page, $offset);
    $this->db->order_by('identrada', 'DESC');
    $query = $this->db->get();
    if($query->num_rows() > 0){
      $entradas = $query->result();
      for ($i=0; $i < sizeof($entradas); $i++) {
        //Traer el proveedor de la entrada
        //$entradas[$i]->proveedor = $this->Proveedor->detalils($entradas[$i]->proveedor);
        $this->db->reset_query();
        $this->db->select('idproveedor, nombre');
        $this->db->from('proveedor');
        $this->db->where('idproveedor', $entradas[$i]->proveedor);
        $query = $this->db->get();
        if($query->num_rows() > 0){
          $entradas[$i]->proveedor = $query->row();
        }else{
          $entradas[$i]->proveedor = null;
        }
        //Traer el usuario(empleado) que registro la entrada
        //$entradas[$i]->usuario = $this->Usuario->details($entradas[$i]->usuario);
        $this->db->reset_query();
        $this->db->select('id_user, nombre, apellidos');
        $this->db->from('usuario');
        $this->db->where('id_user', $entradas[$i]->usuario);
        $query = $this->db->get();
        if($query->num_rows() > 0){
          $entradas[$i]->usuario = $query->row();
        }else{
          $entradas[$i]->usuario = null;
        }
        //Traer la sucursal que registro la entrada
        //$entradas[$i]->sucursal = $this->Sucursal->details($entradas[$i]->sucursal);
        $this->db->reset_query();
        $this->db->select('idsucursal, direccion');
        $this->db->from('sucursal');
        $this->db->where('idsucursal', $entradas[$i]->sucursal);
        $query = $this->db->get();
        if($query->num_rows() > 0){
          $entradas[$i]->sucursal = $query->row();
        }else{
          $entradas[$i]->sucursal = null;
        }
        //Traer el detalle(lista de productos) de la entrada
          $this->db->reset_query();
          $this->db->select('stock_ingreso');
          $this->db->from('detalle_entrada');
          $this->db->where('entrada', $entradas[$i]->identrada);
          $query = $this->db->get();
          $total = 0;
          if($query->num_rows() > 0){
            $data = $query->result();
            for ($z=0; $z < sizeof($data); $z++) {
              $total += $data[$z]->stock_ingreso;
            }
            $entradas[$i]->total_poductos = $total;
          }
          $entradas[$i]->total_entrada = self::total_entrada($entradas[$i]->identrada);
        /**
        **/
      }
      return $entradas;
    }else{
      return null;
    }
  }

  public function total_entrada($entrada){
    $this->db->select('precio_compra, stock_ingreso');
    $this->db->from('detalle_entrada');
    $this->db->where('entrada', $entrada);
    $query = $this->db->get();
    $data = $query->result();
    $sub = 0.00;
    for ($i=0; $i < sizeof($data); $i++) {
      $sub += floatval($data[$i]->precio_compra) * floatval($data[$i]->stock_ingreso);
    }
    return round($sub, 2);
  }

  public function count_total($params = null){
    if($params){
      $this->db->select('identrada');
      $this->db->from('entrada');
      $this->db->where($params);
      $query = $this->db->get();
      return $query->num_rows();
    }else{
      return $this->db->count_all('entrada');
    }
  }

  public function detalle($id){
    $this->db->where('identrada', $id);
    $query = $this->db->get('entrada');
    if($query->num_rows() > 0){
      $entrada = $query->row();
      $this->db->reset_query();
      //Traer info de proveedor
      $this->db->select('idproveedor, nombre, tipo');
      $this->db->from('proveedor');
      $this->db->where('idproveedor', $entrada->proveedor);
      $query = $this->db->get();
      if($query->num_rows() > 0){
        $entrada->proveedor = $query->row();
      }else{
        $entrada->proveedor = null;
      }
      $this->db->reset_query();
      //Traer info del usuario
      $this->db->select('id_user, nombre, apellidos');
      $this->db->from('usuario');
      $this->db->where('id_user', $entrada->usuario);
      $query = $this->db->get();
      if($query->num_rows() > 0){
        $entrada->usuario = $query->row();
      }else{
        $entrada->usuario = null;
      }
      $this->db->reset_query();
      //Traer el detalle de la entrada
      $this->db->where('entrada', $entrada->identrada);
      $query = $this->db->get('detalle_entrada');
      if($query->num_rows() > 0){
        $detalle = $query->result();
        for ($x=0; $x <sizeof($detalle); $x++) {
          $detalle[$x]->producto = $this->Producto->details($detalle[$x]->producto);
        }
        $entrada->detalle = $detalle;
      }else{
        $entrada->detalle = null;
      }
      return $entrada;
    }else{
      return null;
    }
  }

 /**
  * Actualiza un registro de la tabla entrada
  * @param  Int   $id       Id del registro a actualizar
  * @param  Array $data
  * @return Bolean
  */
  public function update($id, $data){
    $this->db->where('identrada', $id);
    return $this->db->update('entrada', $data);
  }

  public function total(){
    return $this->db->count_all('entrada');
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
