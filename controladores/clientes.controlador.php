<?php

class ControladorClientes{

  /***************************************
   * CREAR UN REGISTRO
   ***************************************/
  public function create($datos){

    // VALIDACIONES
    if( isset($datos['nombre']) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/', $datos['nombre']) ){

      $json = array(
        'status' => 404,
        'detalle'=>'El campo nombre es inválido'
      );
      echo json_encode($json, true);
      return;

    }//END IF ISSET

    if( isset($datos['apellido']) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/', $datos['apellido']) ){

      $json = array(
        'status' => 404,
        'detalle'=>'El campo apellido es inválido'
      );
      echo json_encode($json, true);
      return;

    }//END IF ISSET

    if( isset($datos['email']) && !preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $datos['email']) ){

      $json = array(
        'status' => 404,
        'detalle'=>'El campo email es inválido'
      );
      echo json_encode($json, true);
      return;

    }//END IF ISSET

    // VALIDATE UNIQUE EMAIL
    $clientes = ModeloClientes::index('clientes');
    
    foreach($clientes as $key => $value){
      if($value['email'] == $datos['email']){
        $json = array(
          'status' => 404,
          'detalle'=>'El correo ingresado ya esta registrado en sistema'
        );
        echo json_encode($json, true);
        return;
      }
    }//END FOREACH

    // GENERAR CREDENCIALES DE CLIENTE
    $id_cliente = str_replace("$", "a", crypt( $datos['nombre'] . $datos['apellido'] . $datos['email'], '$2a$07$asdfgasdfgasdfg$qwer' ) );

    $llave_secreta = str_replace("$", "o", crypt( $datos['email'] . $datos['apellido'] . $datos['nombre'], '$2a$07$asdfgasdfgasdfg$qwer' ) );

    // LLEVAR DATOS AL MODELO
    $datos = array(
      "nombre" => $datos['nombre'],
      "apellido" => $datos['apellido'],
      "email" => $datos['email'],
      "id_cliente" => $id_cliente,
      "llave_secreta" => $llave_secreta,
      "created_at" => date('Y-m-d h:i:s') ,
      "updated_at" => date('Y-m-d h:i:s')
    );

    $create = ModeloClientes::create('clientes', $datos);
    
    // RESPUEST DEL MODELO
    if($create == 'ok'){

      $json = array(
        'status' => 200,
        'detalle'=>'Registro exitoso, tome sus credenciales y guardelas',
        'credenciales' => array(
          'id_cliente' => $id_cliente,
          'llave_secreta' => $llave_secreta
        )
      );
      echo json_encode($json, true);
      return;

    }


  }//end function

}