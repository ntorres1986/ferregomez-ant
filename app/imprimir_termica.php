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

require 'escpos-php/autoload.php';
use Mike42\Escpos\Printer; 

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector; 
$connector = new NetworkPrintConnector("192.168.0.20", 9100);
$printer = new Printer($connector);
  
class item
{
    private $name;
    private $price;
    private $dollarSign;
    private $cant;

    public function __construct($name = '', $price = '', $dollarSign = false , $cant = '')
    {
        $this -> name = $name;
        $this -> price = $price;
        $this -> dollarSign = $dollarSign;
        $this -> cant = $cant;
    }
    
    public function __toString()
    {
        $rightCols = 6;
        $leftCols = 36;
        $finalCols = 6;
       

        //$first = str_pad( $this->cant, 2 , ".");
        $first = $this->cant;

        echo  $first."<br>"; 

        $name = str_pad($this->name, $leftCols ,' ') ;
         

        $sign = ($this->dollarSign ? '$ ' : '');
        $price = str_pad(  $this->price, $rightCols, ' ', STR_PAD_LEFT);
        $cant = str_pad(  $this->cant, $finalCols, ' ', STR_PAD_LEFT);

        return "$name$cant$price\n";
    }
} 
 
 if(!empty($opcion))
 {
     switch($opcion)
     { 
          case "imprimir_venta":
 
                $query = "SELECT 
                       venta.factura,
                       venta.bolsas,
                       venta.vr_bolsas,
                       producto_venta.cantidad ,
                       producto_venta.precio ,
                       producto.nombre ,
                       YEAR( venta.fecha ) year ,
                       MONTH( venta.fecha ) month ,
                       DAY( venta.fecha ) day ,
                       (producto_venta.cantidad * producto_venta.precio) as total ,
                       concat(nombres,' ',apellidos) cliente ,
                       concat(usuario.nombre,' ',usuario.apellido) vendedor ,
                       cliente.documento ,
                       tipo_venta_id ,
                       venta.usuario_id ,
                       cliente.telefono ,
                       cliente.direccion
                  FROM venta 
                  INNER JOIN producto_venta ON venta_id = venta.id
                  INNER JOIN producto ON producto.id = producto_venta.producto_id
                  LEFT JOIN cliente ON venta.cliente_id = cliente.id 
                  LEFT JOIN usuario ON venta.usuario_id = usuario.id 
                  WHERE venta.id = $id";
                $data = $connection->query($query);
                $results = $data->fetchAll(PDO::FETCH_BOTH);  
                
                
                 
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                 
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
                $printer -> text("FERREGOMEZ JP\n");
                $printer -> selectPrintMode();
                $printer -> text( "Venta de Electricos y Ferreteria en General.\n" );
                $printer -> feed();


                $printer -> text("------------------------------------------------\n");
                $printer -> feed();

                if( !empty($results[0]['cliente']))
                {

                  $texto = str_pad(  "CLIENTE" , 7, ' ' );
                  $printer -> text($texto);
   
                  $total = str_pad( strtoupper( $results[0]['cliente'] )  , 41, ' ' , STR_PAD_LEFT);
                  $printer -> text($total);
                  $printer -> text("\n");

                  
                }
                if( !empty($results[0]['vendedor']))
                {

                  $texto = str_pad(  "VENDEDOR" , 9, ' ' );
                  $printer -> text($texto);
   
                  $total = str_pad( strtoupper( $results[0]['vendedor'] )  , 39, ' ' , STR_PAD_LEFT);
                  $printer -> text($total);
 
                }
                $printer -> text("\n");

                $printer -> text("------------------------------------------------\n");
                

                $printer -> text("\n");
                $printer -> feed();

                $printer -> setEmphasis(true);
                $printer -> text("REMISION\n");
                $printer -> setEmphasis(false);

                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> setEmphasis(true);



                $espacioCantidad = 6;
                $espacioProducto = 28;
                $espacioPrecio = 6;
                $espacioSub = 8; 

                $cantidad = str_pad("Cant", $espacioCantidad , " " ) ; 
                $producto = str_pad("Producto", $espacioProducto , " " ) ; 
                $precio = str_pad("Prec", $espacioPrecio , " " ) ; 
                $subTot = str_pad("Subt", $espacioSub , " " , STR_PAD_LEFT) ; 
                 
                $printer->text("$cantidad$producto$precio$subTot\n");
 
               $printer->setEmphasis(false);

               $total = 0;
               
 
                 
	               if( count( $results) > 0 )
	               { 
	                   foreach ($results as $key => $row) 
	                   {   
                          $total += $row['total'];

                          //$row['precio'] = number_format($row['precio'],0,"",".");
                          $subtotal = $row['precio'] * $row['cantidad'];

                          if( strlen( $row['nombre'] ) > 27 )
                          {
                              $row['nombre'] = substr ( $row['nombre'] , 0 , 27 );

                          } 


                          $cantidad = str_pad($row['cantidad'], $espacioCantidad , " " ) ; 
                          $producto = str_pad($row['nombre'], $espacioProducto , " " ) ; 
                          $precio = str_pad($row['precio'], $espacioPrecio , " " ) ; 
                          $subTot = str_pad($subtotal, $espacioSub , " " , STR_PAD_LEFT) ; 

                          $printer->text("$cantidad$producto$precio$subTot\n");
 
	                   }
	               }


                 $query = "SELECT * FROM venta_anexo WHERE venta_id = $id ";
                 $data = $connection->query($query);
                 $resultsAnexo = $data->fetchAll(PDO::FETCH_BOTH);
                 if( count($resultsAnexo) > 0 )
                 {
                      foreach( $resultsAnexo as $row )
                      {      
                         
                         $row['nombre'] = utf8_encode($row['nombre']); 
                         if( strlen( $row['nombre'] ) > 27 )
                         {
                              $row['nombre'] = substr ( $row['nombre'] , 0 , 27 ); 
                         } 
                        
                         $subtotal = $row['precio'] * $row['cantidad']; 
                         $total += $subtotal;         

                         $cantidad = str_pad($row['cantidad'], $espacioCantidad , " " ) ; 
                         $producto = str_pad($row['nombre'], $espacioProducto , " " ) ; 
                         $precio = str_pad($row['precio'], $espacioPrecio , " " ) ; 
                         $subTot = str_pad($subtotal, $espacioSub , " " , STR_PAD_LEFT ) ; 

                         $printer->text("$cantidad$producto$precio$subTot\n");
  
                      }
                 } 
	              

                
                $date = date('Y-m-d H:i:s');
        


                $printer -> setEmphasis(true); 

                 
	              $total = number_format($total,0,"",".");

 
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

                $printer -> text("\n"); 

                $texto = str_pad(  "TOTAL" , 5, ' ' );
                $printer -> text($texto);
 
                $total = str_pad(  $total , 19, ' ' , STR_PAD_LEFT);
                $printer -> text($total);

                $printer -> selectPrintMode();


				        $printer -> setEmphasis(false);
                $printer -> feed();

                $printer -> feed(2);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text("Gracias por comprar en FerregomezJP\n");
                $printer -> text( "Para mayor informacion visite \nwww.ferregomezjp.com\n");
                $printer -> feed(2);
                $printer -> text($date . "\n");

                $printer -> cut();
                $printer -> pulse();

                $printer -> close(); 
          break; 

          case "imprimir_cotizacion":
 
                $query = "SELECT 
                               concat(nombres,' ',apellidos) cliente ,
                               concat(usuario.nombre,' ',usuario.apellido) vendedor ,
                               cliente.documento ,
                               cotizacion.usuario_id ,
                               cliente.telefono ,
                               cliente.direccion
                          FROM cotizacion 
                          LEFT JOIN cliente ON cotizacion.cliente_id = cliente.id 
                          LEFT JOIN usuario ON cotizacion.usuario_id = usuario.id 
                          WHERE cotizacion.id = $id";
                $data = $connection->query($query);
                $results = $data->fetchAll(PDO::FETCH_BOTH);  
                
                
                 
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                 
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
                $printer -> text("FERREGOMEZ JP\n");
                $printer -> selectPrintMode();
                $printer -> text( "Venta de Electricos y Ferreteria en General.\n" );
                $printer -> feed();


                $printer -> text("------------------------------------------------\n");
                $printer -> feed();

                if( !empty($results[0]['cliente']))
                {

                  $texto = str_pad(  "CLIENTE" , 7, ' ' );
                  $printer -> text($texto);
   
                  $total = str_pad( strtoupper( $results[0]['cliente'] )  , 41, ' ' , STR_PAD_LEFT);
                  $printer -> text($total);
                  $printer -> text("\n");

                  
                }
                if( !empty($results[0]['vendedor']))
                {

                  $texto = str_pad(  "VENDEDOR" , 9, ' ' );
                  $printer -> text($texto);
   
                  $total = str_pad( strtoupper( $results[0]['vendedor'] )  , 39, ' ' , STR_PAD_LEFT);
                  $printer -> text($total);
 
                }
                $printer -> text("\n");

                $printer -> text("------------------------------------------------\n");
                

                $printer -> text("\n");
                $printer -> feed();

                $printer -> setEmphasis(true);
                $printer -> text("COTIZACION\n");
                $printer -> setEmphasis(false);

                $printer -> setJustification(Printer::JUSTIFY_LEFT);
                $printer -> setEmphasis(true);



                $espacioCantidad = 6;
                $espacioProducto = 28;
                $espacioPrecio = 6;
                $espacioSub = 8; 

                $cantidad = str_pad("Cant", $espacioCantidad , " " ) ; 
                $producto = str_pad("Producto", $espacioProducto , " " ) ; 
                $precio = str_pad("Prec", $espacioPrecio , " " ) ; 
                $subTot = str_pad("Subt", $espacioSub , " " , STR_PAD_LEFT) ; 
                 
                $printer->text("$cantidad$producto$precio$subTot\n");
 
               $printer -> setEmphasis(false);

               $total = 0;


               $query = "SELECT 
                       producto_cotizacion.cantidad ,
                       producto_cotizacion.precio ,
                       producto.nombre ,
                       YEAR( cotizacion.fecha ) year ,
                       MONTH( cotizacion.fecha ) month ,
                       DAY( cotizacion.fecha ) day ,
                       (producto_cotizacion.cantidad * producto_cotizacion.precio) as total 
                  FROM cotizacion 
                  INNER JOIN producto_cotizacion ON cotizacion_id = cotizacion.id
                  INNER JOIN producto ON producto.id = producto_cotizacion.producto_id
                  WHERE cotizacion.id = $id";
                $data = $connection->query($query);
                $results = $data->fetchAll(PDO::FETCH_BOTH);  
                 
               if( count( $results) > 0 )
               { 
                   foreach ($results as $key => $row) 
                   {   
                      $total += $row['total'];

                      //$row['precio'] = number_format($row['precio'],0,"",".");
                      $subtotal = $row['precio'] * $row['cantidad'];

                      if( strlen( $row['nombre'] ) > 27 )
                      {
                          $row['nombre'] = substr ( $row['nombre'] , 0 , 27 );

                      }

                      $cantidad = str_pad($row['cantidad'], $espacioCantidad , " " ) ; 
                      $producto = str_pad($row['nombre'], $espacioProducto , " " ) ; 
                      $precio = str_pad($row['precio'], $espacioPrecio , " " ) ; 
                      $subTot = str_pad($subtotal, $espacioSub , " " , STR_PAD_LEFT) ; 

                      $printer->text("$cantidad$producto$precio$subTot\n");

                   }
               }


               
                $date = date('Y-m-d H:i:s');

                $printer -> setEmphasis(true); 
                 
                $total = number_format($total,0,"",".");
 
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

                $printer -> text("\n"); 

                $texto = str_pad(  "TOTAL" , 5, ' ' );
                $printer -> text($texto);
 
                $total = str_pad(  $total , 19, ' ' , STR_PAD_LEFT);
                $printer -> text($total);

                $printer -> selectPrintMode();


                $printer -> setEmphasis(false);
                $printer -> feed();

                $printer -> feed(2);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text("Gracias por elegir a FerregomezJP\n");
                $printer -> text( "Para mayor informacion visite \nwww.ferregomezjp.com\n");
                $printer -> feed(2);
                $printer -> text($date . "\n");

                $printer -> cut();
                $printer -> pulse();

                $printer -> close(); 
          break; 
     }
 }
?>
