<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once('vendor/autoload.php');
include_once(APPPATH.'libraries/REST_Controller.php');

class Seed extends REST_Controller{
  function __construct(){
    $this->faker = Faker\Factory::create('es_ES');
    parent::__construct();
    $this->load->model('Proveedor_model', 'Proveedor');
    $this->load->model('Sucursal_model', 'Sucursal');
    $this->load->model('Producto_model', 'Producto');
    $this->load->model('Categoria_model', 'Categoria');
    $this->load->model('Entrada_model', 'Entrada');
    $this->load->model('Usuario_model', 'Usuario');
    $this->load->model('Medida_model', 'Medida');
  }

  public function categoria_get($max = 20){
    $categorias = array();
    for ($i=0; $i < $max; $i++) {
      $categoria = array(
        'nombre' => $this->faker->sentence($this->faker->numberBetween(1, 3)),
        'descripcion' => $this->faker->text(5),
        'foto' => $this->faker->imageUrl(380, 420)
      );
      array_push($categorias, $categoria);
    }
    if($this->Categoria->insert_categorias($categorias)){
      $response = array(
        'status' => 200,
        'error' => null,
        'categorias' => $categorias
      );
    }else{
      $response = array(
        'status' => 500,
        'error' => $this->db->error()
      );
    }
    $this->response($response);
  }

  public function producto_get($max = 50){
    $productos = array();
    for ($i=0; $i < $max; $i++) {
      $producto = array(
        'nombre' => $this->faker->name(),
        'categoria' => $this->faker->numberBetween(1, $this->db->count_all('categoria')),
        'descripcion' => $this->faker->text(24),
        'long_descripcion' => $this->faker->text($this->faker->numberBetween(7, 14)),
        'status' => $this->faker->numberBetween(0, 2),
        'medida' => $this->faker->numberBetween(1, $this->db->count_all('unidad_medida'))
      );
      array_push($productos, $producto);
    }
    if($this->Producto->insert_productos($productos)){
      $response = array(
        'status' => 200,
        'error' => null,
        'productos' => $productos
      );
    }else{
      $response = array(
        'status' => 500,
        'error' => $this->db->error(),
        'productos' => $productos
      );
    }
    $this->response($response);
  }

  public function sucursal_get($max = 5){
    $sucursales = array();
    for ($i=0; $i < $max; $i++) {
      $sucursal = array(
        'razon_social' => $this->faker->sentence($this->faker->numberBetween(8, 17)),
        'direccion' => $this->faker->streetAddress(),
        'telefono' => $this->faker->phoneNumber(),
        'email' => $this->faker->email(),
        'logo' => $this->faker->imageUrl(200, 200)
      );
      array_push($sucursales, $sucursal);
    }
    if($this->Sucursal->truncate()){
      if($this->Sucursal->insert_sucursales($sucursales)){
        $response = array(
          'status' => 200,
          'error' => null,
          'sucursales' => $sucursales
        );
      }else{
        $response = array(
          'status' => 500,
          'error' => $this->db->error()
        );
      }
    }else{
      $response = array(
        'status' => 500,
        'error' => $this->db->error()
      );
    }
    $this->response($response);
  }

  public function proveedor_get($max = 10){
    $proveedores = array();
    for ($i=0; $i < $max; $i++) {
      $proveedor = array(
        'nombre' => $this->faker->company(),
        'tipo' => $this->faker->numberBetween(1,2),
        'descripcion' => $this->faker->text($this->faker->numberBetween(6,12)),
        'web' => $this->faker->url(),
        'direccion' => $this->faker->streetAddress(),
        'calles' => $this->faker->streetName(),
        'ciudad' => $this->faker->city(),
        'codigo_postal' => $this->faker->postcode(),
        'rep_nombre' => $this->faker->firstName(),
        'rep_apellido' => $this->faker->lastName(),
        'telefono' => $this->faker->phoneNumber(),
        'email' => $this->faker->email()
      );
      array_push($proveedores, $proveedor);
    }
    if($this->Proveedor->truncate()){
      if($this->Proveedor->insert_proveedores($proveedores)){
        $response = array(
          'status' => 200,
          'error' => null,
          'proveedores' => $proveedores
        );
      }else{
        $response = array(
          'status' => 500,
          'error' => $this->db->error()
        );
      }
    }else{
      $response = array(
        'status' => 500,
        'error' => $this->db->error()
      );
    }
    $this->response($response);
  }

