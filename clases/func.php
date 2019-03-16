<?php
  class func
  { 
      function __construct()
      { 
      }
      function Entrada( $connection , $valor , $concepto , $producto_id , $usuario_id , $tipo )
      {
           $query = "INSERT INTO entrada_salida VALUES(NULL,$valor,0,'$concepto',current_timestamp , $producto_id , $usuario_id )";
           $connection->query($query);
           if( $tipo == 1 )
             $this->WriteQuery( $connection , $query );
      }
      function Salida( $connection , $valor , $concepto , $producto_id , $usuario_id , $tipo )
      {
           $query = "INSERT INTO entrada_salida VALUES(NULL,0,$valor,'$concepto',current_timestamp , $producto_id , $usuario_id )";
           $connection->query($query);
           if( $tipo == 1 )
             $this->WriteQuery( $connection , $query );
      }
      function WriteQuery($connection , $query_text  )
      {
           $query_text = str_replace(array("'"), '"', $query_text);
           $query_text = str_replace(array("\n"), " ", $query_text);
           $query = "INSERT INTO query_log VALUES(NULL,'$query_text',current_timestamp,false)";
           $connection->query($query);
      } 
      function WriteQueryCompraVenta($connection , $query_text , $tipo )
      {
           $query_text = str_replace(array("'"), '"', $query_text);
           $query_text = str_replace(array("\n"), " ", $query_text);
           $query = "INSERT INTO query_log_compra_venta VALUES(NULL,$tipo,'$query_text',current_timestamp,false,'')";
           $connection->query($query);
           $id = $connection->getLastId();
           return $id;
      }
	  function SelectOption( $params )
	  {   
          $multiple = "";
          if( !empty( $params['multiple'] ) )
            $multiple = "multiple='multiple'";

          $select=""; 
          $select="<select name='$params[name]' id='$params[id]'  class='$params[class] form-control' $multiple >";  
           
          if( !empty( $params['default'] ) )
            $select.="<option value='' >$params[default]</option>"; 
          
            
          foreach ($params['results'] as $result ) 
          { 
            	//$result[1] = utf8_encode($result[1]); 
              if( strlen($params['value']) > 0 )
              {
                  if( $params['value'] == $result[0])
				            $select.="<option value='$result[0]' selected >$result[1]</option>"; 
                  else
                    $select.="<option value='$result[0]'  >$result[1]</option>";  
              }
              else
              {
				          $select.="<option value='$result[0]'  >$result[1]</option>";  
              }
          }       
	        $select.="</select>";

			    return $select;
		  }
      function Pagination( $params )
      {
            if( empty($params['page'] ) )
                $params['page'] = 1;

            $form = "empty"; 
            if($params['page'])
            {
                $page = $params['page'];
                $cur_page = $page;
                $page -= 1;
                $previous_btn = true;
                $next_btn = true;
                $first_btn = true;
                $last_btn = true;
                $start = $page * $params['per_page']; 
                 
          
                if( count( $params['results'] ) > 0  )
                {  
                    $form = "<table class='table  table-bordered' id='tablePagination' ><thead>"; 
                    foreach( $params['columns'] as $column )
                    {                    
                        $form.="<th>$column</th>";
                    }    
                    $form.="</tr></thead><tbody>";     
                
                    if( $params['custom'] == true )
                    {
                        //var_dump($params);   
 
                        foreach( $params['results'] as $fila )
                        {  
                            if( $params['source_row']  == 'inventario' )
                            {              

                                $form.= $this->GetRow( $params['source_row'] , array( $fila ) ); 
                            }
                            else
                            {
                                $form.= $this->GetRow( $params['source_row'] , $fila );
                            }
                        }
                        if( $params['last_row'] != null )
                        {
                            if( $params['source_row']  == 'inventario' )
                            {
                                $form.= $this->GetRowLast( $params['source_row'] , array( $params['last_row'] ) );   
                            }
                            else
                            {
                                $form.= $this->GetRowLast( $params['source_row'] ,$params['last_row'] );   
                            }
                        }
                    } 
                    else
                    {
                        foreach( $params['results'] as $filas )
                        { 
                            $form.="<tr>";
                            
                            for ($i=0; $i < count($params['columns']); $i++) 
                            { 
                               $form.="<td>$filas[$i]</td>"; 
                            }
                            
                            $form.="</tr>";
                        }
                    }
                    $form .= "</table>";  
                }  
                $no_of_paginations = ceil($params['count'] / $params['per_page'] );
                if ($cur_page >= 7) 
                {
                        $start_loop = $cur_page - 3;
                        if ($no_of_paginations > $cur_page + 3)
                            $end_loop = $cur_page + 3;
                        else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) 
                        {
                            $start_loop = $no_of_paginations - 6;
                            $end_loop = $no_of_paginations;
                        } 
                        else 
                        {
                            $end_loop = $no_of_paginations;
                        }
                } 
                else 
                {
                        $start_loop = 1;
                        if ($no_of_paginations > 7)
                            $end_loop = 7;
                        else
                            $end_loop = $no_of_paginations;
                }

                $pagination = "<nav aria-label='pagination'> 
                                   <ul class='pagination pagination-lg'>"; 
                
                if ($first_btn && $cur_page > 1) 
                {
                     $pagination .= "<li data-page='1' class='waves-effect page-item'><span class='page-link'>Primero</span></li>";
                } 
                else if ($first_btn) 
                {
                    $pagination .= "<li data-page='1' class='page-item disabled'><span class='page-link'>Primero</span></li>";
                } 
                // FOR ENABLING THE PREVIOUS BUTTON
                if ($previous_btn && $cur_page > 1) 
                {
                    $pre = $cur_page - 1;
                    $pagination .= "<li data-page='$pre' class='page-item'>
                                        <a class='page-link' href='#'' aria-label='Previous'>
                                            <span aria-hidden='true'>&laquo;</span>
                                            <span class='sr-only'>Previous</span>
                                        </a>
                                    </li>";
                } 
                else if ($previous_btn) 
                {
                    $pagination .= "<li  class='page-item disabled'> 
                                         <a class='page-link' href='#'' aria-label='Previous'>
                                            <span aria-hidden='true'>&laquo;</span>
                                            <span class='sr-only'>Previous</span>
                                        </a>
                                    </li>";
                }
                for ($i = $start_loop; $i <= $end_loop; $i++) 
                {
                
                    if ($cur_page == $i)
                        $pagination .= "<li data-page='$i'  class='page-item active'><span class='page-link' href='#'>{$i}</span></li>";
                    else
                        $pagination .= "<li data-page='$i' class='page-item'><span class='page-link' href='#'>{$i}</span></li>"; 
                } 
                // TO ENABLE THE NEXT BUTTON
                if ($next_btn && $cur_page < $no_of_paginations) 
                {
                    $nex = $cur_page + 1;
                    $pagination .= "<li data-page='$nex' class='page-item'>
                                       <a class='page-link' href='#'' aria-label='Next'>
                                            <span aria-hidden='true'>&raquo;</span>
                                            <span class='sr-only'>Next</span>
                                       </a>
                                    </li>";
                } 
                else if ($next_btn) 
                {    
                    $pagination .= "<li class='page-item disabled'>
                                      <a class='page-link' href='#'' aria-label='Next'>
                                        <span aria-hidden='true'>&raquo;</span>
                                        <span class='sr-only'>Next</span>
                                      </a>
                                    </li>";
                } 
                if ($last_btn && $cur_page < $no_of_paginations) 
                {
                    $pagination .= "<li data-page='$no_of_paginations' class='page-item'><span class='page-link'>Ultimo</span></li>";
                } 
                else if ($last_btn) 
                {
                    $pagination .= "<li data-page='$no_of_paginations' class='page-item disabled'><span class='page-link'>Ultimo</span></li>";
                }
                $goto = "<input type='text' class='goto' size='1' style='margin-top:-1px;margin-left:60px;'/><input type='button' id='go_btn' class='go_button' value='Go'/>";
                $total_string = "<span class='total' a='$no_of_paginations'>Page <b>" . $cur_page . "</b> of <b>$no_of_paginations</b></span>";
                $pagination .= "</ul></nav>"; 

                $goto_to_page = "<div style='float:left;'>
                                    <input type='text' class='goto form-control' id='goto' style='width:100px;float:left;height:50px;'  />
                                 </div>
                                 <div style='width:50%;float:left;'>
                                      <button type='button' id='go_btn' class='go_button btn btn-primary' style='height:50px;' >Ir</button>
                                 </div> ";
                $number_pages = "<span style='float: right; margin-top:15px;' class='totalPagination' a='$no_of_paginations'>Pagina <b>" . $cur_page . "</b> de <b>$no_of_paginations</b></span>";


                return "<div style='width:100%; float: left;'>
                            <div style='width:20%; float: left;'>$goto_to_page</div>
                            <div style='width:20%; float: right;'>$number_pages</div>   
                            <div style='width:100%; float: left;'>$form</div>
                            <div style='width:60%; float: left;'>$pagination</div>
                        </div>";
                
             
                $form.= "<input type='hidden' value='$no_of_paginations' id='numeroPaginas'>";   
            }  
            return $form; 
      }
      function GetRow( $source_row , $fila )
      {
          switch ($source_row) 
          {  
              case 'inventario':
 
                  $total_precio = $fila[0]['precio'] * $fila[0]['cantidad'];
                  $total_costo = $fila[0]['costo'] * $fila[0]['cantidad'];

                  $total_precio1 = $fila[0]['pre'] * $fila[0]['cnt'];
                  $total_costo1 = $fila[0]['cost'] * $fila[0]['cnt']; 
 
                  $costo = $this->format( $fila[0]['costo'] );
                  $precio = $this->format( $fila[0]['precio'] );
                  $costo = $this->format( $fila[0]['costo'] );

                  

                  $nombre = $fila[0]['nombre'];
                  $cantidad = $fila[0]['cantidad']; 
                  $stock = $fila[0]['stock']; 

                  $cnt = $this->format( $fila[0]['cnt'] );
                  $cost = $this->format( $fila[0]['cost'] );
                  $pre = $this->format( $fila[0]['pre'] );

                  $total_precio = $this->format( $total_precio ); 
                  $total_costo = $this->format( $total_costo ); 
                  $total_precio1 = $this->format( $total_precio1 ); 
                  $total_costo1 = $this->format( $total_costo1 );  


                  $row ="<tr>
                           <td>$nombre</td> 
                           <!-- 
                           <td class='table-success'>$cnt</td>
                           <td class='table-success'>$cost</td>
                           <td class='table-success'>$pre</td>
                           <td class='table-success'>$total_costo1</td>
                           <td class='table-success'>$total_precio1</td>
                           -->
                           <td>$stock</td>

                           <td>$cantidad</td>
                           <td>$costo</td>
                           <td>$precio</td>
                           <td>$total_costo</td>
                           <td>$total_precio</td>
                        </tr>";

                  return $row;
              break;

              case 'PorTerminar':  
                   $nombre = $fila['nombre'];
                  $row ="<tr>
                           <td>$nombre</td>                            
                           <td>$fila[cantidad]</td>  
                           <td>$fila[stock]</td>  
                           <td>$fila[costo]</td>    
                           <td>$fila[precio]</td>    
 
                        </tr>";

                  return $row;
              break; 


              case 'consultar_catalogo':  
                  $ruta = "http://ferregomezjp.com.co/catalogo/".$fila['folder']."/".$fila['imagen'];
                  $row ="<tr>
                           <td>$fila[producto]</td>  
                           <td><img src='$ruta' style='width:48px;height:48px;' /></td>  
                           <td><div class='link delete_product' data-url='set.php' data-title='BORRAR PRODUCTO' data-response='content'  data-data='opcion=EliminarCatalogo&id=$fila[id]' ></div></td>
                            
                        </tr>";

                  return $row;
              break; 

              case 'consultar_marca':  
                  $ruta = "http://ferregomezjp.com.co/marcas/".$fila['imagen'];
                  $row ="<tr>
                           <td><img src='$ruta' style='width:48px;height:48px;' /></td>  
                           <td><div class='link delete_product' data-url='set.php' data-title='BORRAR PRODUCTO' data-response='content'  data-data='opcion=EliminarMarca&id=$fila[id]' ></div></td>
                            
                        </tr>";

                  return $row;
              break; 

              case 'productos':  
                  $precio = $this->format( $fila['precio'] );  
                  $row ="<tr>
                           <td><div class='link poput  edit_product' data-url='get.php' data-title='EDITAR PRODUCTO' data-data='opcion=EditarProducto&id=$fila[id]' ></div></td>
                           <td><div class='link delete_product' data-url='set.php' data-title='BORRAR PRODUCTO' data-response='content'  data-data='opcion=BorrarProducto&id=$fila[id]' ></div></td>
                           <td>$fila[nombre]</td> 
                           <td>$fila[cantidad]</td>
                           <td>$fila[stock]</td>
                           <td>$ $precio</td>
                        </tr>";

                  return $row;
              break; 


              case 'ReporteCantidadVendida':   
                  $row ="<tr>
                          
                           <td>$fila[nombre]</td> 
                           <td>$fila[cantidad]</td>
                        </tr>";

                  return $row;
              break; 

              case 'clientes': 
                  if( $fila['estado'] == true )
                  {
                     $estado = "<div class='active_user link' data-url='set.php' data-response='content' data-data='opcion=InactivarCliente&id=$fila[id]' ></div>";
                  }
                  else
                  {
                     $estado = "<div class='inactive_user link' data-url='set.php'  data-response='content' data-data='opcion=ActivarCliente&id=$fila[id]' ></div>";                     
                  } 
                  $row ="<tr>
                           <td>$fila[nombre]</td> 
                           <td>$fila[documento]</td>
                           <td>$fila[telefono]</td>
                           <td>$fila[fecha]</td>
                           <td><div class='link poput edit_user' data-url='get.php' data-title='EDITAR CLIENTE' data-data='opcion=EditarCliente&idcliente=$fila[id]' ></div></td>
                           <td>$estado</td>
                        </tr>";

                  return $row;
              break;

              case 'DevolucionVenta': 
                   
                  $fila['usuario'] = utf8_encode($fila['usuario']);
                  $fila['cliente'] = utf8_encode($fila['cliente']);
                  $row ="<tr data-ventaid='$fila[id]' class='ExpandirVenta'>
                           <td  data-ventaid='$fila[id]'>$fila[fecha]</td> 
                           <td  data-ventaid='$fila[id]'>$fila[usuario]</td>
                           <td  data-ventaid='$fila[id]'>$fila[cliente]</td>
                           <td  data-ventaid='$fila[id]'>$fila[count]</td>
                           <td  data-ventaid='$fila[id]'><div class='link poput edit_user' data-url='get.php' data-title='EDITAR CLIENTE' data-data='opcion=venta_id&id=$fila[id]' ></div></td>
                        </tr>

                        <tr>
                          <td colspan='5'>
                           <p>
                               Blah blah blah blah blah blah blah blah blah blah blah
                               blah blah blah blah blah blah blah blah blah blah blah blah blah blah
                               blah blah blah blah blah blah blah blah blah blah blah blah blah blah.
                           </p>
                          </td>
                        </tr>";

                  return $row;
              break;

           

              case 'proveedores': 
                  if( $fila['estado'] == true )
                  {
                     $estado = "<div class='active_user link' data-url='set.php' data-response='content' data-data='opcion=InactivarProveedor&id=$fila[id]' ></div>";
                  }
                  else
                  {
                     $estado = "<div class='inactive_user link' data-url='set.php'  data-response='content' data-data='opcion=ActivarProveedor&id=$fila[id]' ></div>";                     
                  } 
                  $row ="<tr>
                           <td>$fila[nombre]</td> 
                           <td>$fila[documento]</td>
                           <td>$fila[telefono]</td>
                           <td>$fila[fecha]</td>
                           <td><div class='link poput edit_user' data-url='get.php' data-title='EDITAR PROVEEDOR' data-data='opcion=EditarProveedor&idproveedor=$fila[id]' ></div></td>
                           <td>$estado</td>
                        </tr>";

                  return $row;
              break;

              case 'trabajadores': 
                  if( $fila['status'] == true )
                  {
                     $estado = "<div class='active_user link' data-url='set.php' data-response='content' data-data='opcion=InactivarTrabajador&id=$fila[id]' ></div>";
                  }
                  else
                  {
                     $estado = "<div class='inactive_user link' data-url='set.php'  data-response='content' data-data='opcion=ActivarTrabajador&id=$fila[id]' ></div>";                     
                  } 
                  $row ="<tr>
                           <td>$fila[nombre]</td> 
                           <td>$fila[apellido]</td>
                           <td>$fila[cedula]</td>                           
                           <td>$fila[telefono]</td>
                           <td><div class='link poput edit_user' data-url='get.php' data-title='EDITAR TRABAJADOR' data-data='opcion=EditarTrabajador&idtrabajador=$fila[id]' ></div></td>
                           <td>$estado</td>
                        </tr>";

                  return $row;
              break;

              case 'puntos': 
                  if( $fila['estado'] == true )
                  {
                     $estado = "<div class='active_sucursal link' data-url='set.php' data-response='content' data-data='opcion=InactivarSucursal&id=$fila[id]' ></div>";
                  }
                  else
                  {
                     $estado = "<div class='inactive_sucursal link' data-url='set.php'  data-response='content' data-data='opcion=ActivarSucursal&id=$fila[id]' ></div>";                     
                  }
                  $row ="<tr>
                           <td>$fila[punto]</td> 
                           <td>$fila[fecha]</td>
                           <td><div class='link poput edit_user' data-url='get.php' data-title='EDITAR SUCURSAL' data-data='opcion=EditarSucursal&idpunto=$fila[id]' ></div></td>
                           <td>$estado</td>
                        </tr>";

                  return $row;
              break;

              case 'resolucion': 
                  if( !$fila['empezado'] )
                  {
                      $editar = "<div class='link poput edit' data-url='get.php' data-title='EDITAR FACTURA' data-data='opcion=EditarFactura&id=$fila[id]' ></div>";
                  } 
                  else
                  {
                      $editar = "";
                  }
                  $row ="<tr>
                           <td>$fila[inicio]</td> 
                           <td>$fila[fin]</td>
                           <td>$fila[prefijo]</td>
                           <td>$fila[fecha]</td>
                           <td>$editar</td>
                        </tr>";

                  return $row;
              break;

              case 'tipoclientes': 
                  if( $fila['status'] == true )
                  {
                     $estado = "<div class='active_sucursal link' data-url='set.php' data-response='content' data-data='opcion=InactivarSucursal&id=$fila[id]' ></div>";
                  }
                  else
                  {
                     $estado = "<div class='inactive_sucursal link' data-url='set.php'  data-response='content' data-data='opcion=ActivarSucursal&id=$fila[id]' ></div>";                     
                  }
                  $row ="<tr>
                           <td>$fila[tipo]</td> 
                           <td><div class='link poput edit_user' data-url='get.php' data-title='EDITAR SUCURSAL' data-data='opcion=EditarSucursal&idpunto=$fila[id]' ></div></td>
                           <td>$estado</td>
                        </tr>";

                  return $row;
              break;

              case 'ReporteCompras':  
                  $fila['total'] = $this->format($fila['total']); 

                  $row ="<tr>
                           <td>$fila[fecha]</td> 
                           <td>$fila[trabajador]</td>
                           <td>$fila[factura]</td>
                           <td>$fila[tipo]</td>
                           <td>$fila[proveedor]</td>
                           <td>$ $fila[total]</td>
                           <td><div class='detalle-compra detalle'  data-titulo='PRODUCTOS DE LA FACTURA' data-page='get.php' data-data='opcion=ConsultarProductoCompra&id=$fila[id]'></div></td>
                        </tr>";

                  return $row;
              break;

              case 'ReporteVentas': 
                  $fila['precio'] = $this->format($fila['precio']); 
                  $fila['total'] = $this->format($fila['total']); 


                  $fila['trabajador'] = utf8_decode($fila['trabajador']);                  
                  $fila['cliente'] = utf8_decode($fila['cliente']);                  
                  
                  if( $fila['checked'] == true )
                     $checked = "checked = 'true' ";
                  else
                     $checked = "";

                   $tdHideShow = "";
                   if( $fila['hide'] == 1 )
                   {
                       $tdHideShow = "<td> 
                                       <label class='ios7-switch'>
                                          <input type='checkbox' class='venta-oculta' data-id='$fila[id]' $checked >
                                          <span></span>
                                      </label> 
                                     </td> ";
                   }

                  $row ="<tr>
                           <td>$fila[fecha]</td> 
                           <td>$fila[producto]</td>
                           <td>$fila[trabajador]</td>
                           <td>$fila[cliente]</td>
                           <td>$fila[recibo]</td>
                           <td>$fila[tipo]</td>
                           <td>$fila[cantidad]</td>
                           <td>$fila[precio]</td>
                           <td>$fila[total]</td>
                           <td><div id='content_print_termica' data-url='imprimir_termica.php' data-data='opcion=imprimir_venta&id=$fila[id]' ></div> </td>
                           <td><div id='content_print' data-title='VENTAS' data-url='reports/Facturas.php' data-data='id=$fila[id]' ></div> </td>
                           <td><div id='content_print' data-title='RECIBO' data-url='reports/Recibos.php' data-data='id=$fila[id]' ></div> </td>
                        </tr>";

                  return $row;
              break;

              case 'ReporteCuentasPorPagar': 
                  $fila['total'] = $this->format($fila['total']); 
                  if( $fila['tipo_compra_id'] == 3 )
                     $tipo = '<div class="pagado"></div>';
                  else
                     $tipo = '<div class="sin_pagar"></div>';

                  $fila['trabajador'] = utf8_decode($fila['trabajador']);
                  $fila['trabajador'] = utf8_decode($fila['trabajador']);

                  
                  $row ="<tr>
                           <td>$tipo</td> 
                           <td>$fila[fecha]</td> 
                           <td>$fila[factura]</td>
                           <td>$fila[trabajador]</td>
                           <td>$fila[proveedor]</td> 
                           <td>$ $fila[total]</td>
                           <td><div class='search detalle' data-titulo='ABONOS' data-page='get.php' data-data='opcion=MostrarAbonos&id=$fila[id]' aria-hidden='true'></div></td>
                        </tr>";

                  return $row;
              break;

              case 'PorPagar': 
                 
                  $fila['total'] = $this->format($fila['total']); 
                  
                  $fila['proveedor'] = utf8_decode($fila['proveedor']);

                  if( $fila['tipo_compra_id'] == 2 )
                  {
                      $abonar = "<div class='abonar' data-row='compra$fila[id]' data-response='modal-body' data-url='get.php' data-data='opcion=AbonarPorPagar&id=$fila[id]'  ></div>";
                  }
                  else
                    $abonar = "";


                  $row ="<tr id='compra$fila[id]'>
                           <td><div class='search detalle' data-titulo='DETALLE DE LA CUENTA POR COBRAR' data-page='get.php' data-data='opcion=DetallePorPagar&id=$fila[id]' aria-hidden='true'></div></td>
                           <td>$abonar</td>
                           <td>$fila[fecha]</td> 
                           <td>$fila[proveedor]</td>
                           <td>$fila[cantidad]</td>
                           <td>$fila[total]</td>  
                        </tr>";

                  return $row;
              break;

              case 'PorCobrar': 
                 
                  $fila['total'] = $this->format($fila['total']); 
                  
                  $fila['cliente'] = utf8_decode($fila['cliente']);

                  if( $fila['tipo_venta_id'] == 2 )
                  {
                     $abonar = "<div class='abonar' data-response='content' data-url='get.php' data-data='opcion=AbonarPorCobrar&id=$fila[id]' ></div>";
                  }
                  else
                  {
                     $abonar = "";
                  }


                  $row ="<tr>
                           <td><div class='search detalle' data-titulo='DETALLE DE LA CUENTA POR PAGAR' data-page='get.php' data-data='opcion=DetallePorCobrar&id=$fila[id]' ></div></td> 
                           <td>$abonar</td> 
                           <td>$fila[fecha]</td> 
                           <td>$fila[cliente]</td>
                           <td>$fila[cantidad]</td>
                           <td>$fila[total]</td>  
                        </tr>";

                  return $row;
              break;

              case 'ReporteCuentasPorCobrar': 
                  $fila['total'] = $this->format($fila['total']); 
                  if( $fila['tipo_id'] == 3 )
                     $tipo = '<div class="pagado"></div>';
                  else
                     $tipo = '<div class="sin_pagar"></div>';


                  $row ="<tr>
                           <td>$tipo</td> 
                           <td>$fila[fecha]</td> 
                           <td>$fila[recibo]</td>
                           <td>$fila[trabajador]</td>
                           <td>$fila[cliente]</td> 
                           <td>$ $fila[total]</td>
                        </tr>";

                  return $row;
              break;
         
              case 'ReporteEgresos':   
                  $fila['valor'] = $this->format($fila['valor']); 
                  $row ="<tr>
                           <td>$fila[fecha]</td> 
                           <td>$fila[concepto]</td>
                           <td>$fila[trabajador]</td>
                           <td>$ $fila[valor]</td>  
                        </tr>";

                  return $row;
              break;

              case 'ReporteGastos':   
                  $fila['valor'] = $this->format($fila['valor']); 
                  $row ="<tr>
                           <td>$fila[fecha]</td> 
                           <td>$fila[concepto]</td>
                           <td>$fila[trabajador]</td>
                           <td>$ $fila[valor]</td>  
                        </tr>";

                  return $row;
              break;

              case 'ReporteServicioTecnico':   
                  $fila['precio'] = $this->format($fila['precio']); 
                  $row ="<tr>
                           <td>$fila[fecha]</td> 
                           <td>$fila[cliente]</td>
                           <td>$fila[cedula]</td>
                           <td>$fila[telefono]</td>
                           <td>$fila[trabajador]</td>
                           <td>$fila[imei]</td>
                           <td>$fila[observaciones]</td>
                           <td>$ $fila[precio]</td>  
                        </tr>";

                  return $row;
              break;

          }
      }
      function GetRowLast( $source_row , $fila )
      {
          switch ($source_row) 
          { 
              case 'inventario':
                  $total_precio = $fila[0]['precio'] * $fila[0]['cantidad'];
                  $total_costo = $fila[0]['costo'] * $fila[0]['cantidad'];                         
 
                  $costo = number_format( $fila[0]['costo'] );
                  $precio = number_format( $fila[0]['precio'] );
                  $costo = number_format( $fila[0]['costo'] );
                  $total_precio = number_format( $total_precio );
                  $total_costo = number_format( $total_costo ); 

                  $cantidad = $fila[0]['cantidad'];

                  $row ="<tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td class='text-bold text-total'>$cantidad</td>
                           <td class='text-bold text-total'></td>
                           <td class='text-bold text-total'>$ $costo</td>
                           <td class='text-bold text-total'>$ $precio</td>
                           <td class='text-bold text-total'>$ $total_costo</td>
                           <td class='text-bold text-total'>$ $total_precio</td>
                        </tr>";
                  $row ="<tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td class='text-bold text-total'></td>
                           <td class='text-bold text-total'></td>
                           <td class='text-bold text-total'></td>
                           <td class='text-bold text-total'></td>
                           <td class='text-bold text-total'></td>
                           <td class='text-bold text-total'></td>
                           <td class='text-bold text-total'></td> 
                           
                        </tr>";

                  return $row;
              break; 

              case "ReporteVentas":
                  $fila['total'] = $this->format( $fila['total'] );
                   

                  $row ="<tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td colspan='8'></td>
                           <td class='text-bold text-total'>$fila[total]</td>
                           <td colspan='2'></td>
                        </tr>";
                  return $row;
              break;

              case "PorCobrar":
                  $total = $fila['total'] - $fila['abono'];
                  $fila['total'] = $this->format( $fila['total'] );
                  $fila['abono'] = $this->format( $fila['abono'] );
                   

                  $row ="
                        <tr>
                           <td class='text-bold text-total'>ABONOS</td> 
                           <td colspan='4'></td>
                           <td class='text-bold text-total'>$fila[abono]</td>
                        </tr>
                        <tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td colspan='4'></td>
                           <td class='text-bold text-total'>$fila[total]</td>
                        </tr>
                       ";
                  return $row;
              break;

              case "PorPagar":
                  $fila['total'] = $this->format( $fila['total'] );
                  $fila['abono'] = $this->format( $fila['abono'] );
                   

                  $row ="
                        <tr>
                           <td class='text-bold text-total'>ABONOS</td> 
                           <td colspan='4'></td>
                           <td class='text-bold text-total'>$fila[abono]</td>
                        </tr>
                        <tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td colspan='4'></td>
                           <td class='text-bold text-total'>$fila[total]</td>
                        </tr>
                       ";
                  return $row;
              break;

              case 'ReporteEgresos':
                  $fila['total'] = $this->format( $fila['total'] );
                   

                  $row ="<tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td colspan='2'></td>
                           <td class='text-bold text-total'>$ $fila[total]</td>
                        </tr>";

                  return $row;
              break; 

              case 'ReporteGastos':
                  $fila['total'] = $this->format( $fila['total'] );
                   

                  $row ="<tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td colspan='2'></td>
                           <td class='text-bold text-total'>$ $fila[total]</td>
                        </tr>";

                  return $row;
              break; 
              case 'ReporteServicioTecnico':
                  $fila['total'] = $this->format( $fila['total'] );
                   

                  $row ="<tr>
                           <td class='text-bold text-total'>TOTAL</td> 
                           <td colspan='6'></td>
                           <td class='text-bold text-total'>$ $fila[total]</td>
                        </tr>";

                  return $row;
              break; 
          }
      }
      function format( $value )
      {
          return number_format( $value , 2 , "," , "." );
      }
      function GetNameMonth($mes)
      {
        switch($mes)
        {
             case 1:
               return "Enero";
             break; 
             
             case 2:
               return "Febrero";
             break;
             
             case 3:
               return "Marzo";
             break;
             
             case 4:
               return "Abril";
             break;
             
             case 5:
               return "Mayo";
             break;
             
             case 6:
               return "Junio";
             break;
             
             case 7:
               return "Julio";
             break;
   
             case 8:
               return "Agosto";
             break;

             case 9:
               return "Septiembre";
             break;
             
             case 10:
               return "Octubre";
             break;

             case 11:
               return "Noviembre";
             break;
             
             case 12:
               return "Diciembre";
             break;
        }
      }
      function GetLastDayMonth($mes)
      {
        switch($mes)
        {
             case 1:
               return 31;
             break; 
             
             case 2:
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
      function CheckAccess( $connection , $submenu_id , $usuario_id)
      {
          $query = "SELECT * FROM usuario_submenu WHERE usuario_id = $usuario_id AND submenu_id = $submenu_id AND status = true";
          $data = $connection->query($query);  
          $results = $data->fetchAll(PDO::FETCH_BOTH );  
          
          return count($results);
      }
      function EliminarPosicion( $array , $key )
      {
           for ($i=0; $i < count($array); $i++) 
           { 

              if( $array[$i] == $key )
              {
                 array_splice($array, $i, 1);              
                 break;
              }
           }

           return $array;
      }
  }
?>