<?php

$arrayRutas = explode('/', $_SERVER['REQUEST_URI']);

if( isset($_GET['page']) && is_numeric($_GET['page']) ){
  $cursos = new ControladorCursos();
  $cursos->index($_GET['page']);
  return;
}//NE IF

//cuando no se hace ninguna petición a la API

$index = 1; // NOTA: INDEX = 1 PARA LOCALHOST, INDEX = 0 PARA DOMINIO

if( count( array_filter($arrayRutas) ) == $index ){
  $json = array(
    'detalle'=>'No encontrado'
  );
  echo json_encode($json, true);
}else{

  // echo count( array_filter( $arrayRutas ) );

  if( count( array_filter( $arrayRutas ) ) == ( $index+1 ) ){

    // solo hay una ruta, no trae argumentos
    if(array_filter($arrayRutas)[ $index+1 ] == 'registro'){

      if( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == "POST" ){

        // OBTENER DATOS
        $datos = array(
          "nombre" => $_POST["nombre"],
          "apellido" => $_POST["apellido"],
          "email" => $_POST["email"]
        );  

        $registro = new ControladorClientes();
        $registro->create($datos);

      }else{
        $json = array(
          "detalle" => "no encontrado"
        );
        echo json_encode($json, true);
        return;
      }//END IF

      
    }//END IF
  
    else if(array_filter($arrayRutas)[ $index+1 ] == 'cursos'){

      if( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == "GET" ){

        $cursos = new ControladorCursos();
        $cursos->index(null);

      }else if( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == "POST" ){

        // CAPTURAR DATOS
        $datos = array(
          "titulo" => $_POST["titulo"],
          "descripcion" => $_POST["descripcion"],
          "instructor" => $_POST["instructor"],
          "imagen" => $_POST["imagen"],
          "precio" => $_POST["precio"],
        );  

        $cursos = new ControladorCursos();
        $cursos->create( $datos );

      }else{
        $json = array(
          "detalle" => "no encontrado"
        );
        echo json_encode($json, true);
        return;
      }//END IF//END IF

    }else{
      
      $json = array(
        "detalle" => "no encontrado"
      );
      echo json_encode($json, true);
      return;

    }//END IF

  }else{
    // la ruta trae algun argumento

    if( array_filter($arrayRutas)[ $index+1 ] == 'cursos' && 
      is_numeric( array_filter($arrayRutas)[ $index+2 ] )
      ){

        // OBTENER INFO DE UN CURSO
        if( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == "GET" ){

          $eliminarCurso = new ControladorCursos();
          $eliminarCurso->show( array_filter($arrayRutas)[ $index+2 ] );  
  
        }//END IF

        // EDITAR CURSO POR ID
        else if( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == "PUT" ){


          // CAPTURAR DATOS
          $datos = array();
          parse_str( file_get_contents('php://input'), $datos );

          $editarCurso = new ControladorCursos();
          $editarCurso->update( array_filter($arrayRutas)[ $index+2 ] , $datos );
  
        }//END IF

        // ELIMINAR CURSO POR ID
        else if( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == "DELETE" ){

          $eliminarCurso = new ControladorCursos();
          $eliminarCurso->delete( array_filter($arrayRutas)[ $index+2 ] );
  
        }else{
          $json = array(
            "detalle" => "no encontrado"
          );
          echo json_encode($json, true);
          return;
        }//END IF

    }else{
      $json = array(
        "detalle" => "no encontrado"
      );
      echo json_encode($json, true);
      return;
    }//END IF

  }//END IF

  

  

}//END IF


