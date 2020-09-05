<?php

class ControladorCursos{

  function validarCredencialesCliente(){

    if( !isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW']) ){
      // return false;
      return ['status' => false, 'id_cliente' => null];
    }

    $clientes = ModeloClientes::index('clientes');

    foreach($clientes as $key => $valueClientes){

      $tokenServer = "Basic " . base64_encode( $_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW'] );

      $tokenDB = "Basic " . base64_encode( $valueClientes['id_cliente'] . ":" . $valueClientes['llave_secreta'] );

      if( $tokenServer == $tokenDB ){
        return ['status' => true, 'id_cliente' => $valueClientes['id'] ];
      }

    }//END FOREACH

    return ['status' => false, 'id_cliente' => null];

  }//END FUNCTION

  /***************************************
   * MOSTRAR TODOS LOS REGISTROS
   ***************************************/
  public function index($page){

    // VERIFY VALIDATIONS
    $validate = ControladorCursos::validarCredencialesCliente();
    if( !$validate['status'] ){

      $json = array(
        'status'=>404,
        'detalle'=>"No tiene autorizacion para recibir los registros"
      );
      echo json_encode($json, true);
      return;
    }//EN IF VALIDATION

    $id_creador = $validate['id_cliente'];

    
    if($page != null){
      // MOSTRAR CURSOS CON PAGINACION
      $cantidad = 10;
      $desde = ($page-1) * $cantidad;
      $cursos = ModeloCursos::index("cursos", "clientes", $cantidad, $desde);
    }else{
      // MOSTRAR TODOS LOS CURSOS
      $cursos = ModeloCursos::index("cursos", "clientes", null, null);
    }

    if(empty($cursos)){
      $json = array(
        'status'=>200,
        'total_registros'=>0,
        'detalle'=>"No existe ningun curso registrado"
      );
      echo json_encode($json, true);
      return;
    }else{
      $json = array(
        'status'=>200,
        'total_registros'=>count($cursos),
        'detalle'=>$cursos
      );
      echo json_encode($json, true);
      return;
    }

    

  }//END FUNCTION

  /***************************************
   * CREAR UN REGISTRO
   ***************************************/
  public function create($datos){

     // VERIFY VALIDATIONS
    $validate = ControladorCursos::validarCredencialesCliente();
    if( !$validate['status'] ){

      $json = array(
        'status'=>404,
        'detalle'=>"No tiene autorizacion para recibir los registros"
      );
      echo json_encode($json, true);
      return;
    }//EN IF VALIDATION

    $id_creador = $validate['id_cliente'];

    // VALIDACIÓN DE DATOS
    // VALIDACIONES

    foreach ($datos as $key => $valueDatos){

      if( isset($valueDatos) && !preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $valueDatos) ){

        $json = array(
          'status' => 404,
          'detalle'=>"Error en el campo $key"
        );
        echo json_encode($json, true);
        return;
  
      }//END IF ISSET

    }//END FOREACH    

    // validar titulo  y descripcón no repetidos
    $cursos = ModeloCursos::index('cursos', 'clientes', null, null);
    foreach($cursos as $key => $value){

      if($value->titulo == $datos['titulo']){
        $json = array(
          'status' => 404,
          'detalle'=>"El título ya existe en la base de datos"
        );
        echo json_encode($json, true);
        return;
      }

      if($value->descripcion == $datos['descripcion']){
        $json = array(
          'status' => 404,
          'detalle'=>"El descripcion ya existe en la base de datos"
        );
        echo json_encode($json, true);
        return;
      }

    }

    // llevar datos al modelo
    $datos = array(
      "titulo" => $datos["titulo"],
      "descripcion" => $datos["descripcion"],
      "instructor" => $datos["instructor"],
      "imagen" => $datos["imagen"],
      "precio" => $datos["precio"],
      "id_creador" => $id_creador,
      "created_at" => date('Y-m-d h:i:s') ,
      "updated_at" => date('Y-m-d h:i:s')
    );  

    $create = ModeloCursos::create('cursos', $datos);

    // recibir respuesta del modelo

    if($create == 'ok'){

      $json = array(
        'status' => 200,
        'detalle'=>'Registo exitoso, su curso ha sido guardado'
      );
      echo json_encode($json, true);
      return;

    }
    
    

  }//end function

