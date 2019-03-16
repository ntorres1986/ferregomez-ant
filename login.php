<?php
    session_start();
    extract($_REQUEST);
    include "clases/connection.php";
    $connection = new connection();  
    
    if( !empty($_POST['opcion']) )
    {    
        switch ($_POST['opcion']) 
        {
            case 'login':
                if(!empty($usuario) and !empty($clave))
                { 
                   Login( $usuario , $clave , $connection);
                }
                else
                   echo json_encode( array( "status" => false , "msg" => "Por favor ingresa todos los campos en el formulario" ) ); 
                    
            break;

            case 'create_session':
                 if( !empty($idpunto) )
                 {
                    $_SESSION["idpunto"] = $idpunto;
                    $sql ="SELECT punto  FROM punto WHERE id = $idpunto";
            
                    $data = $connection->query($sql); 
                    $results = $data->fetch(PDO::FETCH_BOTH ); 

                    if( $results != null )
                    {
                       $_SESSION["punto"] = $results['punto'];
                    }

                    
                    echo json_encode( array("status" => true) );
                 }
                 else
                 {
                     echo json_encode( array( "status" => false , "msg" => "Por favor ingresa todos los campos en el formulario" ) ); 
                  
                 }
            break; 
             
        }
        
    }
    function Login( $usuario , $clave , $connection )
    {   
        $sql ="SELECT id , nombre , apellido FROM usuario WHERE login = md5('$usuario') AND password = md5('$clave')";
        
        $data = $connection->query($sql); 
        $results = $data->fetch(PDO::FETCH_BOTH ); 

        if( $results != null )
        {  
            $_SESSION["idusuario"] = $results['id'];  
            $_SESSION["name"] = trim($results['nombre'])." ".trim($results['apellido']);  

            $idusuario = $results['id'];  
            $query = "SELECT punto.id , punto
                      FROM punto 
                      INNER JOIN usuario_punto ON punto.id = punto_id 
                      WHERE usuario_id = $idusuario ";

            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH );   

            $_SESSION["idpunto"] = $results[0]['id'];
            $_SESSION["punto"] = $results[0]['punto'];

            echo json_encode( array( "status" => true ) ); 
        }
        else
        {
             echo json_encode( array( "status" => false , "msg" => "No se encontro el usuario en el sistema" ) ); 
        }
        
    }
?>