  public function entrada_get($max = 6){
    $entradas = array();
    for ($i = 0; $i < $max; $i++){
      $entrada = array(
        'sucursal' => $this->faker->numberBetween(1, $this->db->count_all('sucursal')),
        'usuario' => $this->faker->numberBetween(1, $this->db->count_all('usuario')),
        //'fecha' => $this->faker->date('Y-m-d', 'now') .' '. $this->faker->time('H:i:s', 'now'),
        'proveedor' => $this->faker->numberBetween(1, $this->db->count_all('proveedor')),
        'estatus' => $this->faker->numberBetween(1, 2)
      );
      $detalles = array();
      //$proveedor_id = $this->faker->numberBetween(1, $this->db->count_all('proveedor'));
      for ($i=0; $i < $this->faker->numberBetween(1, 6); $i++) {
        $detalle = array(
          //'proveedor' => $proveedor_id,
          'producto' => $this->faker->numberBetween(1, $this->db->count_all('producto')),
          'codigo' => $this->faker->ean8(),
          'stock_ingreso' => $this->faker->numberBetween(1, 14),
          'stock_actual' => $this->faker->numberBetween(1, 14),
          'precio_compra' => $this->faker->numberBetween(1, 1300) .'.'.$this->faker->randomNumber(2),
          'precio_venta_distribuidor' => $this->faker->numberBetween(1, 1300) .'.'.$this->faker->randomNumber(2),
          'precio_venta_publico' => $this->faker->numberBetween(1, 1300) .'.'.$this->faker->randomNumber(2)
        );
        array_push($detalles, $detalle);
      }
      $entrada['detalle'] = $detalles;
      array_push($entradas, $entrada);
    }
    $this->Entrada->truncate();
    if($this->Entrada->insert_entrada($entradas)){
      $response = array(
        'status' => 200,
        'error' => false,
        'entradas' => $entradas
      );
    }else{
      $response = array(
        'status' => 500,
        'error' => $this->db->error()
      );
    }
    $this->response($response);
  }

  public function usuario_get($max = 10){
    $usuarios = array();
    for ($i=0; $i < $max; $i++) {
      $usuario = array(
        'nombre' => $this->faker->firstName(),
        'apellidos' => $this->faker->lastName(),
        'email' => $this->faker->email(),
        'pssword' => md5('secret'),
        'telefono' => $this->faker->phoneNumber(),
        'direccion' => $this->faker->address(),
        'sucursal' => $this->faker->numberBetween(1, $this->db->count_all('sucursal'))
      );
      array_push($usuarios, $usuario);
    }
    $usuario = array(
      'nombre' => 'Rafael',
      'apellidos' => 'Hernández Ramírez',
      'email' => 'rafa_hndz@outlook.com',
      'pssword' => password_hash('secret', PASSWORD_BCRYPT, array('cost' => 12)),
      'telefono' => '4661091127',
      'direccion' => 'Leon Rójas #18',
      'sucursal' => $this->faker->numberBetween(1, $this->db->count_all('sucursal'))
    );
    array_push($usuarios, $usuario);
    $this->Usuario->truncate();
    if($this->Usuario->save_many($usuarios)){
      $response = array(
        'status' => 200,
        'error' => null,
        'usuarios' => $usuarios
      );
    }else{
      $response = array(
        'status' => 500,
        'error' => $this->db->error()
      );
    }
    $this->response($response);
  }

  public function medida_get($max = 10){
    $unidades = array();
    for ($i=0; $i < $max; $i++) {
      $code = $this->faker->countryCode();
      $unidad = array(
        'nombre' => $code,
        'simbolo' => strtolower($code),
        'status' => 1
      );
      array_push($unidades, $unidad);
    }
    if($this->Medida->save_medidas($unidades)){
      $response = array(
        'status' => 200,
        'error' => null,
        'medidas' => $unidades
      );
    }else{
      $response = array(
        'status' => 500,
        'error' => $this->db->error()
      );
    }
    $this->response($response);
  }
}