  /***************************************
   * ACTUALIZAR UN CRUSO
   ***************************************/
  public function update($id, $datos){
    
    // VERIFY VALIDATIONS
    $validate = ControladorCursos::validarCredencialesCliente();
    if( !$validate['status'] ){

      $json = array(
        'status'=>404,
        'detalle'=>"No tiene autorizacion para recibir los registros"
      );
      echo json_encode($json, true);
      return;
    }//EN IF VALIDATION

    $id_creador = $validate['id_cliente'];

    // VALIDACIÓN DE DATOS
    // VALIDACIONES

    foreach ($datos as $key => $valueDatos){

      if( isset($valueDatos) && !preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $valueDatos) ){

        $json = array(
          'status' => 404,
          'detalle'=>"Error en el campo $key"
        );
        echo json_encode($json, true);
        return;
  
      }//END IF ISSET

    }//END FOREACH    

    // validar id creador
    $curso = ModeloCursos::show('cursos', 'clientes', $id);

    foreach($curso as $key => $valueCurso){

      if($valueCurso->id_creador == $id_creador){

          // llevar datos al modelo

          $datos = array(
            "id" => $id,
            "titulo" => $datos["titulo"],
            "descripcion" => $datos["descripcion"],
            "instructor" => $datos["instructor"],
            "imagen" => $datos["imagen"],
            "precio" => $datos["precio"],
            "updated_at" => date('Y-m-d h:i:s')
          );  

          $create = ModeloCursos::update('cursos', $datos);

          // recibir respuesta del modelo

          if($create == 'ok'){

            $json = array(
              'status' => 200,
              'detalle'=>'Registo exitoso, su curso ha sido guardado'
            );
            echo json_encode($json, true);
            return;

          }

      }else{
        $json = array(
          'status'=>404,
          'detalle'=>"No tiene autorizacion para recibir los registros"
        );
        echo json_encode($json, true);
        return;
      }

    }

  

  }//end function

  /***************************************
   * MOSTRAR UN SOLO CURSO
   ***************************************/
  public function show($id){

    // VERIFY VALIDATIONS
    $validate = ControladorCursos::validarCredencialesCliente();
    if( !$validate['status'] ){

      $json = array(
        'status'=>404,
        'detalle'=>"No tiene autorizacion para recibir los registros"
      );
      echo json_encode($json, true);
      return;
    }//EN IF VALIDATION

    $id_creador = $validate['id_cliente'];

    $curso = ModeloCursos::show('cursos', 'clientes', $id);

    if(!empty($curso)){

      $json = array(
        "status" => 200,
        "detalle" => $curso
      );
      echo json_encode($json, true);
      return;

    }else{

      $json = array(
        "status" => 200,
        "detalle" => "No existe ningun curso registrado con ese codigo"
      );
      echo json_encode($json, true);
      return;

    }
    
    

  }//end function

  /***************************************
   * ELIMINAR UN SOLO CURSO
   ***************************************/
  public function delete($id){

    // VERIFY VALIDATIONS
    $validate = ControladorCursos::validarCredencialesCliente();
    if( !$validate['status'] ){

      $json = array(
        'status'=>404,
        'detalle'=>"No tiene autorizacion para recibir los registros"
      );
      echo json_encode($json, true);
      return;
    }//EN IF VALIDATION
    $id_creador = $validate['id_cliente'];

    // validar id creador
    $curso = ModeloCursos::show('cursos', 'clientes', $id);

    foreach($curso as $key => $valueCurso){

      if($valueCurso->id_creador == $id_creador){

          // llevar datos al modelo

          $delete = ModeloCursos::delete('cursos', $id);

          // recibir respuesta del modelo

          if($delete == 'ok'){

            $json = array(
              'status' => 200,
              'detalle'=>'Registo exitoso, su curso ha sido eliminado'
            );
            echo json_encode($json, true);
            return;

          }

      }else{
        $json = array(
          'status'=>404,
          'detalle'=>"No tiene autorizacion para eliminar este curso"
        );
        echo json_encode($json, true);
        return;
      }

    }

  }//end function

}