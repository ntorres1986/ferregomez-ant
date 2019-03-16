<?php
date_default_timezone_set('America/Bogota');
extract($_REQUEST);
session_start();
include "../clases/connection.php";
include "../clases/func.php";

 $connection = new connection();
 $func = new func();
 
 include "../clases/msg.php";
 $msg = new msg();
   
 $idusuario  =  $_SESSION["idusuario"] ;
 $idpunto  =  $_SESSION["idpunto"] ;
 
 function limpiarTildes($s)
 {
      $s= str_replace('"', '', $s); 
      $s= str_replace(':', '', $s); 
      $s= str_replace('.', '', $s); 
      $s= str_replace(',', '', $s); 
      $s= str_replace(';', '', $s);  
      $s= str_replace("'", '', $s);  

      return $s; 
 }
 function limpiar($s) 
 { 
      $s = str_replace('á', 'a', $s); 
      $s = str_replace('Á', 'A', $s); 
      $s = str_replace('é', 'e', $s); 
      $s = str_replace('É', 'E', $s); 
      $s = str_replace('í', 'i', $s); 
      $s = str_replace('Í', 'I', $s); 
      $s = str_replace('ó', 'o', $s); 
      $s = str_replace('Ó', 'O', $s); 
      $s = str_replace('Ú', 'U', $s); 
      $s= str_replace('ú', 'u', $s); 
   
      $s= str_replace('"', '', $s); 
      $s= str_replace(':', '', $s); 
      $s= str_replace('.', '', $s); 
      $s= str_replace(',', '', $s); 
      $s= str_replace(';', '', $s);  

      return $s; 
 }
 if(!empty($opcion))
 {
     switch($opcion)
     {
        
          case "ChangePoint":
               $_SESSION['idpunto'] = $id;
               $_SESSION['punto'] = $point; 
               echo json_encode( array('usermane' => $_SESSION['name']  ,  'point' =>  $_SESSION['punto'] ) );
          break; 

          case "UpdateMenuAccess":

               if( $status == 'false' ) 
               {
                  $query = "UPDATE usuario_menu SET status = false WHERE menu_id = $menu_id AND usuario_id = $usuario_id";
                  $connection->query($query);   


                  $query = "UPDATE usuario_submenu 
                            LEFT JOIN submenu ON submenu.id = submenu_id 
                            SET usuario_submenu.status = false
                            WHERE submenu.menu_id = $menu_id AND usuario_submenu.usuario_id = $usuario_id";
                  $connection->query($query);  

               }
               else
               {
                  if( !empty($chAccessItem) )
                  {
                      $query = "SELECT * FROM usuario_menu WHERE menu_id = $menu_id AND usuario_id = $usuario_id";
                      $data = $connection->query($query);  
                      $menus = $data->fetchAll(PDO::FETCH_BOTH ); 


                      if( count($menus) <= 0)
                      {
                          $query = "INSERT INTO usuario_menu VALUES (NULL , $usuario_id , $menu_id , true )";
                          $connection->query($query);
                      }
                      else
                      {
                         $query = "UPDATE usuario_menu SET status = true WHERE menu_id = $menu_id AND usuario_id = $usuario_id ";
                         $connection->query($query);   

                      }

                      foreach ($chAccessItem as $key => $submenu_id) 
                      {
                          $query = "SELECT * FROM usuario_submenu WHERE submenu_id = $submenu_id AND usuario_id = $usuario_id";
                          $data = $connection->query($query);  
                          $submenus = $data->fetchAll(PDO::FETCH_BOTH ); 
                            
                          if( count($submenus) <= 0)
                          {
                              $query = "INSERT INTO usuario_submenu VALUES (NULL , $usuario_id , $submenu_id , true )";
                              $connection->query($query);
                          }
                          else
                          {
                             $query = "UPDATE usuario_submenu SET status = true WHERE submenu_id = $submenu_id AND usuario_id = $usuario_id ";
                             $connection->query($query);   
                          }
                      } 
                  }
                  else if( !empty($chAccessParent))
                  { 
                      $query = "SELECT * FROM usuario_menu WHERE menu_id = $menu_id AND usuario_id = $usuario_id";
                      $data = $connection->query($query);  
                      $menus = $data->fetchAll(PDO::FETCH_BOTH ); 

                      
                      if( count($menus) <= 0)
                      {
                          $query = "INSERT INTO usuario_menu VALUES (NULL , $usuario_id , $menu_id , true )";
                          $connection->query($query);
                      }
                      else
                      {
                         $query = "UPDATE usuario_menu SET status = true WHERE menu_id = $menu_id AND usuario_id = $usuario_id ";
                         $connection->query($query);   

                      } 
                  }
               }
               echo json_encode(null);
          break;

          case "UpdateSubMenuAccess": 
                if( $status == 'false' ) 
                {
                    $query = "UPDATE usuario_submenu SET status = false WHERE submenu_id = $submenu_id AND usuario_id = $usuario_id";
                    $connection->query($query);  
                }
                else
                {
                    $query = "SELECT * FROM usuario_submenu WHERE submenu_id = $submenu_id AND usuario_id = $usuario_id";
                    $data = $connection->query($query);  
                    $submenus = $data->fetchAll(PDO::FETCH_BOTH ); 
                      
                    if( count($submenus) <= 0)
                    {
                        $query = "INSERT INTO usuario_submenu VALUES (NULL , $usuario_id , $submenu_id , true )";
                        $connection->query($query);
                    }
                    else
                    {
                       $query = "UPDATE usuario_submenu SET status = true WHERE submenu_id = $submenu_id AND usuario_id = $usuario_id ";
                       $connection->query($query);   
                    } 
                }

                $query = "SELECT children , menu.id
                          FROM submenu 
                          LEFT JOIN menu ON menu.id = menu_id
                          WHERE submenu.id = $submenu_id";
                $data = $connection->query($query); 
                $results = $data->fetchAll(PDO::FETCH_BOTH );

                if( count($results) > 0 )
                {
                     $row = $results[0]; 
                     if( $row['children'] > 0 )
                     {
                         if( $status == 'false' ) 
                         {
                            $query = "UPDATE usuario_menu SET status = false WHERE menu_id = $row[id] AND usuario_id = $usuario_id";
                            $connection->query($query);  
                         }
                         else
                         {
                            $query = "UPDATE usuario_menu SET status = true WHERE menu_id = $row[id] AND usuario_id = $usuario_id";
                            $connection->query($query);  
                         }
                     }
                } 

                echo json_encode("success"); 
          break;

          case "ReporteCantidadVendida":
             $connection->query("SET NAMES utf8");
             $condition = "";
             if( !empty($idproducto) )
             {
                 if( count($idproducto) > 0 )
                 {
                    $list = trim(implode(",", $idproducto));
                    if( strlen($list) > 0 )
                       $condition .= " AND producto_venta.producto_id IN ( $list )"; 
                 }
             } 
             
             if( !empty($inicio) && !empty($fin) )
                $condition .= " AND DATE(venta.fecha) BETWEEN '$inicio' AND '$fin'";
             else
             {
                if( !empty($inicio) )
                $condition .= " AND DATE(venta.fecha) = '$inicio'";
             }
 
              
             $query = "SELECT producto.nombre , sum( producto_venta.cantidad ) cantidad
                 FROM venta 
                 INNER JOIN producto_venta ON venta.id = producto_venta.venta_id 
                 INNER JOIN producto ON producto.id = producto_venta.producto_id 
                 WHERE 1 $condition
                 GROUP BY producto.id "; 
         
             $data = $connection->query($query);  
             $results = $data->fetchAll(PDO::FETCH_BOTH ); 

             $form = "<table class='table ui selectable ' >
                     <thead>
                        <tr>
                           <th>PRODUCTO</th>
                           <th>CANTIDAD</th>
                        </tr>
                     </thead>"; 


             foreach ($results as $key => $fila) 
             {
               $form .="<tr>
                          
                           <td>$fila[nombre]</td> 
                           <td>$fila[cantidad]</td>
                        </tr>";

             }
             $form.="</table>";    
             echo $form; 
          break;

          case "ConsultarPorDescontar":
           $condicion = "";
           if( !empty($fecha) ) 
           {
               $condicion = " AND date(venta.fecha) = '$fecha' ";
           }
           $query = "SELECT  SUM( producto_venta.cantidad * producto_venta.precio ) total 
                     FROM producto_venta 
                     INNER JOIN venta ON venta.id = venta_id
                     WHERE venta.factura IS  NULL $condicion";
           $data = $connection->query($query);  
           $results = $data->fetch(PDO::FETCH_ASSOC); 
           $total_ventas = $results['total']; 

           $total = $total_ventas;

           $total_ventas = $func->format( $total_ventas ); 

            echo "<div class='titulo'>VENTAS</div>";

              echo "<div class='ui sixteen  centered grid'>
                      <form action='set.php' method='post' data-response='content'  data-paginate='false' autocomplete='off' >
                         <div class='ui form'>

                            <div class='field'>
                               <label>TOTAL VENTAS</label>
                               <h1>$ $total_ventas</h1>
                            </div> 

                            <div class='field'>
                               <label>VALOR</label>
                               <input type='text'  style='font-size:24px;font-weight: bold;' name='valor' style='text-align:center;' class='form-control validar requerido numero' data-min='1' data-max='$total' >
                            </div> 

                            <div class='field'>
                                <div class='ui animated button btn-enviar' >
                                  <div class='visible content'>REGISTRAR</div>
                                  <div class='hidden content'>
                                    <i class='right search outline icon'></i>
                                  </div>
                                </div>
                            </div> 
          
                            <div class='field'>
                               <center><div  style='margin-top:30px;' class='ui secondary button link' data-url='set.php' data-data='opcion=reset&fecha=$fecha' data-response='content'>Resetear</div>
                               </center> 
                            </div>   

                            <input type='hidden' name='opcion' value='resourceSales'>
                            <input type='hidden' name='fecha' value='$fecha'>
                      </form>
                    </div>
                  </div>";

          break;

          case "reset":
             if( !empty($fecha) )
             {
                 $query = "UPDATE venta SET checked = 0 WHERE date(fecha) = '$fecha' ";
                 $connection->query( $query );
                 $func->WriteQuery( $connection , $query );
                 
                 echo $msg->success("Operación registrada exitosamente."); 
             }
          break;

          case "catalogo":
             $total = count($_FILES['imagen']['name']);

            for($i=0; $i<$total; $i++) 
            {
              $tmpFilePath = $_FILES['imagen']['tmp_name'][$i];
              $nombre = $_FILES['imagen']['name'][$i];
 
              if ($tmpFilePath != "")
              { 

                  if( !empty($_POST['nombres'][$i]) )
                    $nombre = utf8_encode($_POST['nombres'][$i]);

                  $categoria_id = $_POST['categoria'][$i];

                  $query = "SELECT folder FROM categoria WHERE id = $categoria_id ";
                  $data = $connection->query($query);  
                  $results = $data->fetch(PDO::FETCH_BOTH ); 
                  $folder = $results['folder']; 
 
                  $newFilePath = "../../catalogo/".$folder."/".$_FILES['imagen']['name'][$i];
                  $file = $_FILES['imagen']['name'][$i];

                  $newFilePath = utf8_decode($newFilePath);
 
                  if( file_exists($newFilePath))
                  {
                     if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                     {
                        $query = "UPDATE catalogo SET producto = '$nombre' , imagen = '$file' WHERE imagen = '$file' ";
                        $connection->query($query); 
                     }                   
                   }
                  else if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                  { 
                      $date = date('Y-m-d H:i:s'); 
                      $query = "INSERT INTO catalogo VALUES(null , '$nombre' , '$file' , $categoria_id , '$date')";
                      $connection->query($query); 
                  }
              }
            }
            /* 
            $total = count($_FILES['imagen']['name']);

            for($i=0; $i<$total; $i++) 
            {
              $tmpFilePath = $_FILES['imagen']['tmp_name'][$i];
              $nombre = $_FILES['imagen']['name'][$i];
 
              if ($tmpFilePath != "")
              { 

                  if( !empty($_POST['nombres'][$i]) )
                    $nombre = utf8_encode($_POST['nombres'][$i]);
                  if( !empty($_POST['folder_categorias'][$i]) )
                    $folder_categoria = utf8_encode($_POST['folder_categorias'][$i]);

                  $categoria_id = $_POST['categoria'][$i];
                  
                  
                  $newFilePath = $_SERVER["DOCUMENT_ROOT"]."/catalogo/"."$folder_categoria/". $_FILES['imagen']['name'][$i];
                  $file = $_FILES['imagen']['name'][$i];

                  $newFilePath = utf8_decode($newFilePath);
 
                  if( file_exists($newFilePath))
                  {
                     if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                     {
                        $query = "UPDATE catalogo SET producto = '$nombre' , imagen = '$file' WHERE imagen = '$file' ";
                        $connection->query($query); 
                     }                   }
                  else if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                  { 
                      $query = "INSERT INTO catalogo VALUES(null , '$nombre' , '$file' , $categoria_id , current_timestamp)";
                      $connection->query($query); 
                  }                  
              }
            }
            */
            echo $msg->success("Operación registrada exitosamente."); 
          break;

          case "CargarProductos":
              if (!empty($_FILES['productos']['tmp_name'])) 
              { 
                  if( $_FILES['productos']['type'] == 'text/csv' or $_FILES['productos']['type'] == "application/vnd.ms-excel" or $_FILES['productos']['type'] == "application/octet-stream")
                  { 
                        $xls =  $_FILES["productos"]['tmp_name'];
                        $archivo = file($xls); 

                        $queryProducto = "INSERT INTO producto VALUES ";
                        $queryProductoPunto = "INSERT INTO producto_punto VALUES ";
                        $sw = 0;
                        $idproducto = 1;
                        $contador = 0;

                        //$connection->query("SET NAMES utf8");
                        

                        foreach ($archivo as $index => $linea)
                        {   

                             $contador++;
                             $separar = explode(';',$linea); 
                             if( empty($separar[1]) )
                                $separar[1] = 0;
                              
                             $separar[0] = limpiar($separar[0]); 
                             $separar[0] = strtolower($separar[0]);
                              
                             $nombre = trim($separar[0]);
                             $cantidad =  trim($separar[1]);  
                             $stock =  trim($separar[2]);   
                             $costo =  trim($separar[3]);  
                             $precio =  trim($separar[4]);  

                             $codigo = "";

                          
                             $array = explode(" ", $nombre); 
                             foreach ($array as $key => $value) 
                             { 
                                  if( !empty($value[0]) )
                                  {
                                      $codigo.=$value[0]."";  
                                  }
                             }  
                            
                              
                             $queryProducto .= "($idproducto,'$nombre','$codigo','$stock','',1,1),";   
                             $queryProductoPunto .=  "(NULL,$cantidad,$precio , $precio , $costo , $costo , 1 , $idproducto, 1 , $precio  , $precio ),"; 

                             $sw = 1;
                             $idproducto++;                              
                             
                        } 
                        if( $sw == 1 )
                        {
                            $queryProducto = substr ($queryProducto, 0, strlen($queryProducto) - 1);
                            $queryProducto.=";"; 

                            $queryProductoPunto = substr ($queryProductoPunto, 0, strlen($queryProductoPunto) - 1);
                            $queryProductoPunto.=";";   

                            $connection->query( $queryProducto );
                            $connection->query( $queryProductoPunto );

                            echo $msg->success("<h1>$contador</h1>Producto cargados exitosamente.");
                         
                        }            
                  }
                  else
                  {
                      echo  $mensaje->error("EL TIPO DE ARCHIVO NO ES VALIDO","");                  
                  }
              } 
          break; 

          case "promociones":
            $total = count($_FILES['imagen']['name']);

            for($i=0; $i<$total; $i++) 
            {
              $tmpFilePath = $_FILES['imagen']['tmp_name'][$i];
              $nombre = $_FILES['imagen']['name'][$i];
 
              if ($tmpFilePath != "")
              { 

                  if( !empty($_POST['nombres'][$i]) )
                    $nombre = $_POST['nombres'][$i];
 
                  $newFilePath = "../../promociones/". $_FILES['imagen']['name'][$i];
                  $file = $_FILES['imagen']['name'][$i];
 
                  if( file_exists($newFilePath))
                  {
                     if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                     {
                        $query = "UPDATE promociones SET titulo = '$nombre' , imagen = '$file' WHERE imagen = '$file' ";
                        $connection->query($query);
                     }                   
                  }
                  else if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                  {
                      $date = date('Y-m-d H:i:s'); 
                      $query = "INSERT INTO promociones VALUES(null , '$nombre' , '$file' , '$date')";
                      $connection->query($query);


                      echo $msg->success("Producto registrado exitosamente.");
                  }
              }
            }
            echo $msg->success("Promoción registrada exitosamente."); 
          break;

          case "marcas":
            $total = count($_FILES['imagen']['name']);

            for($i=0; $i<$total; $i++) 
            {
              $tmpFilePath = $_FILES['imagen']['tmp_name'][$i];
              $nombre = $_FILES['imagen']['name'][$i];
 
              if ($tmpFilePath != "")
              {  
                  
                  $newFilePath = "../../marcas/". $_FILES['imagen']['name'][$i];
                  $file = $_FILES['imagen']['name'][$i];
 
                  if( file_exists($newFilePath))
                  {
                     if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                     {
                        $query = "UPDATE marca SET imagen = '$file' WHERE imagen = '$file' ";
                        $connection->query($query);
                     }                   
                  }
                  else if(move_uploaded_file($tmpFilePath, $newFilePath)) 
                  {
                      
                      $date = date('Y-m-d H:i:s'); 
                      $query = "INSERT INTO marca VALUES(null ,  '$file' , '$date')";
                      $connection->query($query); 
                  }
              }
            }
            echo $msg->success("Marca registrada exitosamente."); 
          break;

          case "DevolverVenta": 
             if( !empty($productos) && !empty($venta_id) && !empty($cantidad) )
             {
                try 
                {
                   $connection->beginTransaction();
                   foreach ($productos as $key => $idp) 
                   {
                      if( $_POST["cantidad"][$key] >= 0 &&   $_POST["cant_ant"][$key] >= 0  )
                      {
                          $cant =  $_POST["cantidad"][$key];
                          $cant_ant =  $_POST["cant_ant"][$key];
                          if( $cant != $cant_ant )
                          {

                            if( $cant == 0)
                              $suma = $cant_ant;
                            else
                              $suma = $cant; 

                            $query = "UPDATE producto_punto SET cantidad = cantidad + $suma WHERE producto_id = $idp AND punto_id = $idpunto ";
                            $connection->query($query);
                            $func->WriteQuery( $connection , $query );


                            $query = "UPDATE producto_venta SET cantidad = cantidad - $suma WHERE producto_id = $idp AND venta_id = $venta_id ";
                            $connection->query($query);
                            $func->WriteQuery( $connection , $query );


                            $suma = $cant_ant;
                            $func->Entrada( $connection , $suma , "DEVOLUCION-WEB" , $idp , $idusuario , 0 );
                           

                            $query = "SELECT cantidad FROM producto_venta WHERE producto_id = $idp and venta_id = $venta_id";
                            $data = $connection->query($query);  
                            $results = $data->fetch(PDO::FETCH_BOTH ); 
                            $cnt_cnt = $results['cantidad']; 
                            if( $cnt_cnt == 0 )
                            {
                                 $query = "DELETE FROM producto_venta WHERE producto_id = $idp and venta_id = $venta_id";
                                 $connection->query( $query );
                            }
 
                          }
                      }
                   }
                   $connection->commit();


                   $query = "SELECT sum( producto_venta.cantidad) FROM producto_venta WHERE venta_id = $venta_id ";
                   $data = $connection->query($query);  
                   $results = $data->fetch(PDO::FETCH_BOTH ); 
                   $count = $results[0]; 
 
                   if( $count == 0 )
                   {
                       $query = "UPDATE venta SET estado = 2 WHERE id = $venta_id ";
                       $connection->query($query);
                       $func->WriteQuery( $connection , $query );
                   }

                   $msg = $msg->success("Devolución registrada exitosamente.");
                   $values = array("count" => $count );
                   echo json_encode( array("row" => "devolver_venta" ,"values" => $values , "msg" => $msg ) );
 
                }
                catch(PDOException $e) 
                { 
                   $connection->rollBack();
                   $msg = $msg->warning("No se pudo registar la devolución.");
                   echo json_encode( array( "msg" => $msg ) ); 
                }
             }
          break;

          case "DevolverCompra":  
             if( !empty($productos) && !empty($compra_id) && !empty($cantidad) )
             {
                try 
                {
                   $connection->beginTransaction();
                   foreach ($productos as $key => $idp) 
                   {
                      if( $_POST["cantidad"][$key] > 0  )
                      {
                          $cant =  $_POST["cantidad"][$key];

                          $query = "UPDATE producto_punto SET cantidad = cantidad - $cant WHERE producto_id = $idp AND punto_id = $idpunto ";
                          $connection->query($query);
                          $func->WriteQuery( $connection , $query );

                          $query = "UPDATE producto_compra SET cantidad = cantidad - $cant WHERE producto_id = $idp AND compra_id = $compra_id ";
                          $connection->query($query);
                          $func->WriteQuery( $connection , $query );
                      }
                   }
                   $connection->commit();


                   $query = "SELECT sum( producto_compra.cantidad) FROM producto_compra WHERE compra_id = $compra_id ";
                   $data = $connection->query($query);  
                   $results = $data->fetch(PDO::FETCH_BOTH ); 
                   $count = $results[0]; 
 
                   if( $count == 0 )
                   {
                       $query = "UPDATE compra SET estado = 2 WHERE id = $compra_id ";
                       $connection->query($query);
                       $func->WriteQuery( $connection , $query );
                   }
                   
                   $msg = $msg->success("Devolución registrada exitosamente.");
                   $values = array("count" => $count );
                   echo json_encode( array("row" => "devolver_compra" ,"values" => $values , "msg" => $msg ) );
 
                }
                catch(PDOException $e) 
                { 
                   $connection->rollBack();
                   $msg = $msg->warning("No se pudo registar la devolución.");
                   echo json_encode( array( "msg" => $msg ) );

                }
             }
          break;  

          case "CrearProducto": 
               $sw = 0;

               $archivo = "";
                     
               if (!empty($_FILES['foto']['tmp_name']) )
               { 
                    $prefijo = substr(md5(uniqid(rand())),0,10);
                    if ($_FILES["foto"]['name'] != "")
                    { 
                        $name = $_FILES["foto"]['name']; 


                        $prefijo = substr(md5(uniqid(rand())),0,10);
                        $extension = explode('.', $name) ;

                        $extension =  $extension[ count($extension) - 1];

                        $destino =  "../assets/products_photo/".$prefijo.'.'.$extension;
                        $archivo =  $prefijo.'.'.$extension;
                        if (copy ($_FILES['foto']['tmp_name'],$destino))
                        { 
                            
                        }
                        else
                        {
                              echo $msg->error("No se pudo copiar el archivo","");
                        }
                    }
               }               

               if(empty($_POST["stock"]))
                 $_POST["stock"] = 0;   
                
               $nombre = utf8_decode($nombre);
                                    
               $_POST["nombre"] = trim( $_POST["nombre"] );      
               $query = "INSERT INTO producto VALUES( null,'$nombre','$codigo',$stock,'$archivo',$idusuario,1,0,NULL) ";
               $connection->query($query);
               $func->WriteQuery( $connection , $query ); 

               if( empty($cantidad) )
                  $cantidad = 0;
               if( empty($precio) )
                  $precio = 0;
               if( empty($costo) )
                  $costo = 0;
               if( empty($precio_minimo) )
                  $precio_minimo = 0;

                
               $query = "SELECT max(id) AS id FROM producto";
               $data = $connection->query($query);
               $results = $data->fetch(PDO::FETCH_BOTH ); 
               $id = $results['id']; 

               $query = "INSERT INTO producto_punto VALUES(NULL,$cantidad,$precio,$precio_minimo,$costo,0,true,$id,$idpunto,0,0,$cantidad,$costo,$precio)"; 
               $connection->query($query); 
               $func->WriteQuery( $connection , $query );    
               
               echo $msg->success("Producto registrado exitosamente");     
          break;

          case "Resetear":
             $query = "UPDATE producto_punto SET cnt = cantidad ";
             $connection->query( $query );

             $query = "UPDATE producto_punto SET cantidad = 0 ";
             $connection->query( $query ); 

             echo $msg->success("Productos reseteados exitosamente");

          break;

          case "Restaurar":
             $query = "UPDATE producto_punto SET cantidad =  cnt";
             $connection->query( $query );
 

             echo $msg->success("Productos restaurado exitosamente");

          break;

          case "EditarProducto": 
              $connection->query("SET NAMES utf8");
              $archivo = "";
              if (!empty($_FILES['foto']['tmp_name']) )
              {
                  $prefijo = substr(md5(uniqid(rand())),0,10);
                  if ($_FILES["foto"]['name'] != "")
                  { 
                      $name = $_FILES["foto"]['name'];  

                      $prefijo = substr(md5(uniqid(rand())),0,10);
                      $extension = explode('.', $name) ;

                      $extension =  $extension[ count($extension) - 1];

                      $destino =  "../assets/products_photo/".$prefijo.'.'.$extension;
                      $archivo =  $prefijo.'.'.$extension;
                      if (copy ($_FILES['foto']['tmp_name'],$destino))
                      {
                          $query = "SELECT imagen FROM producto WHERE id = $id";
                          $data = $connection->query($query);
                          $results = $data->fetch(PDO::FETCH_BOTH ); 
                           
                          if( strlen( trim( $results['imagen']) ) > 0 )
                          {
                            if ( file_exists("../assets/products_photo/".$results['imagen']) )
                              unlink("../assets/products_photo/".$results['imagen']); 
                          }
                      }
                      else
                      {
                            echo $msg->error("No se pudo copiar el archivo","");
                      }
                  }
                  else
                  {
                      echo $msg->error("Error al subir el archivo","");
                  }
              }

              //$nombre = utf8_decode($nombre);

              $query = "SELECT cantidad FROM producto_punto WHERE producto_id = $id";
              $data = $connection->query($query);  
              $result = $data->fetch(PDO::FETCH_ASSOC); 
              $cantidad_anterior = $result['cantidad'];


              if( $cantidad > $cantidad_anterior )
              {
                 $entrada = $cantidad - $cantidad_anterior;
                 $func->Entrada( $connection , $entrada , "EDICION" , $id , $idusuario , 1 ); 
              }
              else if( $cantidad < $cantidad_anterior )
              {
                 $salida = $cantidad_anterior - $cantidad;
                 $func->Salida( $connection , $salida , "EDICION" , $id , $idusuario , 1 ); 
              }

              $nombre = limpiarTildes( $nombre );
              $codigo = limpiarTildes( $codigo ); 

               
              $query = "UPDATE producto SET nombre='$nombre', codigo = '$codigo' , stock='$stock', imagen='$archivo'  WHERE id = $id ";
              $connection->query($query);   
              $func->WriteQuery( $connection , $query );
             
              $query = "UPDATE producto_punto SET cantidad=$cantidad, precio = $precio , precio_minimo=$minimo , cnt=$cantidad, pre = $precio , cost=$costo , costo=$costo   WHERE producto_id = $id ";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );

              $msg = $msg->success("Producto actualizado exitosamente");          

              $values = array("nombre" => $nombre ,
                              "cantidad" => $cantidad, 
                              "stock" => $stock, 
                              "costo" => $costo , 
                              "precio" => $precio ,
                              "total_costo" => $cantidad * $costo , 
                              "total_precio" => $cantidad * $precio );

              echo json_encode( array("row" => "editar_producto" ,"values" => $values , "msg" => $msg ) );
 
          break; 
          
          case "BorrarProducto":
              $query = "UPDATE  producto_punto 
                        SET estado = false WHERE producto_id = $id AND punto_id = $idpunto ";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );

              $query = "UPDATE  producto
                        SET estado = false WHERE id = $id";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Producto borrado exitosamente");
          break; 

          case "eliminarProductoCompra":
              if( !empty($idproducto) && !empty($cantidad) && !empty($idcompra) )
              {
                $query = "SELECT cantidad FROM producto_punto WHERE producto_id = $idproducto";
                $data = $connection->query($query); 
                $producto = $data->fetch(PDO::FETCH_OBJ);
                if( $producto->cantidad >= $cantidad ) 
                {
                    $query = "DELETE FROM  producto_compra WHERE producto_id = $idproducto AND compra_id = $idcompra ";
                    $connection->query($query); 

                    $query = "UPDATE  producto_punto SET cantidad = cantidad - $cantidad WHERE producto_id = $idproducto";
                    $connection->query($query); 

                    $func->Salida( $connection , $cantidad , "EDICION-COMPRA" , $idproducto , $idusuario , 1 ); 

                    echo json_encode( array("status" => true , "cantidad" => $producto->cantidad , "idproducto" => $idproducto ));
                }
                else
                   echo json_encode( array("status" => false , "cantidad" => $producto->cantidad ));
        
              }
              /*
              
              
              $query = "UPDATE  producto SET estado = false WHERE id = $id";
              $connection->query($query);
              */ 
              
              
          break; 

          case "EliminarCatalogo": 
              $query = "DELETE  FROM catalogo WHERE id = $id";
              $connection->query($query); 
              echo $msg->success("Producto borrado del catalogo exitosamente");
          break; 

          case "EliminarMarca": 
              $query = "DELETE  FROM marca WHERE id = $id";
              $connection->query($query); 
              echo $msg->success("Marca borrada exitosamente");
          break; 

          case "CrearSucursal":
           $sw = 0;
           if( !empty($_POST["nombre"])   )
           {      

                 $query = "SELECT * FROM punto WHERE punto = '$nombre'";
                 $data = $connection->query($query);  
                 $puntos = $data->fetchAll(PDO::FETCH_BOTH ); 
                  
                 if( count($puntos) <= 0)
                 {
                    $nombre = trim( $_POST["nombre"] );    
                    $date = date('Y-m-d H:i:s');   
                    $query = "INSERT INTO punto VALUES( null,'$nombre',1,'$date') ";
                    $connection->query($query); 

                    echo $msg->success("Punto o sucursal registrado exitosamente"); 
                 }
                 else
                 {
                    echo $msg->warning("Ya existe una sucursal o punto con el nombre ingresado. Por favor intentelo nuevamente");
                 }              
            }
            else
              echo $msg->danger("Faltan datos en el formulario"); 
          break;          

          case "InactivarCliente":
              $query = "UPDATE cliente SET status = false WHERE id = $id";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Cliente inactivado exitosamente");
          break;

          case "ActivarCliente":
              $query = "UPDATE cliente SET status = true WHERE id = $id";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Cliente activado exitosamente");
          break;

          case "InactivarProveedor":
              $query = "UPDATE proveedor SET status = false  WHERE id = $id";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Proveedor inactivado exitosamente");
          break;

          case "ActivarProveedor":
              $query = "UPDATE proveedor SET status = true  WHERE id = $id";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Proveedor activado exitosamente");
          break;

          case "InactivarTrabajador":
              $query = "UPDATE usuario SET status = false  WHERE id = $id";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Trabajador inactivado exitosamente");
          break;

          case "ActivarTrabajador":
              $query = "UPDATE usuario SET status = true  WHERE id = $id";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Trabajador activado exitosamente");
          break;
 

          case "RegistrarEgreso":
                 if( !empty($valor) and !empty($concepto)  )
                 {    
                     $date = date('Y-m-d H:i:s'); 
                     $concepto = utf8_decode($concepto);
                     $query = "INSERT INTO egreso VALUES(NULL,$valor,'$concepto','$date',$idusuario,$idpunto)";                              
                     $connection->query($query); 
                     $func->WriteQuery( $connection , $query );
                     echo $msg->success("Egreso registrado exitosamente","");
                 } 
                 else
                 {
                     echo $msg->warning("Faltan datos en el formulario","");
                 }
          break;

          case "RegistrarGasto":
                 if( !empty($valor) and !empty($concepto)  )
                 {    
                     $date = date('Y-m-d H:i:s'); 
                     $concepto = utf8_decode($concepto);
                     $query = "INSERT INTO gasto VALUES(NULL,$valor,'$concepto','$date',$idusuario,$idpunto)";                              
                     $connection->query($query); 
                     $func->WriteQuery( $connection , $query );
                     echo $msg->success("Gasto registrado exitosamente","");
                 } 
                 else
                 {
                     echo $msg->warning("Faltan datos en el formulario","");
                 }
          break;

          case "RegistrarCliente":
               if( !empty($nombres) and !empty($apellidos)  and !empty($documento)  and !empty($telefono) )
               {    
                   $date = date('Y-m-d H:i:s'); 

                   if( empty($direccion) )
                    $direccion = "";
                   if( empty($correo) )
                    $correo = "";
                   $nombres = utf8_decode(trim($nombres));
                   $apellidos = utf8_decode(trim($apellidos));
                   $query = "INSERT INTO cliente VALUES(NULL,'$nombres','$apellidos','$correo','$documento','$telefono','$direccion','$date',1,1,$idusuario)";    
                   $connection->query($query); 
                   $func->WriteQuery( $connection , $query );
                   echo $msg->success("Cliente registrado exitosamente","");
               } 
               else
               {
                     echo $msg->warning("Faltan datos en el formulario","");
               }
          break;

          case "EditarProveedor":
              $query = "UPDATE proveedor SET nombres='$nombre', documento='$documento', telefono='$telefono' , direccion='$direccion'  WHERE id = $id ";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );

              $values = array("nombre" => $nombre ,
                              "telefono" => $telefono, 
                              "documento" => $documento, 
                              "direccion" => $direccion);

              $msg = $msg->success("Proveedor actualizado exitosamente");
              echo json_encode( array("row" => "editar_proveedor" ,"values" => $values , "msg" => $msg ) );
                         
          break; 

          case "EditarCliente":
              $query = "UPDATE cliente SET nombres='$nombres', apellidos='$apellidos', documento='$documento', telefono='$telefono' , direccion='$direccion', correo='$correo'  WHERE id = $id ";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              $msg = $msg->success("Cliente actualizado exitosamente");

              $values = array("nombres" => $nombres ,
                              "apellidos" => $apellidos, 
                              "documento" => $documento, 
                              "telefono" => $telefono, 
                              "direccion" => $direccion,
                              "correo" => $correo);
 
              echo json_encode( array("row" => "editar_cliente" ,"values" => $values , "msg" => $msg ) );
          break;  
   

          case "SacarDineroCaja":
              if( !empty($sacar) )
              {
                  $query = "UPDATE caja SET valor = valor - $sacar WHERE status = true ";
                  $connection->query($query);
                  $func->WriteQuery( $connection , $query );

                  echo $msg->success("Operación realizada exitosamente ");
              }
              else
              {
                  echo $msg->warning("Faltan datos en el formulario");
              }
          break;

          case "IngresarDineroCaja":
              if( !empty($ingresar) )
              {
                  $query = "UPDATE caja SET valor = valor + $ingresar WHERE status = true ";
                  $connection->query($query);
                  $func->WriteQuery( $connection , $query );

                  echo $msg->success("Operación realizada exitosamente ");
              }
              else
              {
                  echo $msg->warning("Faltan datos en el formulario");
              }
          break;

          case "EditarResolucion": 
              if( $desde < $hasta )
              {
                $query = "UPDATE resolucion SET inicio = $desde , fin = $hasta , prefijo = '$prefijo' WHERE id = $id ";
                $connection->query($query); 
                echo $msg->success("La facturas fueran actualizadas exitosamente");
              }
              else
              {
                 echo $msg->warning("El valor de inicio no puede ser mayor al valor final");
              }
          break; 

          case "CrearResolucion": 
              $repetido = false;
              if( $desde < $hasta )
              {
                  $query = "SELECT * FROM resolucion WHERE $desde >= inicio and $hasta <= fin"; 
                  $data = $connection->query($query);
                  $results = $data->fetch(PDO::FETCH_BOTH ); 
                  if( !empty($results['id']) )
                  {
                      $repetido = true;
                  }


                  $query = "SELECT * FROM resolucion WHERE $hasta >= inicio and $hasta <= fin";
                  $data = $connection->query($query);
                  $results = $data->fetch(PDO::FETCH_BOTH ); 
                  if( !empty($results['id']) )
                  {
                    $repetido = true;
                  }

                  if( $repetido == false )
                  {
                    $current = date('Y-m-d'); 
                    $query = "INSERT INTO resolucion VALUES( null , $desde , $hasta , '$prefijo' , 1 , '$current' , 0 ) ";
                    $connection->query($query); 
                    $id = $connection->getLastId();
                    
                    $query = "INSERT INTO resolucion_numeros VALUES ";
                    for( $i = $desde ; $i <= $hasta ; $i++ )
                    {
                         $query.="(NULL,$i,$id,1),";
                    }
                    $query = substr ($query, 0, strlen($query) - 1); 
                    $query.=";";
                    $connection->query($query); 

                    $form = $msg->success("Numeros de facturas creados exitosamente");
                    echo json_encode( array("form" => utf8_encode($form)  ));  
                  }
                  else
                  {
                      $form = $msg->warning("Los numeros de facturas que intentas crear ya se encuentran registrados ");
                      echo json_encode( array("msg" => utf8_encode($form) , "status" => false ));  
                  }
              }
              else
              {
                 $form = $msg->warning("El valor de inicio no puede ser mayor al valor final");
                 echo json_encode( array("msg" => utf8_encode($form) , "status" => false ));  
              }
          break; 

          case "EditarProveedor":
              $query = "UPDATE proveedor SET nombre='$nombre', documento='$documento', telefono='$telefono' , direccion='$direccion'  WHERE id = $id ";
              $connection->query($query); 
              $func->WriteQuery( $connection , $query );
              echo $msg->success("Proveedor actualizado exitosamente");
          break;

          case "EditarTrabajador": 
              if (!empty($_FILES['foto']['tmp_name']) )
              {
                  $prefijo = substr(md5(uniqid(rand())),0,10);
                  if ($_FILES["foto"]['name'] != "")
                  { 
                      $name = $_FILES["foto"]['name']; 


                      $prefijo = substr(md5(uniqid(rand())),0,10);
                      $extension = explode('.', $name) ;

                      $extension =  $extension[ count($extension) - 1];

                      $destino =  "../assets/users_photo/".$prefijo.'.'.$extension;
                      $archivo =  $prefijo.'.'.$extension;
                      if (copy ($_FILES['foto']['tmp_name'],$destino))
                      {
                          $query = "SELECT imagen FROM usuario WHERE id = $id";
                          $data = $connection->query($query);
                          $results = $data->fetch(PDO::FETCH_BOTH ); 
                           
                          if( strlen( trim( $results['imagen']) ) > 0 )
                          {
                            if ( file_exists("../assets/users_photo/".$results['imagen']) )
                              unlink("../assets/users_photo/".$results['imagen']); 
                          }
                              
                          $query = "UPDATE usuario set imagen = '$archivo' WHERE id = $id";
                          $connection->query($query);
                      }
                      else
                      {
                            $msg = $msg->error("No se pudo copiar el archivo","");
                      }
                  }
                  else
                  {
                      $msg = $msg->error("Error al subir el archivo","");
                  }
              }
              if( !empty($nombres) and  !empty($apellidos) and  !empty($telefono) and  !empty($cedula) )
              {
                  $query = "UPDATE usuario SET codigo='$codigo' , nombre='$nombres', apellido='$apellidos', telefono='$telefono' , cedula='$cedula'  WHERE id = $id ";
                  $connection->query($query); 
                  $func->WriteQuery( $connection , $query );

                  if( !empty($nombre_usuario) )
                  {
                     $query = "UPDATE usuario SET login=md5('$nombre_usuario') WHERE id = $id ";
                     $connection->query($query);  
                     $func->WriteQuery( $connection , $query );
                  }
                  if( !empty($clave) )
                  {
                     $query = "UPDATE usuario SET password=md5('$clave') WHERE id = $id ";
                     $connection->query($query);  
                     $func->WriteQuery( $connection , $query );
                  }
                  $msg = $msg->success("Trabajador actualizado exitosamente");

                  $values = array("codigo" => $codigo ,
                              "nombre" => $nombres, 
                              "apellido" => $apellidos, 
                              "telefono" => $telefono , 
                              "cedula" => $cedula);

                  echo json_encode( array("row" => "editar_trabajador" ,"values" => $values , "msg" => $msg ) );
                  
              }
              else
              {
                  $msg = $msg->warning("No se pudo editar el trabajador. Faltan datos en el formulario.");
                  echo json_encode( array("msg" => $msg ) );

              }
          break;

          case "RegistrarTrabajador":
                 if( !empty($nombres) and !empty($apellidos)  and !empty($documento) and !empty($telefono) and !empty($codigo)  )
                 {    
                     $nombres = utf8_decode($nombres);
                     $query = "INSERT INTO usuario VALUES(NULL,'$codigo', '$nombres','$apellidos','$telefono','$documento','',md5('$nombre_usuario'),md5('$clave'),true)";    
                     $connection->query($query); 
                     $func->WriteQuery( $connection , $query );

                     $query = "SELECT max(id) AS idusuario FROM usuario";
                     $data = $connection->query($query);
                     $results = $data->fetch(PDO::FETCH_BOTH ); 

                     $usuario_id = $results['idusuario'];  
                     $query = "INSERT INTO usuario_punto VALUES(null,$usuario_id , $idpunto , true)";
                     $connection->query($query);
                     $func->WriteQuery( $connection , $query );

                     echo $msg->success("Cliente registrado exitosamente","");
                 } 
                 else
                 {
                     echo $msg->warning("Faltan datos en el formulario","");
                 }
          break;

          case "RegistrarProveedor":
                 if( !empty($nombre) and !empty($documento) and !empty($direccion) and !empty($telefono) )
                 {     
                     $date = date('Y-m-d H:i:s'); 
                     $nombre = utf8_decode($nombre);
                     $query = "INSERT INTO proveedor VALUES(NULL,'$documento','$telefono','$nombre','','','$direccion','$date',true,$idpunto,$idusuario)";    
                     $connection->query($query); 
                     $func->WriteQuery( $connection , $query );
                     echo $msg->success("Proveedor registrado exitosamente","");
                 } 
                 else
                 {
                     echo $msg->warning("Faltan datos en el formulario","");
                 }
          break; 

          case "RegistrarCotizacion":
              try 
              {
                   $connection->beginTransaction(); 
                   if( !empty($idproducto) and !empty($cantidad) && !empty($idcliente) )
                   {    
                        
                        $date = date('Y-m-d H:i:s'); 
                        $query = "INSERT INTO cotizacion VALUES(NULL,'$date',$idcliente,$idusuario,$idpunto,1)";                              
                        $connection->query($query); 
                        $id = $connection->getLastId();
                        //$func->WriteQuery( $connection , $query ); 




                        foreach ($_POST["idproducto"] as $i => $idp) 
                        {
                          $sumatotal = 0; 
                          if(!empty($_POST["cantidad"][$i]) and !empty($_POST["precio"][$i]) )
                          {
                              
                               $cant = $_POST["cantidad"][$i];
                               $pre = $_POST["precio"][$i];

                               $pre = str_replace(array(',', '$'), "", $pre); 

                               $query = "INSERT INTO producto_cotizacion VALUES(null,$idp,$cant,$pre,$id)";
                               $connection->query($query); 
                               //$func->WriteQuery( $connection , $query );
                          }  
                          
                        } 
                        $m = $msg->success("La cotizacion fue registrada exitosamente","");

                        $msg = "<div class='content'> 
                                    <div style='width:100%' id='respon'>$m</div>
                                    <div id='content_print' data-title='Cotizacion' data-url='reports/Cotizacion.php' data-data='id=$id' ></div> 
                                    <div id='content_print_termica' data-url='imprimir_termica.php' data-data='opcion=imprimir_cotizacion&id=$id' ></div> 
                                 </div>";  
                       echo json_encode( array("msg" => utf8_encode($msg)  ));   
  
                   $connection->commit();
                        
                   }
                   else
                   {
                      echo json_encode( array( "status" => false , "msg" => "Faltan datos en el formulario"));  
                   }

              }
              catch(PDOException $e) 
              { 
                  $connection->rollBack();
                  echo json_encode( array( "status" => false , "msg" => "Error no se pudo registrar la cotización $e"));  
              }
          break;
 

          case "ConvertirCotizacionFactura":
              
              try 
              {
                   $connection->beginTransaction();

                   if( !empty($tipoventa) and !empty($idproducto) and !empty($cantidad) )
                   {   
                         
                       $limite_date = 'NULL';
                       if( !empty($fecha) && !empty($hora) )
                          $limite_date = "'".$fecha." ".$hora.":00"."'"; 

                        if( empty($punto_id) )
                          $punto_id = 0;

                        if( empty($idcliente) )
                        {
                          $idcliente = 'NULL';
                          if( !empty($nombre_cliente) )
                          {
                              $date = date('Y-m-d H:i:s'); 
                              $query = "INSERT INTO cliente VALUES( NULL , '$nombre_cliente' , '' , '' , '' , '' , '' , '$date' , true , 1 , $idusuario ) ";
                              $connection->query( $query );
                              $idcliente = $connection->getLastId(); 
                          }
                        } 
                       $date = date('Y-m-d H:i:s'); 
                       $query = "INSERT INTO venta VALUES(NULL,'$date','','$recibo',$limite_date,$tipoventa,$idpunto,$idusuario,$idcliente , 1 )";                              
                       $connection->query($query);

                       $id = $connection->getLastId(); 

                       foreach ($_POST["idproducto"] as $i => $idp) 
                       {
                          $sumatotal = 0; 
                          if(!empty($_POST["cantidad"][$i]) and !empty($_POST["precio"][$i]) )
                          {
                              
                               $cant = $_POST["cantidad"][$i];
                               $cost_normal = $_POST["costonormal"][$i];
                               $cost_liq = $_POST["costoliquidado"][$i];

                               $pre = $_POST["precio"][$i];
                               $pre = str_replace(array(',', '$'), "", $pre);
                               $total = $cant * $pre;
                               $sumatotal += $total; 
                               $idcliente = 0; 
 
                               $query = "UPDATE producto_punto SET cantidad = cantidad - $cant WHERE producto_id = $idp";
                               $connection->query($query);  

                               $query = "INSERT INTO producto_venta VALUES(null,$idp,$cant,$pre,$cost_normal,$cost_liq,$id)";
                               $connection->query($query); 

                               $query = "SELECT producto.id
                                          FROM  producto
                                          INNER JOIN producto_punto ON producto.id = producto_id
                                          WHERE punto_id = 1
                                                AND producto_punto.cantidad <= producto.stock
                                                AND producto.id = $idp";
                               $data = $connection->query($query);  
                               $results = $data->fetchAll(PDO::FETCH_ASSOC);
                               if( count($results) > 0 )
                               {
                                    $date = date('Y-m-d');
                                    $query = "UPDATE producto SET status_stock = 2 , fecha_terminado = '$date' WHERE id = $idp";                                 
                                    $connection->query($query);

                                    $func->WriteQuery( $connection , $query );
                               }
                          }  
                          
                       } 

                       $query = "UPDATE cotizacion SET status = 2 WHERE id = $cotizacion_id";
                       $connection->query( $query );
                       
                       $m = $msg->success("La venta fue registrada exitosamente","");

                       $_POST['pdf'] = "reports/Ventas.php";
                       $_POST['id'] = $id;
                      
                       $form = "<div class='content'> 
                                    <div style='width:100%' id='respon'>$m</div>
                                    <div id='content_print' data-title='VENTAS' ></div>  
                                    
                                 </div>
                                 
                            </div>";  
                       echo json_encode( array("form" => utf8_encode($form) , "var" => $_POST ));  
                   }
                   $connection->commit();
              }
              catch(PDOException $e) 
              { 
                  $connection->rollBack();
                  echo json_encode( array( "status" => false , "var"=>$_POST, "msg" => "Error no se pudo registrar la venta"));  
              } 
          break;

          case "RegistrarVentas":
              $q = "";
              $json_ventas = "";
              try
              {
                   $connection->beginTransaction();
                   if( !empty($tipoventa) and !empty($idproducto) and !empty($cantidad) )
                   {

                       $query = "SELECT valor FROM configuracion WHERE name = 'Precio bolsas'";
                       $data = $connection->query($query);
                       $results = $data->fetch(PDO::FETCH_ASSOC );

                       $precio_bolsas = $results['valor'];

                       $limite_date = 'NULL';
                       if( !empty($fecha) && !empty($hora) )
                          $limite_date = "'".$fecha." ".$hora.":00"."'";

                        if( empty($punto_id) )
                          $punto_id = 0;

                        if( empty($idcliente) )
                        {
                            $idcliente = 'NULL';
                            if( !empty($nombre_cliente) )
                            {
                                $date = date('Y-m-d H:i:s');
                                $query = "INSERT INTO cliente VALUES( NULL , '$nombre_cliente' , '' , '' , '' , '' , ''  , '$date' , true , 1 , $idusuario ) ";
                                $connection->query( $query );
                                $func->WriteQuery( $connection , $query );
                                $idcliente = $connection->getLastId();
                            }
                        }

                       $date = date('Y-m-d H:i:s');

                       $query = "INSERT INTO venta VALUES(NULL,'','$date',NULL,'$recibo',$limite_date,$tipoventa,$idpunto,$idusuario,$idcliente , 1 , 0 , $precio_bolsas , false )";
                       $connection->query($query);
                       $id = $connection->getLastId();

                       $ventaArray = [
                            "data" => array(
                                            "id"   => $id,
                                            "codigo_venta" => "" ,
                                            "fecha" => $date ,
                                            "factura" => null ,
                                            "recibo" => $recibo ,
                                            "limite" => $limite_date ,
                                            "tipo_venta_id" => $tipoventa,
                                            "punto_id" => $idpunto ,
                                            "usuario_id" => $idusuario ,
                                            "cliente_id" => $idcliente ,
                                            "estado" => 1 ,
                                            "bolsas" => 0 ,
                                            "vr_bolsas" => 0 ,
                                            "checked" => 0 ,
                                           ),
                            "productoVenta" => array()
                       ];
                       $date = date('Y-m-d H:i:s');

                       foreach ($_POST["idproducto"] as $i => $idp)
                       {
                          $sumatotal = 0;
                          if(!empty($_POST["cantidad"][$i]) and !empty($_POST["precio"][$i]) )
                          {
                               $cant = $_POST["cantidad"][$i];
                               $cost_normal = $_POST["costonormal"][$i];

                               $pre = $_POST["precio"][$i];
                               $pre = str_replace(array(',', '$'), "", $pre);
                               $total = $cant * $pre;
                               $sumatotal += $total;
                               $idcliente = 0;

                               $query = "UPDATE producto_punto SET cantidad = cantidad - $cant WHERE producto_id = $idp";
                               $connection->query($query);

                               $func->Salida( $connection , $cant , "VENTA" , $idp , $idusuario , 0 );

                               $query = "INSERT INTO producto_venta VALUES(null,$idp,$cant,$pre,$cost_normal,$id)";
                               $connection->query($query);

                               $arrayProductoVenta = array('idproducto' => $idp , 'precio' => $pre , 'cantidad'  => $cant , 'cantidad'  => $cant , 'costo' => $cost_normal );
                               array_push($ventaArray['productoVenta'], $arrayProductoVenta );


                               $query = "SELECT producto.id
                                          FROM  producto
                                          INNER JOIN producto_punto ON producto.id = producto_id
                                          WHERE punto_id = 1
                                                AND producto_punto.cantidad <= producto.stock
                                                AND producto.id = $idp";
                               $data = $connection->query($query);
                               $results = $data->fetchAll(PDO::FETCH_ASSOC);
                               if( count($results) > 0 )
                               {
                                    $date = date('Y-m-d');
                                    $query = "UPDATE producto SET status_stock = 2 , fecha_terminado = '$date' WHERE id = $idp";
                                    $connection->query($query);
                               }
                          }
                       }

                       $jsonStringVenta = json_encode($ventaArray);
                       $func->WriteQueryCompraVenta( $connection , $jsonStringVenta , 1 );
                       $m = $msg->success("La venta fue registrada exitosamente","");

                       $msg = "<div class='content'>
                                    <div style='width:100%' id='respon'>$m</div>
                                    <!--<div id='content_print' data-title='VENTAS' data-url='reports/Facturas.php' data-data='id=$id' ></div> -->
                                    <div id='content_print' data-title='RECIBO' data-url='reports/Recibos.php'  data-data='id=$id'></div>
                                    <div id='content_print_termica' data-url='imprimir_termica.php' data-data='opcion=imprimir_venta&id=$id' ></div>
                                 </div>
                            </div>";
                       echo json_encode( array("msg" => utf8_encode($msg)  ));
                   }
                   $connection->commit();
              }
              catch(PDOException $e)
              {
                  $connection->rollBack();
                  echo json_encode( array( "status" => false , "e" => $e , "msg" => "Error no se pudo registrar la venta"));
              }
          break;

          case "RegistrarCompra":
              try
              {
                     $connection->beginTransaction();
                     if( !empty($idpunto) and !empty($tipocompra) and !empty($idproducto) and !empty($cantidad) and !empty($idproveedor) and !empty($factura) )
                     {
                         if( $tipocompra == '2' )
                         {
                              if( empty($limite) OR empty($llegada) )
                              {
                                  echo json_encode( array( "status" => false , "msg" => "Faltan datos en el formulario"));
                                  exit();
                              }
                         }
                         $sql = "SELECT max(numero) numero FROM compra ";
                         $data = $connection->query($sql);
                         $results = $data->fetchAll(PDO::FETCH_BOTH );
                         if( count($results) > 0 )
                         {
                             $row = $results[0];
                             $numero = $row['numero'] + 1 ;
                         }
                         else
                             $numero = 1;

                         $limiteJson = "null";
                         $llegadaJson = "null";

                         if( !empty($limite)  )
                         {
                              $limiteJson = $limite;
                              $limite = "'".$limite."'";
                         }
                         else
                             $limite = "null";


                         if( !empty($llegada) )
                         {
                              $llegadaJson = $llegada;
                              $llegada = "'".$llegada."'";
                         }
                         else
                            $llegada = "null";


                         $date = date('Y-m-d H:i:s');
                         $query = "INSERT INTO compra VALUES(NULL,'$date' , '$factura' , '$numero' , $tipocompra , $idproveedor , $idpunto , $idusuario , $limite , $llegada , 1 )";
                         $connection->query($query);
                         $id = $connection->getLastId();

                         $compraArray = [
                              "data" => array(
                                              "id"   => $id,
                                              "fecha" => $date ,
                                              "factura" => $factura ,
                                              "numero" => $numero ,
                                              "tipo_compra_id" => $tipocompra ,
                                              "proveedor_id" => $idproveedor,
                                              "punto_id" => $idpunto ,
                                              "usuario_id" => $idusuario ,
                                              "llegada" => $limiteJson ,
                                              "limite" => $llegadaJson ,
                                              "estado" => 1
                                             ),
                              "productoCompra" => array()
                         ];
                         foreach ($_POST["idproducto"] as $i => $idp)
                         {
                            $sumatotal = 0;
                            if(!empty($_POST["cantidad"][$i]) and !empty($_POST["precio"][$i])  and !empty($_POST["preciominimo"][$i])  )
                            {
                                 $cant = $_POST["cantidad"][$i];
                                 $pre = $_POST["precio"][$i];
                                 $cost = $_POST["costo"][$i];
                                 $pre_min = $_POST["preciominimo"][$i];

                                 $pre = str_replace(array(',', '$'), "", $pre);
                                 $cost = str_replace(array(',', '$'), "", $cost);

                                 $total = $cant * $pre;
                                 $sumatotal += $total;

                                 $query = "INSERT INTO producto_compra VALUES(null,$idp,$id,$cant,$pre,$cost)";
                                 $connection->query($query);

                                 $arrayProductoCompra = array('idproducto' => $idp , 'precio' => $pre , 'precio_minimo'  => $pre_min , 'cantidad'  => $cant , 'costo' => $cost );
                                 array_push($compraArray['productoCompra'], $arrayProductoCompra );

                                 $query = "UPDATE producto_punto SET cantidad = cantidad + $cant , precio = $pre , costo = $cost , precio_minimo = $pre_min  WHERE punto_id = $idpunto AND producto_id = $idp";
                                 $connection->query($query);

                                 $func->Entrada( $connection , $cant , "COMPRA" , $idp , $idusuario , 0 );
                            }
                         }
                         $jsonStringCompra = json_encode($compraArray);
                         $func->WriteQueryCompraVenta( $connection , $jsonStringCompra , 2 );
                         $connection->commit();

                         $msg = $msg->success("La compra fue registrada exitosamente","");
                         echo json_encode( array("msg" => utf8_encode($msg)  ));

                     }
                     else
                     {
                        echo json_encode( array( "status" => false , "msg" => "Faltan datos en el formulario"));

                     }
              }
              catch(PDOException $e)
              {
                  $connection->rollBack();
                  echo json_encode( array( "e" => $e, "status" => false , "msg" => "Error no se pudo registrar la compra"));
              }
          break;

          case "ModificarCompra":
              try
              {
                     
                     $connection->beginTransaction();
                     if( !empty($idpunto) and !empty($tipocompra) and !empty($idproducto) and !empty($cantidad) and !empty($idproveedor) and !empty($factura) )
                     {
                         if( $tipocompra == '2' )
                         {
                              if( empty($limite) OR empty($llegada) )
                              {
                                  echo json_encode( array( "status" => false , "msg" => "Faltan datos en el formulario"));
                                  exit();
                              }
                         }
                         
                         $limiteJson = "null";
                         $llegadaJson = "null";

                         if( !empty($limite)  )
                         {
                              $limiteJson = $limite;
                              $limite = "'".$limite."'";
                         }
                         else
                             $limite = "null";


                         if( !empty($llegada) )
                         {
                              $llegadaJson = $llegada;
                              $llegada = "'".$llegada."'";
                         }
                         else
                            $llegada = "null";


                         $date = date('Y-m-d H:i:s');
                         $query = "UPDATE compra SET factura = '$factura' , tipo_compra_id = $tipocompra , proveedor_id = $idproveedor , llegada = $llegada , limite = $limite WHERE id = $idcompra";
                         $connection->query($query);

                         foreach ($_POST["idproducto"] as $i => $idp)
                         {
                            $sumatotal = 0;
                            if(!empty($_POST["cantidad"][$i]) and !empty($_POST["precio"][$i])  and !empty($_POST["preciominimo"][$i])  )
                            {
                                 $cant = $_POST["cantidad"][$i];
                                 $pre = $_POST["precio"][$i];
                                 $cost = $_POST["costo"][$i];
                                 $pre_min = $_POST["preciominimo"][$i];

                                 $pre = str_replace(array(',', '$'), "", $pre);
                                 $cost = str_replace(array(',', '$'), "", $cost);

                               

                                $query = "SELECT cantidad FROM producto_compra WHERE compra_id = $idcompra AND producto_id = $idp";
                                $data = $connection->query($query); 
                                $producto = $data->fetch(PDO::FETCH_OBJ);
                                if( !empty($producto) )
                                {
                                    //Disminuye
                                    if( $cant < $producto->cantidad )
                                    {
                                        if( $cant == 0 )
                                        {
                                            $query = "DELETE FROM  producto_compra WHERE producto_id = $idp AND compra_id = $idcompra ";
                                            $connection->query($query); 

                                            $query = "UPDATE  producto_punto SET cantidad = cantidad - $cant WHERE producto_id = $idp";
                                            $connection->query($query); 

                                            $func->Salida( $connection , $producto->cantidad , "EDICION-COMPRA" , $idp , $idusuario , 1 ); 
                                        }
                                        else
                                        {
                                            $query = "UPDATE  producto_compra 
                                                    SET cantidad = $cant ,  
                                                        precio = $pre ,
                                                        costo = $cost
                                                    WHERE producto_id = $idp AND compra_id = $idcompra";
                                            $connection->query($query); 

                                            $query = "UPDATE  producto_punto SET cantidad = cantidad - $cant WHERE producto_id = $idp";
                                            $connection->query($query); 

                                            $auxCant = $producto->cantidad - $cant;
                                            $func->Salida( $connection , $auxCant , "EDICION-COMPRA" , $idp , $idusuario , 1 ); 
                                        }
                                    }
                                    //Aumenta
                                    else if( $cant > $producto->cantidad )
                                    {
                                        $query = "UPDATE  producto_compra 
                                                SET  cantidad = $cant , 
                                                        precio = $pre ,
                                                        costo = $cost
                                                WHERE producto_id = $idp AND compra_id = $idcompra";
                                        $connection->query($query); 

                                        $query = "UPDATE  producto_punto SET cantidad = cantidad + $cant WHERE producto_id = $idp";
                                        $connection->query($query); 

                                        $auxCant = $cant - $producto->cantidad;
                                        $func->Entrada( $connection , $auxCant , "EDICION COMPRA" , $idp , $idusuario , 0 );
                                    }
                                }
                                else
                                {
                                    $query = "INSERT INTO producto_compra VALUES(null,$idp,$idcompra,$cant,$pre,$cost)";
                                    $connection->query($query);

                                    $query = "UPDATE producto_punto SET cantidad = cantidad + $cant , precio = $pre , costo = $cost , precio_minimo = $pre_min  WHERE punto_id = $idpunto AND producto_id = $idp";
                                    $connection->query($query);

                                    $func->Entrada( $connection , $cant , "EDICION-COMPRA-NUEVO" , $idp , $idusuario , 0 );
                                }

                            }
                         } 
                         $connection->commit();

                         $msg = $msg->success("La compra fue modificada exitosamente","");
                         echo json_encode( array("msg" => utf8_encode($msg)  ));

                     }
                     else
                     {
                        echo json_encode( array( "status" => false , "msg" => "Faltan datos en el formulario"));

                     }
              }
              catch(PDOException $e)
              {
                  $connection->rollBack();
                  echo json_encode( array( "e" => $e, "status" => false , "msg" => "Error no se pudo registrar la compra"));
              }
          break;




          case "AbonarPorCobrar":
              try 
              {
                    $connection->beginTransaction();
                    if( !empty($valor) )
                    { 
                        $date = date('Y-m-d H:i:s'); 
                        $query = "INSERT INTO abono_venta VALUES( null , $valor , '$date' , $id , $idusuario )";
                        $connection->query( $query );
                        $func->WriteQuery( $connection , $query );


                        $query = "SELECT  sum(precio * cantidad ) total  
                                  FROM venta 
                                  INNER JOIN producto_venta ON  venta_id = venta.id 
                                  WHERE venta.id = $id ";
                        $data = $connection->query($query);  
                        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
                        $row = $results[0]; 
                        $total = $row['total'];
                        

                        $abono = 0;
                        $query = "SELECT  sum(abono) abono FROM abono_venta  WHERE venta_id = $id ";
                        $data = $connection->query($query);
                        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
                        if( count( $results) > 0 ) 
                        {
                          $row = $results[0];
                          $abono = $row['abono'];
                        }

                        $debe = $total - $abono;
                        $values = array("debe" => $debe , "abono" => $abono );

                        if( $debe <= 0 )
                        {
                             $query = "UPDATE venta SET tipo_venta_id = 3 WHERE id = $id ";
                             $connection->query( $query );
                             $func->WriteQuery( $connection , $query );
                             $msg = $msg->success("Venta cancelada exitosamente.");
                        }
                        else
                          $msg = $msg->success("Abono registrado exitosamente ");

                        echo json_encode( array("row" => "por_cobrar" ,"values" => $values , "msg" => $msg ) );
                        $connection->commit();
                    }
                    else
                    {
                        $msg = $msg->warning("Faltan datos en el formulario.");
                        echo json_encode( array( "msg" => $msg , "status" => false  ) ); 
                        $connection->rollBack();
                    }
              }
              catch(PDOException $e) 
              { 
                 $connection->rollBack();
                 $msg = $msg->warning("Ocurrio un error no se pudo registar el abono." .$e); 
                 echo json_encode( array( "msg" => $msg , "status" => false  ) ); 
              }
          break;

          case "PorCobrar":
             $condicion = ""; 
             if( !empty($inicio) && !empty($fin) )
             {
                $condicion .= " AND date(venta.fecha) >= '$inicio' AND date(venta.fecha) <= '$fin' "; 

             }
             else if( !empty($inicio) )
             {
                $condicion .= " AND date(venta.fecha) = '$inicio' ";  
             }


             if( !empty($estado) )
             {
                 $condicion = " AND tipo_venta_id = $estado ";
             }
             else
             {
                $condicion = " AND tipo_venta_id = 2 ";
             }

             if( !empty($idusuarios) )
             {
                 $condicion.=" AND usuario_id IN (";
                 foreach ($idusuarios as $key => $value) 
                 {
                    $condicion .= $value.",";
                 }
                 $condicion = substr ($condicion, 0, strlen($condicion) - 1);
                 
                 $condicion.=")";
             } 

             $per_page = 10;

             $aux_page = $page;
             $aux_page -= 1;
             $start = $aux_page * $per_page; 

             $columns = array("DETALLE" ,"ABONAR" , "FECHA" , "CLIENTE" , "PRODUCTOS" , "TOTAL" );
             $query = "SELECT *
                      FROM venta  
                      WHERE 1  AND venta.punto_id = $idpunto $condicion";
              
              $data = $connection->query($query);  
              $results = $data->fetchAll(PDO::FETCH_BOTH ); 
              $count = count($results); 


              if( $count > 0 )
              {
                 $query = "SELECT tipo_venta_id,venta.id,SUM(cantidad) cantidad, factura , recibo , sum(precio * cantidad ) total ,  venta.fecha , concat(nombres,' ',apellidos) as cliente
                      FROM venta 
                      LEFT JOIN cliente ON cliente_id = cliente.id 
                      INNER JOIN producto_venta ON venta_id = venta.id
                      WHERE 1  AND venta.punto_id = $idpunto $condicion
                      GROUP BY venta.id 
                      LIMIT $start, $per_page"; 
                  $data = $connection->query($query);  
                  $results = $data->fetchAll(PDO::FETCH_BOTH ); 


                  $query = "SELECT sum(precio * cantidad ) total 
                            FROM venta 
                            LEFT JOIN cliente ON cliente_id = cliente.id 
                            INNER JOIN producto_venta ON venta_id = venta.id
                            WHERE 1  AND venta.punto_id = $idpunto $condicion ";  
              
                  $data = $connection->query($query);  
                  $last_row = $data->fetch(PDO::FETCH_BOTH ); 


                  $query = "SELECT sum(abono) total 
                            FROM abono_venta 
                            INNER JOIN venta ON venta_id = venta.id
                            WHERE tipo_venta_id IN(3)  AND venta.punto_id = $idpunto $condicion ";  
              
                  $data = $connection->query($query);  
                  $abono_venta = $data->fetch(PDO::FETCH_BOTH ); 

                  $last_row['abono'] = $abono_venta['total'];

                  if( !empty($estado) )
                  {
                      $last_row = null;
                  }
 
                  $params = array
                            (
                              'page' => $_POST['page'], 
                              'count' => $count, 
                              'results' => $results, 
                              'columns' => $columns ,
                              'per_page' => $per_page ,
                              'custom' => true ,
                              'last_row' => $last_row ,
                              'source_row' => 'PorCobrar'
                            );
                  $form =  $func->Pagination( $params );  
                  $form = "<div class='content'><div id='content_print'></div> $form</div>"; 
                  echo json_encode( array("form" => utf8_encode($form) , "var" => $_POST ));
                
                     
              }
              else
              {
                  $m = $msg->danger("No se encontraron cuentas por cobrar");
                  echo json_encode( array("form" => utf8_encode($m) ));
              } 
          break; 

          case "consultar_catalogo": 
             $condicion = "";

             $per_page = 10;

             $aux_page = $page;
             $aux_page -= 1;
             $start = $aux_page * $per_page; 

             $columns = array("PRODUCTO" ,"IMAGEN" , "ELIMINAR" );
               $query = "SELECT count(*) count
                      FROM catalogo 
                      INNER JOIN categoria ON categoria_id = categoria.id

                      WHERE categoria_id = $idcategoria ";
              
              $data = $connection->query($query);  
              $results = $data->fetch(PDO::FETCH_BOTH ); 
              $count = $results[0]; 
              if( $count > 0 )
              {
                 $query = "SELECT catalogo.id , catalogo.producto , catalogo.imagen ,folder FROM catalogo 
                           INNER JOIN categoria ON categoria_id = categoria.id 
                           WHERE categoria_id = $idcategoria
                           LIMIT $start, $per_page"; 
                  $data = $connection->query($query);  
                  $results = $data->fetchAll(PDO::FETCH_BOTH ); 

                  $params = array
                            (
                              'page' => $_POST['page'], 
                              'count' => $count, 
                              'results' => $results, 
                              'columns' => $columns ,
                              'per_page' => $per_page ,
                              'custom' => true ,
                              'last_row' => null ,
                              'source_row' => 'consultar_catalogo'
                            );
                  $form =  $func->Pagination( $params );  
                  $form = "<div class='content'><div id='content_print'></div> $form</div>"; 
                  echo json_encode( array("form" => utf8_encode($form) , "var" => $_POST ));
                
                     
              }
              else
              {
                  $m = $msg->danger("No se encontraron compras registradas.");
                  echo json_encode( array("form" => utf8_encode($m) ));
              }
          break;  

          case "AbonarPorPagar":
              if( !empty($valor) )
              {
                  $firma = "";
                  if( empty($firmado) )
                    $firma = "";
                  else
                  {
                    $prefijo = substr(md5(uniqid(rand())),0,10);
                    $newfile = $id."".$prefijo.".png";
                    if( copy ("firmas_temp/firma.png" ,  "firmas/".$newfile ) )
                    {
                        $firma = $newfile;
                    }
                  }

                  $date = date('Y-m-d H:i:s'); 
                  $query = "INSERT INTO abono_compra VALUES( null , $valor , '$date' , $id ,  $idusuario , '$firma' )";
                  $connection->query( $query ); 
                  $func->WriteQuery( $connection , $query );


                  $query = "SELECT  sum( costo * cantidad ) total  
                            FROM compra 
                            INNER JOIN producto_compra ON  compra_id = compra.id 
                            WHERE compra.id = $id ";
                  $data = $connection->query($query);  
                  $results = $data->fetchAll(PDO::FETCH_BOTH ); 
                  $row = $results[0]; 
                  $total = $row['total'];
                  

                  $abono = 0;
                  $query = "SELECT  sum(abono) abono FROM abono_compra  WHERE compra_id = $id ";
                  $data = $connection->query($query);
                  $results = $data->fetchAll(PDO::FETCH_BOTH ); 
                  if( count( $results) > 0 ) 
                  {
                    $row = $results[0];
                    $abono = $row['abono'];
                  }

                  $debe = $total - $abono;
                  $values = array("debe" => $debe , "abono" => $abono );
                  if( $debe <= 0 )
                  {
                       $query = "UPDATE compra SET tipo_compra_id = 3 WHERE id = $id  ";
                       $connection->query( $query );
                       $func->WriteQuery( $connection , $query );
                       $msg = $msg->success("Cuenta pagada exitosamente."); 
                  }
                  else
                  {
                    $msg = $msg->success("Abono registrado exitosamente ");  
                  }
                  echo json_encode( array("row" => "por_pagar" ,"values" => $values , "msg" => $msg ) );
              }
          break; 

          case "Diario":
             $titulo = "";
             if( !empty($inicio) && !empty($fin) )
             {
                $condicion = " date(fechasys) >= '$inicio' AND date(fechasys) <= '$fin' ";
                $titulo = "<h1>VENTAS</h1> <span class='minititulo'>Desde $inicio hasta $fin</span>";

             }
             else if( !empty($inicio) )
             {
               $condicion = " date(fechasys) = '$inicio' ";
               $titulo = "<h1>VENTAS</h1> <span class='minititulo'>De $inicio</span>";

             }

             $query = "SELECT 
                         venta.cantidad cantidad, 
                         venta.total precio, 
                         fechasys , 
                         concat(usuario.nombre,' ',usuario.apellido) usuario ,
                         producto.nombre producto  
                       FROM venta
                       INNER JOIN usuario ON usuario_id = usuario.id 
                       INNER JOIN producto ON producto_id = producto.id  
                       WHERE  $condicion ";
             $data = $connection->query($query);  
             $results = $data->fetchAll(PDO::FETCH_BOTH ); 


             if( count( $results) > 0 ) 
             {
                  echo $titulo;
                  $tabla = "<table class='table table-bordered table-striped table-condensed' id='datatable'>
                               <thead>
                                  <tr>
                                     <th>FECHA</th>
                                     <th>USUARIO</th>                            
                                     <th>PRODUCTO</th>                            
                                     <th>CANTIDAD</th>
                                     <th>PRECIO</th>                           
                                     <th>SUBTOTAL</th> 
                                  </tr>
                               </thead>
                          </tbody>";
                
                   $subtotal = 0;
                   $total = 0;
                   
                   foreach( $results as $fila )
                   {
                       
                           $subtotal = $fila['precio'] * $fila['cantidad'];
                            
                           $total+=$subtotal;                        
                            
                           $subtotal = number_format($subtotal,0,"",".");
                           $tabla.= "<tr>                                
                                        <td>$fila[fechasys]</td>
                                        <td>$fila[usuario]</td>
                                        <td>$fila[producto]</td>
                                        <td>$fila[cantidad]</td>
                                        <td>$ $fila[precio]</td>
                                        <td>$ $subtotal</td>   
                                     </tr>";
                       
                   }
                    
                   $total  = number_format($total,0,"",".");
                    
                   $tabla.= "</tbody>
                                <tfoot> 
                                    <tr>
                                         <td>TOTAL</td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td> 
                                         <td><div class='total'>$ $total</div></td>
                                    </tr>
                                </tfoot>";
                    $tabla.= "</table>";
                    echo "<div class='row col-md-12'>$tabla</div>";

             } 
             else
             {
                 echo $msg->danger("No se encontraron ventas para la fecha seleccionada");
             } 

             $query = "SELECT  * FROM servicio_tecnico WHERE date(fecha) = '$inicio' AND estado = 'terminado' ";
             $data = $connection->query($query);  
             $results = $data->fetchAll(PDO::FETCH_BOTH ); 

             $suma = 0;
             if( count( $results) > 0 ) 
             { 
                  echo "<h1>SERVICIO TÉCNICO</h1>";

                  $tabla = "<table class='table table-bordered table-striped table-condensed' id='datatable'>
                               <thead>
                                  <tr>
                                     <th>Fecha</th>
                                     <th>Nombre</th>
                                     <th>Cedula</th>                            
                                     <th>Telefono</th>                            
                                     <th>Imei</th>                            
                                     <th>Valor</th>   
                                  </tr>
                               </thead>
                          </tbody>"; 
                   foreach( $results as $fila )
                   {        
                           $suma+= $fila['precio'];
                           $fila['precio'] = number_format($fila['precio'],0,"",".");
                           $tabla.= "<tr>                                
                                        <td style='font-size: 16px' >$fila[fecha]</td>
                                        <td style='font-size: 16px'>$fila[nombre]</td>
                                        <td style='font-size: 16px'>$fila[cedula]</td>
                                        <td style='font-size: 16px'>$fila[telefono]</td>
                                        <td style='font-size: 16px'>$fila[imei]</td>
                                        <td class='total' style='width:120px;'>$ $fila[precio]</td> 
                                     </tr>";
                       
                   } 
                   $tabla.= "</table>"; 
                   echo "<div class='row col-md-12'>$tabla</div>";
                   
                   $empresa = 0;
                   $servicio = 0;
                   if( $suma > 0 )
                   { 
                        $empresa = $suma / 2;
                        $servicio = $suma / 2;
                   }
                   $suma = number_format($suma,0,"",".");
                   $empresa = number_format($empresa,0,"",".");
                   $servicio = number_format($servicio,0,"",".");


                   echo "<table class='table'>
                            <tr><td><h1>TOTAL SERVICIO TÉCNICO</h1></td><td class='text-right'><h1><b>$ $suma</b></h1></td></tr>
                            <tr><td><h1>EMPRESA</h1></td><td class='text-right'><h1><b>$ $empresa</b></h1></td></tr>
                            <tr><td><h1>SERVICIO TECNICO</h1></td><td class='text-right'><h1><b>$ $servicio</b></h1></td></tr>
                         </table>";

                   echo "<br />";
                 
             } 
             else
             {
                 echo $msg->danger("No se encontraron servicios técnicos para esta fecha");
             } 
          break;  

          case "change_oculta":
            $query = "SELECT valor FROM configuracion WHERE name = 'HideBtn'";
            $data = $connection->query($query);  
            $results = $data->fetch(PDO::FETCH_ASSOC );  

            if( $results['valor'] == 1 )
            { 
                 $query = "SELECT valor FROM configuracion WHERE name = 'HideShowCompras'";
                 $data = $connection->query($query);  
                 $results = $data->fetch(PDO::FETCH_ASSOC );

                 $hide = $results['valor']; 
                 if( $hide == 0 )
                    $hide = 1;
                 else
                    $hide = 0;

                 $query = "UPDATE configuracion SET valor = '$hide' WHERE name = 'HideShowCompras' ";
                 $connection->query( $query );
                 $func->WriteQuery( $connection , $query );

                 $query = "UPDATE menu SET status = $hide WHERE id = 15";
                 $connection->query( $query );
                 $func->WriteQuery( $connection , $query );

                 echo $hide;
            } 
            else
            {
                 $query = "UPDATE configuracion SET valor = 0 WHERE name = 'HideShowCompras' ";
                 $connection->query( $query );
                 $func->WriteQuery( $connection , $query );

                 echo "nada";
            }
          break; 

          case "ocultar_venta":
             $query = "SELECT checked FROM venta WHERE id = $id";
             $data = $connection->query($query);  
             $results = $data->fetch(PDO::FETCH_ASSOC );

             $checked = $results['checked']; 
             if( $checked == false )
                $checked = true;
             else
                $checked = false;

             $query = "UPDATE venta SET checked = '$checked' WHERE id = $id";
             $connection->query( $query );
             $func->WriteQuery( $connection , $query );

             echo "Cambiado"; 
          break;

          case "resourceSalesOnly":
             if( !empty($id) )
             {
                 if( empty($status) )
                    $status = 0;
                 else
                    $status = 1; 
                 
                 $query = "UPDATE venta SET checked = $status WHERE id = $id";
                 $connection->query( $query );
                 $func->WriteQuery( $connection , $query );

                 echo "success";
             }
          break;

          case "resourceSales":  
             $query = "SELECT  
                         venta.id ,
                         SUM( producto_venta.cantidad * producto_venta.precio ) total ,
                         producto.nombre , 
                         SUM(producto_venta.cantidad) cantidad ,
                         venta.fecha ,
                         concat( nombres , ' ' , apellidos ) cliente ,
                         checked
                         FROM producto_venta 
                         INNER JOIN venta ON venta.id = venta_id 
                         INNER JOIN producto ON producto.id = producto_id
                         LEFT JOIN cliente ON venta.cliente_id = cliente.id
                         WHERE venta.factura IS NULL AND date(venta.fecha) = '$fecha' 
                         GROUP BY venta.id 
                         ORDER BY id ASC";
             $data = $connection->query($query);  
             $results = $data->fetchAll(PDO::FETCH_ASSOC); 

             $status = false;
             $suma = 0;

             $tabla = "";

             $contador = 0;

             $ids = "";

             if( count($results) > 0 )
             {  
                  $tabla = "
                            <table class='table ui'>
                            <tr>
                              <thead>
                                 <th>#</th>
                                 <th>FECHA</th>
                                 <th>NOMBRE</th>
                                 <th>CLIENTE</th>
                                 <th>PRODUCTOS</th>
                                 <th>TOTAL</th>
                                 <th>MARCAR</th>
                              </thead>
                            </tr>";

                  $status_sumar = true;
                  foreach ($results as $key => $fila)
                  {
                     $index = $key + 1;
                     $suma += $fila['total']; 
                     
                     if( $suma > $valor )
                     {
                        $checked = ""; 
                     } 
                     else
                     {
                        $ids.= $fila['id'].",";

                        $checked = "checked=true";
                        $contador += $fila['total'];
                        $query = "UPDATE venta SET checked = 1 WHERE id = $fila[id]";                        
                        $connection->query( $query );
                     } 

                        
                     $total =  $fila['total']; 
                     $fila['total'] = $func->format( $fila['total'] ); 

                     $tabla .= "<tr>
                                 <td># $index</td>
                                 <td>$fila[fecha]</td>
                                 <td>$fila[nombre]</td>
                                 <td>$fila[cliente]</td>
                                 <td>$fila[cantidad]</td>
                                 <td>$fila[total]</td>
                                 <td> 
                                    <label class='ios7-switch' style='font-size: 32px;' tabindex='$index' >
                                        <input type='checkbox' class='venta-check' data-id='$fila[id]' value='$total' $checked >
                                        <span></span>
                                    </label>
                                 </td>
                               </tr>"; 
                  }
                  $tabla .= "</table>";
          
                  echo "<div class='contador' >$contador</div>"; 
                  
                  if( strlen($ids) > 0 )
                  {
                     $ids = substr( $ids , 0 , strlen($ids) -1 );
                     $query = "UPDATE venta SET checked = 1 WHERE id IN($ids)";
                     //$func->WriteQuery( $connection , $query );
                  }
                  
                  echo $tabla;

             } 
             else
             {
                echo $msg->warning("No se encontraron ventas.");
             }
          break;

          case "ReporteCuentasPorPagar":
             $condition = "";
             if( !empty($idproveedores) )
             {
                 if( count($idproveedores) > 0 )
                 {
                    $list = trim(implode(",", $idproveedores));
                    if( strlen($list) > 0 )
                       $condition .= " AND compra.proveedor_id IN ( $list )"; 
                 }
             } 
             
             if( !empty($fin) )
                $condition .= " AND DATE(compra.fecha) BETWEEN '$inicio' AND '$fin'";
             else
             {
                $condition .= " AND DATE(compra.fecha) = '$inicio'"; 
                $fin = "";
             }

             if( !empty($factura) )
                $condition .= " AND compra.factura = '$factura'";
              


             $per_page = 5;
             $aux_page = $page;
             $aux_page -= 1;
             $start = $aux_page * $per_page; 

             $columns = array("ESTADO","FECHA" ,"FACTURA" ,"TRABAJADOR" , "PROVEEDOR" , "TOTAL" , "ABONOS");
             $query = "SELECT 
                             compra.id ,
                             compra.fecha ,   
                             concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador ,
                             factura , 
                             tipo_compra_id ,
                             concat( proveedor.nombres,' ', proveedor.apellidos )AS proveedor , 
                             SUM( producto_compra.cantidad * producto_compra.costo ) AS total  
                          FROM compra
                             INNER JOIN usuario ON usuario_id = usuario.id  
                             INNER JOIN producto_compra ON producto_compra.compra_id = compra.id
                             INNER JOIN producto ON producto_id = producto.id 
                             INNER JOIN tipo_compra ON tipo_compra_id = tipo_compra.id 
                             INNER JOIN proveedor ON proveedor_id = proveedor.id 
                          WHERE  tipo_compra_id IN(2,3) AND compra.punto_id = $idpunto $condition 
                          GROUP BY proveedor_id , numero 
                          ORDER BY compra.fecha DESC  "; 
              
             $data = $connection->query($query);  
             $results = $data->fetchAll(PDO::FETCH_BOTH ); 
             $count = count($results); 
             if( $count > 0 )
             {
                   $query = "SELECT 
                               compra.id ,
                               compra.fecha ,   
                               concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador ,
                               factura , 
                               tipo_compra_id ,
                               concat( proveedor.nombres,' ', proveedor.apellidos )AS proveedor , 
                               SUM( producto_compra.cantidad * producto_compra.costo ) AS total  
                            FROM compra
                               INNER JOIN usuario ON usuario_id = usuario.id  
                               INNER JOIN producto_compra ON producto_compra.compra_id = compra.id
                               INNER JOIN producto ON producto_id = producto.id 
                               INNER JOIN tipo_compra ON tipo_compra_id = tipo_compra.id 
                               INNER JOIN proveedor ON proveedor_id = proveedor.id 
                            WHERE  tipo_compra_id IN(2,3) AND compra.punto_id = $idpunto $condition 
                            GROUP BY proveedor_id , numero 
                            ORDER BY compra.fecha DESC 
                            LIMIT $start, $per_page";  
              
                  $data = $connection->query($query);  
                  $results = $data->fetchAll(PDO::FETCH_BOTH ); 

                  $params = array
                            (
                              'page' => $_POST['page'], 
                              'count' => $count, 
                              'results' => $results, 
                              'columns' => $columns ,
                              'per_page' => $per_page ,
                              'custom' => true ,
                              'last_row' => null ,
                              'source_row' => 'ReporteCuentasPorPagar'
                            );
                  $compras =  $func->Pagination( $params ); 
                  $_POST['pdf'] = "reports/ReporteCuentasPorPagar.php";
                  $form = "<div class='content'>
                               <div id='content_title'>REPORTE DE CUENTAS POR PAGAR</div>
                              <div id='content_print' data-title='REPORTE DE CUENTAS POR PAGAR'   >
                           </div>
                           <div style='width:100%' id='responsecxp'>$compras</div>
                      </div>";  
                 echo json_encode( array("form" => utf8_encode($form) , "var" => $_POST ));                 
                     
             }
             else
             {
                  $m = $msg->warning("No se encontraron compras registradas.");
                  echo json_encode( array("form" => utf8_encode($m) ));
             }
          break;

          case "ReporteEntradaSalida":

             $condition = "";
             if( !empty($idproducto) )
             {
                 if( count($idproducto) > 0 )
                 {
                    $list = trim(implode(",", $idproducto));
                    if( strlen($list) > 0 )
                       $condition .= " AND entrada_salida.producto_id IN ( $list )"; 
                 }
             } 
             
             if( !empty($fin) )
                $condition .= " AND DATE(entrada_salida.fecha) BETWEEN '$inicio' AND '$fin'";
             else
             {
                $condition .= " AND DATE(entrada_salida.fecha) = '$inicio'"; 
                $fin = "";
             }  
 
 
             $query = "SELECT 
                             entrada  ,
                             salida , 
                             concepto ,   
                             entrada_salida.fecha ,
                             producto.nombre ,
                             concat( usuario.nombre,' ', usuario.apellido ) AS usuario   
                          FROM entrada_salida
                             INNER JOIN usuario ON usuario_id = usuario.id  
                             INNER JOIN producto ON producto_id = producto.id
                          WHERE 1  $condition
                          ORDER BY entrada_salida.fecha DESC  "; 
              
             $data = $connection->query($query);  
             $results = $data->fetchAll(PDO::FETCH_ASSOC);
             if( count($results) > 0 )
             {
                 $tabla = "<table class='table table-bordered'>
                           <thead>
                             <tr>
                               <th>FECHA</th>
                               <th>PRODUCTO</th>
                               <th>ENTRADA</th> 
                               <th>SALIDA</th>
                               <th>CONCEPTO</th>
                               <th>USUARIO</th>
                             </tr>
                           </thead>";
                 
                 $sumEntrada = 0;
                 $sumSalida = 0;

                 foreach ($results as $key => $value) 
                 {
                      $sumEntrada += $value['entrada'];
                      $sumSalida += $value['salida'];
                      $tabla .= " <tr>
                                     <td>$value[fecha]</td>
                                     <td>$value[nombre]</td>
                                     <td>$value[entrada]</td> 
                                     <td>$value[salida]</td>
                                     <td>$value[concepto]</td>
                                     <td>$value[usuario]</td>
                                  </tr>";
                 }

                 $total = $sumEntrada - $sumSalida;

                 $tabla .= " <tr>
                                 <td><h1>TOTAL</h1></td>
                                 <td></td>
                                 <td></td> 
                                 <td></td>
                                 <td><h1>$total</h1></td>
                                 <td></td>
                             </tr> ";

                 $tabla .= "</table>";

                 echo "<div class='content-scroll' style=''>$tabla</div>";
             } 
             else
             {
                  echo $msg->warning("No se encontraron compras registradas.");
             }
          break;

          case "ReporteCuentasPorCobrar":
             $condition = ""; 

             if( !empty($tiposventas) )
             {
                 if( count($tiposventas) > 0 )
                 {
                    $list = trim(implode(",", $tiposventas));
                    if( strlen($list) > 0 )
                       $condition .=" AND venta.tipo_id IN ( $list )"; 
                 }
             }
             
             if( !empty($fin) )
                $condition .= " AND DATE(venta.fecha) BETWEEN '$inicio' AND '$fin'";
             else
             {
                $condition .= " AND DATE(venta.fecha) = '$inicio'"; 
                $fin = "";
             }
             if( !empty($recibo) )
                $condition .= " AND venta.recibo = '$recibo'";


             $per_page = 5;
             $aux_page = $page;
             $aux_page -= 1;
             $start = $aux_page * $per_page; 

             $columns = array("ESTADO" ,"FECHA" ,"RECIBO" , "TRABAJADOR" , "CLIENTE" , "TOTAL" );
             $query = "SELECT COUNT(*)                            
                         FROM venta
                         INNER JOIN usuario ON usuario_id = usuario.id 
                         INNER JOIN producto ON producto_id = producto.id 
                         INNER JOIN tipo ON tipo_id = tipo.id 
                         WHERE tipo_id IN(2,3) AND venta.punto_id = $idpunto $condition 
                         GROUP BY numero 
                         ORDER BY venta.fecha DESC"; 
              
             $data = $connection->query($query);  
             $results = $data->fetch(PDO::FETCH_BOTH ); 
             $count = $results[0]; 
             if( $count > 0 )
             {
                 $query = "SELECT 
                             venta.fecha , 
                             producto.nombre AS producto , 
                             concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador ,
                             cliente.nombre AS cliente ,
                             recibo , 
                             tipo_id ,
                             SUM( venta.cantidad * venta.precio ) AS total
                            FROM venta
                             INNER JOIN usuario ON usuario_id = usuario.id 
                             INNER JOIN producto ON producto_id = producto.id 
                             LEFT JOIN cliente ON cliente_id = cliente.id 
                             INNER JOIN tipo ON tipo_id = tipo.id 
                            WHERE tipo_id IN(2,3) AND venta.punto_id = $idpunto $condition 
                            GROUP BY numero 
                            ORDER BY venta.fecha DESC
                            LIMIT $start, $per_page";  
              
                  $data = $connection->query($query);  
                  $results = $data->fetchAll(PDO::FETCH_BOTH ); 

                  $params = array
                            (
                              'page' => $_POST['page'], 
                              'count' => $count, 
                              'results' => $results, 
                              'columns' => $columns ,
                              'per_page' => $per_page ,
                              'custom' => true ,
                              'last_row' => null ,
                              'source_row' => 'ReporteCuentasPorCobrar'
                            );
                  $compras =  $func->Pagination( $params ); 
                  $_POST['pdf'] = "reports/ReporteCuentasPorCobrar.php";
                  $form = "<div class='content'>
                              <div id='content_title'>REPORTE DE CUENTAS POR COBRAR</div>
                              <div id='content_print' data-title='REPORTE DE CUENTAS POR COBRAR' >
                           </div>
                           <div style='width:100%' id='responseventas'>$compras</div>
                      </div>"; 

                 echo json_encode( array("form" => utf8_encode($form) , "var" => $_POST ));
                 
                     
             }
             else
             {
                  $m = $msg->warning("No se encontraron cuentas por cobrar registradas.");
                  echo json_encode( array("form" => utf8_encode($m) ));
             }
          break;

          
 
         
          case "DeletePDF": 
              if(file_exists("reports/".$pdf) )
              {
                 unlink("reports/".$pdf); 
                 echo 'success => '."reports/".$pdf;
              }
              else
                echo "error"; 
          break;
     }
 }
?>
