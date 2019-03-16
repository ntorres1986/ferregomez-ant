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
  $idpunto = $_SESSION['idpunto'];

function limpiarString($texto)
{
      $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $texto);                
      return $textoLimpio;
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
    //$s= str_replace('.', '', $s); 
    $s= str_replace(',', '', $s); 
    $s= str_replace(';', '', $s);  

    return $s; 
}
function reemplazar($s) 
{ 
    $s = str_replace('a', 'á', $s); 
    $s = str_replace('A', 'Á', $s); 
    $s = str_replace('e', 'é', $s); 
    $s = str_replace('E', 'É', $s); 
    $s = str_replace('i', 'í', $s); 
    $s = str_replace('I', 'Í', $s); 
    $s = str_replace('o', 'ó', $s); 
    $s = str_replace('O', 'Ó', $s); 
    $s = str_replace('U', 'Ú', $s); 
    $s= str_replace('u', 'ú', $s); 


    return $s; 
}
if(!empty($opcion))
{
  
  switch($opcion)
  {
      case 'cerrar':
         session_destroy();
      break; 

      case "GetMenuAcces":
          $query = "SELECT menu.id , texto , usuario_menu.status
                    FROM menu
                    LEFT JOIN usuario_menu ON menu.id = menu_id AND usuario_id = $usuario_id
                    ORDER BY menu.order ASC";
          $data = $connection->query($query); 
          $resultsMenu = $data->fetchAll(PDO::FETCH_BOTH ); 
          if( count( $resultsMenu ) > 0 )
          {
               $form = "";
               $index = 1;
               foreach ($resultsMenu as $rowMenu) 
               {
                  if( $rowMenu['status'] == true )
                  {
                     $checked = "checked='checked'";
                  }
                  else
                  {
                     $checked = ''; 
                  }

                  $form .= "<div class='content_access'>";
                  $form .= "<form>";
                  
                  $rowMenu['texto'] = utf8_encode(trim($rowMenu['texto']));
                  $form.="<div class='content_access_header'>
                               <div class='content_access_title'>$rowMenu[texto]</div>
                               <div class='content_access_status'>
                                   <label class='ios7-switch'>
                                        <input type='checkbox' class='chAccess' name='chAccessParent' value='$rowMenu[id]' data-index='$index' $checked >
                                        <span></span>
                                   </label>
                               </div>
                          </div>";

                  $query = "SELECT submenu.id,submenu.texto,usuario_submenu.status
                            FROM submenu 
                            LEFT JOIN usuario_submenu ON submenu.id = usuario_submenu.submenu_id AND usuario_id = $usuario_id
                            WHERE menu_id = $rowMenu[id] AND submenu.status = true";
                  $data = $connection->query($query); 
                  $resultsSubMenu = $data->fetchAll(PDO::FETCH_BOTH );

                  $form .= "<div class='content_access_body'>";
                  if( count( $resultsSubMenu ) > 0 )
                  {
                      foreach ($resultsSubMenu as $rowSubMenu ) 
                      {
                        if( $rowSubMenu['status'] == true )
                        {
                           $checked = "checked='checked'";
                        }
                        else
                        {
                           $checked = ''; 
                        }
                        $rowSubMenu['texto'] = utf8_encode(trim($rowSubMenu['texto']));
                        $form .= "<div class='content_access_item'>
                                     <div class='content_access_item_title'>$rowSubMenu[texto]</div>
                                     <div class='content_access_item_icon'>
                                        <label class='ios7-switch'>
                                            <input type='checkbox' value='$rowSubMenu[id]' name='chAccessItem[]' class='chAccess$index chAccessChildren' $checked >
                                            <span></span>
                                        </label>   
                                     </div>
                                   </div>";
                        
                      }
                  } 
                  $form.="<input type='hidden' name='idusuario' value='$idusuario' />";
                  $form.="<input type='hidden' name='opcion' value='UpdateMenuAccess' />";
                  $form.="</form>";
                  $form.="</div>";
                  $index++; 
                  $form.="</div>";
               }

               $form.="</div>";
               echo $form;
          }
          else
            echo "";
      break; 

      case "Permisos":
         $query = "SELECT id,concat(nombre,' ',apellido) trabajador FROM usuario";
         $data = $connection->query($query); 
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 

         $params =  array(
                             "default" => "Seleccione" ,  
                             "name" => "idtrabajador_permiso" , 
                             "id" => "idtrabajador_permiso" , 
                             "class" => "idtrabajar_permiso w100 validar select requerido" ,
                             "value" => "", 
                             "results" => $results
                          ); 
         $trabajador = $func->selectOption($params);            

         $params =  array(
                             "default" => "Seleccione" ,  
                             "name" => "idmenu_permiso" , 
                             "id" => "idmenu_permiso" , 
                             "class" => "idmenu_permiso w100 validar select requerido" ,
                             "value" => "", 
                             "results" => $results
                          );   

         $menu = "<select id='idmenu_permiso' class='form-control'></div>";

         $form = "<form action='set.php' method='post' data-response='content' autocomplete='off' >
                      <div class='titulo'>PERMISOS</div>
                      <table>
                        <tr>
                          <td>
                            <div class='form-group'>
                              <div class='orm-body'>
                                 <label>Seleccion un trabajador</label>
                              </div>
                              <div class='form-body'>                        
                                 $trabajador
                              </div>
                            </div>
                          </td>
                        </td> 
                      </table>
                  </form>
                  <div class='result_access'></div>";
            echo "<div class='content' style='width:100%;'>$form</div>";
      break;

      case "ValidarFactura":
          $query = "SELECT COUNT(*) cant FROM compra WHERE factura = '$factura'";
          $data = $connection->query($query);  
          $results = $data->fetch(PDO::FETCH_BOTH );  
          echo json_encode($results); 
      break;

      case "GetMenu":
          $connection->query("SET NAMES utf8");
          $query = "SELECT  menu.* 
                    FROM menu 
                    INNER JOIN usuario_menu ON ( menu_id = menu.id AND usuario_menu.status = true )
                    WHERE usuario_id = $idusuario AND menu.status = true
                    ORDER BY menu.order ASC";
          $data = $connection->query($query);  
          $results = $data->fetchAll(PDO::FETCH_BOTH ); 

          echo json_encode($results); 
      break;

      case "GetSubMenu":
           $connection->query("SET NAMES utf8");

           $query = "SELECT * 
                    FROM submenu 
                    INNER JOIN usuario_submenu ON submenu.id = submenu_id AND usuario_submenu.usuario_id = $idusuario
                    WHERE menu_id = $menu_id AND usuario_submenu.status = true AND submenu.status = true";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );  
           echo json_encode( $results ); 
      break;

      case "GetPoints":
         $query ="SELECT id,punto  FROM punto";
         $data = $connection->query($query);   
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 

         echo json_encode( array("results" => $results , "idpoint" => $_SESSION['idpunto']) ) ; 
      break;

      case "Egresos": 

          echo "<div class='ui sixteen  centered grid'>
                    <form action='set.php' method='post' data-response='content'  data-paginate='false' autocomplete='off' >
                       <div class='ui form'>
                          <div class='field'>
                             <label>Valor</label>
                             <input type='text' class='form-control validar requerido numero' data-min='1' name='valor' >                               
                          </div>
                          <div class='field'>
                             <label>Concepto</label> 
                             <textarea name='concepto' cols='10' rows='3' class='form-control validar requerido texto' placeholder='Concepto'></textarea>                                  
                          </div>
                          <div class='field'>
                              <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                <div class='visible content'>REGISTRAR</div>
                                <div class='hidden content'>
                                  <i class='right send outline icon'></i>
                                </div>
                              </div>
                          </div>   
                          <input type='hidden' name='opcion' value='RegistrarEgreso' />
                    </form>
                  </div>
                </div>";
      break; 

      case "catalogo":  

            $query = "SELECT * FROM categoria";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 

            $categoria = "<select id='categoria' name='categoria[]' class='form-control validar requerido select chosen' >";
            $categoria.="<option value='' >Seleccione</option>";

            foreach ($results as $key => $value) 
            {
                $value['nombre'] = utf8_encode($value['nombre']);
                $categoria.="<option value='$value[id]' data-folder='$value[folder]'>$value[nombre]</option>";
            }
             $categoria .= "</select>";


             echo "<div class='titulo'>REGISTRAR PRODUCTO PARA EL CATALOGO</div>";

             $form ="<form class='form ui' autocomplete='off' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content'>
                      
                      <div class='three fields' id='referencias'> 

                                <div class='field'>
                                    <label>Imagen</label>
                                    <input type='file' class='form-control validar requerido texto'id='nombreProducto'  name='imagen[]' >
                                </div>

                                <div class='field'>
                                   <label>Nombre del producto</label>
                                   <input type='text' name='nombres[]' class='form-control' />   
                                </div> 

                                <div class='field'>
                                    <label>Categoria</label>
                                    $categoria  
                                    <input type='hidden'  id='folder_categoria' name='folder_categorias[]' value='' />
                                </div>   
                                  
                      </div>

                      <input type='hidden' name='opcion' value='catalogo' />
                      <div class='field'>
                        <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                          <div class='visible content'>GUARDAR</div>
                          <div class='hidden content'>
                            <i class='right send outline icon'></i>
                          </div>
                        </div>
                      </div> 
                     </form>
                     <div class='plus' style='float:right;'></div> ";
             echo $form;   
      break; 

      case "CargarProductos":
             $form ="<form autocomplete='off' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content'>
                      <div class='titulo'>CARGAR PRODUCTO</div>
                      <div class='row'>
                          <div class='col-md-12'> 
                              <div class='row' id='referencias'>
                                <div class='col-md-12'>
                                    <div class='form-group'>
                                      <div class='form-header'>
                                         <label>Imagen</label>
                                      </div>
                                      <div class='form-body'>                        
                                         <input type='file' class='form-control validar requerido texto'id='nombreProducto'  name='productos' >
                                      </div>
                                    </div>
                                </div> 
                              </div>  

                              <div class='row'>
                                <div class='col-md-12'>
                                    <div class='form-group'> 
                                      <div class='form-body'>                        
                                         <input type='submit' class='btn btn-primary btn-save'  value='REGISTRAR' />
                                      </div>
                                    </div>
                                </div>
                                 
                              </div>                              
                          </div> 
                      </div>
                      <input type='hidden' name='opcion' value='CargarProductos' />
                     </form>";
             echo "<div class='content'>$form </div>";   
      break; 

      case "promociones":
             echo "<div class='titulo'>REGISTRAR PROMOSIONES</div>";
             $form ="<form class='ui form' autocomplete='off' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content'>
                       <div class='two fields' id='referencias'> 

                          <div class='field'>
                               <label>Imagen</label>
                               <input type='file' class='form-control validar requerido texto'id='nombreProducto'  name='imagen[]' >
                          </div>

                          <div class='field'>
                               <label>Nombre de la promoción</label> 
                               <input type='text' name='nombres[]' class='form-control' />   
                          </div>   

                      </div>
                       <div class='plus'></div>
                       <div class='field'>
                        <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                          <div class='visible content'>GUARDAR</div>
                          <div class='hidden content'>
                            <i class='right send outline icon'></i>
                          </div>
                        </div>
                      </div>
                      <input type='hidden' name='opcion' value='promociones' />
                     </form>";
             echo "<div class='content'>$form </div>";   
      break; 




      case "marcas": 
             echo "<div class='titulo'>REGISTRAR PRODUCTO PARA EL CATALOGO</div>";

             $form ="<form class='form ui' autocomplete='off' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content'>
                      
                      <div class='two fields' id='referencias'> 

                                <div class='field'>
                                    <label>Imagen</label>
                                    <input type='file' class='form-control validar requerido texto' name='imagen[]' >
                                </div>

                                <div class='field'>
                                   
                                </div>   
                      </div>
                      <div class='plus-marcas'></div>

                      <div class='field'>
                        <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                          <div class='visible content'>GUARDAR</div>
                          <div class='hidden content'>
                            <i class='right send outline icon'></i>
                          </div>
                        </div>
                      </div> 
                      <input type='hidden' name='opcion' value='marcas' />
                     </form>

                     <div class='plus' style='float:right;'></div>
                      ";


             echo $form;   
      break; 

      case "Precios":
            $query = "SELECT id , tipo FROM tipocliente WHERE status = true";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 
            $params = array
                       (
                           "default" => "Seleccione" ,  
                           "name" => "tipocliente" , 
                           "id" => "tCliente" , 
                           "class" => "form-control validar requerido select" ,
                           "value" => "", 
                           "multiple" => false, 
                           "results" => $results
                        );  

            $tipocliente = $func->selectOption($params);

            $form = "<form action='set.php' method='post' data-response='content' data-paginate='false' autocomplete='off' >
                          <div class='titulo'>PRECIOS POR TIPOS DE CLIENTES</div>
                          <div class='form-group'>
                            <div class='form-header'>
                               <label>TIPO DE CLIENTE</label>
                            </div>
                            <div class='form-body'>                        
                               $tipocliente
                            </div>
                          </div>

                          <div class='form-group'>
                             <div class='form-header'>
                               <label>PORCENTAJE DE DESCUENTO</label>
                             </div> 
                             <div class='form-body'>    
                               <input type='text' name='descuento' class='form-control validar requerido numero' data-min='1' data-max='100' />   
                              </div>
                          </div>
                          <div class='form-group'>

                          <input type='submit' class='btn btn-primary btn-save'  value='REGISTRAR' />
                          <input type='hidden' name='opcion' value='Precios' />
                          </div>
                      </form>";
            echo "<div class='content' style='width:400px;'>$form</div>";
      break;

      case "Gastos":

            echo "<div class='titulo'>REGISTRAR GASTOS</div>";
            $form = "<div class='ui sixteen  centered grid'><form class='form ui' action='set.php' method='post' data-response='content' data-paginate='false' autocomplete='off' >
                          <div class='field'>
                               <label>Valor</label>
                               <input type='text' class='form-control validar requerido numero' data-min='1' name='valor' >
                          </div>

                          <div class='field'>
                               <label>Concepto</label>
                               <textarea name='concepto' cols='10' rows='3' class='form-control validar requerido texto' placeholder='Concepto'></textarea>   
                          </div>
                          <div class='field'>
                              <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                <div class='visible content'>GUARDAR</div>
                                <div class='hidden content'>
                                  <i class='right send outline icon'></i>
                                </div>
                              </div>
                          </div>
                          <input type='hidden' name='opcion' value='RegistrarGasto' />
                      </form>";
            echo "<div class='content'>$form</div>";
      break;

      case "Doblecosto":
            $query = "SELECT valor FROM configuracion WHERE name = 'Doble costo'";
            $data = $connection->query($query);  
            $results = $data->fetch(PDO::FETCH_ASSOC );

            if ( $results['valor'] == "Si" )
            {
                $checked = "checked='checked'";
            }
            else
            {
                $checked = "";
            }

            $form = "<form action='set.php' method='post' data-response='content' data-paginate='false' autocomplete='off' >
                          <table class='table table-bordered'>
                            <tr><th><label>Doble costo seleccione </label></th></tr>
                            
                            <tr>
                               <td>
                                  <div class='form-body'> 
                                      <div style='margin-top:20px;'>                       
                                          <label class='ios7-switch'>
                                                <input type='checkbox' name='doble' $checked>
                                                <span></span>
                                           </label>
                                       </div>
                                  </div>
                                </td>
                            </tr>   

                            <tr>
                               <td>
                                  <input type='submit' class='btn btn-primary btn-save'  value='ACEPTAR' />
                                  <input type='hidden' name='opcion' value='Doblecosto' />
                               </td>
                            </tr> 
                          </div>
                      </form>";
            echo "<div class='content'>$form</div>";
      break;

      case "Compras":  
         echo '<div class="loading_input">
                  <div class="cssload-container">
                  <div class="cssload-speeding-wheel"></div>
                  </div>
               </div>';

         $sql="SELECT * FROM producto LIMIT 1";
         $data = $connection->query($sql); 
         $resultss = $data->fetchAll(PDO::FETCH_BOTH ); 
         $data="";

         $query = "SELECT id , tipo FROM tipo_compra WHERE id <= 2";
         $data = $connection->query($query); 
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 
         $params = array
                   (
                       "default" => "Seleccione" ,  
                       "name" => "tipocompra" , 
                       "id" => "tCompra" , 
                       "class" => "dropdown validar requerido select" ,
                       "value" => "", 
                       "multiple" => false, 
                       "results" => $results
                    );  

         $tipocompra = $func->selectOption($params);

         if( count($resultss) > 0 )
         {   

            $query = "SELECT id,concat(nombres,' ',apellidos) proveedor FROM proveedor";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 

            $params =  array(
                               "default" => "Seleccione" ,  
                               "name" => "idproveedor" , 
                               "id" => "idproveedor" , 
                               "class" => "idproveedor dropdown validar select requerido" ,
                               "value" => "", 
                               "results" => $results
                            );  

            $proveedor = $func->selectOption($params); 
            

            $query = "SELECT punto.id,punto FROM punto 
                      INNER JOIN usuario_punto ON punto.id = punto_id 
                      WHERE usuario_id = $idusuario ";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH);  
           
            echo "<div class='titulo'>REGISTRO DE COMPRAS</div>";
            echo " <form class='ui form' action='set.php' method='post' data-response='content' autocomplete='off' data-type='json' data-paginate='false' >";
            echo "<div class='izq' style='width:30%;background-color:#fff; padding:10px;'> 
                        <div class='field'>
                             <label>Factura</label>
                             <input type='text' name='factura'  class='form-control validar requerido texto'   >    
                        </div> 
                        <div class='field'>
                             <label>Nombre del producto</label> 
                             <div class='ui icon input loading'>
                              <input type='text' style='width:100%;'  id='buscarProductoCompra' data-buscar='BuscarProductoVenta' >
                              <i class='search search-producto icon' style='display:none; '></i>
                             </div>  
                        </div> 
                        <div class='field'>
                             <label>Tipo de compra</label>
                             $tipocompra
                        </div> 
               
                       <div class='field' >
                          <label>Proveedor</label>  
                          <div class='ui icon input loading'>
                            <input type='text' style='width:100%;' id='buscarProveedorCompra'  >
                            <i class='search search-cliente icon' style='display:none; '></i>
                          </div> 
                        </div> 

                       <div class='field'>
                           <label>Fecha llegada</label>
                           <input type='text' name='llegada'  class='form-control validar requerido date fecha fllegada'   >    
                       </div> 
                       <div class='field'>
                           <label>Fecha pago</label>
                           <input type='text' name='limite'  class='form-control validar requerido date fecha fpago'   >    
                       </div>                 
                  </div>";
            if( empty($idproductos) )
            {   
                  echo "<div class='der' style='width:68%' >
                               
                                <div class='ui grid'>
                                  <div class='four wide column'>
                                     <div class='header_title ui medium header'>Precio</div>
                                  </div>
                                  <div class='four wide column'>
                                     <div class='header_price ui medium header'>0</div>
                                  </div>
                                  <div class='four wide column'>
                                     <div class='header_title ui medium header'>Costo</div> 
                                  </div>
                                  <div class='four wide column'>
                                     <div class='header_cost ui medium header'>0</div>
                                  </div>
                                </div>

                                <table class='ui single line  table' id='tabla' style='background:white; float:left;'> 
                                     <thead>
                                       <tr>
                                          <th></th> 
                                          <th>Producto</th> 
                                          <th>Cantidad</th>
                                          <th>Costo</th> 
                                          <th>Precio</th> 
                                          <th>Precio minimo</th> 
                                          <th></th>
                                       </tr>
                                     </thead>
                                     <tbody></tbody>
                                     <tfoot class='full-width'>
                                        <tr>
                                          <th colspan='7' class='text-right'>

                                              <div class='ui animated button btn-enviar' tabindex='0' >
                                                <div class='visible content'>Registrar compra</div>
                                                <div class='hidden content'>
                                                  <i class='right arrow icon'></i>
                                                </div>
                                              </div>
     
                                          </th>
                                        </tr>
                                      </tfoot>
                                </table>
                        </div>"; 
            }
            else
            {
                $tablaProductosComprarTerminar = tablaProductosComprarTerminar( $connection , $idproductos , $idpunto );
                echo "<div class='der' style='width:68%' >
                                <div class='header_total'>
                                   <div class='header_title'>Precio total : </div>
                                   <div class='header_price'>0</div>

                                   <div class='header_title'>Costo total : </div>
                                   <div class='header_cost'>0</div>
                                </div>
                                $tablaProductosComprarTerminar
                        </div>"; 
            }

            echo "</div>";
            echo "<input type='hidden' name='opcion' value='RegistrarCompra' />"; 
            echo "<input type='hidden' name='idproveedor' id='hiddenProveedor' value='' />"; 
            
            echo "</form>"; 
           
         } 
         else            
          echo $msg->danger("NO HAY PRODUCTOS DISPONIBLES");  
      break; 

      case "TipoClientes": 
        $per_page = 10;

        $aux_page = $page;
        $aux_page -= 1;
        $start = $aux_page * $per_page; 

        $columns = array("EDITAR","ELIMINAR","TIPO CLIENTE");
        

        $query = "SELECT count(*) as count
                     FROM  tipocliente   
                     WHERE status = true ";
        $data = $connection->query($query);  
        $results = $data->fetch(PDO::FETCH_BOTH ); 
        $count = $results[0]; 
        if( $count > 0 )
        {
              $query = "SELECT *
                         FROM  tipocliente
                         WHERE status = true
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
                          'source_row' => 'tipoclientes'
                        );
              $tipoclientes =  $func->Pagination( $params ); 
              $form = "<div class='content'>
                        <div style='width:100%; float:left;'  >
                          <input type='submit' value='Crear producto' style='float:right;' class='btn btn-primary link' data-url='get.php' data-data='opcion=CrearProducto' data-response='content' />
                        </div>
                        <div style='width:100%' id='responsetipoclientes'>$tipoclientes</div>
                  </div>";

              echo json_encode( array("form" => utf8_encode($form)  ));            
        }
        else
        {
              $message = $msg->warning("No se encontraron productos registrados.");
              $m = "<div style='width:100%; float:left;'  >
                        <input type='submit' value='Crear producto' style='float:right;' class='btn btn-primary link' data-url='get.php' data-data='opcion=CrearProducto' data-response='content' />
                    </div>
                    <div style='width:100%; float:left; '>$message</div> "; 
              echo json_encode( array("form" => utf8_encode($m) ));
        }
      break;

      case "Ventas": 

         $recibo = "";
         $sql="SELECT MAX(recibo) AS recibo FROM venta WHERE punto_id = $idpunto";
         $data = $connection->query($sql); 
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 
         $row = $results[0]; 
         if( $row['recibo'] == null )
            $recibo = 1;
         else
            $recibo = $row['recibo']; 

         $limite = 3;

         $ceros_agregar = $limite - strlen($recibo); 
         if( $ceros_agregar > 0 )
         {
             $ceros = "";
             for ($i=$ceros_agregar; $i <= $limite; $i++) 
             { 
                 $ceros .= "0";
             }
             $recibo = $ceros."".$recibo;
         } 

         

        
         $query="SELECT * FROM producto_punto WHERE cantidad > 0 AND estado = true";
         $data = $connection->query($query); 
         $resultss = $data->fetchAll(PDO::FETCH_BOTH ); 
         $form=""; 

         $query = "SELECT id , tipo FROM tipo_venta WHERE id <= 2";
         $data = $connection->query($query); 
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 
         $params = array
                   (
                       "default" => "" ,  
                       "name" => "tipoventa" , 
                       "id" => "" , 
                       "class" => "dropdown" ,
                       "value" => 1, 
                       "multiple" => false, 
                       "results" => $results
                    );  

         $tipoventa = $func->selectOption($params);
         $access = $func->CheckAccess( $connection , 2 , $idusuario );

         $row_point = "";
         if( $access > 0 )
         {

              $query = "SELECT id , punto FROM punto WHERE id <> $idpunto";
              $data = $connection->query($query); 
              $results = $data->fetchAll(PDO::FETCH_BOTH ); 

              $params =  array
                         (
                             "default" => "Seleccione" ,  
                             "name" => "punto_id" , 
                             "id" => "punto_id" , 
                             "class" => "punto_id w100" ,
                             "value" => "", 
                             "multiple" => false, 
                             "results" => $results
                          );                                
         } 
        
         if( count($resultss) > 0 )
         {   
            $form.= "<div class='titulo'>REGISTRO DE VENTAS</div>";
            $form.= " <form class='ui form' action='set.php' method='post' data-response='content' autocomplete='off' data-type='json' data-paginate='false' >";

            $form.= "<div class='izq' style='width:30%;background-color:#fff; padding:10px;'>
                    
                        <div class='field' >
                          <label>Nombre del producto</label>  
                          <div class='ui icon input loading'>
                            <input type='text' style='width:100%;' id='buscarProductoVenta'>
                            <i class='search search-producto icon' style='display:none; '></i>
                          </div> 
                        </div>  
                        
                        <div class='field' >
                          <label>Cliente</label>  
                          <div class='ui icon input loading'>
                            <input type='text' style='width:100%;' id='buscarClienteVenta' data-buscar='BuscarProductoVenta' >
                            <i class='search search-cliente icon' style='display:none; '></i>
                          </div> 
                        </div> 

                        <div class='field'> 
                             <label>Recibo</label> 
                             <input type='text' name='recibo' class='form-control validar requerido texto' value='$recibo' readonly='readonly' >                                                           
                        </div>
                        <div class='field'>
                             <label>Tipo de venta</label>
                             $tipoventa
                        </div>


                        <div class='field'> 
                             <label>Fecha</label>  
                             <input type='text' style='width:100%;' name='fecha' placeholder='fecha' class='date validar fecha'  />                              
                        </div>  
                     
                  </div>";


 
            $form.= "<div class='der' style='width:68%' >
                          
                          <div class='ui grid'>
                            <div class='four wide column'>
                               <div class='header_title ui medium header'>Precio</div>
                            </div>
                            <div class='four wide column'>
                               <div class='header_price ui medium header'>0</div>
                            </div>
                            
                          </div>

                          <table class='ui single line table' id='tabla' style='background:white;'> 
                               <thead>
                                 <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Descuento</th>
                                    <th>Disponible</th>  
                                    <th>Cantidad</th>
                                    <th>Precio</th> 
                                    <th>Eliminar</th>
                                 </tr>
                               </thead>
                               <tbody></tbody>
                               <tfoot class='full-width'>
                                  <tr>
                                    <th colspan='7' class='text-right'>

                                        <div class='ui animated button btn-enviar' tabindex='0' >
                                          <div class='visible content'>Registrar venta</div>
                                          <div class='hidden content'>
                                            <i class='right arrow icon'></i>
                                          </div>
                                        </div>

                                       <!--<input type='submit' class='btn btn-primary btn-save' disabled='disabled' value='REGISTRAR' />-->
                                    </th>
                                  </tr>
                               </tfoot>
                          </table>
                  </div>

                  <div class='ui flowing popup top left transition hidden'>
                    <div class='ui three column divided center aligned grid'>
                      <div class='column'>
                        <h4 class='ui header'>Basic Plan</h4>
                        <p><b>2</b> projects, $10 a month</p>
                        <div class='ui button'>Choose</div>
                      </div>
                      <div class='column'>
                        <h4 class='ui header'>Business Plan</h4>
                        <p><b>5</b> projects, $20 a month</p>
                        <div class='ui button'>Choose</div>
                      </div>
                      <div class='column'>
                        <h4 class='ui header'>Premium Plan</h4>
                        <p><b>8</b> projects, $25 a month</p>
                        <div class='ui button'>Choose</div>
                      </div>
                    </div>
                  </div> "; 
            $form.= "<input type='hidden' name='opcion' value='RegistrarVentas' />"; 
            $form.= "<input type='hidden' name='idcliente' id='idcliente' />"; 
            $form.= "<input type='hidden' name='tipocliente' id='tipocliente' />"; 
            
            $form.= "</form>"; 
         } 
         else            
          $form.= $msg->danger("NO HAY PRODUCTOS DISPONIBLES"); 
         echo $form;    
      break;  

      case "Cotizacion":
         $numero = ""; 
         $limite = 3;

         $ceros_agregar = $limite - strlen($numero); 
         if( $ceros_agregar > 0 )
         {
             $ceros = "";
             for ($i=$ceros_agregar; $i <= $limite; $i++) 
             { 
                 $ceros .= "0";
             }
             $numero = $ceros."".$numero;
         } 
  
        
         $query="SELECT * FROM producto_punto WHERE cantidad > 0 AND estado = true";
         $data = $connection->query($query); 
         $resultss = $data->fetchAll(PDO::FETCH_BOTH ); 
         $form=""; 

         $query = "SELECT id , tipo FROM tipo_venta WHERE id <= 2";
         $data = $connection->query($query); 
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 
         $params = array
                   (
                       "default" => "Seleccione" ,  
                       "name" => "tipoventa" , 
                       "id" => "tVenta" , 
                       "class" => "form-control validar requerido select" ,
                       "value" => "", 
                       "multiple" => false, 
                       "results" => $results
                    );  

         $tipoventa = $func->selectOption($params);
         $access = $func->CheckAccess( $connection , 2 , $idusuario );

         $row_point = "";
         if( $access > 0 )
         {

              $query = "SELECT id , punto FROM punto WHERE id <> $idpunto";
              $data = $connection->query($query); 
              $results = $data->fetchAll(PDO::FETCH_BOTH ); 

              $params =  array
                         (
                             "default" => "Seleccione" ,  
                             "name" => "punto_id" , 
                             "id" => "punto_id" , 
                             "class" => "punto_id w100" ,
                             "value" => "", 
                             "multiple" => false, 
                             "results" => $results
                          );                                
         } 
        
         if( count($resultss) > 0 )
         {   
            $form.= "<div class='titulo'>REGISTRO DE COTIZACIONES</div>";
            $form.= "<form class='ui form' action='set.php' method='post' data-response='content' autocomplete='off' data-type='json' data-paginate='false' >";

            $form.= "<div class='izq' style='width:30%;background-color:#fff; padding:10px;'>
                      <div style='width:100%;background-color:#fff; padding:10px; float:left;'>
                          
                          <div class='field' >
                               <label>Nombre del producto</label>
                               <input type='text' class='form-control' id='buscarProductoCotizacion' >
                          </div>

                          <div class='field' >
                            <label>Cliente</label>  
                            <div class='ui icon input loading'>
                              <input type='text' style='width:100%;' id='buscarClienteVenta' data-buscar='BuscarProductoVenta' >
                              <i class='search search-cliente icon' style='display:none; '></i>
                            </div> 
                          </div> 

                         
                      </div>   
                     
                  </div>";
 
            $form.= "<div class='der' style='width:68%' >
                     

                          <div class='ui grid'>
                              <div class='four wide column'>
                                 <div class='header_title ui medium header'>Precio total : </div>
                              </div>
                              <div class='four wide column'>
                                 <div class='header_price ui medium header'>0</div>
                              </div>
                          </div>

                          <table class='ui table' id='tabla' style='background:white;'> 
                               <thead>
                                 <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Disponible</th>  
                                    <th>Cantidad</th>
                                    <th>Precio</th> 
                                    <th>Eliminar</th>
                                 </tr>
                               </thead>
                               <tbody></tbody>
                               <tfoot>
                                  <tr>
                                    <td colspan='6' class='text-right'>
                                       <div class='ui animated button btn-enviar' tabindex='0' >
                                          <div class='visible content'>Registrar cotizacion</div>
                                          <div class='hidden content'>
                                            <i class='right arrow icon'></i>
                                          </div>
                                        </div>
                                    </td>
                                  </tr>
                               </tfoot>
                          </table>
                  </div>"; 
            $form.= "<input type='hidden' name='opcion' value='RegistrarCotizacion' />"; 
            $form.= "<input type='hidden' name='idcliente' id='idcliente' />"; 
            $form.= "<input type='hidden' name='tipocliente' id='tipocliente' />"; 
            
            $form.= "</form>"; 
         } 
         else            
          $form.= $msg->danger("NO HAY PRODUCTOS DISPONIBLES");

         echo $form;           
      break;  

      

      case "DevolucionCompra":

            $query = "SELECT id , tipo FROM tipo_compra WHERE id <= 2";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH);  

            $params = array
                     (
                         "default" => "Seleccione" ,  
                         "name" => "tipocompra" , 
                         "id" => "tCompra" , 
                         "class" => "dropdown" ,
                         "value" => "", 
                         "multiple" => false, 
                         "results" => $results
                      );  

            $tipocompra = $func->selectOption($params);

            $query = "SELECT id , nombre FROM producto";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 

            $params =  array
                       (
                           "default" => "Todos" ,  
                           "name" => "idproductos[]" , 
                           "id" => "idproductos" , 
                           "class" => "idproductos w100 chosen" ,
                           "value" => "", 
                           "multiple" => true, 
                           "results" => $results
                        );  
            $productos = $func->selectOption($params);
         
    
            $form = "<div class='titulo'>DEVOLUCIONES EN COMPRAS</div>"; 

            $form.= "<div class='ui sixteen  centered'>
                       <div class='ui form'>
                         <form autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_devolucion_compra' >
                            <div class='seven fields'>
                                <div class='field'>
                                     <label>Tipo de compra</label>
                                      $tipocompra
                                </div>

                                <div class='field'>
                                     <label>Producto</label>
                                      $productos
                                </div>

                                <div class='field ref_add_cliente' >
                                     <label>Proveedor</label>
                                     <input type='text' class='ssin_cliente' name='nombre_proveedor' id='buscarProveedorDevolucion' >
                                </div>  
                                <div class='field'>
                                     <label>Factura</label>
                                     <input type='text' name='factura'  />                              
                                </div> 

                                <div class='field'>
                                 <label>Desde</label>
                                 <input type='text' class='form-control validar texto date'  name='inicio' >
                               </div>

                               <div class='field'>
                                 <label>Hasta</label>
                                 <input type='text' class='form-control validar texto date'  name='fin' >
                               </div> 

                                <div class='field'>
                                    <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                      <div class='visible content'>BUSCAR</div>
                                      <div class='hidden content'>
                                        <i class='right search outline icon'></i>
                                      </div>
                                    </div>
                                </div>
                            </div> 
 
                            <input type='hidden' name='opcion' value='BuscarCompraDevolucion' />
                            <input type='hidden' name='idproveedor' id='idproveedor' /> 
                         </form>
                       </div>
                   </div>";

            $form.= "<div class='sixteen wide column'>
                        <table class='ui table table-devolucion-compra'>
                          <thead class='thead-default'> 
                             <tr>
                               <th>FECHA</th> 
                               <th>TIPO DE COMPRA</th> 
                               <th>TRABAJADOR</th> 
                               <th>PROVEEDOR</th>
                               <th>PRODUCTOS</th>
                               <th>EXPANDIR</th>  
                             </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot> 
                              <tr>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                              </tr>
                          </tfoot>
                        </table>
                     </div>"; 
            $form.= "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div> 
               </div>"; 
            echo $form; 
      break; 

      case "paginar_devolucion_compra": 
         $connection->query("SET NAMES utf8");
         $condicion = "";

         if( !empty($tipoventa) )
            $condicion.= " AND tipo_compra_id = $tipoventa ";
          
         if( !empty($idcliente) )
            $condicion = " AND proveedor_id = $idproveedor "; 

         if( !empty($inicio) && !empty($fin) )
            $condicion .= " AND date(compra.fecha) >= '$inicio' AND date(compra.fecha) <= '$fin' ";           
         else if( !empty($inicio) )
              $condicion .= " AND date(compra.fecha) = '$inicio' ";  
         


         if( !empty($idproductos) )
         {
               if( count($idproductos) > 0 )
               {
                  $list = trim(implode(",", $idproductos));
                  if( strlen($list) > 0 )
                     $condicion .= " AND producto_compra.producto_id IN ( $list )"; 
               }
         } 
          
                    
         $query = "SELECT 
                       compra.id,
                       date(compra.fecha) fecha  , 
                       tipo_compra.tipo , 
                       concat( usuario.nombre , ' ' , usuario.apellido ) usuario , 
                       concat( proveedor.nombres , ' ' , proveedor.apellidos ) proveedor ,
                       SUM( producto_compra.cantidad ) productos 
                   FROM compra 
                   INNER JOIN producto_compra ON producto_compra.compra_id = compra.id 
                   INNER JOIN usuario ON usuario.id = compra.usuario_id 
                   INNER JOIN tipo_compra ON tipo_compra_id = tipo_compra.id
                   LEFT JOIN proveedor ON proveedor.id = compra.proveedor_id
                   WHERE compra.estado = 1 $condicion
                   GROUP BY compra.id"; 

           $data = $connection->query($query);  
           $results = $data->fetch(PDO::FETCH_BOTH );
           $recordsTotal = count($results); 
            
           $query.= " LIMIT $start, $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH);  

           if( !empty($idproductos) )
           {
             if( count($idproductos) > 0 )
             {

               foreach ($results as $key_c => $compra) 
               {
                     $query = "SELECT 
                                    producto_id ,
                                    nombre
                               FROM producto_compra
                               INNER JOIN producto ON producto.id = producto_id
                               WHERE compra_id = $compra[id]";

                     $data = $connection->query($query);  
                     $results_pc = $data->fetchAll(PDO::FETCH_ASSOC); 


                     if( count($results_pc) > 0 )
                     {
                        $results[$key_c]["productos"] = "";
                        foreach ($results_pc as $key_pv => $rpv) 
                        {
                           foreach ($idproductos as $key_id => $idp) 
                           {

                              if( $rpv["producto_id"] == $idp)
                              {
                                 $results[$key_c]["productos"] .= "<div class='ui label'>
                                                                       $rpv[nombre]
                                                                   </div>";
                              }
                           }
                        }
                     }
               }
             }
           }

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results
           ); 
           echo json_encode($json_data); 
      break;



      case "DevolucionVenta":

            $query = "SELECT id , tipo FROM tipo_venta WHERE id <= 2";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH); 

            $params = array
                     (
                         "default" => "Seleccione" ,  
                         "name" => "tipoventa" , 
                         "id" => "tVenta" , 
                         "class" => "dropdown" ,
                         "value" => "", 
                         "multiple" => false, 
                         "results" => $results
                      );  

            $tipoventa = $func->selectOption($params);


            $query = "SELECT id ,  TRIM(UPPER(concat(nombre,' ',apellido))) usuario FROM usuario WHERE status = true";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 
            $params = array
                     (
                         "default" => "" ,  
                         "name" => "idusuarios[]" , 
                         "id" => "" , 
                         "class" => "chosen" ,
                         "value" => "", 
                         "multiple" => true, 
                         "results" => $results
                      );  

            $usuario = $func->selectOption($params); 


            $query = "SELECT id , nombre FROM producto";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 

            $params =  array
                       (
                           "default" => "Todos" ,  
                           "name" => "idproductos[]" , 
                           "id" => "idproductos" , 
                           "class" => "idproductos w100 chosen" ,
                           "value" => "", 
                           "multiple" => true, 
                           "results" => $results
                        );  
            $productos = $func->selectOption($params);
         
    
            $form = "<div class='titulo'>DEVOLUCIONES EN VENTAS</div>"; 

            $form.= "<div class='ui sixteen  centered'>
                       <div class='ui form'>
                         <form autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_devolucion_venta' >                            
                            <div class='six fields'> 

                                <div class='field'>
                                   <label>Producto</label>
                                   $productos
                                </div>

                                <div class='field'>
                                   <label>Tipo de venta</label>
                                   $tipoventa
                                </div>

                                <div class='field'>
                                   <label>Trabajador</label>
                                   $usuario
                                </div>

                                <div class='field ref_add_cliente' >
                                     <label>Cliente</label>
                                     <input type='text' class='form-control sin_cliente' name='nombre_cliente' id='buscarClienteVenta' data-buscar='BuscarProductoVenta'>
                                </div> 

                                <div class='field'>
                                     <label>Fecha</label>
                                     <input type='text' name='fecha' class='form-control date limite_date validar fecha' />                              
                                </div> 

                                <div class='field'>
                                    <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                      <div class='visible content'>GUARDAR</div>
                                      <div class='hidden content'>
                                        <i class='right send outline icon'></i>
                                      </div>
                                    </div>
                                </div>  
                                <input type='hidden' name='idcliente' id='idcliente' />
                                <input type='hidden' name='tipocliente' id='tipocliente' />
                            </form>
                       </div>
                   </div>";
            $form.= "<div class='sixteen wide column'>
                        <table class='ui fixed single line celled table table-devolucion-venta'>
                          <thead class='thead-default'> 
                             <tr>
                               <th>FECHA</th> 
                               <th>PRODUCTOS</th> 
                               <th>TRABAJADOR</th> 
                               <th>CLIENTE</th>
                               <th>PRODUCTOS</th>
                               <th>EXPANDIR</th>  
                             </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot> 
                              <tr>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                                 <td></td>
                              </tr>
                          </tfoot>
                        </table>
                     </div>"; 
            $form.= "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div> 
               </div>"; 
            echo $form; 
      break; 

      case "paginar_devolucion_venta":
          $condicion = "";


          if( !empty($tipoventa) )
              $condicion.= " AND tipo_venta_id = $tipoventa ";
          if( !empty($fecha) )
              $condicion.= " AND date(venta.fecha) = '$fecha' ";
          if( !empty($idcliente) )
              $condicion .= " AND cliente_id = $idcliente ";


          if( !empty($idusuarios) )
           {
               if( count($idusuarios) > 0 )
               {
                  $list = trim(implode(",", $idusuarios));
                  if( strlen($list) > 0 )
                     $condicion .=" AND venta.usuario_id IN ( $list )"; 
               }
           }

          if( !empty($idproductos) )
           {
               if( count($idproductos) > 0 )
               {
                  $list = trim(implode(",", $idproductos));
                  if( strlen($list) > 0 )
                     $condicion .= " AND producto_venta.producto_id IN ( $list )"; 
               }
           }
 
               
           $query = "SELECT 
                       venta.id,
                       date(venta.fecha) fecha , 
                       concat( usuario.nombre , ' ' , usuario.apellido ) usuario , 
                       concat( cliente.nombres , ' ' , cliente.apellidos ) cliente ,
                       SUM( producto_venta.cantidad ) count ,
                       count( producto_venta.producto_id ) productos
                   FROM venta 
                   INNER JOIN producto_venta ON producto_venta.venta_id = venta.id 
                   INNER JOIN usuario ON usuario.id = venta.usuario_id 
                   LEFT JOIN cliente ON cliente.id = venta.cliente_id
                   WHERE venta.estado = 1 $condicion
                   GROUP BY venta.id";

           $data = $connection->query($query);  
           $results = $data->fetch(PDO::FETCH_BOTH );
           $recordsTotal = count($results); 
            
           $query.= " LIMIT $start, $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH);  

           if( !empty($idproductos) )
           {
             if( count($idproductos) > 0 )
             {

               foreach ($results as $key_v => $venta) 
               {
                     $query = "SELECT 
                                    producto_id ,
                                    nombre
                               FROM producto_venta 
                               INNER JOIN producto ON producto.id = producto_id
                               WHERE venta_id = $venta[id]";
                     $data = $connection->query($query);  
                     $results_pv = $data->fetchAll(PDO::FETCH_ASSOC); 


                     if( count($results_pv) > 0 )
                     {
                        $results[$key_v]["productos"] = "";
                        foreach ($results_pv as $key_pv => $rpv) 
                        {
                           foreach ($idproductos as $key_id => $idp) 
                           {

                              if( $rpv["producto_id"] == $idp)
                              {
                                 $results[$key_v]["productos"] .= "<div class='ui label'>
                                                                       $rpv[nombre]
                                                                   </div>";
                              }
                           }
                        }
                     }
               }
             }
           }

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results
           ); 
           echo json_encode($json_data);  
      break;

      

      case "ObtenerProductosCotizacion":
          $numero = "";
          $sql="SELECT MAX(numero) AS numero FROM venta WHERE punto_id = $idpunto";
          $data = $connection->query($sql); 
          $results = $data->fetchAll(PDO::FETCH_BOTH ); 
          $row = $results[0]; 
          if( $row['numero'] == null )
              $numero = 1;
          else
              $numero = $row['numero']; 

          $limite = 3;

          $ceros_agregar = $limite - strlen($numero); 
          if( $ceros_agregar > 0 )
          {
               $ceros = "";
               for ($i=$ceros_agregar; $i <= $limite; $i++) 
               { 
                   $ceros .= "0";
               }
               $numero = $ceros."".$numero;
          } 

          $query = "SELECT id , tipo FROM tipo_venta WHERE id <= 2";
          $data = $connection->query($query); 
          $results = $data->fetchAll(PDO::FETCH_BOTH ); 
          $params = array
                   (
                       "default" => "Seleccione" ,  
                       "name" => "tipoventa" , 
                       "id" => "tVenta" , 
                       "class" => "form-control validar requerido select" ,
                       "value" => "", 
                       "multiple" => false, 
                       "results" => $results
                    );  

          $tipoventa = $func->selectOption($params); 

          $query = "SELECT 
                      producto.id,
                      producto.nombre , 
                      producto.codigo ,
                      producto_cotizacion.cantidad , 
                      producto_cotizacion.precio ,
                      producto_punto.cantidad disponible ,
                      cotizacion.cliente_id , 
                      producto_punto.costo costonormal , 
                      producto_punto.costo_liquidado costoliquidado
                    FROM producto 
                    INNER JOIN producto_punto ON ( producto.id = producto_punto.producto_id )
                    INNER JOIN producto_cotizacion ON ( producto_cotizacion.producto_id = producto.id  AND cotizacion_id = $cotizacion_id )
                    INNER JOIN cotizacion ON cotizacion.id = cotizacion_id ";

            $data = $connection->query($query);  
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 

            $cliente_id = "";
            if( count($results) > 0 )
            {
                $tabla ="<form autocomplete='off' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='response'>";
                $tabla.= "<table class='table table-bordered'>";
                $tabla.= "<thead>
                              <tr style='background:#cecece !important;'>
                                <td style='padding:2px !important;'>NOMBRE</td>
                                <td style='padding:2px !important;'>CODIGO</td>
                                <td style='padding:2px !important;'>CANTIDAD</td>
                                <td style='padding:2px !important;'>PRECIO</td>
                              </tr>
                          </thead> ";

                foreach ($results as $fila ) 
                {
                    $cliente_id = $fila['cliente_id'];
                    $tabla.= "<tr>
                                <td>$fila[nombre]</td>
                                <td>$fila[codigo]</td>
                                <td><input type='text' value='$fila[cantidad]'  name='cantidad[]' class='form-control validar numero requerido' data-min='1' data-max='$fila[disponible]' style='width:70px;' /></td>
                                <td>$fila[precio]</td>
                                <input type='hidden' name='idproducto[]' value='$fila[id]' />
                                <input type='hidden' name='precio[]' value='$fila[precio]' />
                                <input type='hidden' name='costonormal[]' value='$fila[costonormal]' />
                                <input type='hidden' name='costoliquidado[]' value='$fila[costoliquidado]' />
                              </tr>";
                }
                $tabla.="<tr>
                           <td>
                              <div class='form-group'>
                                 <div class='form-header'>
                                   <label>Recibo</label>
                                 </div> 
                                 <div class='form-body'>   
                                   <input type='text' name='recibo' class='form-control validar requerido texto' value='$numero' readonly='readonly' >                              
                                  </div>
                              </div>
                           </td>
                           <td>
                              <div class='form-group'>
                                 <div class='form-header'>
                                   <label>Tipo de venta</label>
                                 </div> 
                                 <div class='form-body'> 
                                   $tipoventa
                                 </div> 
                              </div> 
                           </td>
                           <td colspan='2'>
                              <div class='form-group'>
                                 <div class='form-header'>
                                   <label>Fecha</label>
                                 </div> 
                                 <div class='form-body' style='float:left;width:50%;'> 
                                   <input type='text' name='fecha' class='form-control date limite_date validar fecha requerido'  disabled='disabled' />                              
                                 </div> 
                                  <div class='form-body' style='float:right;width:50%;'> 
                                   <input type='text' name='hora'  class='form-control time limite_time validar hora requerido'  disabled='disabled' />
                                 </div> 
                              </div>
                           </td>
                         </tr>";
                $tabla .= "<input type='hidden' name='opcion' value='ConvertirEnVenta' />";
                $tabla .= "<input type='hidden' name='cotizacion_id' value='$cotizacion_id' />";
                $tabla .= "<input type='hidden' name='idcliente' value='$cliente_id' />";
                $tabla .= "<tr>
                             <td colspan='3'></td>
                             <td><input type='submit' class='btn btn-primary btn-save'  value='CONVERTIR EN VENTA' /></td>
                           </tr>";
                $tabla .= "</form>";
                echo $tabla;
            }
            else
            {
                $message = $msg->warning("No se encontraron productos.");
            }
      break;

      case "ObtenerDetalleDevolverVenta":
           $query = "SELECT 
                      producto.id,
                      producto.nombre , 
                      producto.codigo ,
                      cantidad , 
                      precio 
                    FROM producto 
                    INNER JOIN producto_venta ON ( producto_id = producto.id  AND venta_id = $venta_id )";
            $data = $connection->query($query);  
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 
            if( count($results) > 0 )
            {
                $tabla = "<div class='ui form'>"; 
                $tabla.="<form autocomplete='off' data-type='json' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content-modal'>";
                $tabla.= "<table class='table ui'>";
                $tabla.= "<thead>
                              <tr>
                                <td>NOMBRE</td>
                                <td>CODIGO</td>
                                <td>CANTIDAD</td>
                                <td>PRECIO</td>
                              </tr>
                          </thead> ";
                foreach ($results as $fila ) 
                {
                    $tabla.= "<tr>
                                <td>$fila[nombre]</td>
                                <td>$fila[codigo]</td>
                                <td><input type='text' value='$fila[cantidad]'  name='cantidad[]' class='form-control validar numero requerido' data-min='0' data-max='$fila[cantidad]' style='width:70px;' /></td>
                                <td>$fila[precio]</td>
                                <input type='hidden' name='productos[]' value='$fila[id]' />
                                <input type='hidden' name='cant_ant[]' value='$fila[cantidad]' />
                              </tr>";
                }
                $tabla .= "<input type='hidden' name='opcion' value='DevolverVenta' />";
                $tabla .= "<input type='hidden' name='venta_id' value='$venta_id' />";
                $tabla .= "<tr>
                             <td colspan='3'></td>
                             <td>
                               <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                  <div class='visible content'>DEVOLVER</div>
                                  <div class='hidden content'>
                                    <i class='right reply all icon'></i>
                                  </div>
                               </div>
                             </td>
                           </tr>";
                $tabla .= "</form>";
                $tabla .= "</div>";
                echo $tabla;
            }
            else
            {
                $message = $msg->warning("No se encontraron productos.");
            }
      break;

      case "ObtenerDetalleDevolverCompra":
            $query = "SELECT 
                      producto.id,
                      producto.nombre , 
                      producto.codigo ,
                      cantidad , 
                      precio 
                    FROM producto 
                    INNER JOIN producto_compra ON ( producto_id = producto.id  AND compra_id = $compra_id )";
            $data = $connection->query($query);  
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 
            if( count($results) > 0 )
            {
                $tabla = "<div class='ui form'>";
                $tabla.="<form autocomplete='off' action='set.php' data-type='json'  method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content-modal'>";
                $tabla.= "<table class='ui table'>";
                $tabla.= "<thead>
                              <tr>
                                <td>NOMBRE</td>
                                <td>CODIGO</td>
                                <td>CANTIDAD</td>
                                <td>PRECIO</td>
                              </tr>
                          </thead> ";
                foreach ($results as $fila ) 
                {
                    $tabla.= "<tr>
                                <td>$fila[nombre]</td>
                                <td>$fila[codigo]</td>
                                <td><input type='text' value='$fila[cantidad]'  name='cantidad[]' class='form-control validar numero requerido' data-min='0' data-max='$fila[cantidad]' style='width:70px;' /></td>
                                <td>$fila[precio]</td>
                                <input type='hidden' name='productos[]' value='$fila[id]' />
                              </tr>";
                }
                $tabla .= "<input type='hidden' name='opcion' value='DevolverCompra' />";
                $tabla .= "<input type='hidden' name='compra_id' value='$compra_id' />";
                $tabla .= "<tr>
                             <td colspan='3'></td>
                             <td>
                               <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                  <div class='visible content'>DEVOLVER</div>
                                  <div class='hidden content'>
                                    <i class='right reply all icon'></i>
                                  </div>
                               </div>
                             </td>
                           </tr>";
                $tabla .= "</form>";
                $tabla .= "</div>";
                echo $tabla;
            }
            else
            {
                $message = $msg->warning("No se encontraron productos.");
            }
      break;

      case "ReporteIngresos":
         if( empty($fecha) )
         { 
             $fecha = date('Y-m-d'); 
         }

         $TOTAL = 0;

         $SALDO_CAJA = GET_SALDO_CAJA( $connection , $fecha );

         $VENTAS_CONTADO = GET_VENTAS_CONTADO( $connection , $fecha );
         $VENTAS_CREDITO_ABONOS = GET_VENTAS_CREDITO_ABONOS( $connection , $fecha );
         $VENTAS = $VENTAS_CONTADO + $VENTAS_CREDITO_ABONOS;

         $INGRESOS = 0;

         $COMPRAS_CONTADO = GET_COMPRAS_CONTADO( $connection , $fecha );
         $COMPRAS_CREDITO_ABONOS = GET_COMPRAS_CREDITO_ABONOS( $connection , $fecha );
         $COMPRAS = $COMPRAS_CONTADO + $COMPRAS_CREDITO_ABONOS;


         $EGRESOS = GET_EGRESOS( $connection , $fecha );
         $GASTOS = GET_GASTOS( $connection , $fecha );


         $TOTAL = ( $SALDO_CAJA +  $VENTAS  + $INGRESOS ) - ( $COMPRAS + $EGRESOS + $GASTOS );


         $resumen = GET_VENTAS_RESUMEN_TOTAL( $connection , $fecha ); 

         $MARGEN = 0;
         if( $resumen['precios'] > 0) 
            $MARGEN = ((100 * $resumen['precios']) / $resumen['costos']) -100;

         $UTILIDAD = $resumen['precios'] - $resumen['costos'];

         $porcentaje_pagado = 0;
         if( $VENTAS > 0 )
            $porcentaje_pagado = $VENTAS / $resumen['precios'] * 100;

         $UTILIDAD1 = 0;

         if( $UTILIDAD > 0 )
            $UTILIDAD1 = ( $UTILIDAD * $porcentaje_pagado ) / 100;

         $UTILIDAD2 = $UTILIDAD1 - $GASTOS;


         $form = "<div class='content'  style='width:80%;'>
                     <div style='width:100%; margin-bottom:10px; float:left;'>
                        <div style='width:40%; margin-bottom:10px; float:left;'>
                          <form autocomplete='off' class='ui form' action='get.php' method='post'  data-paginate='false' data-response='content'>   
                              
                              <div class='thow fields'>
                                 <div class='field'>
                                    <label>Seleccione una fecha</label> 
                                    <input type='text' class='form-control validar requerido date' name='fecha' >
                                 </div>
                                 <div class='field'>
                                    <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                      <div class='visible content'>Consultar</div>
                                      <div class='hidden content'>
                                        <i class='right search icon'></i>
                                      </div>
                                    </div>
                                 </div>

                                 <input type='hidden' name='opcion' value='ReporteIngresos' />
                              </div>   
                          </form>
                        </div>
                     </div> 
                     <div style='width:40%; float:left;'>

                          <table class='ui table' >
                               <thead><th>Concepto</th><th>Debito</th><th>Credito</th></thead>
                               <tr>
                                  <td>SALDO EN CAJA</td>
                                  <td>$ ".number_format( $SALDO_CAJA,0,"",".")."</td> 
                                  <td></td>
                              </tr>  
                              <tr>
                                  <td>VENTAS</td>
                                  <td>$ ".number_format( $VENTAS,0,"",".")."</td> 
                                  <td></td> 
                              </tr>
                              <tr>
                                  <td>INGRESOS</td>
                                  <td></td>
                                  <td></td> 
                            </tr> 

                            <tr>
                              <td>COMPRAS</td>
                              <td></td> 
                              <td>$ ".number_format( $COMPRAS,0,"",".")."</td> 
                            </tr>

                            <tr>
                              <td>EGRESOS</td>
                              <td></td>
                              <td>$ ".number_format( $EGRESOS,0,"",".")."</td> 
                            </tr>  

                            <tr>
                              <td>GASTOS</td>
                              <td></td>
                              <td>$ ".number_format( $GASTOS,0,"",".")."</td> 
                            </tr> 
                            <tfoot>
                              <tr>
                                <th>TOTAL</th>
                                <th colspan='2' style='font-size:24px;' class='text-center'><b>$ ".number_format( $TOTAL,0,"",".")."</b></th> 
                              </tr>
                            </tfoot>
                         </table>    
                     </div>
                     <div style='width:55%; float:right;'> 
                           <table class='ui table' >
                             <thead>
                               <tr><th colspan='2'>UTILIDAD DE VENTA</th></tr>
                              
                             </thead>
                              <tr><td>TOTAL VENTAS</td><td>$ ".number_format( $resumen['precios'],0,"",".")."</td></tr>
                              <tr><td>TOTAL COSTOS</td><td>$ ".number_format( $resumen['costos'],0,"",".")."</td></tr>
                              <tr><td>MARGEN</td><td>".number_format( $MARGEN,0,"",".")." %</td></tr>
                              
                              <tr><td>UTILIDAD</td><td>$ ".number_format( $UTILIDAD1 ,0,"",".")."</td></tr>
                              <tr><td>UTILIDAD NETA</td><td>$ ".number_format( $UTILIDAD2 ,0,"",".")."</td></tr>
                           </table>
                     </div>
                  </div>";
          echo $form;
      break;

      case "CrearProducto":  
         $form ="<form class='form ui' autocomplete='off' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content-modal'>
                      <div class='two fields'> 
                          <div class='field'>
                             <label>Nombre del producto</label>
                             <input type='text' class='form-control validar requerido texto'id='nombreProducto'  name='nombre' >
                          </div>
                          
                          <div class='field'>
                              <label>Código</label>
                              <input type='text' name='codigo' class='form-control validar texto'  id='campoCodigo' name='codigo' />
                          </div>

                          
                      </div> 

                      <div class='two fields'>         
                              <div class='field'>
                                <label>Cantidad</label>
                                <input type='text' name='cantidad' class='form-control validar numero'  />  
                              </div>
                              <div class='field'>
                                <label>Precio</label>
                                <input type='text' name='precio' class='form-control validar numero' />   
                              </div>
                      </div>

                       <div class='two fields'> 
                          <div class='field'>
                              <label>Precio minimo</label>
                              <input type='text' name='precio_minimo' class='form-control validar texto'  id='campoCodigo' name='codigo' />
                          </div>
                         
                          <div class='field'>
                              <label>Costo</label>
                              <input type='text' name='costo' class='form-control validar numero'  />                                      
                          </div>
                      </div> 



                      <div class='two fields'>
                          <div class='field'>                                   
                             <label>Stock</label>
                             <input type='text' name='stock' class='form-control validar numero'  />   
                          </div> 
                          <div class='field'> 
                              <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                <div class='visible content'>GUARDAR</div>
                                <div class='hidden content'>
                                  <i class='right send outline icon'></i>
                                </div>
                              </div>
                          </div>
                      </div> 
                      <input type='hidden' name='opcion' value='$opcion' />
                 </form>";
         echo "<div class='content'>$form </div>";             
      break; 

      case "EditarProducto":
         $connection->query("SET NAMES utf8");
         $sql="SELECT * FROM 
               producto
               LEFT JOIN producto_punto ON ( producto_id = producto.id ) 
               WHERE producto.id = $id";
         $data = $connection->query($sql); 
         $results = $data->fetchAll(PDO::FETCH_BOTH );  
         $fila = $results[0]; 

         $form = "<form class='form ui' autocomplete='off' data-type='json' action='set.php' method='post'  data-paginate='false' enctype='multipart/form-data' data-response='content-modal' >
                          <div class='two fields'> 
                             <div class='field'> 
                                 <label>Nombre del producto</label>
                                 <input type='text' class='form-control validar requerido texto' value='$fila[nombre]'  name='nombre' id='nombreProducto' >
                             </div> 

                             <div class='field'> 
                                 <label>Codigo</label>
                                 <input type='text' name='codigo' class='form-control validar texto'  id='campoCodigo' name='codigo' value='$fila[codigo]' />   
                             </div> 
                          </div>

                          <div class='two fields'>  
                             <div class='field cantidades'>
                                <label>Cantidad</label> 
                                <div class='ui action input'>
                                  <input type='text' name='cantidad' value='$fila[cantidad]' id='cantidadEnviar' data-cantidad='$fila[cantidad]' readonly >
                                  <input type='text' placeholder='Escribir cantidad'  id='cantidadEscribir' style='border-radius:0px'>
                                  <button class='ui yellow icon button' data-operacion='1'>
                                    <i class='plus icon'></i>
                                  </button>
                                  <button class='ui icon button' data-operacion='2'>
                                    <i class='minus icon'></i>
                                  </button>
                                  <button class='ui icon button' data-operacion='3'>
                                    <i class='erase icon'></i>
                                  </button>
                                </div>
                                <input id='operacion' type='hidden' value='1' />
                             </div> 
                                   
                             <div class='field'>
                                 <label>Precio</label>
                                 <input type='text' name='precio' class='form-control validar decimal'  value='$fila[precio]'  />   
                             </div>
                          </div> 
                       
                          <div class='two fields'>
                             <div class='field'>
                               <label>Precio minimo</label>
                               <input type='text' name='minimo' class='form-control validar decimal'  value='$fila[precio_minimo]' />   
                             </div>  
                             <div class='field'>
                               <label>Costo</label>     
                               <input type='text' name='costo' class='form-control validar decimal'  value='$fila[costo]' />   
                             </div>
                          </div>  

                       

                          <div class='two fields'>
                              <div class='field'>
                                 <label>Stock</label>
                                 <input type='text' name='stock' class=' validar numero'  value='$fila[stock]'  />   
                              </div>
                              <div class='field'> 
                                  <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                    <div class='visible content'>GUARDAR</div>
                                    <div class='hidden content'>
                                      <i class='right send outline icon'></i>
                                    </div>
                                  </div>
                              </div>
                          </div> 
                          <input type='hidden' name='opcion' value='$opcion' />
                          <input type='hidden' name='id' value='$id' /> 
                  </form>";
         echo $form;          
      break;


      case "Productos":
           $condicion = "";
           if( !empty($buscar) )
           { 
                 $buscar = trim($buscar); 
                 $buscarTilde = reemplazar( trim($buscar) ); 

                 $condicion = " AND ( ( nombre LIKE '%$buscar%' OR nombre LIKE '%$buscarTilde%' ) OR ( codigo LIKE '%$buscar%' OR codigo LIKE '%$buscarTilde%' ) )";
           }  

           $tabla = "<table class='table ui inline table-productos'>
                        <thead>
                          <tr>
                             <th>EDITAR</th>
                             <th>ELIMINAR</th>
                             <th>PRODUCTO</th>
                             <th>CANTIDAD</th>
                             <th>PRECIO</th> 
                             <th>STOCK</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                           <tr>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                          </tr>
                        </tfoot>
                     </table>";

           echo $tabla;     

           echo "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div> 
               </div>";          
      break;  

      case "paginar_productos": 
           $connection->query("SET NAMES utf8");

           $condicion = "";
           if( !empty($producto) )
           {  
               $condicion .= " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";
           } 

           if( !empty($_REQUEST['search']['value']) ) 
           { 
              $producto = $_REQUEST['search']['value'];
              $condicion .= " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";                 
           } 
           
            
           
           $query = "SELECT 
                             producto.id,
                             producto.nombre ,
                             producto_punto.producto_id  ,
                             producto_punto.cantidad ,
                             producto_punto.precio ,
                             producto.stock
                         FROM  producto_punto 
                         INNER JOIN producto ON producto.id = producto_id 
                         WHERE 1 $condicion AND producto_punto.estado = true
                         ORDER BY nombre ASC  ";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );
           $recordsTotal = count($results);

           $query .= " LIMIT $start, $length";                  
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );

          
           $query = "SELECT 
                           SUM(producto_punto.costo * producto_punto.cantidad) total_costo , 
                           SUM(producto_punto.precio * producto_punto.cantidad) total_precio   
                       FROM producto_punto 
                       INNER JOIN producto ON ( producto.id = producto_id )
                       WHERE producto_punto.estado = 1 AND punto_id = $idpunto  $condicion";
           $data = $connection->query($query);  
           $resultsTotal = $data->fetch(PDO::FETCH_BOTH );
           

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results 
           ); 
           echo json_encode($json_data);    
      break; 




      case "Clientes":
          $tabla = "<div class='ui right aligned segment abrir-modal link' data-url='get.php' data-title='Crear cliente' data-data='opcion=CrearCliente' data-response='content-modal'>
                        <div class='ui animated button '>
                          <div class='visible content' >Crear cliente</div>
                          <div class='hidden content'>
                            <i class='right plus icon'></i>
                          </div>
                        </div>
                    </div>";    

          $tabla .= "<table class='table ui inline table-clientes'>
                        <thead>
                          <tr> 
                             <th>EDITAR</th>
                             <th>ACTIVAR/INACTIVAR</th>
                             <th>NOMBRES</th>
                             <th>APELLIDOS</th> 
                             <th>CEDULA</th>
                             <th>TELEFONO</th> 
                             <th>DIRECCION</th>
                             <th>CORREO</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                           <tr>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td> 
                          </tr>
                        </tfoot>
                     </table>";

          $tabla.= "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div>
                    </div>";  

          echo $tabla;    
             
      break;  

      case "paginar_clientes": 
           $connection->query("SET NAMES utf8"); 
           
           $query = "SELECT *
                     FROM  cliente  
                     ORDER BY concat(nombres,' ',apellidos) ASC";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH);
           $recordsTotal = count($results);
                 
           $query .= " LIMIT $start , $length"; 
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH); 
          


           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  
           ); 
           echo json_encode($json_data);    
      break;

      case "CrearCliente":
        

        $form = "<form class='form ui' action='set.php' method='post' data-response='content-modal' data-paginate='false' autocomplete='off'>
                          <div class='two fields'>
                              <div class='field'>
                                   <label>Nombres</label>
                                   <input type='text' name='nombres' class='form-control validar requerido texto'  />
                              </div>

                              <div class='field'>
                                   <label>Apellidos</label>
                                   <input type='text' name='apellidos' class='form-control validar requerido texto' />   
                              </div>
                          </div>

                          <div class='two fields'> 
                              <div class='field'>
                                   <label>Cedula</label> 
                                   <input type='text' name='documento' class='form-control validar requerido texto' />                                   
                              </div> 

                              <div class='field'>
                                   <label>Telefono</label> 
                                   <input type='text' name='telefono' class='form-control validar requerido texto' />   
                              </div> 
                          </div> 

                          <div class='two fields'> 
                              <div class='field'>
                                   <label>Dirección</label> 
                                   <input type='text' name='direccion' class='form-control validar requerido texto'  />                                   
                              </div> 

                              <div class='field'>
                                   <label>Correo</label> 
                                   <input type='text' name='correo' class='form-control validar requerido texto'  />   
                              </div> 
                          </div> 

                          <div class='field'> 
                            <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>GUARDAR</div>
                              <div class='hidden content'>
                                <i class='right send outline icon'></i>
                              </div>
                            </div> 
                            <input type='hidden' name='opcion' value='RegistrarCliente' />
                          </div>
                      </form>";
        echo $form;         
      break; 

      case "EditarCliente":
        $query = "SELECT * FROM cliente WHERE id = $idcliente";
        $data = $connection->query($query);  
        $cliente = $data->fetch(PDO::FETCH_BOTH ); 

        $form = "<form action='set.php' method='post' class='form ui' data-type='json' data-response='content-modal' data-paginate='false' autocomplete='off' >
                          <div class='two fields'>
                              <div class='field'>
                                   <label>Nombres</label>
                                   <input type='text' name='nombres' class='form-control validar requerido texto'   value='$cliente[nombres]' />
                              </div>

                              <div class='field'>
                                   <label>Apellidos</label>
                                   <input type='text' name='apellidos' class='form-control validar requerido texto' value='$cliente[apellidos]' />   
                              </div>
                          </div>

                          <div class='two fields'> 
                              <div class='field'>
                                   <label>Cedula</label> 
                                   <input type='text' name='documento' class='form-control validar requerido texto' value='$cliente[documento]' />                                   
                              </div> 

                              <div class='field'>
                                   <label>Telefono</label> 
                                   <input type='text' name='telefono' class='form-control validar requerido texto' value='$cliente[telefono]' />   
                              </div> 
                          </div> 

                          <div class='two fields'> 
                              <div class='field'>
                                   <label>Dirección</label> 
                                   <input type='text' name='direccion' class='form-control validar requerido texto' value='$cliente[direccion]' />                                   
                              </div> 

                              <div class='field'>
                                   <label>Correo</label> 
                                   <input type='text' name='correo' class='form-control validar requerido texto' value='$cliente[correo]' />   
                              </div> 
                          </div> 

                          <div class='field'> 
                            <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>GUARDAR</div>
                              <div class='hidden content'>
                                <i class='right send outline icon'></i>
                              </div>
                            </div>
                            <input type='hidden' name='id' value='$cliente[id]' />
                            <input type='hidden' name='opcion' value='EditarCliente' />
                          </div>
                      </form>";
        echo $form;   
    
      break; 

      case "Sucursales":
           $per_page = 5;

           $aux_page = $page;
           $aux_page -= 1;
           $start = $aux_page * $per_page; 

           $columns = array("SUCURSAL" ,"FECHA CREACION" , "EDITAR" , "ESTADO");
           $query = "SELECT count(*) as count
                     FROM  punto ";
            
           $data = $connection->query($query);  
           $results = $data->fetch(PDO::FETCH_BOTH ); 
           $count = $results[0]; 
           if( $count > 0 )
           {
                $query = "SELECT id , punto , fecha , estado
                     FROM  punto 
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
                            'source_row' => 'puntos'
                          );
                $puntos =  $func->Pagination( $params ); 
                $form = "<div class='content'>
                        <div style='width:100%; float:right; margin-bottom:10px;' >
                            <input type='submit' value='Crear sucursal o punto' style='float:right;' class='btn btn-primary link' data-url='get.php' data-data='opcion=CrearSucursal' data-response='content' />
                        </div>
                        <div style='width:100%' id='responsePuntos'>$puntos</div>
                    </div>";

                echo json_encode( array("form" => utf8_encode($form)  ));         
           }
           else
           {
                $m = $msg->warning("No se encontraron Sucursales registradas.");
                echo json_encode( array("form" => utf8_encode($m) ));  
           }
      break; 

      case "Facturas":
           $per_page = 5;

           $aux_page = $page;
           $aux_page -= 1;
           $start = $aux_page * $per_page; 

           $columns = array("DESDE" ,"HASTA" , "PREFIJO" , "FECHA" , "EDITAR");
           $query = "SELECT count(*) as count
                     FROM  resolucion ";
            
           $data = $connection->query($query);  
           $results = $data->fetch(PDO::FETCH_BOTH ); 
           $count = $results[0]; 
           if( $count > 0 )
           {
                $query = "SELECT id , inicio , fin , prefijo , fecha , status , empezado
                     FROM  resolucion 
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
                            'source_row' => 'resolucion'
                          );
                $facturas =  $func->Pagination( $params ); 
                $form = "<div class='content'>
                        <div style='width:100%; float:right; margin-bottom:10px;' >
                            <input type='submit' value='Crear factura' style='float:right;' class='btn btn-primary link' data-url='get.php' data-data='opcion=CrearFactura' data-response='content' />
                        </div>
                        <div style='width:100%' id='responsefacturas'>$facturas</div>
                    </div>";

                echo json_encode( array("form" => utf8_encode($form)  ));         
           }
           else
           {
                $create = "<div style='width:100%; float:right; margin-bottom:10px;' >
                            <input type='submit' value='Crear factura' style='float:right;' class='btn btn-primary link' data-url='get.php' data-data='opcion=CrearFactura' data-response='content' />
                        </div>";
                $m = $msg->warning("No se encontraron numeros de facturas registrados");
                echo json_encode( array("form" => utf8_encode($m . " " . $create ) ));  
           }
      break;  

      case "CrearSucursal":
        $form = "<form action='set.php' method='post' data-response='content' data-paginate='false' autocomplete='off' style='width:500px' >
                          <div class='titulo'>REGISTRAR SUCURSAL</div>
                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Nombre de la sucursal o punto</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control validar requerido texto'  name='nombre' >
                            </div>
                          </div> 
                           
                          <div class='form-group'> 
                            <input type='submit' class='btn btn-primary btn-save'  value='REGISTRAR' />
                            <input type='hidden' name='opcion' value='CrearSucursal' />
                          </div>
                      </form>";
        echo "<div class='content'>$form</div>";       
      break;

      case "EditarSucursal":
        
        $sql="SELECT * FROM punto WHERE id = $idpunto";
        $data = $connection->query($sql); 
        $sucursal = $data->fetch(PDO::FETCH_BOTH );  
        

        $form = "<form action='set.php' method='post' data-paginate='false' data-response='modal-body' autocomplete='off' style='width:500px' >
                     
                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Nombre de la sucursal o punto</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control validar requerido texto'  name='nombre' value='$sucursal[punto]' >
                            </div>
                          </div> 
                           
                          <div class='form-group'> 
                            <input type='submit' class='btn btn-primary btn-save'  value='EDITAR' />
                            <input type='hidden' name='opcion' value='EditarSucursal' />
                            <input type='hidden' name='id' value='$sucursal[id]' />
                          </div>
                      </form>";
        echo "<div class='content'>$form</div>";       
      break; 
     
      case "EditarFactura":
        
        $sql="SELECT * FROM resolucion WHERE id = $id";
        $data = $connection->query($sql); 
        $resolucion = $data->fetch(PDO::FETCH_BOTH );  
        

        $form = "<form action='set.php' method='post' data-paginate='false' data-response='modal-body' autocomplete='off' style='width:500px' >
                     
                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Desde</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control validar requerido numero'  data-min='1' name='desde' value='$resolucion[inicio]' >
                            </div>
                          </div> 

                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Hasta</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control validar requerido numero'  data-min='1' name='hasta' value='$resolucion[fin]' >
                            </div>
                          </div> 

                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Hasta</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control'  data-min='1' name='prefijo' value='$resolucion[prefijo]' >
                            </div>
                          </div> 
                           
                          <div class='form-group'> 
                            <input type='submit' class='btn btn-primary btn-save'  value='EDITAR' />
                            <input type='hidden' name='opcion' value='EditarResolucion' />
                            <input type='hidden' name='id' value='$resolucion[id]' />
                          </div>
                      </form>";
        echo "<div class='content'>$form</div>";       
      break; 

      case "Caja":
          $fecha = date('Y-m-d');
          $query = "SELECT * FROM caja WHERE fecha = $fecha ";
          $data = $connection->query($query);
          $results = $data->fetch(PDO::FETCH_BOTH );

           
          $saldo = '0';
          if( count($results) > 0 )
              $saldo = $results['valor'];

          $saldo = number_format( $saldo,0,"","."); 

          $form = "<div class='form-group'>
                      <div class='form-header'>
                         <label class='text-center'>SALDO EN CAJA</label>
                      </div>
                      <div class='form-body text-center' style='font-size:30px; paddin-top:6px;'>                        
                        $ $saldo
                      </div>
                    </div>  
                     
                    <div class='form-group text-center'> 
                      <input type='submit' class='btn btn-primary btn-save sacar_dinero'  value='SACAR DINERO' />
                      <input type='submit' class='btn btn-primary btn-save ingresar_dinero'  value='INGRESAR DINERO' />
                    </div> ";
          echo "<div class='content'>$form</div>";   
      break;

      case "SacarDinero":
        $fecha = date('Y-m-d');
        $query = "SELECT * FROM caja WHERE fecha = '$fecha' ";
        $data = $connection->query($query);
        $results = $data->fetch(PDO::FETCH_BOTH );

        $saldo = '0';
          if( count($results) > 0 )
              $saldo = $results['valor'];
        
        if( $saldo >  0 )
        {

            $form = "<form action='set.php' method='post' data-paginate='false' data-response='modal-body' autocomplete='off' style='width:500px' >
                         
                              <div class='form-group'>
                                <div class='form-header'>
                                   <label style='padding-left:10px;'>Valor a sacar</label>
                                </div>
                                <div class='form-body'>                        
                                   <input type='text' class='form-control validar requerido numero'  data-min='1' name='sacar' value='$saldo' data-max='$saldo'>
                                </div>
                              </div>  
                               
                              <div class='form-group'> 
                                <input type='submit' class='btn btn-primary btn-save'  value='EDITAR' />
                                <input type='hidden' name='opcion' value='SacarDineroCaja' /> 
                              </div>
                          </form>";
            echo "<div class='content'>$form</div>"; 
        }
        else
        {
            echo $msg->warning("No se puede sacar dinero de la caja");
        }
      break; 

      case "IngresarDinero":
        $fecha = date('Y-m-d');
        $query = "SELECT * FROM caja WHERE fecha = '$fecha' ";
        $data = $connection->query($query);
        $results = $data->fetch(PDO::FETCH_BOTH );

        $saldo = '0';
          if( count($results) > 0 )
              $saldo = $results['valor'];
        
        if( $saldo >  0 )
        { 
            $form = "<form action='set.php' method='post' data-paginate='false' data-response='modal-body' autocomplete='off' style='width:500px' >                                
                        <div class='titulo'>Saldo actual : $saldo</div>
                        <div class='form-group'>
                          <div class='form-header'>
                             <label style='padding-left:10px;'>Valor a ingresar</label>
                          </div>
                          <div class='form-body'>                        
                             <input type='text' class='form-control validar requerido numero'  data-min='1' name='ingresar' >
                          </div>
                        </div>                                   
                        <div class='form-group'> 
                          <input type='submit' class='btn btn-primary btn-save'  value='INGRESAR' />
                          <input type='hidden' name='opcion' value='IngresarDineroCaja' /> 
                        </div>
                    </form>";
            echo "<div class='content'>$form</div>"; 
        }
        else
        {
            echo $msg->warning("No se puede sacar dinero de la caja");
        }
      break;

      case "CrearFactura": 
        $form = "<form action='set.php' method='post' data-paginate='false' data-response='content' data-type='json' autocomplete='off' style='width:500px' >
                         <div class='titulo'>REGISTRAR NUMEROS DE FACTURAS</div>
                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Desde</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control validar requerido numero'  data-min='1' name='desde'  >
                            </div>
                          </div> 

                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Hasta</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control validar requerido numero'  data-min='1' name='hasta' >
                            </div>
                          </div> 

                          <div class='form-group'>
                            <div class='form-header'>
                               <label>Resolución</label>
                            </div>
                            <div class='form-body'>                        
                               <input type='text' class='form-control'  name='prefijo' >
                            </div>
                          </div> 
                           
                          <div class='form-group'> 
                            <input type='submit' class='btn btn-primary btn-save'  value='CREAR' />
                            <input type='hidden' name='opcion' value='CrearResolucion' /> 
                          </div>
                      </form>";
        echo "<div class='content'>$form</div>";       
      break; 

      

      case "EditarProveedor":
        $query = "SELECT * FROM proveedor WHERE id = $idproveedor";
        $data = $connection->query($query);  
        $proveedor = $data->fetch(PDO::FETCH_BOTH ); 

        $form = "<form action='set.php' method='post' class='form ui' data-type='json' data-response='content-modal' data-paginate='false' autocomplete='off' >
                          <div class='two fields'>
                              <div class='field'>
                                   <label>Nombre del proveedor</label>
                                   <input type='text' class='form-control validar requerido texto'  name='nombre' value='$proveedor[nombres]' />
                              </div>

                              <div class='field'>
                                   <label>Número de documento</label>
                                   <input type='text' name='documento' class='form-control validar requerido texto' value='$proveedor[documento]' />   
                              </div>
                          </div>

                          <div class='two fields'> 
                              <div class='field'>
                                   <label>Teléfono</label> 
                                   <input type='text' name='telefono' class='form-control validar requerido texto' value='$proveedor[telefono]' />                                   
                              </div> 

                              <div class='field'>
                                   <label>Dirección</label> 
                                   <input type='text' name='direccion' class='form-control validar requerido texto' value='$proveedor[telefono]' />   
                              </div> 
                          </div> 

                          <div class='field'> 
                            <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>GUARDAR</div>
                              <div class='hidden content'>
                                <i class='right send outline icon'></i>
                              </div>
                            </div>
                            <input type='hidden' name='id' value='$proveedor[id]' />
                            <input type='hidden' name='opcion' value='EditarProveedor' />
                          </div>
                      </form>";
        echo $form;       
      break; 


      case "Proveedores": 
          $tabla = "<div class='ui right aligned segment abrir-modal link' data-url='get.php' data-title='Crear proveedor' data-data='opcion=CrearProveedor' data-response='content-modal'>
                        <div class='ui animated button '>
                          <div class='visible content' >Crear proveedor</div>
                          <div class='hidden content'>
                            <i class='right search icon'></i>
                          </div>
                        </div>
                    </div>";    

          $tabla .= "<table class='table ui inline table-proveedores'>
                        <thead>
                          <tr>
                             <th>EDITAR</th>
                             <th>ACTIVAR/INACTIVAR</th> 
                             <th>NOMBRE</th>
                             <th>DOCUMENTO</th> 
                             <th>TELEFONO</th>
                             <th>DIRECCION</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                           <tr>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td> 
                          </tr>
                        </tfoot>
                     </table>";

          $tabla.= "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div>
                    </div>";  

          echo $tabla;              
      break;  

      case "paginar_proveedores": 
           $connection->query("SET NAMES utf8"); 
           
           $query = "SELECT id , 
                            concat(nombres , ' ' , apellidos ) nombre , 
                            documento , 
                            telefono , 
                            direccion , 
                            status
                     FROM  proveedor 
                     WHERE punto_id = $idpunto
                     ORDER BY nombre ASC";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH);
           $recordsTotal = count($results);
                 
           $query .= " LIMIT $start , $length"; 
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH); 
          


           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  
           ); 
           echo json_encode($json_data);    
      break;

      case "CrearProveedor":
        $form = "<div class='ui form'>
                     <form action='set.php' method='post' data-response='content-modal' data-paginate='false' autocomplete='off'>
                          <div class='two fields'>
                              <div class='field'>
                                   <label>Nombre del proveedor</label>
                                   <input type='text' class='form-control validar requerido texto'  name='nombre' >
                              </div>

                              <div class='field'>
                                   <label>Número de documento o nit</label>
                                   <input type='text' name='documento' class='form-control validar requerido texto' />   
                              </div>
                          </div>

                          <div class='two fields'>
                            <div class='field'>
                                 <label>Teléfono</label>
                                 <input type='text' name='telefono' class='form-control validar requerido texto' />   
                            </div> 
                            <div class='field'>
                                 <label>Dirección</label>
                                 <input type='text' name='direccion' class='form-control validar requerido texto' />   
                            </div> 
                          </div>  

                          <div class='field'> 
                                <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                  <div class='visible content'>GUARDAR</div>
                                  <div class='hidden content'>
                                    <i class='right send outline icon'></i>
                                  </div>
                                </div>
                            <input type='hidden' name='opcion' value='RegistrarProveedor' />
                          </div>
                      </form>
                  </div>";
            echo $form;       
      break;   

      case "Trabajadores": 
          $tabla = "<div class='ui right aligned segment abrir-modal link' data-url='get.php' data-title='Crear trabajador' data-data='opcion=CrearTrabajador' data-response='content-modal'>
                        <div class='ui animated button '>
                          <div class='visible content' >Crear trabajador</div>
                          <div class='hidden content'>
                            <i class='right search icon'></i>
                          </div>
                        </div>
                    </div>";
          $tabla.= "<table class='table ui inline table-trabajadores'>
                        <thead>
                          <tr>
                             <th>EDITAR</th>
                             <th>ACTIVAR/INACTIVAR</th> 
                             <th>CODIGO</th>
                             <th>NOMBRES</th>
                             <th>APELLIDOS</th>
                             <th>DOCUMENTO</th>
                             <th>TELEFONO</th>
                             
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                           <tr>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td> 
                          </tr>
                        </tfoot>
                     </table>";

          $tabla.= "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div>
                </div>";  

          echo $tabla;              
      break;  

      case "paginar_trabajadores": 
           $connection->query("SET NAMES utf8"); 
           
           $query = "SELECT * FROM  usuario";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH);
           $recordsTotal = count($results);
                 
           $query .= " LIMIT $start , $length"; 
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH); 

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  
           ); 
           echo json_encode($json_data);    
      break;

      case "CrearTrabajador":
        $form = "<form action='set.php' method='post' class='form ui' data-response='content-modal' data-paginate='false' autocomplete='off' >
                          
                      <div class='two fields'>
                          <div class='field'>
                             <label>Codigo</label>
                             <input type='text' class='form-control validar requerido texto'  name='codigo' >
                          </div>
               
                         <div class='field'>
                             <label>Nombres</label>
                             <input type='text' class='form-control validar requerido texto'  name='nombres' >
                         </div>
                      </div>
                      
                      <div class='two fields'>
                          <div class='field'>
                               <label>Apellidos</label>
                               <input type='text' name='apellidos' class='form-control validar requerido texto' />   
                          </div>
                            
                          <div class='field'>
                               <label>Documento</label>
                               <input type='text' name='documento' class='form-control validar requerido texto' />   
                          </div>
                      </div>
                      
                      <div class='two fields'>        
                          <div class='field'>
                               <label>Teléfono</label>
                               <input type='text' name='telefono' class='form-control validar requerido texto' />   
                          </div>
                     
                          <div class='field'>
                               <label>Nombre de usuario</label>
                               <input type='text' class='form-control validar requerido texto'  name='nombre_usuario' >
                          </div>
                      </div>

                      <div class='two fields'>
                          <div class='field'>
                               <label>Contraseña</label>
                               <input type='text' name='clave' class='form-control validar requerido texto' />   
                          </div> 
                           
                          <div class='field'> 
                            <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>GUARDAR</div>
                              <div class='hidden content'>
                                <i class='right send outline icon'></i>
                              </div>
                            </div>
                            <input type='hidden' name='opcion' value='RegistrarTrabajador' />
                          </div> 
                      </div>                            
                </form>";
            echo $form;       
      break; 

      case "EditarTrabajador":

        $query = "SELECT * FROM usuario WHERE id = $idtrabajador ";
        $data = $connection->query($query);  
        $trabajador = $data->fetch(PDO::FETCH_BOTH ); 

   
        $form = "<div class='sixteen'>
                  <form action='set.php' method='post' data-type='json' data-response='content-modal' data-paginate='false' autocomplete='off'>
                    <div class='ui form'>

                        <div class='two fields'>  
                            <div class='field'>
                                 <label>Codigo</label>
                                 <input type='text' class='form-control validar requerido texto'  name='codigo' value='$trabajador[codigo]' >
                            </div>  

                            <div class='field'>
                                 <label>Nombres</label>
                                 <input type='text' class='form-control validar requerido texto'  name='nombres' value='$trabajador[nombre]' >
                            </div>
                        </div> 

                        <div class='two fields'>          
                            <div class='field'>
                                 <label>Apellidos</label>
                                 <input type='text' name='apellidos' class='form-control validar requerido texto' value='$trabajador[apellido]' />   
                            </div>
                                    
                            <div class='field'>
                                 <label>Documento</label>
                                 <input type='text' name='cedula' class='form-control validar requerido texto' value='$trabajador[cedula]' />   
                            </div>
                        </div>
                        
                        <div class='two fields'>       
                          <div class='field'>
                               <label>Teléfono</label>
                               <input type='text' name='telefono' class='form-control validar requerido texto' value='$trabajador[telefono]' />   
                          </div>
                                
                          <div class='field'>
                               <label>Nombre de usuario</label>
                               <input type='text' class='form-control validar texto'  name='nombre_usuario' >
                          </div>
                        </div>
                        
                        <div class='two fields'>          
                          <div class='field'>
                               <label>Contraseña</label>
                               <input type='text' name='clave' class='form-control validar texto' />   
                          </div>
                              
                          <div class='field'> 
                                <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                  <div class='visible content'>GUARDAR</div>
                                  <div class='hidden content'>
                                    <i class='right send outline icon'></i>
                                  </div>
                                </div>
                            <input type='hidden' name='opcion' value='EditarTrabajador' />
                            <input type='hidden' name='id' value='$trabajador[id]' />
                          </div>
                        </div> 

                    </div>  
                  </form>
                </div>";
                    
            echo $form;       
      break;  

      case "Inventario":
           $condicion = "";
           if( !empty($producto) )
           {  
               $condicion = " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";
           } 


           $tabla = "<div class='ui right aligned segment abrir-modal link' data-url='get.php' data-title='Crear producto' data-data='opcion=CrearProducto' data-response='content-modal'>
                        <div class='ui animated button '>
                          <div class='visible content' >Crear producto</div>
                          <div class='hidden content'>
                            <i class='right search icon'></i>
                          </div>
                        </div>
                    </div>
                    <br>";
                 

           $tabla .= "<table class='table ui inline table-inventario'>
                        <thead>
                          <tr>
                             <th></th>
                             <th></th>
                             <th>PRODUCTO</th>
                             <th>CANTIDAD</th>
                             <th>STOCK</th>
                             <th>COSTO</th>
                             <th>PRECIO</th>
                             <th>TOTAL COSTO</th>
                             <th>TOTAL PRECIO</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                           <tr>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                          </tr>
                        </tfoot>
                     </table>";

           echo $tabla;               
      break;  

      case "paginar_inventario": 
           $connection->query("SET NAMES utf8");

           $condicion = "";
           if( !empty($producto) )
           {  
               $condicion .= " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";
           } 

           if( !empty($_REQUEST['search']['value']) ) 
           { 
              $producto = $_REQUEST['search']['value'];
              $condicion .= " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";                 
           } 
           
           $order = "";
           if( !empty($_REQUEST['search']['value']) ) 
           { 
              $producto = $_REQUEST['search']['value'];
              $condicion .= " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";                 
           } 

           if( !empty($producto) ) 
           { 
              $condicion .= " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";                 
           } 
           
           $query = "SELECT 
                           producto.id ,
                           producto.nombre ,
                           producto_punto.producto_id  ,
                           producto_punto.cantidad ,
                           producto_punto.precio ,
                           producto_punto.costo , 
                           producto_punto.costo * producto_punto.cantidad total_costo , 
                           producto_punto.precio * producto_punto.cantidad total_precio , 
                           producto.stock 
                       FROM  producto_punto 
                       INNER JOIN producto ON ( producto.id = producto_id )
                       WHERE producto_punto.estado = 1 AND punto_id = $idpunto  $condicion
                       ORDER BY producto.nombre ASC";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );
           $recordsTotal = count($results);

           $query .= " LIMIT $start, $length";                  
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );

           foreach ($results as $key => $value) 
           {
              $results[$key]['precio'] = $func->format($results[$key]['precio']);
              $results[$key]['costo'] = $func->format($results[$key]['costo']);
              $results[$key]['total_costo'] = $func->format($results[$key]['total_costo']);
              $results[$key]['total_precio'] = $func->format($results[$key]['total_precio']);
           }

          
           $query = "SELECT 
                           SUM(producto_punto.costo * producto_punto.cantidad) total_costo , 
                           SUM(producto_punto.precio * producto_punto.cantidad) total_precio   
                       FROM producto_punto 
                       INNER JOIN producto ON ( producto.id = producto_id )
                       WHERE producto_punto.estado = 1 AND punto_id = $idpunto  $condicion";
           $data = $connection->query($query);  
           $resultsTotal = $data->fetch(PDO::FETCH_BOTH );
           

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  , 
              "total_costo"     => $resultsTotal['total_costo'] ,
              "total_precio"     => $resultsTotal['total_precio'] 
           ); 
           echo json_encode($json_data);    
      break;  
      
      case "PorCobrar":
           
        $query = "SELECT id ,  TRIM(UPPER(concat(nombre,' ',apellido))) usuario FROM usuario WHERE status = true";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $params = array
                 (
                     "default" => "" ,  
                     "name" => "idusuarios[]" , 
                     "id" => "" , 
                     "class" => "dropdown" ,
                     "value" => "", 
                     "multiple" => true, 
                     "results" => $results
                  );  

        $usuario = $func->selectOption($params); 

        $estado = "<select name='estado' class='dropdown'>
                     <option value='2'>Pendiente</option>
                     <option value='3'>Pagadas</option>
                   </select>";
        echo "<div class='titulo'>CUENTAS POR COBRAR</div>";
        
        $data ="<form autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_cxc' >
                  <div class='ui form'>
                      <div class='five fields'> 

                         <div class='field'>
                           <label>Fecha inicial</label>
                           <input type='text' class='form-control validar texto date'  name='inicio' >
                         </div>

                         <div class='field'>
                           <label>Fecha final</label>
                           <input type='text' class='form-control validar texto date'  name='fin' >
                         </div> 

                         <div class='field'>
                            <label>Trabajador</label>
                            $usuario
                         </div>

                         <div class='field'>
                            <label>Estado</label>
                            $estado
                         </div>

                         <div class='field'>
                            <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>Consultar</div>
                              <div class='hidden content'>
                                <i class='right search icon'></i>
                              </div>
                            </div>
                         </div> 
                      </div> 
                  </div> 

                  <input type='hidden' name='opcion' value='$opcion'>
                </form>";

         echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
               <div class='sixteen wide column'>
                 <table class='ui table table-cxc'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>FECHA</th> 
                           <th>CLIENTE</th> 
                           <th>PRODUCTOS</th>
                           <th>TOTAL</th>
                           <th>ABONO</th>
                           <th>DEBE</th>
                           <th>DETALLLE</th> 
                           <th>ABONAR</th>  
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <td>TOTAL</td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                          </tr>
                      </tfoot>
                  </table>
               </div>";   

         echo "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div> 
               </div>"; 
      break;

      case "DetallePorCobrar":
        $query = "SELECT  sum(precio * cantidad ) total 
                  FROM venta  
                  INNER JOIN producto_venta ON venta_id = venta.id
                  WHERE venta.id = '$id' ";
        $data = $connection->query($query);  
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $row = $results[0]; 
        $total = $row['total'];
        

        $abono = 0;
        $query = "SELECT  sum(abono) abono 
                  FROM abono_venta  
                  WHERE venta_id = $id";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        if( count( $results) > 0 ) 
        {
          $row = $results[0];
          if( $row['abono'] == null )
            $abono = 0;
          else
            $abono = $row['abono'];
        }

        $debe = $total - $abono;
        $auxdebe = $debe;
        $debe = number_format($debe,0,"",".");



          $query = "SELECT producto_venta.cantidad , producto_venta.precio , venta.fecha ,  IFNULL( concat(cliente.nombres,' ',cliente.apellidos) , '' ) cliente , producto.nombre producto
                    FROM venta 
                    INNER JOIN producto_venta ON venta_id = venta.id
                    LEFT JOIN cliente ON cliente_id = cliente.id
                    LEFT JOIN producto ON producto_id = producto.id
                    WHERE venta.id = $id";
          $data = $connection->query($query);  
          $results = $data->fetchAll(PDO::FETCH_BOTH ); 



          if( count( $results) > 0 ) 
          {
                $tabla = "<table class='ui single line celled table' id='datatable'>
                             <thead>
                                <tr>
                                   <th>Fecha</th>    
                                   <th>Concepto</th>    
                                   <th>Cliente</th>         
                                   <th>Producto</th>
                                   <th>Cantidad</th>
                                   <th>Precio</th>                           
                                   <th>Total</th>                           
                                </tr>
                             </thead>
                        </tbody>";
              
                 $total = 0; 
                 
                 foreach( $results as $fila )
                 { 
                         
                         $suma  = $fila['precio'] * $fila['cantidad'];
                         $total += $suma;

                         $fila['precio'] = number_format( $fila['precio'],0,"",".");
                         $suma = number_format( $suma,0,"",".");

                         $fila['producto'] = utf8_encode($fila['producto']);
                         $fila['cliente'] = utf8_encode($fila['cliente']); 
                           
                         $tabla.= "<tr>                                
                                      <td>$fila[fecha]</td>
                                      <td>Venta</td>
                                      <td>$fila[cliente]</td>
                                      <td>$fila[producto]</td>
                                      <td>$fila[cantidad]</td>
                                      <td>$ $fila[precio]</td> 
                                      <td>$ $suma</td> 
                                      
                                   </tr>"; 
                 }
                  
                 $total  = number_format($total,0,"",".");

                 $query = "SELECT  * 
                           FROM abono_venta 
                           WHERE id = '$id' ";
                $data = $connection->query($query);
                $results = $data->fetchAll(PDO::FETCH_BOTH ); 
                $abonos = "";
                if( count( $results) > 0 ) 
                {
                     foreach ($results as $row ) 
                     {
                          $row['abono'] = number_format($row['abono'],0,"",".");
                          $abonos .= "<tr>
                                         <td>$row[fecha]</td>
                                         <td>Abono</td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td class='total'>$ $row[abono]</td>
                                     </tr>";
                     }
                }
                  
                 $tabla.= "</tbody>
                              <tfoot> 
                                  <tr>
                                       <td></td>
                                       <td>TOTAL</td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td class='total'><b>$ $total</b></td>
                                  </tr>
                                  $abonos
                                  <tr>
                                       <td></td>
                                       <td>SALDO</td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td class='total'><b>$ $debe</b></td>
                                  </tr> 
                              </tfoot>";
                  $tabla.= "</table>";
                  echo "<div class='row col-md-12'>$tabla</div>";

          } 
          else
          {
               $msg->danger("Error no se  encontraron producotos");
          }
      break;

      case "DetalleVenta":  
        
          $query = "SELECT 
                      producto_venta.cantidad ,
                      producto_venta.precio ,
                      producto.nombre producto
                    FROM venta 
                    INNER JOIN producto_venta ON venta_id = venta.id
                    LEFT JOIN cliente ON cliente_id = cliente.id
                    LEFT JOIN producto ON producto_id = producto.id
                    WHERE venta.id = $id";
          $data = $connection->query($query);  
          $results = $data->fetchAll(PDO::FETCH_BOTH ); 



          if( count( $results) > 0 ) 
          {
                $tabla = "<table class='ui table' id='datatable'>
                             <thead>
                                <tr>
                                   <th>Producto</th>
                                   <th>Cantidad</th>
                                   <th>Precio</th>                           
                                   <th>Total</th>                           
                                </tr>
                             </thead>
                        </tbody>"; 
                 $total = 0; 
                 
                 foreach( $results as $fila )
                 { 
                         
                         $suma  = $fila['precio'] * $fila['cantidad'];
                         $total += $suma;

                         $fila['precio'] = number_format( $fila['precio'],0,"",".");
                         $suma = number_format( $suma,0,"",".");

                         $fila['producto'] = utf8_encode($fila['producto']);
                           
                         $tabla.= "<tr>                                
                                      <td>$fila[producto]</td>
                                      <td>$fila[cantidad]</td>
                                      <td>$ $fila[precio]</td> 
                                      <td>$ $suma</td> 
                                      
                                   </tr>"; 
                 }
                  
                 $total  = number_format($total,0,"",".");

              
                  
                 $tabla.= "</tbody>
                              <tfoot> 
                                  <tr>
                                       <th>TOTAL</th>
                                       <th></th>
                                       <th></th>
                                       <th class='total'><b>$ $total</b></th>
                                  </tr>
                                  
                              </tfoot>";
                  $tabla.= "</table>";
                  echo $tabla;

          } 
          else
          {
               $msg->danger("Error no se  encontraron producotos");
          }
      break;

      case "AbonarPorCobrar":
        $query = "SELECT  sum(precio * cantidad ) total 
                  FROM venta  
                  INNER JOIN producto_venta ON venta_id = venta.id
                  WHERE venta.id = $id ";
        $data = $connection->query($query);  
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $row = $results[0]; 
        $total = $row['total'];
        

        $abono = 0;
        $query = "SELECT  sum(abono) abono 
                  FROM abono_venta  
                  WHERE venta_id = $id ";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        if( count( $results) > 0 ) 
        {
          $row = $results[0];
          $abono = $row['abono'];
        }

        $debe = $total - $abono;
        $auxdebe = $debe;
        $debe = number_format($debe,0,"",".");

        $data = "
                  <form autocomplete='off' data-type='json' data-paginate='false' action='set.php' method='post'  enctype='multipart/form-data' data-response='content-modal' id='form' >
                    <div class='ui  aligned grid'> 
                      <div class='center aligned two column row'>
                        <div class='column'>
                          <div class='ui segment'>
                              <div class='ui form'> 

                                  <div class='field'>
                                    <label>Saldo</label>
                                    <div class='ui huge header'>$ $debe</div>
                                  </div> 

                                  <div class='field'>
                                    <label>Valor del abono</label>
                                    <input type='text' name='valor' class='centered validar numero requerido ' data-min='1' data-max='$auxdebe' >
                                  </div>  

                                  <div class='field'>
                                      <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                        <div class='visible content'>GUARDAR</div>
                                        <div class='hidden content'>
                                          <i class='right send outline icon'></i>
                                        </div>
                                      </div>
                                  </div>   
                              </div>
                          </div>
                        </div> 
                      </div>  
                    </div>
                    <input type='hidden' name='opcion' value='$opcion'> 
                    <input type='hidden' name='id' value='$id'>
                </form>";
         echo $data; 
      break;

      break;

      case 'BuscarProductoVenta': 
         $implode = "";
         if( !empty($_POST['listProductos'])) 
         {                   
              $listProductos = $_POST['listProductos']; 

              if( !empty($listProductos) )
              {
                  $listProductos = implode(",", $listProductos );
                  $implode = " AND producto.id NOT IN ( $listProductos ) ";
              }
         }  

         $condicionInner = "";
         $selectPorcentaje = "";
         if( !empty($producto) )
            $condicion = " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";



         $query = "SELECT
                      producto.id , 
                      producto.nombre , 
                      producto_punto.costo, 
                      producto_punto.costo_liquidado, 
                      producto_punto.precio ,
                      producto_punto.cantidad                 
                   FROM producto 
                   INNER JOIN producto_punto ON producto.id = producto_punto.producto_id
                   WHERE 
                      producto_punto.estado = 1 AND 
                      producto_punto.cantidad >= 0 $implode $condicion
                   LIMIT 100";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_BOTH); 


         if( count( $results) > 0 ) 
         {
              $tabla = "<table class='ui single line table' id='datatable'>
                           <thead>
                              <tr>
                                 <th>Nombre</th>
                                 <th>Cantidad</th> 
                                 <th>Precio</th>   
                                 <th></th> 
                              </tr>
                           </thead>
                      </tbody>";
            
               $total_pre_venta = 0;
               $total_val_ivent = 0;
               $total_val_venta = 0;
               
               foreach( $results as $fila )
               {
                   $porcentajeValor = 0;
                   if( !empty($fila['porcentaje']) )
                   {
                      $porcentajeValor = $fila['precio'] * $fila['porcentaje'] / 100; 
                      $fila['precio'] = ( $fila['precio'] - $porcentajeValor );

                   }

                   $fila['nombre'] = utf8_encode($fila['nombre']);
                   $tabla.= "<tr>                                
                                <td>$fila[nombre]</td>
                                <td>$fila[cantidad]</td> 
                                <td>$ $fila[precio]</td>
                                <td> 
                                  <i style='font-size:24px;' class='icon arrow right btn-add-producto-venta' data-idproducto='$fila[id]'  data-nombre='$fila[nombre]'  data-cantidad='$fila[cantidad]' data-precio='$fila[precio]' data-costonormal='$fila[costo]' data-costoliquidado='$fila[costo_liquidado]' data-descuento='$porcentajeValor' ></i>
                                </td>
                             </tr>"; 
               } 
                  
               $tabla.= "</tbody>
                            <tfoot> 
                                <tr>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                </tr>
                            </tfoot>";
               $tabla.= "</table>";
               echo "<div id='close_absolute'></div><div id='result_absolute'>$tabla</div>"; 
         }
         else
         {
            echo '<div class="alert alert-warning" role="alert">
                    <strong>Error !</strong> No se encontraron productos
                  </div>';
         }
      break; 

      case 'BuscarProveedor': 

         $query = "SELECT id ,concat(nombres,' ',apellidos) proveedor               
                   FROM proveedor
                   HAVING proveedor LIKE '%$escrito%' ";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_BOTH); 

         if( count( $results) > 0 ) 
         {
              $tabla = "<table class='table ui'>
                           <thead>
                              <tr>
                                 <th>Proveedor</th>
                                 <th></th> 
                              </tr>
                           </thead>
                      </tbody>";
            
               $total_pre_venta = 0;
               $total_val_ivent = 0;
               $total_val_venta = 0;
               
               foreach( $results as $fila )
               {
                   

                   $fila['proveedor'] = utf8_encode($fila['proveedor']);
                   $tabla.= "<tr>                                
                                <td>$fila[proveedor]</td>
                                <td> 
                                    <i style='font-size:24px;' class='icon arrow right seleccionar_proveedor' data-idproveedor='$fila[id]'  data-proveedor='$fila[proveedor]' ></i>
                                </td>
                             </tr>"; 
               } 
                  
               $tabla.= "</tbody>
                            <tfoot> 
                                <tr>
                                   <td></td>
                                   <td></td>
                                </tr>
                            </tfoot>";
               $tabla.= "</table>";
               echo "<div id='close_absolute'></div><div id='result_absolute'>$tabla</div>"; 
         }
         else
         {
            echo '<div class="alert alert-warning" role="alert">
                    <strong>Error !</strong> No se encontraron proveedores
                  </div>';
         }
      break; 

      case 'buscarProductoCotizacion': 
         $implode = "";
         if( !empty($_POST['listProductos'])) 
         {                   
              $listProductos = $_POST['listProductos']; 

              if( !empty($listProductos) )
              {
                  $listProductos = implode(",", $listProductos );
                  $implode = " AND producto.id NOT IN ( $listProductos ) ";
              }
         }  

         if( !empty($producto) )
            $condicion = " AND producto.nombre LIKE '%$producto%'";
      

         $query = "SELECT
                      producto.id , 
                      producto.nombre , 
                      producto_punto.costo, 
                      producto_punto.costo_liquidado, 
                      producto_punto.precio ,
                      producto_punto.precio2,
                      producto_punto.precio3 ,
                      producto_punto.cantidad
                   FROM producto 
                   INNER JOIN producto_punto ON producto.id = producto_punto.producto_id 
                   WHERE 
                      producto_punto.estado = 1 AND 
                      producto_punto.cantidad > 0 $implode $condicion
                   LIMIT 100";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 


         if( count( $results) > 0 ) 
         {
              $tabla = "<table class='ui table'>
                           <thead>
                              <tr>
                                 <th>Nombre</th>
                                 <th>Cantidad</th> 
                                 <th>Precio</th>   
                                 <th></th> 
                              </tr>
                           </thead>
                      </tbody>";
            
               $total_pre_venta = 0;
               $total_val_ivent = 0;
               $total_val_venta = 0;
               
               foreach( $results as $fila )
               {
                   if( !empty($fila['precio_usuario']) )
                      $fila['precio'] = $fila['precio_usuario'];

                   $fila['precio'] = number_format( $fila['precio'],0,"",",");

                   
                   $tabla.= "<tr>                                
                                <td>$fila[nombre]</td>
                                <td>$fila[cantidad]</td> 
                                <td>$ $fila[precio]</td>
                                <td> 
                                  <i style='font-size:24px;' class='icon arrow right btn-add-producto-cotizacion' data-idproducto='$fila[id]'  data-nombre='$fila[nombre]'  data-cantidad='$fila[cantidad]' data-precio='$fila[precio]' data-precio2='$fila[precio2]' data-precio3='$fila[precio3]' data-costonormal='$fila[costo]' data-costoliquidado='$fila[costo_liquidado]'  ></i>
                                </td>
                             </tr>"; 
               } 
                  
               $tabla.= "</tbody>
                            <tfoot> 
                                <tr>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                </tr>
                            </tfoot>";
               $tabla.= "</table>";
               echo "<div id='close_absolute'></div><div id='result_absolute'>$tabla</div>"; 
         }
         else
         {
            echo '<div class="alert alert-warning" role="alert">
                    <strong>Error !</strong> No se encontraron productos
                  </div>';
         }
      break;

      case "buscarClienteVenta":
         $query = "SELECT id,concat(nombres,' ',apellidos) cliente , tipocliente_id
                   FROM cliente 
                   WHERE status = true
                   HAVING cliente LIKE '%$escrito%' 
                   LIMIT 20";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_ASSOC);  

         $data = "";

         if( count( $results) > 0 ) 
         {
              $data .= "<table class='ui single line table' id='datatable'>
                           <thead>
                              <tr>
                                 <th>Cliente</th>   
                                 <th></th>   
                              </tr>
                           </thead>
                      </tbody>"; 
               foreach( $results as $fila )
               {
                   $data.= "<tr>                                
                                <td>$fila[cliente]</td> 
                                <td>
                                    <i style='font-size:24px;' class='icon arrow right btn-add-cliente-venta' data-id='$fila[id]'  data-cliente='$fila[cliente]' data-tipocliente='$fila[tipocliente_id]' ></i>
                                </td>
                             </tr>"; 
               }   
               $data.= "</table>";
               $tabla = "<div id='close_absolute'></div><div id='result_absolute'>$data</div>"; 

               echo $tabla;
         }
         else
         {
            $tabla = '<div class="alert alert-warning" role="alert">
                    <strong>Error !</strong> No se encontraron productos
                  </div>'; 
            echo $tabla;
         }  
      break;

      case "buscarClienteCotizacion":
         $query = "SELECT id,concat(nombres,' ',apellidos) cliente , tipocliente_id
                   FROM cliente 
                   WHERE status = true
                   HAVING cliente LIKE '%$escrito%' 
                   LIMIT 20";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_ASSOC);  

         $data = "";

         if( count( $results) > 0 ) 
         {
              $data .= "<table class='table table-bordered table-striped table-condensed' id='datatable'>
                           <thead>
                              <tr>
                                 <th>Cliente</th>   
                                 <th></th>   
                              </tr>
                           </thead>
                      </tbody>"; 
               foreach( $results as $fila )
               {
                   $data.= "<tr>                                
                                <td>$fila[cliente]</td> 
                                <td>
                                    <div class='arrow-pointing-to-right btn-add-cliente-venta' data-id='$fila[id]'  data-cliente='$fila[cliente]' data-tipocliente='$fila[tipocliente_id]' ></div>
                                </td>
                             </tr>"; 
               }   
               $data.= "</table>";
               $tabla = "<div id='close_absolute'></div><div id='result_absolute'>$data</div>"; 

               echo json_encode( array('data' => $tabla , 'status' => true) );
         }
         else
         {
            $tabla = '<div class="alert alert-warning" role="alert">
                    <strong>Error !</strong> No se encontraron productos
                  </div>';
            echo json_encode( array('data' => $tabla , 'status' => false) );
         } 
      break;

      case "buscarProveedorDevolucion":
         $query = "SELECT id,concat(nombres,' ',apellidos) proveedor
                   FROM proveedor 
                   WHERE status = true
                   HAVING proveedor LIKE '%$escrito%' 
                   LIMIT 20";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_ASSOC);  

         $data = "";

         if( count( $results) > 0 ) 
         {
              $data .= "<table class='ui table' id='datatable'>
                           <thead>
                              <tr>
                                 <th>Proveedor</th>   
                                 <th></th>   
                              </tr>
                           </thead>
                      </tbody>"; 
               foreach( $results as $fila )
               {
                   $data.= "<tr>                                
                                <td>$fila[proveedor]</td> 
                                <td>
                                    <i style='font-size:24px;' class='icon arrow right btn-add-proveedor-devolucion' data-id='$fila[id]'  data-proveedor='$fila[proveedor]'  ></i>
                                </td>
                             </tr>"; 
               }   
               $data.= "</table>";
               $tabla = "<div id='close_absolute'></div><div id='result_absolute'>$data</div>"; 

               echo json_encode( array('data' => $tabla , 'status' => true) );
         }
         else
         {
            $tabla = '<div class="alert alert-warning" role="alert">
                    <strong>Error !</strong> No se encontraron productos
                  </div>';
            echo json_encode( array('data' => $tabla , 'status' => false) );
         } 
      break;

      case 'BuscarProductoCompra':
         $implode = "";
         if( !empty($_POST['listProductos'])) 
         {                   
              $listProductos = $_POST['listProductos']; 

              if( !empty($listProductos) )
              {
                  $listProductos = implode(",", $listProductos );
                  $implode = " AND producto.id NOT IN ( $listProductos ) ";
              }
         }  
         if( !empty($producto) )
             $condicion = " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";


         $query = "SELECT 
                      producto.id ,
                      nombre , 
                      precio ,
                      IFNULL (precio , 0 )  precio, 
                      IFNULL (precio_minimo , 0 ) precio_minimo,
                      IFNULL (costo , 0 )  costo, 
                      IFNULL (costo_liquidado , 0 ) costo_liquidado 
                   FROM producto 
                   LEFT JOIN producto_punto ON ( producto.id =  producto_id  )
                   WHERE producto.estado = 1 AND producto_punto.estado = 1 $implode $condicion LIMIT 6";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_BOTH); 


         if( count( $results) > 0 ) 
         {
              $tabla = "<table class='ui single line table' >
                           <thead>
                              <tr>
                                 <th>Nombre</th> 
                                 <th>Seleccionar</th> 
                              </tr>
                           </thead>
                      </tbody>";
            
               $total_pre_venta = 0;
               $total_val_ivent = 0;
               $total_val_venta = 0;
               
               foreach( $results as $fila )
               {
                   $fila['nombre'] = utf8_encode($fila['nombre']);
                   $tabla.= "<tr>                                
                                <td>$fila[nombre]</td> 
                                <td>
                                    <i style='font-size:24px;' class='icon arrow right btn-add-producto-compra' data-idproducto='$fila[id]'  data-nombre='$fila[nombre]' data-costonormal='$fila[costo]' data-costoliquidado='$fila[costo_liquidado]'  data-precio='$fila[precio]' data-preciominimo='$fila[precio_minimo]' ></i>
                                </td>
                             </tr>"; 

               }                     
               $tabla.= "</tbody>
                            <tfoot> 
                                <tr>
                                   <td></td>
                                   <td></td> 
                                </tr>
                            </tfoot>";
               $tabla.= "</table>";

               echo "<div id='close_absolute'></div><div id='result_absolute'>$tabla</div>"; 
         }
         else
         {
            echo '<div class="alert alert-warning" role="alert">
                    <strong>Error !</strong> No se encontraron productos
                  </div>';
         }
      break;

      case "PorPagar": 
        $query = "SELECT id , TRIM(UPPER(concat(nombres,' ',apellidos))) proveedor FROM proveedor WHERE status = true ORDER BY concat(nombres,' ',apellidos) ASC ";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $params = array
                 (
                     "default" => "Seleccione" ,  
                     "name" => "idproveedor[]" , 
                     "id" => "" , 
                     "class" => "chosen" ,
                     "value" => "", 
                     "multiple" => true, 
                     "results" => $results
                  );  

        $proveedor = $func->selectOption($params); 

        $estado = "<select name='estado' class='dropdown'>
                     <option value='2'>Pendiente</option>
                     <option value='3'>Pagadas</option>
                   </select>";

        echo "<div class='titulo'>CUENTAS POR PAGAR</div>";
         
        $data ="<form autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_cxp' >
                  
                  <div class='ui form'>
                      <div class='four fields'>
                         <div class='field'>
                             <label>Fecha inicial</label>
                             <input type='text' class='form-control start'  name='inicio' > 
                         </div>

                         <div class='field'>
                             <label>Fecha inicial</label>
                             <input type='text' class='form-control end'  name='fin' > 
                         </div>

                         <div class='field'>
                             <label>Proveedor</label>
                             $proveedor 
                         </div>

                         <div class='field'>
                             <label>Estado</label>
                             $estado 
                         </div>  

                         <div class='field'>
                            <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>Consultar</div>
                              <div class='hidden content'>
                                <i class='right search icon'></i>
                              </div>
                            </div>
                        </div>

                      </div>  
                      <input type='hidden' name='opcion' value='$opcion'> 
                 </form>";
         echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
               <div class='sixteen wide column'>
                 <table class='ui fixed single line celled table table-cxp'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>FECHA</th> 
                           <th>PROVEEDOR</th> 
                           <th>CANTIDAD</th>
                           <th>TOTAL</th>
                           <th>ABONO</th>
                           <th>DEBE</th>
                           <th>DETALLLE</th> 
                           <th>ABONOS</th>  
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <td>TOTAL</td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                          </tr>
                      </tfoot>
                  </table>
              </div>";  

          echo "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div>
                      <!--
                      <div class='actions'>
                        
                          <div class='ui black deny button'>
                            Nope
                          </div>
                        
                          <div class='ui positive button btn-enviar'>
                            REGISTRAR
                          </div>
                       </div>
                        -->  
                </div>";   
      break;

      case "paginar_cxp":
           $connection->query("SET NAMES utf8");
           
           $condicion = "";
           if( !empty($inicio) && !empty($fin) )
           {
              $condicion .= " AND date(compra.fecha) >= '$inicio' AND date(compra.fecha) <= '$fin' ";
           }
           else if( !empty($inicio) )
           {
              $condicion .= " AND date(compra.fecha) = '$inicio' ";  
           }

           if( !empty($estado) )
           {
               $condicion .= " AND tipo_compra_id = $estado ";
               $tipo_compra = " AND tipo_compra_id = $estado ";
           }
           else
           {
              $condicion .= " AND tipo_compra_id = 2 ";
              $tipo_compra = " AND tipo_compra_id = 2 ";
           }
           if( !empty($idproveedor) )
           {
               $aux_idproveedor =" AND proveedor_id IN (";
               foreach ($idproveedor as $key => $value) 
               {
                  $aux_idproveedor .= $value.",";
               }
               $aux_idproveedor = substr ($aux_idproveedor, 0, strlen($aux_idproveedor) - 1);
               
               $aux_idproveedor.=")";

               $condicion.= $aux_idproveedor;
           } 

           $fecha = date('Y-m-d');
           if( !empty($terminar) )
              $condicion .= "AND datediff(llegada, '$fecha') <= 3";

          

           $query = "SELECT 
                         compra.id , 
                         SUM(cantidad) cantidad , 
                         numero , 
                         sum( costo * cantidad ) total ,  
                         date(compra.fecha) fecha , 
                         concat(nombres,' ',apellidos) proveedor
                      FROM compra 
                      INNER JOIN proveedor ON proveedor_id = proveedor.id
                      INNER JOIN producto_compra ON  compra_id = compra.id
                      INNER JOIN producto ON  producto_id = producto.id
                      WHERE  1 AND compra.punto_id = 1 $condicion
                      GROUP BY compra.id
                      HAVING total > 0 "; 
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_ASSOC); 

           $recordsTotal = count($results);

           $query .= " LIMIT $start , $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_ASSOC);

           foreach ($results as $key => $value) 
           {
              $query = "SELECT sum(abono) abono FROM abono_compra WHERE compra_id = $value[id]";
              $data = $connection->query($query);
              $res = $data->fetch(PDO::FETCH_ASSOC);

              $results[$key]['abono'] = $res['abono'];
              $results[$key]['debe'] =  $results[$key]['total'] - $res['abono'];

           }

           $query = "SELECT sum(costo * cantidad ) total 
                      FROM compra 
                      LEFT JOIN proveedor ON proveedor_id = proveedor.id 
                      INNER JOIN producto_compra ON compra_id = compra.id
                      WHERE compra.punto_id = $idpunto $condicion ";   

           $query = "SELECT sum(abono) total 
                      FROM abono_compra
                      INNER JOIN compra ON compra_id = compra.id
                      WHERE compra.punto_id = $idpunto $condicion "; 


            $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  
           ); 
           echo json_encode($json_data);  
      break;

      case "paginar_cxc":
           $connection->query("SET NAMES utf8");
           
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

           $query = "SELECT 
                        tipo_venta_id,
                        venta.id,
                        SUM(cantidad) cantidad, 
                        factura , 
                        recibo , 
                        SUM(precio * cantidad ) total ,  
                        venta.fecha , 
                        UPPER(CONCAT(nombres,' ',apellidos)) as cliente
                    FROM venta 
                    LEFT JOIN cliente ON cliente_id = cliente.id 
                    INNER JOIN producto_venta ON venta_id = venta.id
                    WHERE 1  AND venta.punto_id = $idpunto $condicion
                    GROUP BY venta.id  "; 
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );  

           $recordsTotal = count($results);

           $query .= " LIMIT $start , $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_ASSOC);

           foreach ($results as $key => $value) 
           {
              $query = "SELECT sum(abono) abono FROM abono_venta WHERE venta_id = $value[id]";
              $data = $connection->query($query);
              $res = $data->fetch(PDO::FETCH_ASSOC);

              $results[$key]['abono'] = $res['abono'];
              $results[$key]['debe'] =  $results[$key]['total'] - $res['abono'];

           } 


           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  
           ); 
           echo json_encode($json_data);  
      break;

      case "DetallePorPagar":
        $query = "SELECT  sum( producto_compra.costo * producto_compra.cantidad ) total 
                  FROM compra  
                  INNER JOIN producto_compra ON  compra_id = compra.id 
                  WHERE compra.id = $id ";
        $data = $connection->query($query);  
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $row = $results[0]; 
        $total = $row['total'];
        

        $abono = 0;
        $query = "SELECT  sum(abono) abono 
                  FROM abono_compra  
                  WHERE  compra_id = $id";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        if( count( $results) > 0 ) 
        {
          $row = $results[0];
          $abono = $row['abono'];
        }

        $debe = $total - $abono;
        $auxdebe = $debe;
        $debe = number_format($debe,0,"",".");

         $query = "SELECT cantidad , costo , cantidad  ,  compra.fecha ,  concat (proveedor.nombres,' ',proveedor.apellidos)  proveedor, producto.nombre producto
                    FROM compra 
                    INNER JOIN producto_compra ON  compra_id = compra.id 
                    INNER JOIN proveedor ON proveedor_id = proveedor.id
                    INNER JOIN producto ON producto_id = producto.id
                    WHERE compra.id = $id";
          $data = $connection->query($query);  
          $results = $data->fetchAll(PDO::FETCH_BOTH ); 

          if( count( $results) > 0 ) 
          {
                $tabla = "<table class='ui single line celled table'>
                             <thead>
                                <tr>
                                   <th>Fecha</th>    
                                   <th>Concepto</th>    
                                   <th>Proveedor</th>         
                                   <th>Producto</th>
                                   <th>Cantidad</th>
                                   <th>Costo</th>                           
                                   <th>Total</th>                           
                                </tr>
                             </thead>
                        </tbody>";
              
                 $total = 0; 
                 
                 foreach( $results as $fila )
                 { 
                         
                         $suma  = $fila['costo'] * $fila['cantidad'];
                         $total += $suma;

                         $fila['costo'] = number_format( $fila['costo'],0,"",".");
                         $suma = number_format( $suma,0,"",".");
                         $fila['producto'] = utf8_encode($fila['producto']);
                         $fila['proveedor'] = utf8_encode($fila['proveedor']);
                           
                         $tabla.= "<tr>                                
                                      <td>$fila[fecha]</td>
                                      <td>Compra</td>
                                      <td>$fila[proveedor]</td>
                                      <td>$fila[producto]</td>
                                      <td>$fila[cantidad]</td>
                                      <td>$ $fila[costo]</td> 
                                      <td>$ $suma</td> 
                                      
                                   </tr>"; 
                 }
                  
                 $total  = number_format($total,0,"",".");

                $query = "SELECT  * 
                           FROM abono_compra 
                           WHERE compra_id = $id";
                $data = $connection->query($query);
                $results = $data->fetchAll(PDO::FETCH_BOTH ); 
                $abonos = "";
                if( count( $results) > 0 ) 
                {
                     foreach ($results as $row ) 
                     {
                          $row['abono'] = number_format($row['abono'],0,"",".");
                          $abonos .= "<tr>
                                         <td>$row[fecha]</td>
                                         <td>Abono</td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td><i class='print icon' style='font-size:24px;' id='content_print' data-title='COMPROBANTE DE PAGO' data-url='reports/firma.php' data-data='id=$row[id]'></i></td>
                                         <td class='total'>$ $row[abono]</td>
                                     </tr>";
                     }
                }
                  
                 $tabla.= "</tbody>
                              <tfoot> 
                                  <tr>
                                       <td></td>
                                       <td>Total</td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td class='total'><b>$ $total</b></td>
                                  </tr>
                                  $abonos
                                  <tr>
                                       <td></td>
                                       <td>Saldo</td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td class='total'><b>$ $debe</b></td>
                                  </tr>
                                  <tr> 
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                  </tr>
                              </tfoot>";
                  $tabla.= "</table>";
                  echo "<div class='row col-md-12'>$tabla</div>";

          } 
      break;


      case "AbonarPorPagar": 

        $sql="SELECT MAX(id) + 1 AS id FROM abono_compra";
         $data = $connection->query($sql); 
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 
         $row = $results[0]; 
         if( $row['id'] == null )
            $idcuenta = 1;
         else
            $idcuenta = $row['id']; 




        $query = "SELECT  sum( costo * cantidad ) total 
                  FROM compra  
                  INNER JOIN producto_compra ON  compra_id = compra.id 
                  WHERE compra.id = '$id' ";
        $data = $connection->query($query);  
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $row = $results[0]; 
        $total = $row['total'];
        

        $abono = 0;
        $query = "SELECT  sum(abono) abono 
                  FROM abono_compra 
                  WHERE compra_id = $id ";
        $data = $connection->query($query);
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        if( count( $results) > 0 ) 
        {
          $row = $results[0];
          $abono = $row['abono'];
        }

        $debe = $total - $abono;
        $auxdebe = $debe; 

        $debe = number_format($debe,0,",",",");

        $data = "
                  <form autocomplete='off' data-type='json' data-paginate='false' action='set.php' method='post'  enctype='multipart/form-data' data-response='content-modal' id='form' >
                  <div class='ui  aligned grid'> 
                  <div class='center aligned two column row'>
                    <div class='column'>
                      <div class='ui segment'>
                          <div class='ui form'> 
                              <div class='field'>
                                <label>Saldo</label>
                                <div class='ui huge header'>$ $debe</div>
                              </div> 

                              <div class='field'>
                                <label>Valor del abono</label>
                                <input type='text' name='valor' class='centered validar numero requerido ' data-min='1' data-max='$auxdebe' >
                              </div>

                              <div class='field'>
                                <label>Cargar firma</label>
                                <div class='firmar' data-idcuenta='$idcuenta'></div>
                              </div> 

                              <div class='field'>
                                  <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                    <div class='visible content'>GUARDAR</div>
                                    <div class='hidden content'>
                                      <i class='right send outline icon'></i>
                                    </div>
                                  </div>
                              </div>  
                            
                          </div>
                      </div>
                    </div>
                    <div class='column'>
                      <div class='ui segment'>
                         <fieldset>
                             <legend>Firma</legend>
                               <div style='width : 340px; height : 230px; padding :10px;'>
                                <img id='respuesta-firma' />
                                <input type='hidden' name='id' value='$id' />
                                <input type='hidden' name='firmado' id='firmado' />
                                <input type='hidden' name='opcion' value='AbonarPorPagar' />
                               </div>
                           </fieldset>
                      </div>
                    </div>
                  </div> 

                  
                </div>
                </form>";
         echo $data; 
      break;  

      case "Diario":
        echo "<br /><br /><br /><br />";
         $data ="<div class='col-md-10 offset-md-1 text-center'>
                  <form autocomplete='off' action='setdata.php' method='post'  enctype='multipart/form-data' data-response='contenido' id='form' >
                   <table class='table table-bordered '>
                      <thead>
                         <tr>
                            <th colspan='4'><h1>Crear producto</h1></th>
                         </tr>
                      </thead>
                      <tr>
                         <td>Desde</td>
                         <td><input type='text' name='inicio' class='form-control  start' required ></td>
                      </tr>
                       
                          
                      <input type='hidden' name='opcion' value='$opcion'>
                                              
                      <tr>
                         <td colspan='4' class='text-right'><input type='submit' class='btn btn-primary' value='CONSULTAR'></td>                  
                      </tr>  
                   </table>
                 </form>
                </div>";
         echo $data;           
      break;

      case "Resetear":
         echo "<div class='titulo'>RESETEAR CANTIDADES</div>";
         $data ="<form class='form ui' autocomplete='off' action='set.php' method='post' data-paginate='false'  enctype='multipart/form-data' data-response='content' >
                   <div class='fields one'> 
                      <div class='field'>
                          <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                            <div class='visible content'>RESETEAR</div>
                            <div class='hidden content'>
                              <i class='right send outline icon'></i>
                            </div>
                          </div>
                      </div>  
                      <input type='hidden' name='opcion' value='Resetear'> 
                   </div>
                 </form>

                 <form class='form ui' autocomplete='off' action='set.php' method='post' data-paginate='false'  enctype='multipart/form-data' data-response='content' >
                   <div class='fields one'> 
                      <div class='field'>
                          <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                            <div class='visible content'>RESTAURAR</div>
                            <div class='hidden content'>
                              <i class='right send outline icon'></i>
                            </div>
                          </div>
                      </div>  
                      <input type='hidden' name='opcion' value='Restaurar'> 
                   </div>
                 </form>";
          echo $data;         
      break; 

      case "ViewNotifications":
          $suma = 0; 
          $query = "SELECT COUNT(*) count
                    FROM venta  
                    WHERE tipo_venta_id = 2 AND punto_id =  $idpunto";
          $data = $connection->query($query);  
          $result = $data->fetch(PDO::FETCH_BOTH ); 
          $suma+=$result['count'];


          $query = "SELECT count(*) count
                    FROM compra
                    WHERE  tipo_compra_id = 2 AND punto_id = $idpunto"; 
          $data = $connection->query($query);  
          $result = $data->fetch(PDO::FETCH_BOTH ); 
          $suma+=$result['count'];

          echo count( $suma );
      break;

      case "GetAllNotification":
          $array = array(); 


          $query = "SELECT COUNT(*) count FROM cotizacion WHERE status = true AND punto_id = $idpunto";
          $data = $connection->query($query);  
          $result = $data->fetch(PDO::FETCH_BOTH ); 
          
          $form = "";
          if( $result['count'] > 0 )
          {
               $form.="<div class='notification_info link' data-response='content'  data-url='get.php' data-data='opcion=BuscarCotizacion&auto=true' >
                           <div class='notification_info_icon'><img src='../assets/imagenes/warning.png' /></div>
                           <div class='notification_info_text'><span class='circle'>$result[count]</span> Cotizaciones pendientes</div>
                       </div>";
          } 

          $query = "SELECT count(*) as count  
                     FROM  producto_punto 
                     INNER JOIN producto ON producto.id = producto_id 
                     WHERE punto_id = 1  
                           AND producto_punto.cantidad <= producto.stock 
                           AND producto_punto.estado = 1
                           AND producto.estado = 1";

                           

          $data = $connection->query($query);  
          $result = $data->fetch(PDO::FETCH_BOTH ); 
          if( $result['count'] > 0 )
          {
               $form.="<div class='notification_info link' data-paginate='true' data-opcion='PorTerminar' data-url='get.php' data-data='opcion=PorTerminar' data-response='content'>
                           <div class='notification_info_icon'><img src='../assets/imagenes/warning.png' /></div>
                           <div class='notification_info_text'><span class='circle'>$result[count]</span> Productos por terminarse</div>
                       </div>";
          }


          $query = "SELECT COUNT(*) count
                    FROM venta  
                    WHERE tipo_venta_id = 2 AND punto_id =  $idpunto";
          $data = $connection->query($query);  
          $result = $data->fetch(PDO::FETCH_BOTH ); 
          if( $result['count'] > 0 )
          {
               $form.="<div class='notification_info link' data-url='get.php' data-data='opcion=PorCobrar' data-response='content'>
                           <div class='notification_info_icon'><img src='../assets/imagenes/warning.png' /></div>
                           <div class='notification_info_text'><span class='circle'>$result[count]</span> Cuentas por cobrar</div>
                       </div>";
          } 
          $fecha = date('Y-m-d');

          $query = "SELECT count(*) count
                    FROM compra
                    WHERE  tipo_compra_id = 2 and punto_id = $idpunto AND datediff(llegada, '$fecha') <= 3"; 
          $data = $connection->query($query);  
          $result = $data->fetch(PDO::FETCH_BOTH ); 
          if( $result['count'] > 0 )
          {
               $form.="<div class='notification_info link' data-url='set.php' data-opcion='PorPagar' data-terminar='true' data-response='content' data-paginate='true'>
                           <div class='notification_info_icon'><img src='../assets/imagenes/warning.png' /></div>
                           <div class='notification_info_text'><span class='circle'>$result[count]</span> Cuentas por pagar</div>
                       </div>";
          }  

          $query = "SELECT *
                    FROM resolucion
                    WHERE  status = 1"; 
          $data = $connection->query($query);  
          $result = $data->fetchAll(PDO::FETCH_BOTH ); 
          if( count($result) > 0 )
          {
               $id = $result[0]['id'];
               $query = "SELECT
                           (( fin - inicio ) + 1 ) total,
                           ( SELECT COUNT(*) FROM resolucion_numeros WHERE resolucion_id = 1 AND estado = 2 )  cant
                         FROM resolucion , resolucion_numeros
                         WHERE resolucion.id = 1
                         GROUP BY(resolucion.id);";
               $data = $connection->query($query);  
               $result = $data->fetchAll(PDO::FETCH_BOTH ); 
               if( count($result) >0 )
               {
                   $porcentaje = 0;
                   $cant = $result[0]['cant'];
                   $total = $result[0]['total'];
                   if( $total >  0 )
                      $porcentaje = ($cant / $total) * 100;

                   if( $porcentaje >= 20 )
                   {
                       $form.="<div class='notification_info link' data-url='get.php' data-data='opcion=CrearFactura' data-response='content'>
                                   <div class='notification_info_icon'><img src='../assets/imagenes/warning.png' /></div>
                                   <div class='notification_info_text'><span class='circle'>$porcentaje%</span> Facturas por terminarse </div>
                               </div>";
                   }
               }
          }  
          echo $form; 
      break; 

      case "BuscarCotizacion":

         $fecha = "";
         $idcliente = "";
         $form_cotizacion = GET_ALL_COTIZACIONES( $connection , $fecha , $idcliente , $idpunto , $func );
            
         $query="SELECT * FROM producto_punto WHERE cantidad > 0 AND estado = true";
         $data = $connection->query($query); 
         $resultss = $data->fetchAll(PDO::FETCH_BOTH ); 
         $form=""; 

         $query = "SELECT id , tipo FROM tipo_venta WHERE id <= 2";
         $data = $connection->query($query); 
         $results = $data->fetchAll(PDO::FETCH_BOTH ); 
         $params = array
                   (
                       "default" => "Seleccione" ,  
                       "name" => "tipoventa" , 
                       "id" => "tVenta" , 
                       "class" => "form-control validar requerido select" ,
                       "value" => "", 
                       "multiple" => false, 
                       "results" => $results
                    );  

         $tipoventa = $func->selectOption($params);
         $access = $func->CheckAccess( $connection , 2 , $idusuario );

         $row_point = "";
         if( $access > 0 )
         {

              $query = "SELECT id , punto FROM punto WHERE id <> $idpunto";
              $data = $connection->query($query); 
              $results = $data->fetchAll(PDO::FETCH_BOTH ); 

              $params =  array
                         (
                             "default" => "Seleccione" ,  
                             "name" => "punto_id" , 
                             "id" => "punto_id" , 
                             "class" => "punto_id w100" ,
                             "value" => "", 
                             "multiple" => false, 
                             "results" => $results
                          );                                
         } 
        
         if( count($resultss) > 0 )
         {   
            $form.= "<div class='titulo'>CONVERTIR COTIZACION A VENTA</div>";
            $form.= "<form action='set.php' method='post' data-response='derecho' data-type='json' autocomplete='off'  data-paginate='false' >";

            $form.= "<div class='izq' style='width:30%;background-color:#fff; padding:10px;'>
                        <div style='width:100%;background-color:#fff; padding:10px; float:left;'>
                            <div class='form-group'>
                              <div class='form-header'>
                                 <label>Fecha</label>
                              </div>
                              <div class='form-body'>                        
                                 <input type='text' class='form-control date validar fecha' name='fecha' >
                              </div>
                            </div>

                            <div class='form-group ref_add_cliente' >
                              <div class='form-header'>
                                 <label>Cliente</label>
                              </div>
                              <div class='form-body'>                        
                                 <input type='text' class='form-control sin_cliente' name='nombre_cliente' id='buscarClienteVenta' data-buscar='BuscarProductoVenta'>
                              </div> 
                            </div>   
                        </div>  
                     </div>";
 
            $form.= "<div class='derecho' id='derecho' style='width:68%; background:white;float: right;' >
                          $form_cotizacion
                     </div>"; 
            $form.= "<input type='hidden' name='opcion' value='ConvertirCotizacionFactura' />";  
            $form.= "</form>"; 
         } 
         else            
          $form.= $msg->danger("NO HAY PRODUCTOS DISPONIBLES");

         echo $form;        
      break;

      case "DetalleCompra":
          $query = "SELECT 
                         producto.nombre , 
                         producto_compra.cantidad , 
                         producto_compra.precio , 
                         producto_compra.costo
                    FROM 
                        compra
                    INNER JOIN producto_compra ON compra.id = compra_id 
                    INNER JOIN producto ON producto.id = producto_compra.producto_id 
                    WHERE compra.id = $id";
          $data = $connection->query($query); 
          $results = $data->fetchAll(PDO::FETCH_ASSOC); 
          if( count( $results ) > 0 )
          {

               $form = "<table class='ui table'>";
               $form.="<thead>
                          <tr>
                           <th>PRODUCTO</th>
                           <th>CANTIDAD</th>
                           <th>COSTO</th>
                           <th>PRECIO</th>
                           <th>TOTAL</th>
                          </tr>
                        </thead>"; 
               $suma = 0; 
               foreach ($results as $row) 
               {
                   $total = $row['cantidad'] * $row['costo'];
                   $suma += $total;

                   $row['nombre'] = utf8_encode($row['nombre']);

                   $row['costo']= $func->format( $row['costo'] );
                   $row['precio'] = $func->format( $row['precio'] );


                   $total = $func->format($total);
                   $form.="<tr>
                             <td>$row[nombre]</td>
                             <td>$row[cantidad]</td>
                             <td>$row[costo]</td>
                             <td>$row[precio]</td>
                             <td>$total</td>
                           </tr>";
               }
               $suma = $func->format($suma);
               $form.="<tr>
                             <td><b>TOTAL</b></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td><b>$suma</b></td>
                           </tr>";
               $form.="</table>";

               echo $form;
          }
          else
          {
             echo $msg->warning("No se encontraron productos para la factura seleccionada");
          } 
      break;

      case "editarCompra":
         echo '<div class="loading_input">
                  <div class="cssload-container">
                  <div class="cssload-speeding-wheel"></div>
                  </div>
               </div>';
         
         $sql="SELECT * FROM compra WHERE id = $id";
         $data = $connection->query($sql); 
         $compra = $data->fetch(PDO::FETCH_OBJ);  


         $sql="SELECT id , concat(nombres,' ',apellidos) as name FROM proveedor WHERE id = $compra->proveedor_id";
         $data = $connection->query($sql); 
         $proveedor = $data->fetch(PDO::FETCH_OBJ);  


 
            $query = "SELECT id , tipo FROM tipo_compra WHERE id <= 2";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 
            $params = array
                   (
                       "default" => "Seleccione" ,  
                       "name" => "tipocompra" , 
                       "id" => "tCompra" , 
                       "class" => "dropdown validar requerido select" ,
                       "value" => $compra->tipo_compra_id, 
                       "multiple" => false, 
                       "results" => $results
                    );  

             $tipocompra = $func->selectOption($params);
 

            $query = "SELECT punto.id,punto FROM punto 
                      INNER JOIN usuario_punto ON punto.id = punto_id 
                      WHERE usuario_id = $idusuario ";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH);  


            $query = "SELECT 
                        producto.id,
                        producto.nombre , 
                        producto_compra.cantidad , 
                        producto_compra.precio , 
                        producto_compra.costo,
                        producto_punto.precio_minimo
                      FROM 
                        compra
                      INNER JOIN producto_compra ON compra.id = compra_id 
                      INNER JOIN producto ON producto.id = producto_compra.producto_id 
                      INNER JOIN producto_punto ON producto_punto.producto_id = producto_compra.producto_id  
                      WHERE compra.id = $compra->id";
            $data = $connection->query($query); 
            $productos = $data->fetchAll(PDO::FETCH_OBJ);
            $listProductos = ""; 
            $indice = 1;
            if( count($productos) > 0 )
            {
              foreach ($productos as $k => $producto) 
              {
                  $listProductos.= "<tr class='row_dinamic row_dinamic_compra'>
                                      <td class='indice'>$indice</td>
                                      <td>$producto->nombre</td>
                                      <td>
                                          <input type='text' style='width:100px;' value='$producto->cantidad' name='cantidad[]' class='form-control cantidad validar requerido numero' data-min='1'  />
                                      </td>
                                      <td>
                                          <input type='text' style='width:100px;' value='$producto->costo' name='costo[]'  value='+costonormal+' class='form-control costo required validar requerido decimal' data-min='1' />
                                      </td>
                                      <td>
                                          <input type='text' style='width:100px;' value='$producto->precio' name='precio[]' value='+precio+' class='form-control precio validar requerido decimal'  data-min='1' />
                                      </td>
                                      <td>
                                          <input type='text' style='width:100px;' name='preciominimo[]' value='$producto->precio_minimo' class='form-control  validar requerido decimal'  data-min='1' />
                                      </td>
                                      <td>
                                          <div class='delete-photo eliminarProductoCompra' data-idcompra='$id' data-cantidad='$producto->cantidad' data-idproducto='$producto->id'></div>
                                      </td>
                                      <input type='hidden' name='idproducto[]' value='$producto->id' />
                                  </tr>"; 
              }   
            }

           
            echo "<div class='titulo'>REGISTRO DE COMPRAS</div>";
            echo " <form class='ui form' action='set.php' method='post' data-response='content' autocomplete='off' data-type='json' data-paginate='false' >";
            echo "<div class='izq' style='width:30%;background-color:#fff; padding:10px;'> 
                        <div class='field'>
                             <label>Factura</label>
                             <input type='text' name='factura' value='$compra->factura'  class='form-control validar requerido texto'   >    
                        </div> 
                        <div class='field'>
                             <label>Nombre del producto</label> 
                             <div class='ui icon input loading'>
                              <input type='text' style='width:100%;'  id='buscarProductoCompra' data-buscar='BuscarProductoVenta' >
                              <i class='search search-producto icon' style='display:none; '></i>
                             </div>  
                        </div> 
                        <div class='field'>
                             <label>Tipo de compra</label>
                             $tipocompra
                        </div> 
               
                       <div class='field' >
                          <label>Proveedor</label>  
                          <div class='ui icon input loading'>
                            <input type='text' style='width:100%;' id='buscarProveedorCompra' value='$proveedor->name'  >
                            <i class='search search-cliente icon' style='display:none; '></i>
                          </div> 
                        </div> 

                       <div class='field'>
                           <label>Fecha llegada</label>
                           <input type='text' name='llegada'  value='$compra->llegada' class='form-control validar requerido date fecha fllegada'   >    
                       </div> 
                       <div class='field'>
                           <label>Fecha pago</label>
                           <input type='text' name='limite'  value='$compra->limite'  class='form-control validar requerido date fecha fpago'   >    
                       </div>                 
                  </div>";

            if( empty($idproductos) )
            {   
                  echo "<div class='der' style='width:68%' >
                               
                                <div class='ui grid'>
                                  <div class='four wide column'>
                                     <div class='header_title ui medium header'>Precio</div>
                                  </div>
                                  <div class='four wide column'>
                                     <div class='header_price ui medium header'>0</div>
                                  </div>
                                  <div class='four wide column'>
                                     <div class='header_title ui medium header'>Costo</div> 
                                  </div>
                                  <div class='four wide column'>
                                     <div class='header_cost ui medium header'>0</div>
                                  </div>
                                </div>

                                <table class='ui single line  table' id='tabla' style='background:white; float:left;'> 
                                     <thead>
                                       <tr>
                                          <th></th> 
                                          <th>Producto</th> 
                                          <th>Cantidad</th>
                                          <th>Costo</th> 
                                          <th>Precio</th> 
                                          <th>Precio minimo</th> 
                                          <th></th>
                                       </tr>
                                     </thead>
                                     <tbody>$listProductos</tbody>
                                     <tfoot class='full-width'>
                                        <tr>
                                          <th colspan='7' class='text-right'>

                                              <div class='ui animated button btn-enviar' tabindex='0' >
                                                <div class='visible content'>Modificar compra</div>
                                                <div class='hidden content'>
                                                  <i class='right arrow icon'></i>
                                                </div>
                                              </div>
     
                                          </th>
                                        </tr>
                                      </tfoot>
                                </table>
                        </div>"; 
            
            echo "</div>";
            echo "<input type='hidden' name='opcion' value='ModificarCompra' />"; 
            echo "<input type='hidden' name='idcompra' value='$id' />"; 
            echo "<input type='hidden' name='idproveedor' id='hiddenProveedor' value='$compra->proveedor_id' />"; 
            
            echo "</form>"; 
           
         } 
         else            
          echo $msg->danger("NO HAY PRODUCTOS DISPONIBLES");  
      break;

      case "ReporteCompras":
        $tipocompra = "<select name='tiposcompras[]'  class='form-control chosen' style='width:100%;' multiple >
                          <option value=''>Todas</option>
                          <option value='1'>Contado</option>
                          <option value='2'>Credito</option>
                       </select>";


        $query = "SELECT id , concat(nombres,' ',apellidos) nombre FROM proveedor";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 

        $params =  array
                   (
                       "default" => "Todos" ,  
                       "name" => "idproveedores[]" , 
                       "id" => "idproveedor" , 
                       "class" => "idproveedor w100 chosen" ,
                       "value" => "", 
                       "multiple" => true, 
                       "results" => $results
                    );  

        $proveedor = $func->selectOption($params);


        $query = "SELECT id , nombre FROM producto";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 

        $params =  array
                   (
                       "default" => "Todos" ,  
                       "name" => "idproductos[]" , 
                       "id" => "idproductos" , 
                       "class" => "idproductos w100 chosen" ,
                       "value" => "", 
                       "multiple" => true, 
                       "results" => $results
                    );  
        $productos = $func->selectOption($params);

        $data ="<form class='ui form' autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_reporte_compras' >
                  <div class='six fields'>  

                      <div class='field'>
                         <label>Fecha inicial</label>
                         <input type='text' class='validar texto date'  name='inicio' >
                      </div>

                      <div class='field'>
                         <label>Fecha final</label>
                         <input type='text' class='validar texto date'  name='fin' >
                      </div> 

                      <div class='field'>
                         <label>Productos</label>
                          $productos
                      </div>

                      <div class='field'>
                         <label>Proveedor</label>
                          $proveedor
                      </div>

                      <div class='field'>
                         <label>Tipo de compra</label>
                          $tipocompra
                      </div>

                      
                      <input type='hidden' name='opcion' value='$opcion'> 
                                              
                      <div class='field'>
                          <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>Consultar</div>
                              <div class='hidden content'>
                                <i class='right search icon'></i>
                              </div>
                          </div>
                      </div>  
                   </table>
                 </form>";
        echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
              <div class='sixteen wide column'>
                 <table class='ui table table-reporte-compras'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>EDITAR</th>
                           <th>FECHA</th>
                           <th>USUARIO</th> 
                           <th>FACTURA</th>
                           <th>TIPO</th> 
                           <th>PROVEEDOR</th>  
                           <th>TOTAL</th>  
                           <th>PRODUCTOS</th>  
                           <th>DETALLLE</th>  
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <th>TOTAL</th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                      </tfoot>
                  </table>
              </div>";   

              echo "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div> 
               </div>";   
      break; 

      case "paginar_reporte_compras":
           $connection->query("SET NAMES utf8");
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
           $listTipos = "";
           if( !empty($tiposcompras) )
           {
               if( count($tiposcompras) > 0 )
               {
                  $list = trim(implode(",", $tiposcompras));
                  $listTipos = $list;
                  if( strlen($list) > 0 )
                     $condition .=" AND tipo_compra_id IN ( $list )"; 
               }
           }

           if( !empty($idproductos) )
           {
               if( count($idproductos) > 0 )
               {
                  $list = trim(implode(",", $idproductos));
                  if( strlen($list) > 0 )
                     $condition .= " AND producto_compra.producto_id IN ( $list )"; 
               }
           }
           
           if( !empty($inicio) && !empty($fin) )
              $condition .= " AND DATE(compra.fecha) BETWEEN '$inicio' AND '$fin'";
           else
           {
              if( !empty($inicio) )
              $condition .= " AND DATE(compra.fecha) = '$inicio'";
           } 
         

           $query = "SELECT 
                           compra.id ,
                           compra.fecha ,  
                           concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador ,
                           factura , 
                           tipo ,
                           tipo_compra_id ,
                           concat( proveedor.nombres , ' ' , proveedor.apellidos) AS proveedor ,
                           SUM(producto_compra.cantidad * producto_compra.costo) AS total ,
                           producto_id ,
                           count( producto_compra.producto_id ) productos
                          FROM compra
                               INNER JOIN producto_compra ON compra.id = producto_compra.compra_id 
                               INNER JOIN tipo_compra ON compra.tipo_compra_id =tipo_compra.id  
                               INNER JOIN usuario ON usuario_id = usuario.id 
                               INNER JOIN producto ON producto_compra.producto_id = producto.id 
                               LEFT JOIN proveedor ON proveedor_id = proveedor.id 
                          WHERE compra.punto_id = $idpunto $condition 
                          GROUP BY compra.id
                          ORDER BY compra.fecha DESC";
           
           $data = $connection->query($query);  
           $resultsTotal = $data->fetchAll(PDO::FETCH_BOTH ); 
           $recordsTotal = count($resultsTotal);

           $query .= " LIMIT $start , $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH ); 

           if( !empty($idproductos) )
           {
             if( count($idproductos) > 0 )
             {

               foreach ($results as $key_c => $compra) 
               {
                     $query = "SELECT 
                                    producto_id ,
                                    nombre
                               FROM producto_compra 
                               INNER JOIN producto ON producto.id = producto_id
                               WHERE compra_id = $compra[id]";
                     $data = $connection->query($query);  
                     $results_pc = $data->fetchAll(PDO::FETCH_ASSOC); 


                     if( count($results_pc) > 0 )
                     {
                        $results[$key_c]["productos"] = "";
                        foreach ($results_pc as $key_cp => $rpc) 
                        {
                           foreach ($idproductos as $key_id => $idp) 
                           {

                              if( $rpc["producto_id"] == $idp)
                              {
                                 $results[$key_c]["productos"] .= "<div class='ui label'>
                                                                       $rpc[nombre]
                                                                   </div>";
                              }
                           }
                        }
                     }
               }
             }
           }


           $query = "SELECT 
                      SUM(producto_compra.cantidad * producto_compra.costo) AS total 
                      FROM compra 
                       INNER JOIN producto_compra ON compra.id = producto_compra.compra_id 
                       INNER JOIN tipo_compra ON compra.tipo_compra_id =tipo_compra.id  
                       INNER JOIN usuario ON usuario_id = usuario.id 
                       INNER JOIN producto ON producto_compra.producto_id = producto.id 
                       LEFT JOIN proveedor ON proveedor_id = proveedor.id
                      WHERE compra.punto_id = $idpunto $condition ";
           $data = $connection->query($query);  
           $resultsTotal = $data->fetch(PDO::FETCH_BOTH );



           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  ,
              "total"           => $resultsTotal['total'] 
           ); 
           echo json_encode($json_data); 
      break;

      case "ReporteCantidadVendida":
        $tipoventa = "<select name='tiposcompras[]'  class='form-control chosen' style='width:100%;' multiple >
                          <option value=''>Todas</option>
                          <option value='1'>Contado</option>
                          <option value='2'>Credito</option>
                       </select>";

        echo "<div class='titulo'>REPORTE DE CANTIDADES</div>";


        $data = "<form class='ui form' autocomplete='off' action='set.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='false'>
                  <div class='four fields'>
                     <div class='field'>
                       <label>Buscar Producto</label>
                       <input type='text' class='form-control'  id='buscarProductoCantidades'>
                     </div> 
                     <div class='field'>
                       <label>Fecha inicial</label>
                       <input type='text' class='form-control validar texto date'  name='inicio' >
                     </div>
                    <div class='field'>
                       <label>Fecha final</label>
                       <input type='text' class='form-control validar texto date'  name='fin' >
                    </div> 
                       
                    <input type='hidden' name='opcion' value='$opcion'>
                    <input type='hidden' name='page' value='1'>
                                              
                    <div class='field'>
                          <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                            <div class='visible content'>Consultar</div>
                            <div class='hidden content'>
                              <i class='right search icon'></i>
                            </div>
                          </div>
                    </div>  
                  </div>  
    
                   <table class='ui table' id='recibe' style='background:white; float:left;'> 
                     <thead>
                       <tr>
                          <th>Producto</th>  
                          <th>Eliminar</th>
                       </tr>
                     </thead>
                     <tbody>
                     </tbody>
                   </table>
                </form><BR>"; 
         echo $data; 
      break; 

      case "buscarProductoCantidad":
        $tabla = "";  

        $query = "SELECT
                      producto.id , 
                      producto.nombre , 
                      producto_punto.costo, 
                      producto_punto.costo_liquidado, 
                      producto_punto.precio ,
                      producto_punto.cantidad  
                   FROM producto 
                   INNER JOIN producto_punto ON producto.id = producto_punto.producto_id 
                   WHERE 
                      producto_punto.estado = 1 AND 
                      producto_punto.cantidad >= 0 AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )
                   LIMIT 100";
        $data = $connection->query($query);  
        $results = $data->fetchAll(PDO::FETCH_BOTH);  

        if( count( $results) > 0 ) 
        {
              $tabla = "<table class='ui table table-bordered table-striped table-condensed' >
                           <thead>
                              <tr>
                                 <th>Nombre</th>
                                 <th>Cantidad</th> 
                                 <th>Precio</th>   
                                 <th></th> 
                              </tr>
                           </thead>
                      </tbody>";
            
               $total_pre_venta = 0;
               $total_val_ivent = 0;
               $total_val_venta = 0;
               
               foreach( $results as $fila )
               {
                   $porcentajeValor = 0;
                   if( !empty($fila['porcentaje']) )
                   {
                      $porcentajeValor = $fila['precio'] * $fila['porcentaje'] / 100; 
                      $fila['precio'] = ( $fila['precio'] - $porcentajeValor );

                   }

                   $fila['nombre'] = utf8_encode($fila['nombre']);
                   $tabla.= "<tr>                                
                                <td>$fila[nombre]</td>
                                <td>$fila[cantidad]</td> 
                                <td>$ $fila[precio]</td>
                                <td> 
                                  <i style='font-size:24px;' class='icon arrow right btn-add-producto-cantidad' data-idproducto='$fila[id]'  data-nombre='$fila[nombre]'  data-cantidad='$fila[cantidad]' data-precio='$fila[precio]' data-costonormal='$fila[costo]' data-costoliquidado='$fila[costo_liquidado]' data-descuento='$porcentajeValor' ></i>
                                </td>
                             </tr>"; 
               } 
                  
               $tabla.= "</tbody>
                            <tfoot> 
                                <tr>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                </tr>
                            </tfoot>";
               $tabla.= "</table>";

               echo "<div id='close_absolute'></div><div id='result_absolute'>$tabla</div>"; 
        }
        else
           echo $msg->success("Operación registrada exitosamente."); 
      break; 

      case "ReporteVentas": 

        $query = "SELECT id , nombre FROM producto";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 

        $params =  array
                   (
                       "default" => "Todos" ,  
                       "name" => "idproductos[]" , 
                       "id" => "idproductos" , 
                       "class" => "idproductos w100 chosen" ,
                       "value" => "", 
                       "multiple" => true, 
                       "results" => $results
                    );  
        $productos = $func->selectOption($params);


        $query = "SELECT id ,  TRIM(UPPER(concat(nombres,' ',apellidos))) name FROM cliente";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $params = array
                 (
                     "default" => "" ,  
                     "name" => "idclientes[]" , 
                     "id" => "" , 
                     "class" => "chosen" ,
                     "value" => "", 
                     "multiple" => true, 
                     "results" => $results
                  );  

        $clientes = $func->selectOption($params); 


        $tipoventa = "<select name='tiposventas[]'  class='chosen' multiple >
                          <option value=''>Todas</option>
                          <option value='1'>Contado</option>
                          <option value='2'>Credito</option>
                       </select>";


        $query = "SELECT id , concat(nombre,' ',apellido) FROM usuario";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 

        $params =  array
                   (
                       "default" => "Todos" ,  
                       "name" => "idusuarios[]" , 
                       "id" => "idusuario" , 
                       "class" => "idusuario chosen" ,
                       "value" => "", 
                       "multiple" => true, 
                       "results" => $results
                    );  

        $usuario = $func->selectOption($params);



        $data ="<form autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_reporte_ventas' >
                    <div class='ui form'>
                      <div class='seven fields'>
                        <div class='field'>
                          <label>Fecha inicio</label>
                          <input type='text' class='start'  name='inicio' >
                        </div>
                        <div class='field'>
                          <label>Fecha fin</label>
                          <input type='text' class='end'  name='fin' >
                        </div>
                        <div class='field'>
                          <label>Trabajador</label>  
                          $usuario 
                        </div>

                        <div class='field'>
                          <label>Producto</label>  
                          $productos 
                        </div>
                        <div class='field'>
                          <label>Tipo de venta</label>  
                          $tipoventa 
                        </div>
                        <div class='field'>
                          <label>Cliente</label>  
                          $clientes
                        </div>
                        <div class='field'>
                          <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                            <div class='visible content'>Consultar</div>
                            <div class='hidden content'>
                              <i class='right search icon'></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                 </form>";
         echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
              <div class='sixteen wide column'>
                 <table class='ui table table-reporte-ventas'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>FECHA</th> 
                           <th>TRABAJADOR</th> 
                           <th>CLIENTE</th>
                           <th>RECIBO</th>
                           <th>TIPO</th> 
                           <th>TOTAL</th>  
                           <th>TERMICA</th>  
                           <th>RECIBO</th>  
                           <th>FACTURA</th>  
                           <th>DETALLLE</th>  
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <th>TOTAL</th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                      </tfoot>
                  </table>
              </div>";   

              echo "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div> 
               </div>"; 
      break;

      case "GraficoVenta":  

        $query = "SELECT id ,  TRIM(UPPER(concat(nombres,' ',apellidos))) name FROM cliente";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 
        $params = array
                 (
                     "default" => "" ,  
                     "name" => "idclientes[]" , 
                     "id" => "" , 
                     "class" => "chosen" ,
                     "value" => "", 
                     "multiple" => true, 
                     "results" => $results
                  );  

        $clientes = $func->selectOption($params); 


        $tipoventa = "<select name='tiposventas[]'  class='chosen' multiple >
                          <option value=''>Todas</option>
                          <option value='1'>Contado</option>
                          <option value='2'>Credito</option>
                       </select>";


        $query = "SELECT id , concat(nombre,' ',apellido) FROM usuario";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 

        $params =  array
                   (
                       "default" => "Todos" ,  
                       "name" => "idusuarios[]" , 
                       "id" => "idusuario" , 
                       "class" => "idusuario chosen" ,
                       "value" => "", 
                       "multiple" => true, 
                       "results" => $results
                    );  

        $usuario = $func->selectOption($params); 

        $data ="<form autocomplete='off' action='get.php' method='post' data-type='json' data-get='char' enctype='multipart/form-data' data-response='content' data-paginate='false' data-opcion='paginar_reporte_ventas' >
                    <div class='ui form'>
                      <div class='six fields'>
                        <div class='field'>
                          <label>Fecha inicio</label>
                          <input type='text' class='start'  name='inicio' >
                        </div>
                        <div class='field'>
                          <label>Fecha fin</label>
                          <input type='text' class='end'  name='fin' >
                        </div>
                        <div class='field'>
                          <label>Trabajador</label>  
                          $usuario 
                        </div>
                        <div class='field'>
                          <label>Tipo de venta</label>  
                          $tipoventa 
                        </div>
                        <div class='field'>
                          <label>Cliente</label>  
                          $clientes
                        </div>
                        <div class='field'>
                          <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                            <div class='visible content'>Consultar</div>
                            <div class='hidden content'>
                              <i class='right search icon'></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <input type='hidden' name='opcion' value='GetGraficoVenta'> 
                 </form>
                 <div id='chartdiv'>Char Div</div>";
         echo $data;
            
      break;

      case "GetGraficoVenta":
           $connection->query("SET NAMES utf8"); 
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


           if( !empty($idclientes) )
           {
               if( count($idclientes) > 0 )
               {
                  $list = trim(implode(",", $idclientes));
                  if( strlen($list) > 0 )
                     $condition .=" AND venta.cliente_id IN ( $list )"; 
               }
           }

           if( !empty($idusuarios) )
           {
               if( count($idusuarios) > 0 )
               {
                  $list = trim(implode(",", $idusuarios));
                  if( strlen($list) > 0 )
                     $condition .=" AND venta.usuario_id IN ( $list )"; 
               }
           }
           
           if( !empty($inicio) && !empty($fin) )
              $condition .= " AND DATE(venta.fecha) BETWEEN '$inicio' AND '$fin'";
           else
           {
              if( !empty($inicio) )
              $condition .= " AND DATE(venta.fecha) = '$inicio'";
           }

           $query = "SELECT
                        UPPER(CONCAT(nombre,' ',apellido)) as trabajador ,
                        SUM(cantidad * precio) as total , 
                        color
                     FROM  venta
                     INNER JOIN producto_venta ON venta.id = venta_id
                     INNER JOIN usuario ON usuario.id = usuario_id
                     WHERE usuario.id NOT IN(1) $condition  
                     GROUP BY usuario_id
                     ORDER BY total DESC";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_ASSOC);

           echo json_encode(  $results );
      break;

      case "paginar_reporte_ventas":
           $connection->query("SET NAMES utf8"); 



           $query = "SELECT valor FROM configuracion WHERE name = 'HideBtn'";
           $data = $connection->query($query);  
           $results = $data->fetch(PDO::FETCH_ASSOC );

           if( !empty($results['valor']) )
              $hide = $results['valor']; 
           else
              $hide = 0;


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


           if( !empty($idclientes) )
           {
               if( count($idclientes) > 0 )
               {
                  $list = trim(implode(",", $idclientes));
                  if( strlen($list) > 0 )
                     $condition .=" AND venta.cliente_id IN ( $list )"; 
               }
           }

           if( !empty($idproductos) )
           {
               if( count($idproductos) > 0 )
               {
                  $list = trim(implode(",", $idproductos));
                  if( strlen($list) > 0 )
                     $condition .= " AND producto_venta.producto_id IN ( $list )"; 
               }
           }

           if( !empty($idusuarios) )
           {
               if( count($idusuarios) > 0 )
               {
                  $list = trim(implode(",", $idusuarios));
                  if( strlen($list) > 0 )
                     $condition .=" AND venta.usuario_id IN ( $list )"; 
               }
           }
           
           if( !empty($inicio) && !empty($fin) )
              $condition .= " AND DATE(venta.fecha) BETWEEN '$inicio' AND '$fin'";
           else
           {
              if( !empty($inicio) )
              $condition .= " AND DATE(venta.fecha) = '$inicio'";
           }

           if( $hide == 0 )
           {
              $condition .= " AND venta.checked = 0";
           }  

           
            

           $query = "SELECT 
                           venta.id ,
                           venta.fecha  ,
                           concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador ,
                           concat( cliente.nombres,' ',cliente.apellidos) AS cliente,                           
                           recibo , 
                           tipo_venta.tipo ,
                           SUM(producto_venta.cantidad * producto_venta.precio) AS total ,
                           checked 
                          FROM venta 
                           INNER JOIN producto_venta ON venta_id = venta.id
                           INNER JOIN usuario ON venta.usuario_id = usuario.id 
                           INNER JOIN tipo_venta ON venta.tipo_venta_id = tipo_venta.id 
                           LEFT JOIN cliente ON cliente_id = cliente.id 
                          WHERE 1 $condition  
                          GROUP BY venta.id
                          ORDER BY venta.fecha DESC ";
           $data = $connection->query($query);  
           $resultsTotal = $data->fetchAll(PDO::FETCH_BOTH );

           $recordsTotal = count($resultsTotal);


           $query .= " LIMIT $start, $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );

           $query = "SELECT 
                          SUM(producto_venta.cantidad * producto_venta.precio) AS total 
                          FROM venta 
                           INNER JOIN producto_venta ON venta_id = venta.id
                           INNER JOIN usuario ON venta.usuario_id = usuario.id 
                           INNER JOIN tipo_venta ON venta.tipo_venta_id = tipo_venta.id 
                           LEFT JOIN cliente ON cliente_id = cliente.id 
                          WHERE 1 $condition";

           $data = $connection->query($query);  
           $resultsTotal = $data->fetch(PDO::FETCH_BOTH );


           /*"total"           => $resultsTotal['total'] */

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  ,
              "total"           => $resultsTotal['total'] 
           ); 
           echo json_encode($json_data); 
      break; 

      case "consultar_catalogo":  

            $query = "SELECT id , nombre  FROM categoria";
            $data = $connection->query($query); 
            $results = $data->fetchAll(PDO::FETCH_BOTH ); 
            $params = array
                       (
                           "default" => "Seleccione" ,  
                           "name" => "idcategoria" , 
                           "id" => "idcategoria" , 
                           "class" => "form-control validar requerido select" ,
                           "value" => "", 
                           "multiple" => false, 
                           "results" => $results
                        );  

            $categoria = $func->selectOption($params);

            $data ="<div class='col-md-10 offset-md-1 text-center'>
                  <form autocomplete='off' action='set.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' >
                   <table>
                      <thead>
                         <tr>
                            <th><div class='titulo'>CONSULTAR CATALOGO</div></th>
                         </tr>
                      </thead>
                      <tr>
                         <td style='vertical-align: top;'>
                              <div class='form-group' >
                                <div class='form-header'>
                                   <label>Seleccione una categoria</label>
                                </div>
                                <div class='form-body'>                        
                                   $categoria
                                </div>
                              </div> 
                         </td>
                      

                      </tr> 
    
                      <input type='hidden' name='opcion' value='$opcion'>
                      <input type='hidden' name='page' value='1'>
                                              
                      <tr> 
                         <td class='text-right'><input type='submit' class='btn btn-outline-primary' value='CONSULTAR'></td>
                      </tr>  
                   </table>
                 </form>
                </div>";
            echo "<div class='content'>$data</div>";   
      break;  

      case "consultar_marcas":
           $condicion = "";
           if( !empty($producto) )
           {  
               $condicion = " AND ( ( producto.nombre LIKE '%$producto%' ) OR ( producto.codigo LIKE '%$producto%') )";
           }  
                 

           $tabla = "<table class='table ui inline table-marcas'>
                        <thead>
                          <tr>
                             <th></th>
                             <th>IMAGEN</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                           <tr>
                             <td></td>
                             <td></td> 
                          </tr>
                        </tfoot>
                     </table>";

           echo $tabla;               
      break;  

      case "paginar_marcas": 
           $connection->query("SET NAMES utf8");

           $condicion = "";
            
           
           $query = "SELECT * FROM marca"; 

           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );
           $recordsTotal = count($results);

           $query .= " LIMIT $start, $length";                  
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );
           

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  
           ); 
           echo json_encode($json_data);    
      break;

      case "ReporteCuentasPorPagar":
        $query = "SELECT id , concat(nombres,' ',apellidos) nombre FROM proveedor";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 

        $params =  array
                   (
                       "default" => "Todos" ,  
                       "name" => "idproveedores[]" , 
                       "id" => "idproveedor" , 
                       "class" => "idproveedor w100 chosen" ,
                       "value" => "", 
                       "multiple" => true, 
                       "results" => $results
                    );  

        $proveedor = $func->selectOption($params);

        echo "<div class='titulo'>REPORTE DE CUENTAS POR PAGAR</div>";

        $tipocompra = "<select name='tiposcompras[]'  class='form-control chosen' style='width:100%;' multiple >
                          <option value=''>Todas</option>
                          <option value='1'>Contado</option>
                          <option value='2'>Credito</option>
                       </select>";


        $data ="<div class='col-md-10 offset-md-1 text-center'>
                  <form class='ui form' autocomplete='off' action='set.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' >
                      <div class='four fields'>

                        <div class='field' >
                             <label>Fecha inicial</label>
                             <input type='text' class='form-control validar requerido texto date'  name='inicio' >
                        </div> 

                        <div class='field'>
                             <label>Fecha final</label>
                             <input type='text' class='form-control validar texto date'  name='fin' >
                        </div>   

                        <div class='field'>
                             <label>Proveedor</label>
                             $proveedor
                        </div> 
                        
                        <div class='field'>
                             <label>Factura</label>
                             <input type='text' class='form-control validar texto'  name='factura' >
                        </div> 
                          
                   
                        <input type='hidden' name='opcion' value='$opcion'>
                        <input type='hidden' name='page' value='1'>
                                              
                        <div class='field'>
                          <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                              <div class='visible content'>Consultar</div>
                              <div class='hidden content'>
                                <i class='right search icon'></i>
                              </div>
                          </div>
                        </div>
                      </div>
                  </form>";
         echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
              <div class='sixteen wide column'>
                 <table class='ui table table-reporte-ventas'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>FECHA</th> 
                           <th>TRABAJADOR</th> 
                           <th>CLIENTE</th>
                           <th>RECIBO</th>
                           <th>TIPO</th> 
                           <th>TOTAL</th>  
                           <th>TERMICA</th>  
                           <th>RECIBO</th>  
                           <th>FACTURA</th>  
                           <th>DETALLLE</th>  
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <th>TOTAL</th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                      </tfoot>
                  </table>
              </div>";   

              echo "<div class='ui modal'>
                      <i class='close icon'></i>
                      <div class='header header-modal'>
                        Profile Picture
                      </div>
                      <div class='content scrolling' id='content-modal'></div> 
               </div>";
      break; 

      case "ReporteEntradaSalida":
        $query = "SELECT id , nombre FROM producto";
        $data = $connection->query($query); 
        $results = $data->fetchAll(PDO::FETCH_BOTH ); 

        $params =  array
                   (
                       "default" => "Todos" ,  
                       "name" => "idproducto[]" , 
                       "id" => "idproducto" , 
                       "class" => "idproducto w100 chosen" ,
                       "value" => "", 
                       "multiple" => true, 
                       "results" => $results
                    );  

        $producto = $func->selectOption($params);


        echo "<div class='titulo'>REPORTE DE ENTRADAS Y SALIDA</div>"; 

        $data ="<form class='form ui' autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_reporte_entrada_salida' >
                   <div class='four fields'> 

                          <div class='field'>
                             <label>Fecha inicial</label>
                             <input type='text' class='form-control validar requerido texto date'  name='inicio' >
                          </div>
                         
                          <div class='field'>
                             <label>Fecha final</label>
                             <input type='text' class='form-control validar texto date'  name='fin' >
                          </div>  

                          <div class='field'>
                             <label>PRODUCTO</label>
                             $producto
                          </div> 
                     
                          <input type='hidden' name='opcion' value='$opcion'>
                          <input type='hidden' name='page' value='1'>
                                              
                          <div class='field'>
                              <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                <div class='visible content'>CONSULTAR</div>
                                <div class='hidden content'>
                                  <i class='right search icon'></i>
                                </div>
                              </div>
                          </div>  
                   </table>
                 </form>
                </div>";

         echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
              <div class='sixteen wide column'>
                 <table class='ui table table-reporte-entrada-salida'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>FECHA</th> 
                           <th>PRODUCTO</th> 
                           <th>ENTRADA</th>
                           <th>SALIDA</th>
                           <th>CONCEPTO</th> 
                           <th>USUARIO</th> 
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                      </tfoot>
                  </table>
              </div>";   

      break;

      case "paginar_reporte_entrada_salida":
           $connection->query("SET NAMES utf8"); 

           $query = "SELECT valor FROM configuracion WHERE name = 'HideBtn'";
           $data = $connection->query($query);  
           $results = $data->fetch(PDO::FETCH_ASSOC );

           if( !empty($results['valor']) )
              $hide = $results['valor']; 
           else
              $hide = 0;


           $condition = ""; 


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
           
           if( !empty($inicio) && !empty($fin) )
              $condition .= " AND DATE(entrada_salida.fecha) BETWEEN '$inicio' AND '$fin'";
           else
           {
              if( !empty($inicio) )
              $condition .= " AND DATE(entrada_salida.fecha) = '$inicio'"; 
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
                        ORDER BY entrada_salida.fecha ASC  "; 

           $data = $connection->query($query);  
           $resultsTotal = $data->fetchAll(PDO::FETCH_BOTH ); 
           $recordsTotal = count($resultsTotal);


           $query .= " LIMIT $start, $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );

          
           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results  
           ); 
           echo json_encode($json_data);
      break;

      case "MostrarAbonos": 
         $query = "SELECT id ,abono , fecha , firmado 
                   FROM abono_compra WHERE compra_id = $id";
         $data = $connection->query($query);  
         $results = $data->fetchAll(PDO::FETCH_ASSOC); 
         if( count($results) > 0 )
         {
             $form = "<table class='table table-sm' id='tabla' style='background:white; float:left;'> 
                   <thead>
                     <tr>
                        <th>FECHA</th> 
                        <th>VALOR</th>
                        <th>COMPROBANTE</th>  
                     </tr>
                   </thead>
                   <tbody>";
          foreach ($results as $row )
          { 
               $form.="<tr class='row_dinamic'> 
                         <td>$row[fecha]</td>
                         <td>$row[abono]</td>
                         <td><td><div id='content_print' data-title='COMPROBANTE DE PAGO' data-url='reports/firma.php' data-data='id=$row[id]' ></div> </td></td>
                          
                       </tr>";
          }
          $form.="</tbody>";
            
          $form.="</table>";

          echo $form;
         }
         else
         {
             echo $msg->warning("No se encontraron abonos para la compra seleccionada");
         }           
      break;


      case "ReporteEgresos": 

        echo "<div class='titulo'>REPORTE DE VENTAS</div>";
       
        $data ="<form class='ui form' autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_reporte_egresos' >
                  <div class='three fields'>

                      <div class='field'>
                         <label>Fecha inicial</label>
                         <input type='text' class='form-control validar texto date'  name='inicio' >
                      </div>

                      <div class='field'>
                         <label>Fecha final</label>
                         <input type='text' class='form-control validar texto date'  name='fin' >
                      </div>
                     
                      <input type='hidden' name='opcion' value='$opcion'>
                      <input type='hidden' name='page' value='1'>
                                          
                      <div class='field'>
                        <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                            <div class='visible content'>Consultar</div>
                            <div class='hidden content'>
                              <i class='right search icon'></i>
                            </div>
                        </div>
                      </div>  
                   </div>
                </form>";
         echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
              <div class='sixteen wide column'>
                 <table class='ui table table-reporte-egresos'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>FECHA</th> 
                           <th>CONCEPTO</th>
                           <th>TRABAJADOR</th>
                           <th>VALOR</th> 
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <th>TOTAL</th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                      </tfoot>
                  </table>
              </div>";   
      break;

      case "paginar_reporte_egresos":
           $condition = "";  

           if( !empty($inicio) && !empty($fin) )
              $condition .= " AND DATE(egreso.fecha) BETWEEN '$inicio' AND '$fin'";
           else
           {
              if( !empty($inicio) )
              $condition .= " AND DATE(egreso.fecha) = '$inicio'";
           }


           $query = "SELECT 
                           egreso.fecha , 
                           concepto , 
                           valor ,
                           concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador 
                      FROM egreso
                      INNER JOIN usuario ON usuario_id = usuario.id 
                      WHERE egreso.punto_id = $idpunto $condition";  
            
            $data = $connection->query($query);  
            $results = $data->fetch(PDO::FETCH_BOTH );
            $recordsTotal = count($results); 
            
            $query.= " LIMIT $start, $length";
            $data = $connection->query($query);  
            $results = $data->fetchAll(PDO::FETCH_BOTH);  

            $json_data = array
            (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results
            ); 
            echo json_encode($json_data);
      break;

      case "ReporteGastos": 

        echo "<div class='titulo'>REPORTE DE GASTOS</div>";
       
        $data ="<form class='ui form' autocomplete='off' action='get.php' method='post' enctype='multipart/form-data' data-response='content' data-paginate='true' data-opcion='paginar_reporte_gastos' >
                  <div class='three fields'>

                      <div class='field'>
                         <label>Fecha inicial</label>
                         <input type='text' class='form-control validar texto date'  name='inicio' >
                      </div>

                      <div class='field'>
                         <label>Fecha final</label>
                         <input type='text' class='form-control validar texto date'  name='fin' >
                      </div>
                     
                      <input type='hidden' name='opcion' value='$opcion'>
                      <input type='hidden' name='page' value='1'>
                                          
                      <div class='field'>
                        <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                            <div class='visible content'>Consultar</div>
                            <div class='hidden content'>
                              <i class='right search icon'></i>
                            </div>
                        </div>
                      </div>  
                   </div>
                </form>";
         echo "<div class='sixteen wide column'style='margin-bottom:10px;'>$data</div>
              <div class='sixteen wide column'>
                 <table class='ui table table-reporte-gastos'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>FECHA</th> 
                           <th>CONCEPTO</th>
                           <th>TRABAJADOR</th>
                           <th>VALOR</th> 
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <th>TOTAL</th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                      </tfoot>
                  </table>
              </div>";   
      break;
      
      case "paginar_reporte_gastos":
           $condition = "";  

           if( !empty($inicio) && !empty($fin) )
              $condition .= " AND DATE(gasto.fecha) BETWEEN '$inicio' AND '$fin'";
           else
           {
              if( !empty($inicio) )
              $condition .= " AND DATE(gasto.fecha) = '$inicio'";
           }


           $query = "SELECT 
                           gasto.fecha , 
                           concepto , 
                           valor ,
                           concat( usuario.nombre , ' ' , usuario.apellido) AS trabajador 
                          FROM gasto
                           INNER JOIN usuario ON usuario_id = usuario.id 
                          WHERE gasto.punto_id = $idpunto $condition 
                          ORDER BY gasto.fecha DESC";  
            
            $data = $connection->query($query);  
            $results = $data->fetch(PDO::FETCH_BOTH );
            $recordsTotal = count($results); 
            
            $query.= " LIMIT $start, $length";
            $data = $connection->query($query);  
            $results = $data->fetchAll(PDO::FETCH_BOTH);  


            $query = "SELECT 
                           sum( valor ) total
                          FROM gasto
                           INNER JOIN usuario ON usuario_id = usuario.id 
                          WHERE gasto.punto_id = $idpunto $condition 
                          ORDER BY gasto.fecha DESC";  
            
            $data = $connection->query($query);  
            $resultsTotal = $data->fetch(PDO::FETCH_BOTH );


            $json_data = array
            (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results ,
              "total"           => $resultsTotal['total']
            ); 
            echo json_encode($json_data);
      break; 

      case "menu_ventas": 
          echo "<div class='ui sixteen  centered grid'>
                    <form action='set.php' method='post' data-response='content'  data-paginate='false' autocomplete='off' >
                       <div class='ui form'>
                          <div class='field'>
                             <label>SELECCIONE LA FECHA</label>
                             <input type='text' class='form-control validar requerido texto date'  name='fecha' >
                          </div> 
                          <div class='field'>
                              <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:24px;'>
                                <div class='visible content'>REGISTRAR</div>
                                <div class='hidden content'>
                                  <i class='right search outline icon'></i>
                                </div>
                              </div>
                          </div>   
                          <input type='hidden' name='opcion' value='ConsultarPorDescontar'>
                    </form>
                  </div>
                </div>";
      break;


      case "PorTerminar": 
      if(empty($todos))
      $todos = "";

      $query = "SELECT count(*) count
                FROM  producto
                INNER JOIN producto_punto ON producto.id = producto_id
                WHERE punto_id = 1
                      AND producto_punto.cantidad <= producto.stock
                      AND producto_punto.estado = 1
                      AND producto.estado = 1 
                      AND producto.status_stock IN( 0 , 1 , 2 ) ";
          
       $data = $connection->query($query);  
       $results = $data->fetch(PDO::FETCH_BOTH );
       $count = $results['count'];

       echo  "<div class='sixteen wide column'>
                <div class='ui labeled button floated right' >
                  <div class='ui basic blue button link' data-paginate='true' data-opcion='PorTerminar' data-url='get.php' data-data='opcion=PorTerminar&todos=si' data-response='content' >
                    <i class='fork icon'></i> Ver todos
                  </div>
                  <a class='ui basic left pointing blue label'>
                    $count
                  </a> 
                </div> 
                 <form class='ui form' action='get.php' method='post' data-response='content'  data-opcion='PorTerminar' data-paginate='true' data-opcion='paginar_por_terminar' autocomplete='off' >
                      <div class='fields'>
                        <div class='field'>
                          <label>Desde</label>
                          <input type='text' class='form-control validar texto date'  name='inicio' >
                        </div>
                        <div class='field'>
                          <label>Hasta</label>
                          <input type='text' class='form-control validar texto date'  name='fin' >
                        </div>
                        <div class='field'>
                           <div class='ui animated button btn-enviar' tabindex='0' style='margin-top:18px;'>
                                <div class='visible content'>REGISTRAR</div>
                                <div class='hidden content'>
                                  <i class='right search outline icon'></i>
                                </div>
                              </div>
                        </div>
                      </div>
                    </div>
                    <input type='hidden' name='accion' value='PorTerminar' />
                  </form>
                </div>
              </div>
              <br> ";

       echo "<div class='sixteen wide column'>
                 <table class='ui table table-por-terminar'>
                      <thead class='thead-default'> 
                         <tr>
                           <th>PRODUCTO</th>
                           <th>CANTIDAD</th> 
                           <th>STOCK</th>
                           <th>COSTO</th> 
                           <th>PRECIO VENTA</th>   
                         </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot> 
                          <tr>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                      </tfoot>
                  </table>
                  <input type='hidden' id='todos' value='$todos' />
              </div>";   
           
      break;     

      case "paginar_por_terminar":
           $connection->query("SET NAMES utf8"); 

           //$condition = " AND  status_stock IN ( 2 ) ";
          /// if(!empty($todos))
           $condicion = " AND  status_stock IN ( 0 , 1 , 2 )  ";


           if( !empty($inicio) && !empty($fin) )
           {
              $condicion .= " AND fecha_terminado >= '$inicio' AND fecha_terminado <= '$fin' "; 

           }
           else if( !empty($inicio) )
           {
              $condicion .= " AND fecha_terminado = '$inicio' ";  
           }





           
           $query = "SELECT producto.id , nombre , cantidad , stock , precio , costo , producto_id
                          FROM  producto
                          INNER JOIN producto_punto ON producto.id = producto_id
                     WHERE punto_id = 1
                      AND producto_punto.cantidad <= producto.stock
                      AND producto_punto.estado = 1
                      AND producto.estado = 1 
                      $condicion 
                      ORDER BY nombre ASC";

           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH );
           $recordsTotal = count($results); 

            
           $query.= " LIMIT $start, $length";
           $data = $connection->query($query);  
           $results = $data->fetchAll(PDO::FETCH_BOTH); 

           /*
           $query = "UPDATE producto ,  producto_punto 
                     SET status_stock = 1
                     WHERE producto.estado = 1 
                     AND producto.id = producto_id 
                     AND producto_punto.cantidad <= producto.stock   ";
           $connection->query($query);
           $func->WriteQuery($connection , $query); 
           */         

           $json_data = array
           (
              "draw"            => intval( $draw ), 
              "recordsTotal"    => intval( $recordsTotal ), 
              "recordsFiltered" => intval( $recordsTotal ), 
              "data"            => $results
           ); 
           echo json_encode($json_data); 
    break;

   } 
}
function tablaProductosComprarTerminar( $connection , $idproductos , $idpunto )
{ 
   $ids = implode(",", $idproductos ); 
   $query = "SELECT 
                   producto.nombre ,
                   producto_punto.producto_id  ,
                   producto_punto.cantidad ,
                   producto_punto.precio ,
                   producto_punto.costo ,
                   producto_punto.precio_minimo ,
                   producto.stock
               FROM  producto_punto 
               INNER JOIN producto ON producto.id = producto_id 
               WHERE punto_id = $idpunto  AND producto_punto.cantidad <= producto.stock AND producto.id IN ($ids)";
          
     $data = $connection->query($query);  
     $results = $data->fetchAll(PDO::FETCH_ASSOC);  
     if( count($results) > 0 )
     {

            $form = "<table class='table' id='tabla' style='background:white; float:left;'> 
                     <thead>
                       <tr>
                          <th>Producto</th> 
                          <th>Cantidad</th>
                          <th>Costo</th> 
                          <th>Precio</th> 
                          <th>Precio minimo</th> 
                          <th>Eliminar</th>
                       </tr>
                     </thead>
                     <tbody>";
            foreach ($results as $row )
            { 
                 $form.="<tr class='row_dinamic'>
                           <input type='hidden' name='idproducto[]' value='$row[producto_id]' />
                           <td>$row[nombre]</td>
                           <td><input type='text' name='cantidad[]' class='form-control cantidad validar requerido numero' data-min='1'  /></td>
                           <td>
                               <input type='text' name='costo[]'  value='$row[costo]' class='form-control costo required validar requerido numero' data-min='1' />
                           </td>
                           <td>
                               <input type='text' name='precio[]' value='$row[precio]' class='form-control precio validar requerido numero'  data-min='1' />
                           </td>
                           <td>
                               <input type='text' name='preciominimo[]' value='$row[precio_minimo]' class='form-control  validar requerido numero'  data-min='1' />
                           </td>
                           <td>
                               <div class='delete-photo eliminarProducto' data-idproducto='$row[producto_id]'></div>
                           </td>
                         </tr>";
            }
            $form.="</tbody>";
              
            $form.="<tfoot>
                        <tr>
                          <td colspan='7' class='text-right'>
                             <input type='submit' class='btn btn-primary btn-save' value='REGISTRAR' />
                          </td>
                        </tr>
                    </tfoot>
                    </table>";
     }                
     return $form;
}
function getDia($month,$year)
{
        switch($month)
        {
             case 1:
                return 31;
             break;
             
             case 2:
                if($year % 2 == 0)
                return 29;
                else
                return 28;
             break;
             
             case 3:
                return 31;
             break;
             
             case 4:
                return 30;
             break;
             
             case 5:
                return 31;
             break;
             
             case 6:
                return 30;
             break;
             
             case 7:
                return 31;
             break;
             
             case 8:
                return 31;
             break;
             
             case 9:
                return 30;
             break;
             
             case 10:
                return 31;
             break;
             
             
             case 11:
                return 30;
             break;
             
             
             case 12:
                return 31;
             break;
              
        } 
}
function getListaProductos( $buscar , $connection , $msg  )
{ 
     $condicion = "";
     $buscar = trim($buscar);
     if( !empty($buscar) )
     { 
         $condicion = " AND nombre LIKE '%$buscar%'";
     }


     $query = "SELECT * 
                    FROM producto
                    WHERE estado = 'activo'  $condicion
                    GROUP BY (id)
                    ORDER BY (nombre) ASC";

      $data = $connection->query($query);  
      $results = $data->fetchAll(PDO::FETCH_BOTH ); 


 
      if( count( $results) > 0 ) 
      {
            $tabla = "<table class='table table-condensed' >
                         <thead>
                            <tr class='header'>
                               <th>Editar</th>
                               <th>Borrar</th>
                               <th>Nombre</th>
                               <th>Costo</th>                            
                               <th>Precio</th>                            
                               <th>Stock</th>
                               <th>Cantidad</th>                           
                               <th>Ubicación</th>                           
                               <th>Total</th> 
                            </tr>
                         </thead>
                         <tbody>";
          
             $total_pre_venta = 0;
             $total_val_ivent = 0;
             $total_val_venta = 0;
             
             foreach( $results as $fila )
             {
                 
                     $vr_invent = $fila['precio'] * $fila['cantidad'];
                      
                     $total_val_ivent+=$vr_invent;                        
                      
                     $vr_invent = number_format($vr_invent,0,"",".");
                     $fila['costo'] = number_format( $fila['costo'],0,"",".");
                     $fila['precio'] = number_format( $fila['precio'],0,"",".");

                     $tabla.= "<tr>                                
                                  <td><div class='link Edit-32' data-respuesta='contenido' data-url='getdata.php' data-data='opcion=editarProducto&id=$fila[id]'></div></td>
                                  <td><div class='link borrar delete-photo' data-respuesta='contenido' data-url='setdata.php' data-data='opcion=borrarProducto&id=$fila[id]'></div></td>
                                   <td>$fila[nombre]</td>
                                  <td>$ $fila[costo]</td>
                                  <td>$ $fila[precio]</td> 
                                  <td>$fila[stock_min]</td> 
                                  <td>$fila[cantidad]</td> 
                                  <td>Cajon $fila[ubicacion]</td> 
                                  <td>$ $vr_invent</td>   
                               </tr>"; 
             }
              
             $total_val_ivent  = number_format($total_val_ivent,0,"",".");
              
              $tabla.= "</tbody>
                          <tfoot> 
                              <tr>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                                   <td></td>
                              </tr>
                          </tfoot>";
              $tabla.= "</table>";
              

              return $tabla;

      } 
      else
      {
          return $msg->danger("No se encontraron productos");
      } 
}
function GetPunto( $idusuario , $connection , $msg )
{
     $query = "SELECT punto.id
               FROM punto 
               INNER JOIN usuario_punto ON punto.id = punto_id 
               WHERE usuario_id = $idusuario ";

              $data = $connection->query($query); 
              $results = $data->fetchAll(PDO::FETCH_BOTH ); 
     if( count($results) > 0 )
     {
         $row = $results[0]; 
         return $row['id'];
     } 
     return null; 
}
function GET_SALDO_CAJA( $connection , $fecha )
{   
    $query = "SELECT valor FROM caja WHERE fecha = '$fecha'";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results['valor'];
    } 
    return 0; 
}
function GET_VENTAS_CONTADO( $connection , $fecha )
{   
    $query = "SELECT SUM( cantidad * precio ) valor
              FROM venta 
              INNER JOIN producto_venta ON venta.id = venta_id 
              WHERE DATE(fecha) = '$fecha' AND tipo_venta_id = 1";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results['valor'];
    } 
    return 0; 
}
function GET_VENTAS_CREDITO_ABONOS( $connection , $fecha )
{   
    $query = "SELECT SUM( abono ) valor
              FROM abono_venta 
              INNER JOIN venta ON venta.id = venta_id 
              WHERE DATE(venta.fecha) = '$fecha' AND tipo_venta_id >= 2";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results['valor'];
    } 
    return 0; 
} 
function GET_VENTAS_RESUMEN_TOTAL( $connection , $fecha )
{   
    $query = "SELECT SUM( cantidad * precio ) precios , SUM( cantidad * costo ) costos 
              FROM venta 
              INNER JOIN producto_venta ON venta.id = venta_id 
              WHERE DATE(fecha) = '$fecha'";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results;
    } 
    return 0; 
} 
function GET_COMPRAS_CONTADO( $connection , $fecha )
{   
    $query = "SELECT SUM( cantidad * precio ) valor
              FROM compra 
              INNER JOIN producto_compra ON compra.id = compra_id 
              WHERE DATE(fecha) = '$fecha' AND tipo_compra_id = 1";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results['valor'];
    } 
    return 0; 
}
function GET_COMPRAS_CREDITO_ABONOS( $connection , $fecha )
{   
    $query = "SELECT SUM( abono ) valor
              FROM abono_compra 
              INNER JOIN compra ON compra.id = compra_id 
              WHERE DATE(compra.fecha) = '$fecha' AND tipo_compra_id >= 2";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results['valor'];
    } 
    return 0; 
}
function GET_EGRESOS( $connection , $fecha )
{   
    $query = "SELECT SUM( valor ) valor
              FROM egreso  
              WHERE DATE(fecha) = '$fecha'";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results['valor'];
    } 
    return 0; 
}
function GET_GASTOS( $connection , $fecha )
{   
    $query = "SELECT SUM( valor ) valor
              FROM gasto  
              WHERE DATE(fecha) = '$fecha'";
    $data = $connection->query($query); 
    $results = $data->fetch(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         return $results['valor'];
    } 
    return 0; 
} 
function GET_ALL_COTIZACIONES( $connection , $fecha , $idcliente , $idpunto , $func )
{     
    $tabla = "";
    $query = "SELECT 
                cotizacion.id,
                cotizacion.fecha , 
                concat( cliente.nombres , ' ' , cliente.apellidos ) AS cliente,
                concat( usuario.nombre , ' ' , usuario.apellido ) AS trabajador  ,
                SUM( producto_cotizacion.cantidad ) productos               
              FROM cotizacion 
                INNER JOIN usuario ON  usuario.id = cotizacion.usuario_id  
                INNER JOIN producto_cotizacion ON cotizacion_id = cotizacion.id
                LEFT JOIN cliente ON cliente.id = cotizacion.cliente_id
              GROUP BY( cotizacion.id)";
    $data = $connection->query($query); 
    $results = $data->fetchAll(PDO::FETCH_BOTH ); 
    if( count($results) > 0 )
    { 
         $tabla.= "<table class='table table-bordered'>";
         $tabla.= "<thead>
                        <tr style='background:#cecece !important;'>
                          <td >FECHA</td>
                          <td>CLIENTE</td>
                          <td>TRABAJADOR</td>
                          <td>PRODUCTOS</td> 
                        </tr>
                    </thead> ";
          foreach ($results as $fila ) 
          {
              $tabla.= "<tr class='ExpandirProductoCotizacion' data-cotizacionid='$fila[id]'>
                          <td>$fila[fecha]</td>
                          <td>$fila[cliente]</td>
                          <td>$fila[trabajador]</td>
                          <td>$fila[productos]</td>
                        </tr>
                        <tr>
                          <td colspan='4'>
                           <p> 
                           </p>
                          </td>
                        </tr>";
          }
          $tabla.="</table>";
    } 
    return $tabla;
}
?>